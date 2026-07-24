<?php

namespace App\Livewire\Modais\ContasReceber;

use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\DB;

use App\Helpers\Empresa;

use App\Models\Parcela;

use App\Services\MovimentacaoService;
use App\Services\FormaPagamentoService;
use App\Services\AnexoService;
use App\Services\ContaService;

class ReceberParcela extends Component
{

    use WithFileUploads;
    
    public $parcela;
    public $formasPagamento;
    public $pagamentoData;
    public $pagamentoValor;
    public $pagamentoFormaId = '';
    public $comprovante;
    public $descricaoAnexo;
    public $contas;
    public $contaId = '';

    public function mount($parcelaId, FormaPagamentoService $formasPagamentoService, ContaService $contaService){
        $this->parcela = Parcela::with('titulo.entidade', 'movimentacoes')->findOrFail($parcelaId);
        $this->formasPagamento = $formasPagamentoService->show();
        $this->contas = $contaService->show();
        $this->pagamentoData = today()->format('Y-m-d');
        $this->pagamentoValor = $this->parcela->saldo_devedor;
    }

    public function salvarRecebimento(MovimentacaoService $movimentacaoService){
        $this->validate([
            'pagamentoData' => 'required|date',
            'pagamentoValor' => 'required|numeric|min:0.01|max:' . $this->parcela->saldo_devedor, // Evita pagar mais que o devido
            'pagamentoFormaId' => 'required|exists:forma_pagamento,id',
            'contaId' => 'required|exists:conta,id',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'descricaoAnexo' => 'nullable|string|max:255',
        ], [
            'pagamentoData.required' => 'A data do pagamento é obrigatória.',
            'pagamentoData.date' => 'Informe uma data de pagamento válida.',

            'pagamentoValor.required' => 'O valor do pagamento é obrigatório.',
            'pagamentoValor.numeric' => 'O valor do pagamento deve ser um número.',
            'pagamentoValor.min' => 'O valor do pagamento deve ser maior que zero.',
            'pagamentoValor.max' => 'O valor pago não pode ser maior que o saldo devedor.',

            'pagamentoFormaId.required' => 'A forma de pagamento é obrigatória.',
            'pagamentoFormaId.exists' => 'A forma de pagamento selecionada é inválida.',
            
            'contaId.required' => 'A conta de recebimento é obrigatória.',
            'contaId.exists' => 'A Conta selecionada é inválida.',

            'comprovante.file' => 'O comprovante deve ser um arquivo válido.',
            'comprovante.mimes' => 'O comprovante deve ser um arquivo do tipo: PDF, JPG ou PNG.',
            'comprovante.max' => 'O comprovante não pode ser maior que 5MB.',

            'descricaoAnexo.string' => 'A descrição do anexo deve ser um texto válido.',
            'descricaoAnexo.max' => 'A descrição do anexo pode ter no máximo 255 caracteres.',

        ]);

        DB::transaction(function () use ($movimentacaoService) {
            $movimentacao = $movimentacaoService->store([
                'forma_pagamento_id' => $this->pagamentoFormaId ?? null,
                'empresa_parametro_id' => Empresa::id(),
                'conta_id' => $this->contaId ?? null,
                'parcela_id' => $this->parcela->id,
                'valor_pago' => $this->pagamentoValor,
                'data_pagamento' => $this->pagamentoData
            ]);

            if($this->comprovante){
                $service = new AnexoService();

                $service->criarAnexoMovimentacao($movimentacao, $this->comprovante, 'comprovante', $this->descricaoAnexo ?? null);
            }
        });

        $this->reset(['pagamentoData', 'pagamentoValor', 'pagamentoFormaId', 'contaId', 'comprovante', 'descricaoAnexo']);

        $this->dispatch('fechar-modal-recebimento');    

        $this->dispatch('toast-message', 'Recebimento lançado com sucesso!');
    }

    public function render()
    {
        return view('livewire.modais.contas-receber.receber-parcela');
    }
}
