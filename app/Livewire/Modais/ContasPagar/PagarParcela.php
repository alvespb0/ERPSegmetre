<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Models\Parcela;

use App\Services\MovimentacaoService;
use App\Services\FormaPagamentoService;

class PagarParcela extends Component
{
    public $parcela;
    public $formasPagamento;
    public $pagamentoData;
    public $pagamentoValor;
    public $pagamentoFormaId = '';
    public $contas;

    public function mount($parcelaId, FormaPagamentoService $formasPagamentoService){
        $this->parcela = Parcela::with('titulo.entidade', 'movimentacoes')->findOrFail($parcelaId);
        $this->formasPagamento = $formasPagamentoService->show();
        $this->pagamentoData = today()->format('Y-m-d');
        $this->pagamentoValor = $this->parcela->saldo_devedor;
    }

    public function salvarPagamento(MovimentacaoService $movimentacaoService){
        $this->validate([
            'pagamentoData' => 'required|date',
            'pagamentoValor' => 'required|numeric|min:0.01|max:' . $this->parcela->saldo_devedor, // Evita pagar mais que o devido
            'pagamentoFormaId' => 'required|exists:forma_pagamento,id',
        ], [
            'pagamentoData.required' => 'A data do pagamento é obrigatória.',
            'pagamentoData.date' => 'Informe uma data de pagamento válida.',

            'pagamentoValor.required' => 'O valor do pagamento é obrigatório.',
            'pagamentoValor.numeric' => 'O valor do pagamento deve ser um número.',
            'pagamentoValor.min' => 'O valor do pagamento deve ser maior que zero.',
            'pagamentoValor.max' => 'O valor pago não pode ser maior que o saldo devedor.',

            'pagamentoFormaId.required' => 'A forma de pagamento é obrigatória.',
            'pagamentoFormaId.exists' => 'A forma de pagamento selecionada é inválida.',
        ]);

        $movimentacaoService->store([
            'forma_pagamento_id' => $this->pagamentoFormaId ?? null,
            'parcela_id' => $this->parcela->id,
            'valor_pago' => $this->pagamentoValor,
            'data_pagamento' => $this->pagamentoData
        ]);

        $this->reset(['pagamentoData', 'pagamentoValor', 'pagamentoFormaId']);

        $this->dispatch('fechar-modal-pagamento');    

        $this->dispatch('toast-message', 'Pagamento lançado com sucesso!');
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.pagar-parcela');
    }
}
