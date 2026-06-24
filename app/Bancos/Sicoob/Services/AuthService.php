<?php
namespace App\Bancos\Sicoob\Services;

use App\Services\IntegracaoCredencialService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\Integracao;

class AuthService
{
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