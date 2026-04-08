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


    public $search = '';
    public $filtroCompetencia;

    /* ['ontem', 'hoje'] filtros: */
    public $filtroDiaEspecifico;
    public $labelDiaEspecifico;

    /* semana filtros: */
    public $inicioSemana;
    public $fimSemana;

    /* Mes filtros: */
    public $filtroMesAno;
    public $labelMesAno;

    /* Range filtros: */
    public $dataInicioRange;
    public $dataFimRange;

    public function updatedFiltroCompetencia(){
        $this->resetarFiltrosDeData();
        switch ($this->filtroCompetencia){
            case 'hoje':
                $this->filtroDiaEspecifico = Carbon::today();
                $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
                break;
            case 'ontem':
                $this->filtroDiaEspecifico = Carbon::yesterday();
                $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
                break;
            case 'semana':
                $this->inicioSemana = Carbon::now()->startOfWeek();
                $this->fimSemana = Carbon::now()->endOfWeek();
                break;
            case 'mes':
                $this->filtroMesAno = Carbon::now()->format('Y-m');
                $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01')->format('m/Y');
                break;
            case 'custom':
                $this->dataInicioRange = Carbon::now()->startOfMonth()->toDateString();
                $this->dataFimRange = Carbon::now()->endOfMonth()->toDateString();
                break;
            default:
                break;
        }
    }

    /**
     * Limpa todos os filtros aplicados.
     *
     * @return void
     */
    public function limparFiltros(){
        $this->resetarFiltrosDeData();
        $this->search = '';
        $this->filtroCard = '';
        $this->filtroCompetencia = '';
    }

    /**
     * Reseta todos os filtros de data.
     *
     * @return void
     */
    public function resetarFiltrosDeData(){
        $this->filtroDiaEspecifico = null;
        $this->labelDiaEspecifico = null;
        $this->inicioSemana = null;
        $this->fimSemana = null;
        $this->filtroMesAno = null;
        $this->labelMesAno = null;
        $this->dataInicioRange = null;
        $this->dataFimRange = null;
    }

    /**
     * Retrocede um dia no filtro de data específica.
     *
     * @return void
     */
    public function diaAnterior(){
        $this->filtroDiaEspecifico->subDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }

    /**
     * Avança um dia no filtro de data específica.
     *
     * @return void
     */
    public function diaPosterior(){
        $this->filtroDiaEspecifico->addDay();
        $this->labelDiaEspecifico = $this->filtroDiaEspecifico->format('d/m/Y');
    }


    /**
     * Retrocede um mês no filtro de mês/ano.
     *
     * @return void
     */
    public function mesAnterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->subMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }

    /**
     * Avança um mês no filtro de mês/ano.
     *
     * @return void
     */
    public function mesPosterior(){
        $this->filtroMesAno = Carbon::parse($this->filtroMesAno . '-01')->addMonth()->format('Y-m');
        $this->labelMesAno = Carbon::parse($this->filtroMesAno . '-01') ->format('m/Y');
    }

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
        if($this->filtroDiaEspecifico){
            $data = $this->filtroDiaEspecifico->toDateString();
            $query->whereDate('data_vencimento', $data);
        }

        if($this->inicioSemana && $this->fimSemana){
            $query->whereBetween('data_vencimento', [$this->inicioSemana, $this->fimSemana]);
        }
        
        if($this->filtroMesAno){
            $query->whereYear('data_vencimento', substr($this->filtroMesAno, 0, 4))
                ->whereMonth('data_vencimento', substr($this->filtroMesAno, 5, 2));
        }
        
        if($this->dataInicioRange && $this->dataFimRange){
            $query->whereBetween('data_vencimento', [$this->dataInicioRange, $this->dataFimRange]);
        }

        if($this->search){
            $query->where(function($query){
                $query->whereHas('titulo.entidade', function($q){
                        $q->where('razao_social', 'like', '%' . $this->search . '%')
                        ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('titulo', function($q){
                        $q->where('numero_nf', 'like', '%' . $this->search . '%')
                        ->orWhere('descricao', 'like', '%' . $this->search . '%')
                        ->orWhere('observacoes', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('valor', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function render(){
        $query = $this->aplicarFiltros(Parcela::query());

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
            ->with(['titulo' => function ($q) { $q->withCount('parcelas'); }])
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
