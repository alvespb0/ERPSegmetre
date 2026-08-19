<?php

namespace App\Livewire\Titulo\ContasPagar;

use Illuminate\Support\Facades\Storage;

use Livewire\Component;

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
    
    public function render()
    {
        $query = $this->getQuery();

        $pagamentos = $query->paginate(10);

        return view('livewire.titulo.contas-pagar.pagamentos-nao-conciliados', [
            'pagamentos' => $pagamentos
        ]);
    }
}
