<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use App\Models\Parcela;

use App\Services\MovimentacaoService;

class DetalhesParcela extends Component
{
    public $parcela;
    
    public function mount($parcelaId){
        $this->parcela = Parcela::with('titulo.entidade', 'movimentacoes')->findOrFail($parcelaId);
    }

    public function fechar(){
        $this->dispatch('fechar-modal');
    }

    public function excluirMovimentacao(MovimentacaoService $movimentacaoService, $id){
        $movimentacaoService->destroy($id);

        $this->dispatch('toast-message', 'Movimentação excluída com sucesso');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.detalhes-parcela');
    }
}
