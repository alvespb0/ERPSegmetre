<?php

namespace App\Livewire\FormaPagamento;

use Livewire\Component;
use App\Services\FormaPagamentoService;

class CreateFormaPagamento extends Component
{
    public $nome;

    public function rules(){
        return [
            'nome' => 'required|string|min:2|max:255|unique:forma_pagamento,nome'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo deve ser em formato de texto.',
            'nome.min' => 'O campo deve ter ao menos 2 caracteres.',
            'nome.max' => 'O campo deve ter no máximo 255 caracteres.',
            'nome.unique' => 'Já há uma forma de pagamento com esse nome'
        ];
    }

    public function submit(){
        $data = $this->validate();

        $pagamentoData = [
            'nome' => $data['nome']
        ];

        $service = new FormaPagamentoService();

        $service->store($pagamentoData);

        $this->dispatch('toast-message', 'Forma de pagamento cadastrada com sucesso');
    }

    public function render()
    {
        return view('livewire.forma-pagamento.create-forma-pagamento');
    }
}
