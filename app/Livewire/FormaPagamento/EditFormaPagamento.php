<?php

namespace App\Livewire\FormaPagamento;

use Livewire\Component;
use App\Models\FormaPagamento;
use App\Services\FormaPagamentoService;
use Illuminate\Validation\Rule; 
use App\Helpers\Empresa;

class EditFormaPagamento extends Component
{
    public $nome, $id, $formaPagamento;

    public function mount($id){
        $this->id = $id;
        $this->formaPagamento = FormaPagamento::withTrashed()->findOrFail($id);
        $this->nome = $this->formaPagamento->nome;
    }

    public function rules(){
        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('forma_pagamento', 'nome'
                    )
                    ->ignore($this->id)
                    ->where(fn ($q) => 
                        $q->where('empresa_parametro_id', Empresa::id()
                    )
                )
            ],
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

        $dataForma = [
            'nome' => $data['nome']
        ];

        $service = new FormaPagamentoService();

        $service->update($dataForma, $this->id);

        $this->dispatch('toast-message', 'Forma de pagamento atualizada com sucesso');
    }

    public function render()
    {
        return view('livewire.forma-pagamento.edit-forma-pagamento');
    }
}
