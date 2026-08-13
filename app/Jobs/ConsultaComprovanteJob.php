<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

use App\Models\Movimentacao;
use App\Models\SolicitacoesPagamento;

use App\Factories\IntegracaoFactory;

use App\Services\MovimentacaoService;
use App\Services\ParcelaService;

class ConsultaComprovanteJob implements ShouldQueue
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
        $pagamentosEmProcessamento = SolicitacoesPagamento::where('status', 'em_processamento')
                                        ->whereHas('conta.configuracaoCobranca', function($q) {
                                            $q->whereNotNull('integracao_id');
                                        })
                                        ->whereNotNull('end_to_end_id')
                                        ->with('conta.configuracaoCobranca.integracao')
                                        ->get();
        \Log::debug([
            'Iniciado JOB para consulta de comprovantes',
            'Pagamentos em processamento' => $pagamentosEmProcessamento->count()
        ]);

        foreach($pagamentosEmProcessamento as $pagamento){
            try{
                $provider = $factory->make($pagamento->conta->configuracaoCobranca->integracao, 'pix_pagamento');
                
                $resultado = $provider->consultaComprovanteProducao($pagamento);

                if($resultado['status'] != $pagamento->status){
                    \Log::info([
                        'Solicitacao de pagamento atualizada' => [
                            'solicitacao_id' => $pagamento->id,
                            'status_antigo' => $pagamento->status,
                            'status_novo' => $resultado['status'],
                        ]
                    ]);

                    match ($resultado['status']) {
                        'pago' => $this->processarPagamentoEfetivado($resultado, $pagamento),
                        'recusado' => $pagamento->update(['status' => 'recusado']),
                        default => throw new \RuntimeException('Status de pagamento desconhecido.')
                    };
                }
            } catch (\Throwable $e) {
                $contexto = method_exists($e, 'context') ? $e->context() : [];
                \Log::error([
                    'Erro ao consultar pagamento em processamento' => [
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
        if($solicitacao->parcela_id){
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

            $dataPagamento = Carbon::parse($retorno['pagamento']['data_pagamento'])
                ->setTimezone(config('app.timezone'))
                ->format('Y-m-d H:i:s');

            $solicitacao->update([
                'movimentacao_id' => $movimentacao->id ?? $solicitacao->movimentacao_id,
                'data_pagamento' => $dataPagamento,
                'comprovante_path' => $compPath ?? null,
                'status' => 'pago'
            ]);
        }else{
            $service = new \App\Services\SolicitacoesPagamentoService;

            $compPath = $service->makeComprovantePagamento($retorno);

            $dataPagamento = Carbon::parse($retorno['pagamento']['data_pagamento'])
                ->setTimezone(config('app.timezone'))
                ->format('Y-m-d H:i:s');

            $solicitacao->update([
                'movimentacao_id' => $movimentacao->id ?? $solicitacao->movimentacao_id,
                'data_pagamento' => $dataPagamento,
                'comprovante_path' => $compPath ?? null,
                'valor' => $retorno['pagamento']['valor'],
                'status' => 'pago'
            ]);

        }
    }
}
