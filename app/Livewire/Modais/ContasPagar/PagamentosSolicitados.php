<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Models\SolicitacoesPagamento;

class PagamentosSolicitados extends Component
{
    public $solicitacao;

    public function mount($solicitacaoId){
        $this->solicitacao = SolicitacoesPagamento::findOrFail($solicitacaoId);    
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.pagamentos-solicitados');
    }
}
