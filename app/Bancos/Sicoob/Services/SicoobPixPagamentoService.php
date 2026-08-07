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

}