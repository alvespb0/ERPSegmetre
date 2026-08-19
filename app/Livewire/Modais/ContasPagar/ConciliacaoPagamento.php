<?php

namespace App\Livewire\Modais\ContasPagar;

use Livewire\Component;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

use App\Models\SolicitacoesPagamento;
use App\Models\Parcela;

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

    public function render()
    {
        return view('livewire.modais.contas-pagar.conciliacao-pagamento', [
            'parcelasElegiveis' => $this->parcelasElegiveis,
        ]);
    }
}
