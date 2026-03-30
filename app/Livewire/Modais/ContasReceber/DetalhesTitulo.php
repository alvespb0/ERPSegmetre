<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

use App\Models\TituloFinanceiro;

class DetalhesTitulo extends Component
{
    public $titulo;

    public function mount($tituloId){
        $this->titulo = TituloFinanceiro::findOrFail($tituloId);
    }

    public function fechar(){
        $this->dispatch('fechar-modal');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.detalhes-titulo');
    }
}
