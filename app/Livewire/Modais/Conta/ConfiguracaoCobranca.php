<?php

namespace App\Livewire\Modais\Conta;

use Livewire\Component;

use App\Services\ConfiguracaoCobrancaService;

use App\Models\Conta;
use App\Models\EmpresaParametro;
use App\Models\Integracao;

use Illuminate\Validation\Rule;

class ConfiguracaoCobranca extends Component
{

    public $conta;
    public $configCobranca;
    public $empresasParametro;
    public $integracoes;

    public $empresa_parametro_id;
    public $integracao_id;
    public $codigo_cedente;
    public $codigo_juros;
    public $valor_juros;
    public $dias_inicio_juros;
    public $codigo_multa;
    public $valor_multa;
    public $dias_inicio_multa;
    public $dias_limite_pagamento;
    public $carteira;
    public $ambiente;
    public $layout_cnab;
    public $numero_inicial_cobranca;

    public function rules(){
        return [
            'empresa_parametro_id'    => 'required',
            'integracao_id'           => ['nullable', Rule::exists('integracoes', 'id')->where('escopo', 'banco')],
            'codigo_cedente'          => 'required|string|max:255',
            'codigo_juros'            => 'nullable|in:0,1,2',
            'valor_juros'             => 'nullable|numeric|min:0',
            'dias_inicio_juros'       => 'nullable|integer|min:0',
            'codigo_multa'            => 'nullable|in:0,1,2',
            'valor_multa'             => 'nullable|numeric|min:0',
            'dias_inicio_multa'       => 'nullable|integer|min:0',
            'dias_limite_pagamento'   => 'nullable|integer|min:0',
            'carteira'                => 'nullable|string|max:255',
            'ambiente'                => 'required|in:homologacao,producao',
            'layout_cnab'             => 'required|in:240,400',
            'numero_inicial_cobranca' => 'required|numeric',
        ];
    }

    public function messages(){
        return [
            'empresa_parametro_id.required'    => 'A empresa vinculada é obrigatória.',

            'integracao_id.exists'             => 'A integração selecionada é inválida ou não pertence ao escopo banco.',

            'codigo_cedente.required'          => 'O código do cedente é obrigatório.',
            'codigo_cedente.string'            => 'O código do cedente deve ser um texto.',
            'codigo_cedente.max'               => 'O código do cedente deve ter no máximo :max caracteres.',

            'codigo_juros.in'                  => 'O código de juros selecionado é inválido.',
            'valor_juros.numeric'              => 'O valor de juros deve ser numérico.',
            'valor_juros.min'                  => 'O valor de juros não pode ser negativo.',
            'dias_inicio_juros.integer'        => 'Os dias para início de juros devem ser um número inteiro.',
            'dias_inicio_juros.min'            => 'Os dias para início de juros não podem ser negativos.',

            'codigo_multa.in'                  => 'O código de multa selecionado é inválido.',
            'valor_multa.numeric'              => 'O valor da multa deve ser numérico.',
            'valor_multa.min'                  => 'O valor da multa não pode ser negativo.',
            'dias_inicio_multa.integer'        => 'Os dias para início de multa devem ser um número inteiro.',
            'dias_inicio_multa.min'            => 'Os dias para início de multa não podem ser negativos.',

            'dias_limite_pagamento.integer'    => 'Os dias limite de pagamento devem ser um número inteiro.',
            'dias_limite_pagamento.min'        => 'Os dias limite de pagamento não podem ser negativos.',

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
        $this->integracoes = $this->carregarIntegracoes();
        $this->empresa_parametro_id = $this->configCobranca->empresa_parametro_id ?? null;
        $this->integracao_id = $this->configCobranca->integracao_id ?? null;
        $this->codigo_cedente = $this->configCobranca->codigo_cedente ?? null;
        $this->codigo_juros = $this->configCobranca->codigo_juros ?? null;
        $this->valor_juros = $this->configCobranca->valor_juros ?? null;
        $this->dias_inicio_juros = $this->configCobranca->dias_inicio_juros ?? null;
        $this->codigo_multa = $this->configCobranca->codigo_multa ?? null;
        $this->valor_multa = $this->configCobranca->valor_multa ?? null;
        $this->dias_inicio_multa = $this->configCobranca->dias_inicio_multa ?? null;
        $this->dias_limite_pagamento = $this->configCobranca->dias_limite_pagamento ?? null;
        $this->carteira = $this->configCobranca->carteira ?? null;
        $this->ambiente = $this->configCobranca->ambiente ?? null;
        $this->layout_cnab = $this->configCobranca->layout_cnab ?? null;
        $this->numero_inicial_cobranca = $this->configCobranca->numero_inicial_cobranca ?? null;
    }

    public function submit(ConfiguracaoCobrancaService $service){
        $this->validate();
        
        $dados = [
            'empresa_parametro_id' => $this->empresa_parametro_id,
            'integracao_id' => $this->integracao_id ?: null,
            'codigo_cedente' => $this->codigo_cedente,
            'codigo_juros' => $this->codigo_juros ?: null,
            'valor_juros' => $this->valor_juros !== '' && $this->valor_juros !== null ? $this->valor_juros : null,
            'dias_inicio_juros' => $this->dias_inicio_juros !== '' && $this->dias_inicio_juros !== null ? $this->dias_inicio_juros : null,
            'codigo_multa' => $this->codigo_multa ?: null,
            'valor_multa' => $this->valor_multa !== '' && $this->valor_multa !== null ? $this->valor_multa : null,
            'dias_inicio_multa' => $this->dias_inicio_multa !== '' && $this->dias_inicio_multa !== null ? $this->dias_inicio_multa : null,
            'dias_limite_pagamento' => $this->dias_limite_pagamento !== '' && $this->dias_limite_pagamento !== null ? $this->dias_limite_pagamento : null,
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

    private function carregarIntegracoes()
    {
        return Integracao::query()
            ->where('escopo', 'banco')
            ->with('empresaParametro')
            ->orderBy('nome')
            ->get();
    }

    public function render()
    {
        return view('livewire.modais.conta.configuracao-cobranca');
    }
}
