<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use Illuminate\Validation\ValidationException;

use App\Models\Parcela;
use App\Models\Conta;

class GerarCobranca extends Component
{
    public $parcela;
    public $pagador;
    public $contas;
    public $selectedConta;
    public $configuracoes;
    public $tipoIntegracao;

    public $modalidade = 1;
    public $especie_documento = 'DM';
    public $codigo_juros;
    public $valor_juros;
    public $dias_inicio_juros;
    public $codigo_multa;
    public $valor_multa;
    public $dias_inicio_multa;
    public $dias_limite_pagamento;
    public $info_complementares;

    public function mount($parcelaId){
        $this->parcela = Parcela::with(['titulo.entidade'])->findOrFail($parcelaId);
        $this->parcela->titulo->loadCount('parcelas');
        $this->pagador = $this->parcela->titulo->entidade;
        $this->contas = Conta::orderBy('nome', 'asc')->get();
    }

    public function fecharModal(){
        $this->dispatch('fechar-modal-cobranca');
    }

    public function selectContaCobranca(Conta $conta){
        $conta->load([
            'banco',
            'configuracaoCobranca'
        ]);

        $this->selectedConta = $conta;
        $this->configuracoes = $conta->configuracaoCobranca;
        $this->codigo_juros = $this->configuracoes->codigo_juros ?? '';
        $this->valor_juros = $this->configuracoes->valor_juros ?? '';
        $this->codigo_multa = $this->configuracoes->codigo_multa ?? '';
        $this->valor_multa = $this->configuracoes->valor_multa ?? '';
        $this->dias_inicio_juros = $this->configuracoes->dias_inicio_juros ?? '';
        $this->dias_inicio_multa = $this->configuracoes->dias_inicio_multa ?? '';
        $this->dias_limite_pagamento = $this->configuracoes->dias_limite_pagamento ?? '';
        
        $integracao = $this->configuracoes->integracao ?? null;
        $this->tipoIntegracao = $integracao
            ? 'api'
            : 'remessa';
    }

    public function limparContaCobranca(){
        $this->selectedConta = null;
        $this->configuracao = null;
    }  

    public function rules(){
        return [
            'especie_documento' => 'required|in:CH,DM,DMI,DS,DSI,DR,LC,NCC,NCE,NCI,NCR,NP,NPR,TM,TS,NS,RC,FAT,ND,AP,ME,PC,NF,DD,BDP,OU',
            'modalidade' => 'required|in:1,2,3,4,5,outro',
            'info_complementares' => 'nullable|string|max:100',
            'dias_limite_pagamento' => 'required|integer|min:0',
            'codigo_juros' => 'required|in:0,1,2',
            'valor_juros' => 'required_unless:codigo_juros,0|numeric',
            'dias_inicio_juros' => 'required|integer|min:0|lte:dias_limite_pagamento',
            'codigo_multa' => 'required|in:0,1,2',
            'valor_multa' => 'required_unless:codigo_multa,0|numeric',
            'dias_inicio_multa' => 'required|integer|min:0|lte:dias_limite_pagamento',
        ];
    }

    public function messages(){
        return [
            'especie_documento.required' => 'A espécie do documento é obrigatória.',
            'especie_documento.in' => 'A espécie do documento informada é inválida.',

            'modalidade.required' => 'A modalidade de cobrança é obrigatória.',
            'modalidade.in' => 'A modalidade de cobrança informada é inválida.',

            'info_complementares.string' => 'As informações complementares devem ser um texto válido.',
            'info_complementares.max' => 'As informações complementares não podem ultrapassar 100 caracteres.',

            'dias_limite_pagamento.required' => 'Informe o prazo limite para pagamento do boleto.',
            'dias_limite_pagamento.integer' => 'O prazo limite para pagamento deve ser um número inteiro.',
            'dias_limite_pagamento.min' => 'O prazo limite para pagamento não pode ser negativo.',

            'codigo_juros.required' => 'Informe o tipo de juros.',
            'codigo_juros.in' => 'O tipo de juros informado é inválido.',

            'valor_juros.required_unless' => 'Informe o valor dos juros.',
            'valor_juros.numeric' => 'O valor dos juros deve ser numérico.',

            'dias_inicio_juros.required' => 'Informe em quantos dias após o vencimento os juros serão aplicados.',
            'dias_inicio_juros.integer' => 'Os dias para início dos juros devem ser um número inteiro.',
            'dias_inicio_juros.min' => 'Os dias para início dos juros não podem ser negativos.',
            'dias_inicio_juros.lte' => 'Os dias para início dos juros não podem ser maiores que o prazo limite para pagamento.',

            'codigo_multa.required' => 'Informe o tipo de multa.',
            'codigo_multa.in' => 'O tipo de multa informado é inválido.',

            'valor_multa.required_unless' => 'Informe o valor da multa.',
            'valor_multa.numeric' => 'O valor da multa deve ser numérico.',

            'dias_inicio_multa.required' => 'Informe em quantos dias após o vencimento a multa será aplicada.',
            'dias_inicio_multa.integer' => 'Os dias para início da multa devem ser um número inteiro.',
            'dias_inicio_multa.min' => 'Os dias para início da multa não podem ser negativos.',
            'dias_inicio_multa.lte' => 'Os dias para início da multa não podem ser maiores que o prazo limite para pagamento.',
        ];
    }

    public function gerar(){
        try{
            $data = $this->validate();
            
            if($this->tipoIntegracao == 'api'){
                $factory = new \App\Factories\IntegracaoFactory;
                #dd($this->configuracoes->integracao);
                $serviceProvider = $factory->make($this->configuracoes->integracao, 'cobranca');
            }else{
                $serviceProvider = '\Bancos\Gerador\Remessa240Generica'; # por enquanto hardcodado eu vou fazer algo pra quando não tem integração vinculada
            }

            dd($serviceProvider);


        } catch (ValidationException $e) {
            throw $e; 
        } catch(\Exception $e){
            \Log::error([
                    'Erro ao gerar boleto' => $e->getMessage(),
                ]);
            return $this->dispatch('toast-error', 'Erro ao gerar boleto.');
        }
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.gerar-cobranca');
    }
}
