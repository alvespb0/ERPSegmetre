<?php

namespace App\Livewire\Modais\Conta;

use Livewire\Component;

use App\Models\Conta;
use App\Models\EmpresaParametro;

class ConfiguracaoCobranca extends Component
{

    public $conta;
    public $configCobranca;
    public $empresasParametro;

    public function mount($contaId){
        $this->conta = Conta::findOrFail($contaId);
        $this->configCobranca = $this->configuracaoCobranca ?? null;
        $this->empresasParametro = EmpresaParametro::orderBy('razao_social', 'asc')->get();
    }

    public function fechar(){
        $this->dispatch('fechar-modal');
    }

    public function render()
    {
        return view('livewire.modais.conta.configuracao-cobranca');
    }
}
