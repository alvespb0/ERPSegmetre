<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Models\Parcela;

use App\Services\AnexoService;

class Anexos extends Component
{
    public $parcela;
    public $titulo;
    public $anexosMovimentacoes;

    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo' => function ($q) { $q->withCount('parcelas');}, 'movimentacoes.anexos'])
                        ->findOrFail($parcelaId);
        $this->titulo = $this->parcela->titulo;
        $this->anexosMovimentacoes = $this->parcela->movimentacoes->flatMap(function($movimentacao) {
            return $movimentacao->anexos->map(function($anexo) use ($movimentacao) {
                $anexo->movimentacao_id = $movimentacao->id;
                return $anexo;
            });
            
        });
    }

    public function downloadAnexo($anexoId, AnexoService $service){
        return $service->download($anexoId);
    }

    public function excluirAnexo($anexoId, AnexoService $service){
        $service->destroy($anexoId);

        $this->dispatch('toast-message', 'Anexo excluído com sucesso.');
    
        $this->dispatch('fechar-modal-anexos');
    }

    public function fechar(){
        $this->dispatch('fechar-modal-anexos');
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.anexos');
    }
}
