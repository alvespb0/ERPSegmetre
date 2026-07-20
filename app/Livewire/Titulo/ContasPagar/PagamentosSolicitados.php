<?php

namespace App\Livewire\Titulo\ContasPagar;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

use Carbon\Carbon;

use App\Models\SolicitacoesPagamento;

class PagamentosSolicitados extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $tipoFiltroData = 'solicitacao';
    public $search = '';
    public $filtroCompetencia;
    public $status = 'pendente';
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

    public $openModalPagamento = false;
    public ?int $solicitacao_id;

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

    /**
     * Aplica todos os filtros na query de parcelas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function aplicarFiltros($query){
        if($this->filtroDiaEspecifico){
            if($this->tipoFiltroData == 'vencimento'){
                $data = $this->filtroDiaEspecifico->toDateString();
                $query->whereHas('parcela', function ($q) use ($data) {
                    $q->whereDate('data_vencimento', $data);
                });
            }else{
                $data = $this->filtroDiaEspecifico->toDateString();
                $query->whereDate('data_solicitacao', $data);
            }
        }

        if($this->inicioSemana && $this->fimSemana){
            if($this->tipoFiltroData == 'vencimento'){
                $query->whereHas('parcela', function ($q) {
                    $q->whereBetween('data_vencimento', [$this->inicioSemana, $this->fimSemana]);
                });
            }else{
                $query->whereBetween('data_solicitacao', [$this->inicioSemana, $this->fimSemana]);
            }
        }
        
        if($this->filtroMesAno){
            if($this->tipoFiltroData == 'vencimento'){
                $query->whereHas('parcela', function ($q) {
                    $q->whereYear('data_vencimento', substr($this->filtroMesAno, 0, 4))
                    ->whereMonth('data_vencimento', substr($this->filtroMesAno, 5, 2));
                });
            }else{
                $query->whereYear('data_solicitacao', substr($this->filtroMesAno, 0, 4))
                ->whereMonth('data_solicitacao', substr($this->filtroMesAno, 5, 2));
            }
        }
        
        if($this->dataInicioRange && $this->dataFimRange){
            if($this->tipoFiltroData == 'vencimento'){
                $query->whereHas('parcela', function ($q) {
                    $q->whereBetween('data_vencimento', [$this->dataInicioRange, $this->dataFimRange]);
                });
            }else{
                $query->whereBetween('data_solicitacao', [$this->dataInicioRange, $this->dataFimRange]);
            }
        }

        if($this->search){
            $query->where(function($query){
                $query->whereHas('parcela.titulo.entidade', function($q){
                        $q->where('razao_social', 'like', '%' . $this->search . '%')
                        ->orWhere('cpf_cnpj', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('parcela.titulo', function($q){
                        $q->where('numero_nf', 'like', '%' . $this->search . '%')
                        ->orWhere('descricao', 'like', '%' . $this->search . '%')
                        ->orWhere('observacoes', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('valor', 'like', '%' . $this->search . '%');
            });
        }

        if($this->status){
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function updated($property){
        $this->resetPage();
    }

    private function getQuery(){
        $query = $this->aplicarFiltros(SolicitacoesPagamento::query());

        return $query->whereHas('parcela.titulo', function ($q) {
            $q->where('tipo', 'pagar');
        });
    }

    public function abrirDetalhes($solicitacao_id){
        $this->openModalPagamento = true;
        $this->solicitacao_id = $solicitacao_id;
    }
    
    public function render()
    {
        $query = $this->getQuery();

        $solicitacoes = $query->with('parcela.titulo.entidade', 'parcela.titulo.categoriaFinanceira')->paginate(20);

        return view('livewire.titulo.contas-pagar.pagamentos-solicitados', [
            'solicitacoes' => $solicitacoes
        ]);
    }
}
