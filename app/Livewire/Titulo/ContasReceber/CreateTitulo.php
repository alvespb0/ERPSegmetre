<?php

namespace App\Livewire\Titulo\ContasReceber;

use Livewire\Component;
use Carbon\Carbon;
use App\Services\ContaService;
use App\Services\CategoriaFinanceiraService;
use App\Services\CentroCustoService;
use App\Services\EntidadeService;
use App\Services\FormaPagamentoService;

class CreateTitulo extends Component
{
    public $entidade_id, $descricao, $status, $valor_total, $data_vencimento, $quantidade_parcelas;
    public $parcelas = [];

    public function gerarParcelas(){
        try{
            if(!$this->valor_total || !$this->quantidade_parcelas || !$this->data_vencimento){
                $this->dispatch('toast-error', 'Verifique se os campos Valor Total, Parcelas e 1º Vencimento estão preenchidos.');
            }else{
                if(!empty($this->parcelas)){
                    $this->parcelas = [];
                }
                $valorTotalCentavos = (int) round($this->valor_total * 100);
                $valorParcelaCentavos = intdiv($valorTotalCentavos, $this->quantidade_parcelas);

                $somaCentavos = $valorParcelaCentavos * $this->quantidade_parcelas;
                $diferencaCentavos = $valorTotalCentavos - $somaCentavos;

                for($i = 0; $i < $this->quantidade_parcelas; $i++){
                    $valorCentavos = $valorParcelaCentavos;

                    if ($i === $this->quantidade_parcelas - 1) {
                        $valorCentavos += $diferencaCentavos;
                    }

                    $data = Carbon::parse($this->data_vencimento);
                    $this->parcelas[] = [
                        'parcela_numero' => $i + 1,
                        'data_vencimento_parcela' => $data->addMonths($i)->format('Y-m-d'),
                        'valor_parcela' => $valorCentavos/100,
                    ];
                }
                $this->dispatch('toast-message', 'Parcelas geradas com successo!');
            }
        }catch(\Exception $e){
            $this->dispatch('toast-error', 'Erro ao gerar parcelas.');
            \Log::error("Erro ao gerar parcelas: ", ['erro' => $e->getMessage()]);
        }
    }

    public function submit(){

    }

    public function render(
        ContaService $contaService,
        CategoriaFinanceiraService $categoriaFinanceiraService,
        CentroCustoService $centroCustoService,
        EntidadeService $entidadeService,
        FormaPagamentoService $formaPagamentoService
    )
    {
        $contas = $contaService->show();
        $categorias = $categoriaFinanceiraService->showReceitas();
        $centrosCusto = $centroCustoService->show();
        $entidades = $entidadeService->show();
        $formasPagamento = $formaPagamentoService->show();

        return view('livewire.titulo.contas-receber.create-titulo', [
            'contas' => $contas,
            'categoriasFinanceira' => $categorias,
            'centrosCusto' => $centrosCusto,
            'entidades' => $entidades,
            'formasPagamento' => $formasPagamento,
        ]);

    }
}
