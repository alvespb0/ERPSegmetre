<?php

namespace App\Bancos\Sicoob\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\BoletoCobranca;
use App\Models\Integracao;

use App\Bancos\Sicoob\Payloads\BoletoPayload;
use App\Bancos\Sicoob\Payloads\ConsultaBoletoPayload;

class SicoobCobrancaService
{
    protected $integracao;

    public function __construct(Integracao $integracao){
        $this->integracao = $integracao;
    }

    /**
     * Registra um boleto na API de cobrança do Sicoob Sandbox.
     *
     * Atualmente utiliza autenticação simplificada do ambiente
     * Sandbox, enviando client_id no header e access_token
     * diretamente como Bearer Token.
     *
     * O payload é montado a partir dos dados do boleto,
     * parcela, conta bancária, configuração de cobrança
     * e pagador.
     *
     * @param BoletoCobranca $boleto Boleto a ser registrado.
     *
     * @return array Resposta retornada pela API do Sicoob.
     */
    public function gerarBoletoSandbox(BoletoCobranca $boleto){
        /* BLOCO AUTH, EM SANDBOX NAO PRECISA DE LOGICA DE OAUTH, SERÁ FEITO DEPOIS, POR ISSO FUNÇÃO COM SANDBOX NO NOME */
        $client_id = $this->integracao->credenciais->client_id; # em SANDBOX isso só vai ser passado no header client_id => $client_id
        $access_token = $this->integracao->credenciais->access_token; # em SANDBOX atua como um bearer

        $payLoadMounter = new BoletoPayload;

        $payload = $payLoadMounter->payloadMount($boleto);

        \Log::debug(['Payload de geração de boleto sandbox' => $payload]);

        /* BLOCO QUERY */
        $response = Http::withToken($access_token)
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->post(
                $this->integracao->endpoint . 'cobranca-bancaria/v3/boletos',
                $payload
            );
        
        return $response;
    }

    /**
     * Registra um boleto na API de cobrança do Sicoob .
     *
     * Autentica no fluxo oAuth 2.0 com o escopo boletos_inclusao
     *
     * O payload é montado a partir dos dados do boleto,
     * parcela, conta bancária, configuração de cobrança
     * e pagador.
     *
     * @param BoletoCobranca $boleto Boleto a ser registrado.
     *
     * @return array Resposta retornada pela API do Sicoob.
     */
    public function gerarBoletoProducao(BoletoCobranca $boleto){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'boletos_inclusao');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payLoadMounter = new BoletoPayload;

        $payload = $payLoadMounter->payloadMount($boleto);

        \Log::debug(['Payload de geração de boleto produção' => $payload]);

        /* BLOCO QUERY */
        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->post(
                $this->integracao->endpoint . 'cobranca-bancaria/v3/boletos',
                $payload
            );
        
        if (!$response->successful()) {
            \Log::error([
                'Erro ao registrar boleto' => [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]
            ]);
            throw new \Exception('Falha ao registrar boleto junto ao banco.');
            return;
        }

        $resultado = $response->json('resultado');

        $pdfPath = null;

        if (!empty($resultado['pdfBoleto'])) {
            $pdf = base64_decode($resultado['pdfBoleto']);
            $fileName = $boleto->numero_documento . '.pdf';
            Storage::disk('public')->put("boletos/{$fileName}", $pdf);
            $pdfPath = "boletos/{$fileName}";
        }

        return [
            'status' => 'registrado',
            'nosso_numero' => $resultado['nossoNumero'],
            'linha_digitavel' => $resultado['linhaDigitavel'],
            'codigo_barras' => $resultado['codigoBarras'],
            'pdf_path' => $pdfPath,
        ];
    }

    /**
     * Consulta um boleto de cobrança no ambiente de produção da API do Sicoob.
     *
     * Realiza a autenticação, envia a requisição de consulta do boleto e retorna
     * as principais informações, incluindo status, valor e data de pagamento
     * (quando disponível).
     *
     * A data de pagamento é obtida a partir do histórico do boleto, considerando
     * o registro cujo tipo de histórico seja igual a "6".
     *
     * @param BoletoCobranca $boleto Instância do boleto a ser consultado.
     *
     * @return array{
     *     status: string,
     *     valor: float|int|string|null,
     *     data_pagamento: string|null
     * }
     * Retorna um array contendo:
     * - status: Status do boleto mapeado pela aplicação.
     * - valor: Valor do boleto retornado pela API.
     * - data_pagamento: Data do pagamento, ou null caso o boleto não tenha sido pago.
     *
     * @throws SicoobException Quando a API retornar uma resposta de erro.
     */
    public function consultarBoletoProducao(BoletoCobranca $boleto){
        $authService = new AuthService;
        $access_token = $authService->auth($this->integracao, 'boletos_consulta');
        $client_id = $this->integracao->credenciais->client_id;
        $cert = $this->integracao->empresaParametro->certificadoDigital;

        $payloadMounter = new ConsultaBoletoPayload;
        $payload = $payloadMounter->payloadMount($boleto);

        \Log::debug(['Payload de consta de boleto produção' => $payload]);

        $response = Http::withToken($access_token)
            ->withOptions([
                'cert' => Storage::disk('local')->path($cert->cert_path)
            ])
            ->withHeaders([
                'client_id' => $client_id,
            ])
            ->get($this->integracao->endpoint . 'cobranca-bancaria/v3/boletos', $payload);

        if(!$response->successful()){ # Erro silencioso pra não quebrar o loop
            throw new SicoobException(
                'Erro ao consultar boleto',
                $response->status(),
                $response->body()
            );
        }
        
        $dataPagamento = null;

        foreach ($response->json('resultado.listaHistorico', []) as $historico) {
            if ($historico['tipoHistorico'] === '6') { 
                $dataPagamento = $historico['dataHistorico']; 
                break;
            }
        }

        return [
            'status' => $this->mapearStatus($response->json('resultado.situacaoBoleto')),
            'valor' => $response->json('resultado.valor'),
            'data_pagamento' => $dataPagamento,
        ];
    }

    private function mapearStatus($status){
        return match($status){
            'Em Aberto' => 'registrado',
            'Registrado' => 'registrado',
            'Liquidado' => 'liquidado',
            'Pago' => 'liquidado',
            'Baixado' => 'baixado',
            'Rejeitado' => 'rejeitado',
            'Cancelado' => 'cancelado',
        };
    }
}