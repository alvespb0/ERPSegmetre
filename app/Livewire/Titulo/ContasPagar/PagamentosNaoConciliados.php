<?php

namespace App\Livewire\Titulo\ContasPagar;

use Illuminate\Support\Facades\Storage;

use Livewire\Component;
use Livewire\Attributes\On;

use Carbon\Carbon;

use App\Models\SolicitacoesPagamento;

class PagamentosNaoConciliados extends Component
{
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

    public $openModalConciliacao = false;
    public $pagamento_selecionado_id = null;
    
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
     * Aplica todos os filtros na query de solicitacao.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function aplicarFiltros($query){
        if($this->filtroDiaEspecifico){
            $data = $this->filtroDiaEspecifico->toDateString();
            $query->whereDate('data_solicitacao', $data);
        }

        if($this->inicioSemana && $this->fimSemana){
            $query->whereBetween('data_solicitacao', [$this->inicioSemana, $this->fimSemana]);
        }
        
        if($this->filtroMesAno){
            $query->whereYear('data_solicitacao', substr($this->filtroMesAno, 0, 4))
                ->whereMonth('data_solicitacao', substr($this->filtroMesAno, 5, 2));
        }
        
        if($this->dataInicioRange && $this->dataFimRange){
            $query->whereBetween('data_solicitacao', [$this->dataInicioRange, $this->dataFimRange]);
        }

        if($this->search){
            $query->where(function($query){
                $query->where('valor', 'like', '%' . $this->search . '%')
                    ->orWhere('identificador', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    private function getQuery(){
        $query = $this->aplicarFiltros(SolicitacoesPagamento::query());

        return $query->whereNull('parcela_id')
                    ->where('status', 'pago')
                    ->with('conta');
    }

    public function downloadComprovante($id){
        $pagamento = SolicitacoesPagamento::findOrFail($id);

        if(!$pagamento->comprovante_path){
            $this->dispatch('toast-error', 'Nenhum comprovante de pagamento localizado.');
            return;
        }

        return Storage::disk('public')->download($pagamento->comprovante_path);
    }

    public function abrirModalConciliacao($solicitacao_id){
        $this->openModalConciliacao = true;
        $this->pagamento_selecionado_id = $solicitacao_id;
    }

    #[On('fechar-modal-conciliacao')]
    public function fecharModalConciliacao(){
        $this->openModalConciliacao = false;
        $this->pagamento_selecionado_id = null;
    }

    public function render()
    {
        $query = $this->getQuery();

        $pagamentos = $query->paginate(10);

        return view('livewire.titulo.contas-pagar.pagamentos-nao-conciliados', [
            'pagamentos' => $pagamentos
        ]);
    }
}
