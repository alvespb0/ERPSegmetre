<?php

namespace App\Livewire\Titulo\ContasPagar;

use Livewire\Component;

use App\Models\Conta;
use App\Models\Integracao;

class ModuloDDA extends Component
{
    public $contas;
    public ?int $selectedConta = null;
    public $integracao = null;

    public $titulos = [];

    public function mount(){
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get();
    }

    public function updatedSelectedConta(){
        $conta = Conta::findOrFail($this->selectedConta);

        $config = $conta->configuracaoCobranca;

        if(!$config->integracao){
            $this->dispatch('toast-error', 'Conta selecionada não possui integracao ou nao possui modulo de DDA.');
            return;
        }

        $this->integracao = $config->integracao;

        try{
            $factory = new \App\Factories\IntegracaoFactory;
            $serviceProvider = $factory->make($this->integracao, 'pagamento');

            if ($config->ambiente === 'homologacao') {
                if (!method_exists($serviceProvider, 'ddaSandbox')) {
                    $this->dispatch('toast-error', 'Integração não implementa DDA Sandbox.');
                    return;
                }
                $this->titulos = $serviceProvider->ddaSandbox();
            } elseif ($config->ambiente === 'producao') {
                if (!method_exists($serviceProvider, 'ddaProducao')) {
                    $this->dispatch('toast-error', 'Integração não implementa DDA.');
                    return;
                }
                $this->titulos = $serviceProvider->ddaProducao();
            }

            $this->dispatch('toast-message', 'Boletos resgatados com sucesso.');
        }catch (\Throwable $e){
            \Log::error([
                'Erro ao resgatar cobrancas DDA' => $e->getMessage(),
                'Empresa parametro' => $conta->empresa_parametro_id,
                'Conta' => $this->selectedConta
            ]);

            $this->dispatch('toast-error', 'Erro ao resgatar cobrancas DDA.');
        }
    }
    
    public function render()
    {
        return view('livewire.titulo.contas-pagar.modulo-d-d-a');
    }
}
