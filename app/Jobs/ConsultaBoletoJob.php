<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

use App\Models\BoletoCobranca;
use App\Models\Movimentacao;

use App\Factories\IntegracaoFactory;

use App\Services\MovimentacaoService;
use App\Services\ParcelaService;

class ConsultaBoletoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(IntegracaoFactory $factory): void
    {
        $boletosEmAberto = BoletoCobranca::where('status', 'registrado')
                            ->whereHas('configuracaoCobranca', function ($q) {
                                    $q->whereNotNull('integracao_id');
                            })
                            ->with('configuracaoCobranca.integracao')
                            ->get();

        \Log::debug([
            'Iniciado JOB para consulta de boletos',
            'Boletos em aberto' => $boletosEmAberto->count()
        ]);

        foreach($boletosEmAberto as $boleto){
            try {
                $provider = $factory->make($boleto->configuracaoCobranca->integracao, 'cobranca');
                
                $resultado = $provider->consultarBoletoProducao($boleto); # array
            
                if($resultado['status'] !== $boleto->status){
                    \Log::info([
                        'Boleto atualizado' => [
                            'boleto_id' => $boleto->id,
                            'nosso_numero' => $boleto->nosso_numero,
                            'status_antigo' => $boleto->status,
                            'status_novo' => $resultado['status'],
                        ]
                    ]);

                    if($resultado['status'] == 'liquidado'){
                        $this->lancarMovimentacao($boleto, $resultado['valor'], $resultado['data_pagamento'] ?? Carbon::today());

                        $boleto->update([
                            'status' => $resultado['status'],
                            'data_liquidacao' => $resultado['data_pagamento']
                        ]);

                        continue;
                    }

                    $boleto->update([
                        'status' => $resultado['status'],
                    ]);
                }
            } catch (\Throwable $e) {
                $contexto = method_exists($e, 'context') ? $e->context() : [];
                Log::error([
                    'Erro ao consultar boleto' => [
                        'boleto_id' => $boleto->id,
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

    private function lancarMovimentacao(BoletoCobranca $boleto, $valorMovimentacao, $dataPagamento){
        \Log::info([
            'Iniciado Lancamento de Movimentacao' => [
                'boleto_id' => $boleto->id,
                'valor' => $valorMovimentacao,
                'data_pagamento' => $dataPagamento,
                'empresa_parametro_id' => $boleto->empresa_parametro_id,
            ]
        ]);
        $serviceMovimentacao = new MovimentacaoService;

        $serviceMovimentacao->store([
            'conta_id' => $boleto->configuracaoCobranca->conta_id,
            'empresa_parametro_id' => $boleto->empresa_parametro_id,
            'parcela_id' => $boleto->parcela_id,
            'valor_pago' => $valorMovimentacao,
            'data_pagamento' => $dataPagamento
        ]);
    }
}
