<?php
namespace App\Bancos\Sicoob\Services;

use App\Services\IntegracaoCredencialService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\Integracao;

/**
 * Responsável por autenticar e obter tokens OAuth2 da API do Sicoob.
 *
 * O serviço reutiliza tokens válidos previamente armazenados
 * na integração, evitando chamadas desnecessárias ao endpoint
 * de autenticação.
 *
 * Caso não exista token válido para o escopo solicitado,
 * uma nova autenticação é realizada utilizando certificado
 * digital e fluxo Client Credentials.
 */
class AuthService
{
    /**
     * Obtém um access token válido para o escopo informado.
     *
     * Fluxo:
     * - Valida se a integração possui credenciais cadastradas;
     * - Verifica se existe token ainda válido para o mesmo escopo;
     * - Valida existência de certificado digital;
     * - Solicita novo token ao Sicoob caso necessário;
     * - Atualiza as credenciais armazenadas;
     * - Retorna o access token.
     *
     * @param Integracao $integracao Integração Sicoob.
     * @param string $scope Escopo solicitado pela API.
     *
     * @return string Access token válido.
     *
     * @throws \Exception Quando não existirem credenciais,
     * certificado digital ou ocorrer falha na autenticação.
     */
    public function auth(Integracao $integracao, $scope){
        $empresaParametro = $integracao->empresaParametro; # objeto

        if(!$integracao->credenciais){
            throw new \Exception('Essa integracao não possui credenciais cadastradas');
            return;
        }

        $credenciais = $integracao->credenciais; # objeto

        if($credenciais->token_valido && $credenciais->scope == $scope){ # acessor
            return $credenciais->access_token;
        }

        if(!$empresaParametro->certificadoDigital){
            throw new \Exception('Empresa base não possui certificado digital válido');
            return;
        }

        $cert = $empresaParametro->certificadoDigital; # objeto

        $response = $this->getAccessToken($credenciais, $cert, $scope);

        if(!$response->successful()){
            \Log::error([
                'Erro ao tentar obter access token sicoob' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]
            ]);
            
            throw new \Exception('Erro ao autenticar com a sicoob.');
            return;
        }

        $response->json();

        $service = new IntegracaoCredencialService;

        $service->update($credenciais->id, [
            'access_token' => $response['access_token'],
            'token_expires_at' => Carbon::now()->addSeconds($response['expires_in']),
            'scope' => $scope
        ]);

        return $response['access_token'];
    }

    /**
     * Solicita um novo access token ao endpoint OAuth2 do Sicoob.
     *
     * A autenticação é realizada através do fluxo
     * Client Credentials utilizando certificado digital.
     *
     * O certificado PEM deve conter tanto o certificado
     * público quanto a chave privada.
     *
     * Endpoint:
     * https://auth.sicoob.com.br/auth/realms/cooperado/protocol/openid-connect/token
     *
     * @param mixed $credenciais Credenciais da integração.
     * @param mixed $cert Certificado digital vinculado à empresa.
     * @param string $scope Escopo solicitado.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function getAccessToken($credenciais, $cert, $scope){
        $response = Http::asForm()
        ->withOptions([
            'cert' => Storage::disk('local')->path($cert->cert_path)
        ])
        ->withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])
        ->post('https://auth.sicoob.com.br/auth/realms/cooperado/protocol/openid-connect/token',[
            'client_id' => $credenciais->client_id,
            'grant_type' => 'client_credentials',
            'scope' => $scope
        ]);

        return $response;
    }
}