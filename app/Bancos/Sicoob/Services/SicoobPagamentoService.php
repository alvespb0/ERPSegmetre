<?php

namespace App\Bancos\Sicoob\Services;

use App\Exceptions\SicoobException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\BoletoCobranca;
use App\Models\Conta;
use App\Models\Integracao;

use App\Bancos\Sicoob\Payloads\DDAPayload;
use App\Bancos\Sicoob\Payloads\PagamentoPayload;

use App\Helpers\IdempotencyKey;

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

    /**
     * Consulta boletos DDA no ambiente de Produção.
     *
     * Realiza autenticação OAuth, utiliza certificado digital e
     * consulta a API de pagamentos do Sicoob.
     *
     * @param string|\DateTimeInterface $dataInicial Data inicial da consulta.
     * @param string|\DateTimeInterface $dataFinal Data final da consulta.
     * @param string $situacao Situação dos boletos.
     * @param string $numConta Número da conta.
     *
     * @return array<int, array{
     *     vencimento: Carbon,
     *     nome_beneficiario: string,
     *     documento_beneficiario: string,
     *     linha_digitavel: string,
     *     valor: float|int|string,
     *     situacao: string
     * }>
     *
     * @throws SicoobException Quando a API retornar erro.
     */
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

    public function consultaBoletoPagamentoProducao(Conta $conta, $codigoBarras){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pagamentos_consulta');
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
                $this->integracao->endpoint . "pagamentos/v3/boletos/{$codigoBarras}", [
                    'numeroConta' => preg_replace('/-/', '', $conta->conta)
                ]
            );

        if(!$response->successful()) {
            \Log::error([
                'Erro ao resgatar boleto pagamento' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id
                ]
            ]);

            throw new SicoobException(
                'Erro ao resgatar boleto para pagamento',
                $response->status(),
                $response->body()
            );
        }

        $resultado = $response->json('resultado');

        return [
            'banco_beneficiario' => $resultado['nomeInstituicaoEmissora'],
            'cpf_cnpj_beneficiario' => $resultado['numeroCpfCnpjBeneficiario'],
            'razao_social_beneficiario' => $resultado['nomeRazaoSocialBeneficiario'],
            'nome_fantasia_beneficiario' => $resultado['nomeFantasiaBeneficiario'] ?? null,
            'cpf_cnpj_pagador' => $resultado['numeroCpfCnpjPagador'],
            'razao_social_pagador' => $resultado['nomeRazaoSocialPagador'],
            'nome_fantasia_pagador' => $resultado['nomeFantasiaPagador'] ?? null,
            'valor_boleto' => $resultado['valorBoleto'],
            'valor_abatimento' => $resultado['valorAbatimentoDesconto'],
            'valor_multa' => $resultado['valorMultaMora'] ?? 0.00,
            'valor_final' => $resultado['valorPagamento'] ?? $resultado['valorBoleto'],
            'vencimento_boleto' => $resultado['dataVencimentoBoleto'],
            'aceita_valor_divergente' => $resultado['permiteAlterarValor'], # bool
            'identificador_consulta' => $resultado['identificadorConsulta'] # isso vem em hash e é requisito obrigatorio para o post de pagamento de boleto
        ];
    }

    public function processarPagamentoProducao(Conta $conta, string $codigoBarras, array $boletoPagamento){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pagamentos_inclusao');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payloadMounter = new PagamentoPayload;

        $payload = $payloadMounter->payloadMount($boletoPagamento, $conta);

        \Log::debug([
            'Payload de pagamaento' => $payload
        ]);
        
        $idemKey = IdempotencyKey::make(strtok($conta->agencia, '-'), preg_replace('/-/', '', $conta->conta));

        #dd([$payload, $idemKey]);

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path),
                'decode_content' => false,
            ])
            ->withHeaders([
                'client_id' => $client_id,
                'x-idempotency-key' => $idemKey,
            ])
            ->post(
                $this->integracao->endpoint . "pagamentos/v3/boletos/pagamentos/{$codigoBarras}", $payload
            );

        if(!$response->successful()) {
            \Log::error([
                'Erro na tentativa de pagamento' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id
                ]
            ]);

            throw new SicoobException(
                'Erro na tentativa de pagamento',
                $response->status(),
                $response->body()
            );
        }

        $resultado = $response->json('resultado');

        return match ($response->status()) {
            200 => match ($resultado['situacaoPagamento']) {
                'Efetivado' => [
                    'status' => 'pago',
                    'idempotency_key' => $idemKey,

                    'destinatario' => array_filter([
                        'nome' => $resultado['nomeRazaoSocialBeneficiario'] ?? null,
                        'cpf_cnpj' => $resultado['numeroCpfCnpjBeneficiario'] ?? null,
                        'banco' => $resultado['nomeInstituicaoEmissora'] ?? null,
                        'documento' => $resultado['numeroDocumento'] ?? null,
                        'nosso_numero' => $resultado['nossoNumero'] ?? null,
                    ], fn ($v) => !is_null($v)),

                    'pagador' => array_filter([
                        'nome' => $resultado['nomeRazaoSocialPagador'] ?? null,
                        'cpf_cnpj' => $resultado['numeroCpfCnpjPagador'] ?? null,
                    ], fn ($v) => !is_null($v)),

                    'pagamento' => array_filter([
                        'valor' => $resultado['valorPagamento'] ?? null,
                        'valor_boleto' => $resultado['valorBoleto'] ?? null,
                        'valor_desconto' => $resultado['valorAbatimentoDesconto'] ?? null,
                        'valor_multa' => $resultado['valorMultaMora'] ?? null,
                        'data_pagamento' => $resultado['dataPagamento'] ?? null,
                        'data_vencimento' => $resultado['dataVencimento'] ?? null,
                        'codigo_autenticacao' => $resultado['numeroAutenticacaoPagamento'] ?? null,
                        'id_pagamento' => $resultado['idPagamento'] ?? null,
                    ], fn ($v) => !is_null($v)),
                ],

                'Agendado' => [
                    'status' => 'agendado',
                    'idempotency_key' => $idemKey,
                    'mensagem' => $resultado['descricaoDetalheSituacao'] ?? null,
                ],

                'Rejeitado' => [
                    'status' => 'rejeitado',
                    'idempotency_key' => $idemKey,
                    'mensagem' => $resultado['descricaoDetalheSituacao'] ?? null,
                ],

                default => throw new SicoobException(
                    'Situação de pagamento desconhecida.',
                    $response->status(),
                    $response->body()
                ),
            },

            202 => [
                'status' => 'pendente_assinatura',
                'idempotency_key' => $idemKey,
            ],

            204 => [
                'status' => 'processado',
                'idempotency_key' => $idemKey,
            ],

            default => throw new SicoobException(
                'Resposta inesperada do Sicoob.',
                $response->status(),
                $response->body()
            ),
        };
    }

}