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
        
        return $response;
    }

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

        return $response;
    }
}