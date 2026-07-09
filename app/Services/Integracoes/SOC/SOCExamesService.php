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
    public function getFaturamento($dataInicial, $dataFinal){
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

        return $response->body();

    }
}

?>