<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

use App\Models\SolicitacoesPagamento;
use App\Models\Movimentacao;

use App\Factories\IntegracaoFactory;

use App\Services\MovimentacaoService;
use App\Services\ParcelaService;

class ConsultaPagamentoAgendadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(IntegracaoFactory $factory): void
    {
        $pagamentosAgendados = SolicitacoesPagamento::where('status', 'agendado')
                                ->whereHas('conta.configuracaoCobranca', function($q) {
                                    $q->whereNotNull('integracao_id');
                                })
                                ->whereNotNull('chave_idempotente')
                                ->with('conta.configuracaoCobranca.integracao')
                                ->get();

        \Log::debug([
            'Iniciado JOB para consulta de pagamentos agendados',
            'Pagamentos agendados: ' => $pagamentosAgendados->count()
        ]);

        foreach($pagamentosAgendados as $pagamento){
            try {
                $provider = $factory->make($pagamento->conta->configuracaoCobranca->integracao, 'pagamento');
                
                $resultado = $provider->consultaComprovanteProducao($pagamento); # array

                if($resultado['status'] !== $pagamento->status){
                    \Log::info([
                        'Solicitacao de pagamento atualizada' => [
                            'solicitacao_id' => $pagamento->id,
                            'status_antigo' => $pagamento->status,
                            'status_novo' => $resultado['status'],
                        ]
                    ]);

                    match ($resultado['status']) {
                        'pago' => $this->processarPagamentoEfetivado($resultado, $pagamento),
                        'pendente_assinatura' => $pagamento->update(['status' => 'pendente_assinatura']),
                        'processado' => $pagamento->update(['status' => 'pago']),
                        'rejeitado' => throw new \Exception(
                            $resultado['mensagem'] ?? 'Pagamento rejeitado.'
                        ),
                        default => throw new \RuntimeException('Status de pagamento desconhecido.')
                    };

                }
            } catch (\Throwable $e) {
                $contexto = method_exists($e, 'context') ? $e->context() : [];
                \Log::error([
                    'Erro ao consultar pagamento agendado' => [
                        'solicitacao_id' => $pagamento->id,
                        'erro' => $e->getMessage(),
                        'contexto' => $contexto,
                    ]
                ]);
                continue;
            } finally {
                usleep(300000);
            }
        }
    }


    private function processarPagamentoEfetivado(array $retorno, $solicitacao){
        \Log::info([
            'Iniciado Lancamento de Movimentacao' => [
                'solicitacao' => $solicitacao->id,
                'valor' => $retorno['pagamento']['valor'],
                'data_pagamento' => $retorno['pagamento']['data_pagamento'],
                'empresa_parametro_id' => $solicitacao->empresa_parametro_id
            ]
        ]);

        if (!$solicitacao->movimentacao_id) {
            $serviceMovimentacao = new MovimentacaoService;
            
            $movimentacao = $serviceMovimentacao->store([
                'conta_id' => $solicitacao->conta->id,
                'empresa_parametro_id' => $solicitacao->empresa_parametro_id,
                'parcela_id' => $solicitacao->parcela_id,
                'valor_pago' => $retorno['pagamento']['valor'] ?? $solicitacao->valor,
                'data_pagamento' => $retorno['pagamento']['data_pagamento'] ?? Carbon::today()->toDateString()
            ]);
        }
        
        $service = new \App\Services\SolicitacoesPagamentoService;

        $compPath = $service->makeComprovantePagamento($retorno);

        $solicitacao->update([
            'movimentacao_id' => $movimentacao->id ?? $solicitacao->movimentacao_id,
            'data_pagamento' => $retorno['pagamento']['data_pagamento'],
            'codigo_autenticacao' => $retorno['pagamento']['codigo_autenticacao'] ?? null,
            'id_pagamento' => $retorno['pagamento']['id_pagamento'] ?? null,
            'comprovante_path' => $compPath ?? null,
            'status' => 'pago'
        ]);
    }
}
