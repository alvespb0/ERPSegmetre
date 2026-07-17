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
    public $filtroConta, $dataInicial, $dataFinal, $situacao;
    public $titulos = [];

    public $openModalDespesa = false;

    public function mount(){
        $this->contas = Conta::whereHas('configuracaoCobranca')->with('banco', 'tipoConta', 'configuracaoCobranca')->get();
    }

    /**
     * Consulta os boletos DDA da conta selecionada utilizando
     * a integração configurada para o ambiente correspondente.
     *
     * @return void
     */
    public function buscarBoletos(){
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
                $this->titulos = $serviceProvider->ddaSandbox($this->dataInicial, $this->dataFinal, $this->situacao, preg_replace('/-/', '', $conta->conta));
            } elseif ($config->ambiente === 'producao') {
                if (!method_exists($serviceProvider, 'ddaProducao')) {
                    $this->dispatch('toast-error', 'Integração não implementa DDA.');
                    return;
                }
                $this->titulos = $serviceProvider->ddaProducao($this->dataInicial, $this->dataFinal, $this->situacao, preg_replace('/-/', '', $conta->conta));
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
    
    public function cadastrarDespesa($linhaDigitavel){
        $titulo = collect($this->titulos)->firstWhere('linha_digitavel', $linhaDigitavel);
        
        $this->openModalDespesa = true;
    }

    public function render()
    {
        return view('livewire.titulo.contas-pagar.modulo-d-d-a');
    }
}
