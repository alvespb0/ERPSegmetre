<?php

namespace App\Livewire\Usuario;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ListUsuario extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';

    public $tipo = 'todos';

    public $status = 'todos';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inativarUsuario(int $id): void
    {
        if ($id === Auth::id()) {
            $this->dispatch('toast-message', 'Você não pode inativar sua própria conta.');

            return;
        }

        $service = new UserService();
        $service->destroy($id);

        $this->dispatch('toast-message', 'Usuário inativado com sucesso!');
    }

    public function ativarUsuario(int $id): void
    {
        $service = new UserService();
        $service->restore($id);

        $this->dispatch('toast-message', 'Usuário reativado com sucesso!');
    }

    public function editarUsuario(int $id): void
    {
        $idEnc = Crypt::encrypt($id);

        redirect()->route('erp.usuarios.update', $idEnc);
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->tipo !== 'todos') {
            $query->where('tipo', $this->tipo);
        }

        $query->when($this->status === 'inativo', function ($q) {
            $q->onlyTrashed();
        });

        $query->when($this->status === 'ativo', function ($q) {
            $q->whereNull('deleted_at');
        });

        $query->when($this->status === 'todos', function ($q) {
            $q->withTrashed();
        });

        $usuarios = $query->orderBy('name')->paginate(10);

        return view('livewire.usuario.list-usuario', [
            'usuarios' => $usuarios,
        ]);
    }
}
