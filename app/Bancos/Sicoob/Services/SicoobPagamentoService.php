<?php

namespace App\Bancos\Sicoob\Services;

use App\Exceptions\SicoobException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\BoletoCobranca;
use App\Models\Integracao;

use App\Bancos\Sicoob\Payloads\DDAPayload;

class SicoobPagamentoService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    public function ddaSandbox($dataInicial, $dataFinal, $situacao, $numConta){
        $client_id = $this->integracao->credenciais->client_id; # em SANDBOX isso só vai ser passado no header client_id => $client_id
        $access_token = $this->integracao->credenciais->access_token; # em SANDBOX atua como um bearer
       
        $payLoadMounter = new DDAPayload;

        $payload = $payLoadMounter->payloadMount($dataInicial, $dataFinal, $numConta, $situacao);

        \Log::debug(['Payload de resgate de cobranca SANDBOX DDA' => $payload]);

        $response = Http::withToken($access_token)
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->get(
                $this->integracao->endpoint . 'cobranca-bancaria-pagamentos/v3/boletos',
                $payload
            );

        if(!$response->successful()) {
            \Log::error([
                'Erro ao resgatar cobrancas do DDA' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id
                ]
            ]);

            throw new SicoobException(
                'Erro ao resgatar cobrancas do DDA',
                $response->status(),
                $response->body()
            );
        }

        $resultados = collect($response->json());

        return $resultados->map(function ($boleto) {
            return [
                'vencimento' => Carbon::parse($boleto['dataVencimentoBoleto']),
                'nome_beneficiario' => $boleto['nomeRazaoSocialBeneficiario'],
                'documento_beneficiario' => $boleto['numeroCpfCnpjBeneficiario'],
                'linha_digitavel' => $boleto['numeroCodigoBarras'],
                'valor' => $boleto['valorBoleto'],
                'situacao' => $boleto['descricaoSituacaoBoleto']
            ];
        })->values()->all();
    }

    public function ddaProducao($dataInicial, $dataFinal, $situacao, $numConta){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pagamentos_consulta');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payLoadMounter = new DDAPayload;
        $payload = $payLoadMounter->payloadMount($dataInicial, $dataFinal, $numConta, $situacao);

        \Log::debug(['Payload de resgate de cobranca DDA' => $payload]);

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->get(
                $this->integracao->endpoint . 'pagamentos/v3/boletos',
                $payload
            );

        if(!$response->successful()) {
            \Log::error([
                'Erro ao resgatar cobrancas do DDA' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id
                ]
            ]);

            throw new SicoobException(
                'Erro ao resgatar cobrancas do DDA',
                $response->status(),
                $response->body()
            );
        }

        $resultados = collect($response->json('resultado'));

        return $resultados->map(function ($boleto) {
            return [
                'vencimento' => Carbon::parse($boleto['dataVencimentoBoleto']),
                'nome_beneficiario' => $boleto['nomeRazaoSocialBeneficiario'],
                'documento_beneficiario' => $boleto['numeroCpfCnpjBeneficiario'],
                'linha_digitavel' => $boleto['numeroCodigoBarras'],
                'valor' => $boleto['valorBoleto'],
                'situacao' => $boleto['descricaoSituacaoBoleto']
            ];
        })->values()->all();

    }
}