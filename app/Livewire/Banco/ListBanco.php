<?php

namespace App\Livewire\Banco;

use Livewire\Component;
use App\Models\Banco;

class ListBanco extends Component
{
    public function render()
    {
        $bancos = Banco::orderBy('nome', 'asc')->paginate(10);

        return view('livewire.banco.list-banco', ['bancos' => $bancos]);
    }
}
