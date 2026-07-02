<?php

namespace App\Livewire\Banco;

use Livewire\Component;
use App\Models\Banco;
use App\Services\BancoService;
use Illuminate\Validation\Rule; 

class EditBanco extends Component
{
    public $banco, $id, $nome, $cnpj, $numero_banco;

    public function rules(){
        return [
            'nome' => 'required|string|min:3|max:255',
            'cnpj' => [
                'required',
                'string',
                'max:18',
                Rule::unique('banco', 'cnpj')->ignore($this->id)
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

    public function mount($id){
        $this->id = $id;
        $this->banco = Banco::withTrashed()->findOrFail($id); 
        $this->nome = $this->banco->nome;
        $this->cnpj = $this->banco->cnpj;
        $this->numero_banco = $this->banco->numero_banco;
    }

    public function submit(){
        $data = $this->validate();

        $dataBanco = [
            'nome' => $data['nome'],
            'cnpj' => $data['cnpj'],
            'numero_banco' => $data['numero_banco']
        ];

        $service = new BancoService();
        
        $service->update($dataBanco, $this->id);

        $this->dispatch('toast-message', 'Banco atualizado com sucesso!');

    }

    public function render()
    {
        return view('livewire.banco.edit-banco');
    }
}
