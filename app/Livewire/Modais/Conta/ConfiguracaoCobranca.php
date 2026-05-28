<?php

namespace App\Livewire\Modais\Conta;

use Livewire\Component;

use App\Services\ConfiguracaoCobrancaService;

use App\Models\Conta;
use App\Models\EmpresaParametro;

class ConfiguracaoCobranca extends Component
{

    public $conta;
    public $configCobranca;
    public $empresasParametro;

    public $empresa_parametro_id;
    public $codigo_cedente;
    public $carteira;
    public $ambiente;
    public $layout_cnab;
    public $numero_inicial_cobranca;

    public function mount($contaId){
        $this->conta = Conta::findOrFail($contaId);
        $this->configCobranca = $this->conta->configuracaoCobranca ?? null;
        $this->empresasParametro = EmpresaParametro::orderBy('razao_social', 'asc')->get();
        $this->empresa_parametro_id = $this->configCobranca->empresa_parametro_id ?? null;
        $this->codigo_cedente = $this->configCobranca->codigo_cedente ?? null;
        $this->carteira = $this->configCobranca->carteira ?? null;
        $this->ambiente = $this->configCobranca->ambiente ?? null;
        $this->layout_cnab = $this->configCobranca->layout_cnab ?? null;
        $this->numero_inicial_cobranca = $this->configCobranca->numero_inicial_cobranca ?? null;
        $this->nosso_numero = $this->configCobranca->nosso_numero ?? null;
    }

    public function submit(ConfiguracaoCobrancaService $service){
        $dados = [
            'empresa_parametro_id' => $this->empresa_parametro_id,
            'codigo_cedente' => $this->codigo_cedente,
            'carteira' => $this->carteira,
            'layout_cnab' => $this->layout_cnab,
            'ambiente' => $this->ambiente,
            'numero_inicial_cobranca' => $this->numero_inicial_cobranca,
        ];

        $service->updateOrCreate($dados, $this->conta->id);

        $this->dispatch('fechar-modal-configuracao-bancaria');
        
        $this->dispatch('toast-message', 'Configuração salva com sucesso.');
    }

    public function fechar(){
        $this->dispatch('fechar-modal');
    }

    public function render()
    {
        return view('livewire.modais.conta.configuracao-cobranca');
    }
}
