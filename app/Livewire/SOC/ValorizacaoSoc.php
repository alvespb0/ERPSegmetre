<?php

namespace App\Livewire\SOC;

use Livewire\Component;

use App\Models\Integracao;

class ValorizacaoSoc extends Component
{
    public $integracao;

    public $dataInicio, $dataFim;
    
    public $examesValorizados;

    public function mount(){
        $this->integracao = Integracao::where('slug', 'soc-exames-producao')->get();
    }

    public function getValorizacoes(){
        
    }
    
    public function render()
    {
        return view('livewire.s-o-c.valorizacao-soc');
    }
}
