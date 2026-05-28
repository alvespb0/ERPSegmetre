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

    public function rules(){
        return [
            'empresa_parametro_id'    => 'required',
            'codigo_cedente'          => 'required|string|max:255',
            'carteira'                => 'nullable|string|max:255',
            'ambiente'                => 'required|in:homologacao,producao',
            'layout_cnab'             => 'required|in:240,400',
            'numero_inicial_cobranca' => 'required|numeric',
        ];
    }

    public function messages(){
        return [
            'empresa_parametro_id.required'    => 'A empresa vinculada é obrigatória.',

            'codigo_cedente.required'          => 'O código do cedente é obrigatório.',
            'codigo_cedente.string'            => 'O código do cedente deve ser um texto.',
            'codigo_cedente.max'               => 'O código do cedente deve ter no máximo :max caracteres.',

            'carteira.string'                  => 'A carteira deve ser um texto.',
            'carteira.max'                     => 'A carteira deve ter no máximo :max caracteres.',

            'ambiente.required'                => 'O ambiente de emissão é obrigatório.',
            'ambiente.in'                      => 'O ambiente selecionado é inválido.',

            'layout_cnab.required'             => 'O layout CNAB é obrigatório.',
            'layout_cnab.in'                   => 'O layout selecionado é inválido.',

            'numero_inicial_cobranca.required' => 'O número inicial de cobrança é obrigatório.',
            'numero_inicial_cobranca.numeric'  => 'O número inicial deve ser um valor numérico.',
        ];
    }

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
        $this->validate();
        
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
