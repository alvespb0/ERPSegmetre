<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;

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

    public $codigo_juros;
    public $valor_juros;
    public $dias_inicio_juros;
    public $codigo_multa;
    public $valor_multa;
    public $dias_inicio_multa;
    public $dias_limite_pagamento;

    public function mount($parcelaId){
        $this->parcela = Parcela::findOrFail($parcelaId);
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

    public function render()
    {
        return view('livewire.modais.contas-receber.gerar-cobranca');
    }
}
