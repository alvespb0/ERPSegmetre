<?php

namespace App\Livewire\Banco;

use Livewire\Component;
use App\Services\BancoService;
use Illuminate\Validation\Rule;
use App\Helpers\Empresa;

class CreateBanco extends Component
{
    public $nome, $cnpj, $numero_banco;
    
    public function rules(){
        return [
            'nome' => 'required|string|min:3|max:255',
            'cnpj' => [
                'required',
                'string',
                'max:18',
                Rule::unique('banco')
                    ->where(fn ($q) =>
                        $q->where(
                            'empresa_parametro_id',
                            Empresa::id()
                        )
                    )
            ],
            'numero_banco' => 'nullable|string'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'nome.max' => 'O nome pode ter no máximo 255 caracteres.',

            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.string' => 'O CNPJ deve ser um texto válido.',
            'cnpj.max' => 'O CNPJ pode ter no máximo 18 caracteres.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
        ];
    }

    public function submit(){
        $data = $this->validate();

        $bancoData = [
            'nome' => $data['nome'],
            'cnpj' => $data['cnpj'],
            'numero_banco' => $data['numero_banco']
        ];

        $service = new BancoService();

        $service->store($bancoData);

        $this->dispatch('toast-message', 'Banco cadastrado com sucesso');
    }

    public function render(){
        return view('livewire.banco.create-banco');
    }
}
