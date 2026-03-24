<?php

namespace App\Livewire\Conta;

use Livewire\Component;
use App\Models\Conta;

class ListConta extends Component
{
    public function render()
    {
        $contas = Conta::orderBy('nome', 'asc')->paginate(10);
        return view('livewire.conta.list-conta', ['contas' => $contas]);
    }
}
