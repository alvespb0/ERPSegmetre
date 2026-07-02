<?php

namespace App\Livewire\Integracao;

use App\Models\Integracao;
use App\Services\IntegracaoService;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListIntegracao extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $status = 'todos';
    public $escopo = 'todos';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEscopo(): void
    {
        $this->resetPage();
    }

    public function inativarIntegracao(int $id): void
    {
        (new IntegracaoService())->destroy($id);

        $this->dispatch('toast-message', 'Integração inativada com sucesso');
    }

    public function ativarIntegracao(int $id): void
    {
        (new IntegracaoService())->restore($id);

        $this->dispatch('toast-message', 'Integração reativada com sucesso');
    }

    public function editarIntegracao(int $id): void
    {
        redirect()->route('erp.dev.integracoes.update', Crypt::encrypt($id));
    }

    public function render()
    {
        $query = Integracao::query()->with('empresaParametro');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%')
                    ->orWhere('provider', 'like', '%' . $this->search . '%')
                    ->orWhere('endpoint', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->escopo !== 'todos') {
            $query->where('escopo', $this->escopo);
        }

        $query->when($this->status === 'inativo', fn ($q) => $q->onlyTrashed());
        $query->when($this->status === 'ativo', fn ($q) => $q->whereNull('deleted_at'));
        $query->when($this->status === 'todos', fn ($q) => $q->withTrashed());

        $integracoes = $query->orderBy('nome')->paginate(10);

        return view('livewire.integracao.list-integracao', [
            'integracoes' => $integracoes,
        ]);
    }
}
