<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

use App\Models\Parcela;
use App\Models\SolicitacoesPagamento;
use App\Services\SolicitacoesPagamentoService;

class VincularTituloDDA extends Component
{
    public array $dadosDDA;
    public $vencimentoDDA;
    public $valorDDA;
    public $nomeBeneficiario;
    public $documentoBeneficiario;
    public $linhaDigitavel;

    public $parcela_id;
    public $busca = '';
    public $filtrarPorFornecedor = true;

    public function mount(array $dadosDDA)
    {
        $this->dadosDDA = $dadosDDA;
        $this->valorDDA = $dadosDDA['valor'];
        $this->vencimentoDDA = $dadosDDA['vencimento']->toDateString();
        $this->nomeBeneficiario = $dadosDDA['nome_beneficiario'];
        $this->documentoBeneficiario = $dadosDDA['documento_beneficiario'];
        $this->linhaDigitavel = $dadosDDA['linha_digitavel'];
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
            'parcela_id.required' => 'Selecione uma parcela para vincular o boleto.',
            'parcela_id.exists' => 'A parcela selecionada é inválida.',
        ];
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

        if ($this->filtrarPorFornecedor && $this->documentoBeneficiario) {
            $documento = preg_replace('/[^0-9]/', '', $this->documentoBeneficiario);
            if ($documento) {
                $query->whereHas('titulo.entidade', function ($q) use ($documento) {
                    $q->whereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '-', ''), '/', ''), ' ', '') = ?",
                        [$documento]
                    );
                });
            }
        }

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
            ->filter(fn (Parcela $parcela) => $parcela->saldo_devedor >= $this->valorDDA)
            ->values();
    }

    public function submit(SolicitacoesPagamentoService $solicitacaoService)
    {
        try {
            $this->validate();

            if (SolicitacoesPagamento::where('identificador', $this->linhaDigitavel)->exists()) {
                $this->dispatch('toast-error', 'Este boleto já está vinculado a uma despesa.');
                return;
            }

            $parcela = Parcela::with(['solicitacoesPagamento', 'movimentacoes'])
                ->findOrFail($this->parcela_id);

            if ($parcela->status === 'cancelado') {
                $this->dispatch('toast-error', 'Não é possível vincular boleto a parcela cancelada.');
                return;
            }

            if ($parcela->possuiSolicitacaoPagamento) {
                $this->dispatch('toast-error', 'Parcela já possui uma solicitação de pagamento pendente.');
                return;
            }

            if ($parcela->saldo_devedor == 0 || $parcela->saldo_devedor < $this->valorDDA) {
                $this->dispatch('toast-error', 'Saldo devedor insuficiente para o valor do boleto.');
                return;
            }

            $solicitacaoService->store([
                'parcela_id' => $parcela->id,
                'tipo' => 'codigo_barras',
                'identificador' => $this->linhaDigitavel,
                'valor' => $this->valorDDA,
                'data_solicitacao' => Carbon::now(),
            ]);

            $this->dispatch('toast-message', 'Boleto vinculado à despesa existente com sucesso!');
            $this->dispatch('boleto-vinculado-dda', linhaDigitavel: $this->linhaDigitavel);
            $this->fechar();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Erro ao vincular boleto DDA à despesa existente', [
                'erro' => $e->getMessage(),
                'linha_digitavel' => $this->linhaDigitavel,
            ]);
            $this->dispatch('toast-error', 'Erro ao vincular boleto à despesa.');
        }
    }

    public function fechar()
    {
        $this->dispatch('fechar-modal-vincular-despesa');
    }

    public function render()
    {
        return view('livewire.modais.contas-pagar.vincular-titulo-d-d-a', [
            'parcelasElegiveis' => $this->parcelasElegiveis,
        ]);
    }
}
