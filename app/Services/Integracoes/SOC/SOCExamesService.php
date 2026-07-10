<?php
namespace App\Services\Integracoes\SOC;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

use App\Models\Integracao;

class SOCExamesService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    /**
     * Integracao para puxar os exames valorizados no sistema SOC
     * Sistema legado, ele PEDE esse formato d/m/Y
     */
    public function getFaturamento($dataInicial, $dataFinal): array{
        $dataInicialFormatada = Carbon::parse($dataInicial)->format('d/m/Y'); 
        $dataFinalFormatada = Carbon::parse($dataFinal)->format('d/m/Y');

        if(!$this->integracao->credenciais){
            throw new \Exception([
                'Integracao não possui credenciais cadastradas'
            ]);
        }

        $codEmpresa = $this->integracao->credenciais->username;
        $token = Crypt::decryptString($this->integracao->credenciais->password_enc);

        $jsonString = "{\"empresa\":\"{$codEmpresa}\",\"codigo\":\"217845\",\"chave\":\"{$token}\",\"tipoSaida\":\"json\",\"dataInicio\":\"{$dataInicialFormatada}\",\"dataFim\":\"{$dataFinalFormatada}\"}";

        $response = Http::get($this->integracao->endpoint, [
            'parametro' => $jsonString,
        ]);

        if($response->ok()){
            $body = $response->body();
            $bodyUtf8 = $this->convertToUtf8($body);
            $dados = json_decode($bodyUtf8, true);

            if(empty($dados)){
                throw new \Exception([
                    'Não foi possível resgatar a valorização do SOC, conjunto de dados retornou vazio.'
                ]);
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