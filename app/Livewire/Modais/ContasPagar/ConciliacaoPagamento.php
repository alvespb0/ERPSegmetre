<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use App\Helpers\Empresa;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

use App\Models\SolicitacoesPagamento;
use App\Models\Parcela;

use App\Services\MovimentacaoService;

class ConciliacaoPagamento extends Component
{
    public $pagamento;

    public $filtrarPorValorSemelhante = false;
    public $parcela_id;
    public $busca = '';

    public function mount($solicitacaoId){
        $this->pagamento = SolicitacoesPagamento::findOrFail($solicitacaoId);
    }

    public function getParcelasElegiveisProperty(): Collection
    {
        $query = Parcela::with([
            'titulo.entidade',
            'titulo' => fn ($q) => $q->withCount('parcelas'),
            'movimentacoes',
            'solicitacoesPagamento',
        ])
            ->where('status', 'ativo')
            ->whereHas('titulo', fn ($q) => $q->where('tipo', 'pagar')->where('status', 'ativo'))
            ->whereDoesntHave('solicitacoesPagamento', fn ($q) => $q->where('status', 'pendente'));

        if ($this->busca) {
            $termo = '%' . $this->busca . '%';
            $query->where(function ($q) use ($termo) {
                $q->whereHas('titulo', fn ($t) => $t->where('descricao', 'like', $termo))
                    ->orWhereHas('titulo.entidade', function ($e) use ($termo) {
                        $e->where('razao_social', 'like', $termo)
                            ->orWhere('nome_fantasia', 'like', $termo)
                            ->orWhere('cpf_cnpj', 'like', $termo);
                    });
            });
        }

        return $query
            ->orderBy('data_vencimento', 'asc')
            ->limit(50)
            ->get()
            ->filter(fn (Parcela $parcela) => $parcela->saldo_devedor >= $this->pagamento->valor)
            ->values();
    }

    public function fechar(){
        $this->dispatch('fechar-modal-conciliacao');
    }

    public function rules()
    {
        return [
            'parcela_id' => 'required|exists:parcelas,id',
        ];
    }

    public function messages()
    {
        return [
            'parcela_id.required' => 'Selecione uma parcela para vincular o pagamento.',
            'parcela_id.exists' => 'A parcela selecionada é inválida.',
        ];
    }

    public function submit(MovimentacaoService $movimentacaoService){
        try {
            $this->validate();


            $parcela = Parcela::findOrFail($this->parcela_id);

            if ($parcela->status === 'cancelado') {
                $this->dispatch('toast-error', 'Não é possível vincular pagamento a parcela cancelada.');
                return;
            }

            if ($parcela->possuiSolicitacaoPagamento) {
                $this->dispatch('toast-error', 'Parcela já possui uma solicitação de pagamento pendente.');
                return;
            }

            if ($parcela->saldo_devedor == 0 || $parcela->saldo_devedor < $this->pagamento->valor) {
                $this->dispatch('toast-error', 'Saldo devedor insuficiente mentor que valor de pagamento.');
                return;
            }

            if(!$this->pagamento->conta_id){
                $this->dispatch('toast-error', 'Pagamento sem conta de origem.');
                return;
            }

            DB::beginTransaction();

            $movimentacao = $movimentacaoService->store([
                'conta_id' => $this->pagamento->conta_id,
                'empresa_parametro_id' => Empresa::id(),
                'parcela_id' => $parcela->id,
                'valor_pago' => $this->pagamento->valor,
                'data_pagamento' => $this->pagamento->data_pagamento ?? $this->pagamento->data_solicitacao,
            ]);

            $this->pagamento->update([
                'movimentacao_id' => $movimentacao->id,
                'parcela_id' => $parcela->id,
            ]);

            DB::commit();
            $this->dispatch('toast-message', 'Pagamento vinculado à despesa existente com sucesso!');
            $this->fechar();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            \Log::error('Erro ao fazer conciliação de pagamento e parcela', [
                'erro' => $e->getMessage(),
            ]);
            $this->dispatch('toast-error', 'Erro na conciliação.');
        }

    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.conciliacao-pagamento', [
            'parcelasElegiveis' => $this->parcelasElegiveis,
        ]);
    }
}
