<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;
use App\Models\Parcela;

class SolicitacaoPagamento extends Component
{
    public $parcela;
    public $tipo = '';
    public $identificador;
    public $valor;

    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo' => function ($q) { $q->withCount('parcelas');}, 'movimentacoes', 'solicitacoesPagamento'])->findOrFail($parcelaId);
    }

    public function fechar(){
        $this->dispatch('fechar-modal-solicitacao-pagamento');
    }

    public function rules(){

    }

    public function salvarSolicitacao(){

    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.solicitacao-pagamento');
    }
}
