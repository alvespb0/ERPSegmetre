<?php

namespace App\Livewire\CentroCusto;

use Livewire\Component;
use App\Models\CentroCusto;
use App\Services\CentroCustoService;
use Illuminate\Validation\Rule; 

class EditCentroCusto extends Component
{
    public $id, $nome, $descricao, $centroCusto;

    public function mount($id){
        $this->id = $id;

        $this->centroCusto = CentroCusto::withTrashed()->findOrFail($id);

        $this->nome = $this->centroCusto->nome;
        $this->descricao = $this->centroCusto->descricao ?? null;
    }

    public function rules(){
        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('centro_custo', 'nome')->ignore($this->id),
            ],
            'descricao' => 'nullable|string|min:3|max:255'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.unique' => 'Já existe um centro de custo cadastrado com esse nome',
            'nome.min' => 'O nome deve ter no mínimo :min caracteres.',
            'nome.max' => 'O nome deve ter no máximo :max caracteres.',

            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.min' => 'A descrição deve ter no mínimo :min caracteres.',
            'descricao.max' => 'A descrição deve ter no máximo :max caracteres.',
        ];
    }

    public function submit(){
        $data = $this->validate();

        $cenCustoData = [
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null
        ];

        $service = new CentroCustoService();
        
        $service->update($cenCustoData, $this->id);

        $this->dispatch('toast-message', 'Centro de Custo atualizada com sucesso!');
    }

    public function render()
    {
        return view('livewire.centro-custo.edit-centro-custo');
    }
}
