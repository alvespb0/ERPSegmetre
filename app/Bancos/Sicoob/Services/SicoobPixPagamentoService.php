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

    /**
     * Inicia um pagamento PIX por chave DICT em produção.
     *
     * Obtém um token de acesso com a permissão de escrita em PIX Pagamentos,
     * monta o payload da requisição e consulta a chave PIX informada junto
     * ao Sicoob.
     *
     * O método não efetiva o pagamento. Ele apenas inicia a operação e
     * retorna os dados necessários para a etapa posterior de confirmação.
     *
     * @param string $chave Chave PIX do destinatário.
     * @param string|null $dataPagamento Data prevista para o pagamento, quando aplicável.
     *
     * @return array{
     *     e2eId: string,
     *     chave: string,
     *     tipo: string,
     *     proprietario: array{
     *         nome: string,
     *         identificador: string
     *     }
     * } Dados da chave PIX e do proprietário retornados pelo Sicoob.
     *
     * @throws SicoobException Quando o Sicoob retorna uma resposta não
     *         bem-sucedida ao iniciar o pagamento.
     */
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

    /**
     * Confirma um pagamento PIX previamente iniciado.
     *
     * Determina o meio de iniciação do pagamento com base no tipo do PIX,
     * monta o payload de confirmação e envia a solicitação ao endpoint
     * de confirmação de pagamentos PIX do Sicoob.
     *
     * O resultado da API é normalizado para os status utilizados pela
     * aplicação:
     *
     * - `pago`: pagamento finalizado com sucesso;
     * - `em_processamento`: pagamento ainda sendo processado;
     * - `recusado`: pagamento rejeitado pela instituição bancária.
     *
     * @param string $e2eId EndToEndId da operação PIX iniciada.
     * @param float|string $valor Valor do pagamento PIX.
     * @param string $tipoPix Tipo de iniciação do PIX. Valores esperados:
     *        `chave` ou `copia_cola`.
     *
     * @return array{
     *     status: string,
     *     endToEndId: string,
     *     mensagem: string,
     *     destinatario?: array{
     *         nome?: string,
     *         cpf_cnpj?: string,
     *         conta?: string,
     *         agencia?: string,
     *         tipo_conta?: string
     *     },
     *     pagador?: array{
     *         nome?: string,
     *         cpf_cnpj?: string,
     *         conta?: string,
     *         agencia?: string,
     *         tipo_conta?: string
     *     },
     *     pagamento?: array{
     *         valor?: float|string,
     *         data_pagamento?: string
     *     }
     * } Resultado normalizado do pagamento PIX.
     *
     * @throws \Exception Quando o tipo de pagamento PIX informado não
     *         corresponde a um meio de iniciação conhecido.
     *
     * @throws SicoobException Quando o Sicoob retorna uma resposta não
     *         bem-sucedida ou um estado de pagamento não tratado.
     */
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
                'mensagem' => 'Pix efetuado com sucesso!',

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
                'mensagem' => 'Pix em processamento!',

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
            'REJEITADO' => [
                'status' => 'recusado',
                'endToEndId' => $resultado['endToEndId'],
                'mensagem' => $resultado['detalheRejeicao'] ?? 'Pagamento rejeitado pela instituição bancária.',
            ],
            default => throw new SicoobException(
                'Pagamento não realizado.',
                $response->status(),
                $response->body()
            ),
        };
    }

    public function consultaComprovanteProducao(SolicitacoesPagamento $solicitacao){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'pixpagamentos_consulta');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id
            ])
            ->get(
                $this->integracao->endpoint . "pix-pagamentos/v2/pagamentos/{$solicitacao->end_to_end_id}");

        if(!$response->successful()) {
            \Log::error([
                'Erro ao consultar pagamento pix' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'empresa_parametro' => $this->integracao->empresa_parametro_id,
                ]
            ]);

            throw new SicoobException(
                'Erro ao consultar pagamento pix',
                $response->status(),
                $response->body()
            );
        }
        
        $resultado = $response->json();

        return match($resultado['estado']){
            'FINALIZADO' => [
                'status' => 'pago',
                'endToEndId' => $resultado['endToEndId'],
                'mensagem' => 'Pix efetuado com sucesso!',

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
                'mensagem' => 'Pix em processamento!',

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
            'REJEITADO' => [
                'status' => 'recusado',
                'endToEndId' => $resultado['endToEndId'],
                'mensagem' => $resultado['detalheRejeicao'] ?? 'Pagamento rejeitado pela instituição bancária.',
            ],
            default => throw new SicoobException(
                'Pagamento não realizado.',
                $response->status(),
                $response->body()
            ),
        };

    }
}