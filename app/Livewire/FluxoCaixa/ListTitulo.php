<?php

namespace App\Livewire\FluxoCaixa;

use Livewire\Component;
use App\Models\Parcela;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use \Carbon\Carbon;

class ListTitulo extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $chartLabels = [];
    public $chartRecebimentos = [];
    public $chartPagamentos = [];
    public $chartSaldo = [];

    public function gerarGrafico($query){
        $dados = [];
        
        $parcelas = (clone $query)->get();

        foreach($parcelas as $parcela){
            $data = Carbon::parse($parcela->data_vencimento)->format('d/m');

            if(!isset($dados[$data])){
                $dados[$data] = [
                    'receita' => 0,
                    'despesa' => 0
                ];
            }

            if ($parcela->titulo->tipo === 'receber') {
                $dados[$data]['receita'] += $parcela->valor;
            } else {
                $dados[$data]['despesa'] += $parcela->valor;
            }
        }

        ksort($dados);

        $saldoAcumulado = 0;

        foreach($dados as $data => $valores){
            $this->chartLabels[] = $data;
            $this->chartRecebimentos[] = $valores['receita'];
            $this->chartPagamentos[] = $valores['despesa'];

            $saldoAcumulado += ($valores['receita'] - $valores['despesa']);
            $this->chartSaldo[] = $saldoAcumulado;
        }
    }

    public function aplicarFiltros($query){

    }

    public function render(){
        $query = Parcela::query();


        $queryBase = clone $query;

        $pagos = (clone $queryBase)
            ->whereHas('titulo', function($q){
                $q->where('tipo', 'pagar');
            })
            ->get()
            ->sum('valor_pago');
        
        $recebidos = (clone $queryBase)
            ->whereHas('titulo', function($q){
                $q->where('tipo', 'receber');
            })
            ->get()
            ->sum('valor_pago');

        $parcelas = $query
            ->with(['titulo.entidade'])
            ->orderBy('data_vencimento', 'asc')
            ->paginate(10);

        $this->gerarGrafico($queryBase);
        
        return view('livewire.fluxo-caixa.list-titulo', [
            'parcelas' => $parcelas,
            'pagos' => $pagos,
            'recebidos' => $recebidos,
        ]);
    }
}
