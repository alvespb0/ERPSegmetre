<?php

namespace App\Livewire\Usuario;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ListUsuario extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $tipo = 'todos';

    public function updatingSearch()
    {
        $this->resetPage();
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

        $usuarios = $query->orderBy('name')->paginate(10);

        return view('livewire.usuario.list-usuario', [
            'usuarios' => $usuarios,
        ]);
    }
}
