<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Services\BoletoCobrancaService;

use App\Models\Parcela;
use App\Models\Conta;
use App\Models\BoletoCobranca;

class GerarCobrancaLote extends Component
{
    public array $parcelasIds = [];
    public $parcelas;
    public $contas;
    public $selectedConta;
    public $configuracoes;

    public $modalidade = 1;
    public $especie_documento = 'DM';
    public $codigo_juros;
    public $valor_juros;
    public $dias_inicio_juros;
    public $codigo_multa;
    public $valor_multa;
    public $dias_inicio_multa;
    public $dias_limite_pagamento;
    public $info_complementares = [];

    public function mount(array $parcelasIds){
        $this->parcelasIds = $parcelasIds;
        $this->parcelas = Parcela::with(['titulo.entidade'])->whereIn('id', $this->parcelasIds)->get();
        $this->contas = Conta::orderBy('nome', 'asc')->get();
    }

    /**
     * Dispara um evento para o front-end indicando o fechamento do modal de cobrança.
     *
     * @return void
     */
    public function fecharModal(){
        $this->dispatch('fechar-modal-cobranca-lote');
    }

    /**
     * Seleciona uma conta para cobrança e preenche automaticamente as configurações atreladas a ela.
     *
     * @param Conta $conta A instância da conta bancária selecionada.
     * @return void
     */
    public function selectContaCobranca(Conta $conta){
        $conta->load([
            'banco',
            'configuracaoCobranca'
        ]);

        $this->selectedConta = $conta;
        $this->configuracoes = $conta->configuracaoCobranca ?? null;
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


    public function render()
    {
        return view('livewire.modais.contas-receber.gerar-cobranca-lote');
    }
}
