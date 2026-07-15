<?php
namespace App\Services\Integracoes\SOC;

use App\Exceptions\SocException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

use App\Models\Integracao;

class SOCEmpresasService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    public function getEmpresasSoc(): array{
        if(!$this->integracao->credenciais){
            throw new SocException(
                'Nao possuem credenciais cadastradas para essa integracao. Empresa parametro: ' . $this->integracao->empresa_parametro_id,
                'Credenciais não localizadas para essa integracao SOC.'
            );
        }

        $codEmpresa = $this->integracao->credenciais->username;
        $token = Crypt::decryptString($this->integracao->credenciais->password_enc);

        $jsonString = "{\"empresa\":\"$codEmpresa\",\"codigo\":\"211287\",\"chave\":\"{$token}\",\"tipoSaida\":\"json\"}";

        $response = Http::get($this->integracao->endpoint, [
            'parametro' => $jsonString
        ]);

        if($response->ok()){
            $body = $response->body();
            $bodyUtf8 = $this->convertToUtf8($body);
            $dados = json_decode($bodyUtf8, true);
            if(empty($dados)){
                throw new SocException(
                    'Nao foi possivel resgatar as empresas SOC.',
                    'Erro ao resgatar empresas SOC, SOC Retornou um conjunto vazio de empresas.'
                );
            }

            return $dados;
        }

        return [];
    }

    /**
     * Converte uma string para UTF-8, preservando caracteres especiais
     * 
     * Tenta detectar o encoding atual e converte para UTF-8.
     * Se a detecção falhar, tenta converter de ISO-8859-1 para UTF-8.
     * 
     * @param string $string String a ser convertida
     * @return string String convertida para UTF-8
     */
    private function convertToUtf8(string $string): string
    {
        // Detecta o encoding atual
        $encoding = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        // Se já está em UTF-8, retorna como está
        if ($encoding === 'UTF-8') {
            return $string;
        }
        // Converte para UTF-8
        return mb_convert_encoding($string, 'UTF-8', $encoding ?: 'ISO-8859-1');
    }

}

?>