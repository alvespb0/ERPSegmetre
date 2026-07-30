<?php

namespace App\Bancos\Sicoob\Services;

use App\Exceptions\SicoobException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\Integracao;

class SicoobCCOService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    public function getSaldoSandbox(){
        return [
            'saldo' => 100.00,
            'limite' => 1000.00,
            'bloqueado' => 0.00
        ];
    }

    public function getSaldoProducao($numConta){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'cco_consulta');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->get(
                $this->integracao->endpoint . 'conta-corrente/v4/saldo',[
                    'numeroContaCorrente' => $numConta
                ]
            );

        if(!$response->successful()) {
            \Log::error([
                'Erro ao resgatar saldo da conta corrente' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id
                ]
            ]);

            throw new SicoobException(
                'Erro ao resgatar saldo SICOOB',
                $response->status(),
                $response->body()
            );
        }

        $resultado = $response->json('resultado');

        return [
            'saldo' => $resultado['saldo'] ?? 0.00,
            'limite' => $resultado['saldoLimite'] ?? 0.00,
            'bloqueado' => $resultado['saldoBloqueado'] ?? 0.00,
            'origem' => 'api',
        ];
    }

    
}