<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

use App\Models\TituloFinanceiro;

class DetalhesTitulo extends Component
{
    public $titulo;

    public $parcelasSelecionadas = [];

    public function mount($tituloId){
        $this->titulo = TituloFinanceiro::with('parcelas')->findOrFail($tituloId);
    }

    public function gerarCobrancasLote(){
        $this->dispatch('abrir-modal-cobranca-lote', parcelas: $this->parcelasSelecionadas);
        $this->fechar();
    }

    public function fechar(){
        $this->dispatch('fechar-modal-titulo');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.detalhes-titulo');
    }
}
