<?php

namespace App\Bancos\Sicoob\Services;

use App\Exceptions\SicoobException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\BoletoCobranca;
use App\Models\Conta;
use App\Models\SolicitacoesPagamento;
use App\Models\Integracao;

use App\Bancos\Sicoob\Payloads\IniciaPixPayload;
use App\Bancos\Sicoob\Payloads\ConfirmaPixPayload;

class SicoobPixPagamentoService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    public function iniciaPixProducao($chave, $dataPagamento = null){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pixpagamentos_escrita');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payloadMounter = new IniciaPixPayload;

        $payload = $payloadMounter->payloadMount($chave, $dataPagamento);

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id
            ])
            ->post(
                $this->integracao->endpoint . 'pix-pagamentos/v2/pagamentos', $payload);

        if(!$response->successful()) {
            \Log::error([
                'Erro ao iniciar pagamento pix por chave DICT' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id,
                ]
            ]);

            throw new SicoobException(
                'Erro ao iniciar pagamento pix por chave DICT',
                $response->status(),
                $response->body()
            );
        }
        
        $resultado = $response->json();

        return [
            'e2eId' => $resultado['endToEndId'],
            'chave' => $resultado['chave'],
            'tipo' => $resultado['tipo'],
            'proprietario' => [
                'nome' => $resultado['proprietario']['nome'],
                'identificador' => $resultado['proprietario']['identificador']
            ]
        ];
    }

    public function confirmaPixProducao($e2eId, $valor, $tipoPix){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pixpagamentos_escrita');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payloadMounter = new ConfirmaPixPayload;

        $meioIniciacao = '';

        if($tipoPix == 'chave'){
            $meioIniciacao = 'CHAVE';
        }else if($tipoPix == 'copia_cola'){
            $meioIniciacao = 'QRCODE';
        }else { 
            \Log::error([
                    'Erro ao confirmar pagamento pix, erro ao resgatar tipo de pagamento pix' => [
                    'empresa_parametro' => $this->integracao->empresa_parametro_id,
                ]
            ]);

            throw new \Exception(
                'Erro ao confirmar pagamento pix, erro ao resgatar tipo de pagamento pix',
            );
        }

        $payload = $payloadMounter->payloadMount($e2eId, $valor, $meioIniciacao);

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id
            ])
            ->post(
                $this->integracao->endpoint . 'pix-pagamentos/v2/pagamentos/confirmacao', $payload);

        if(!$response->successful()) {
            \Log::error([
                'Erro ao confirmar pagamento pix' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id,
                ]
            ]);

            throw new SicoobException(
                'Erro ao confirmar pagamento pix',
                $response->status(),
                $response->body()
            );
        }
        
        $resultado = $response->json();

        return match($resultado['estado']){
            'FINALIZADO' => [
                'status' => 'pago',
                'endToEndId' => $resultado['endToEndId'],

                'destinatario' => array_filter([
                    'nome' => $resultado['destino']['nome'] ?? null,
                    'cpf_cnpj' => $resultado['destino']['cpfCnpj'] ?? null,
                    'conta' => $resultado['destino']['conta'] ?? null,
                    'agencia' => $resultado['destino']['agencia'] ?? null,
                    'tipo_conta' => $resultado['destino']['tipo'] ?? null,
                ], fn ($v) => !is_null($v)),

                'pagador' => array_filter([
                    'nome' => $resultado['origem']['nome'] ?? null,
                    'cpf_cnpj' => $resultado['origem']['cpfCnpj'] ?? null,
                    'conta' => $resultado['origem']['conta'] ?? null,
                    'agencia' => $resultado['origem']['agencia'] ?? null,
                    'tipo_conta' => $resultado['origem']['tipo'] ?? null,
                ], fn ($v) => !is_null($v)),

                'pagamento' => array_filter([
                    'valor' => $resultado['valor'] ?? null,
                    'data_pagamento' => $resultado['horario'] ?? null,
                ], fn ($v) => !is_null($v)),
            ],
            'EM_PROCESSAMENTO' => [
                'status' => 'em_processamento',
                'endToEndId' => $resultado['endToEndId'],

                'destinatario' => array_filter([
                    'nome' => $resultado['destino']['nome'] ?? null,
                    'cpf_cnpj' => $resultado['destino']['cpfCnpj'] ?? null,
                    'conta' => $resultado['destino']['conta'] ?? null,
                    'agencia' => $resultado['destino']['agencia'] ?? null,
                    'tipo_conta' => $resultado['destino']['tipo'] ?? null,
                ], fn ($v) => !is_null($v)),

                'pagador' => array_filter([
                    'nome' => $resultado['origem']['nome'] ?? null,
                    'cpf_cnpj' => $resultado['origem']['cpfCnpj'] ?? null,
                    'conta' => $resultado['origem']['conta'] ?? null,
                    'agencia' => $resultado['origem']['agencia'] ?? null,
                    'tipo_conta' => $resultado['origem']['tipo'] ?? null,
                ], fn ($v) => !is_null($v)),

                'pagamento' => array_filter([
                    'valor' => $resultado['valor'] ?? null,
                    'data_pagamento' => $resultado['horario'] ?? null,
                ], fn ($v) => !is_null($v)),
            ],
            default => throw new SicoobException(
                'Pagamento não realizado.',
                $response->status(),
                $response->body()
            ),
        };
    }


}