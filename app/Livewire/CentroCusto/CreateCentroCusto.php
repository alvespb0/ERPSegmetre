<?php

namespace App\Livewire\CentroCusto;

use Livewire\Component;
use App\Services\CentroCustoService;

class CreateCentroCusto extends Component
{
    public $nome, $descricao;

    public function rules(){
        return [
            'nome' => 'required|string|min:3|max:255',
            'descricao' => 'nullable|string|min:3|max:255'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string'   => 'O campo nome deve ser um texto.',
            'nome.min'      => 'O nome deve ter no mínimo :min caracteres.',
            'nome.max'      => 'O nome deve ter no máximo :max caracteres.',

            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.min'    => 'A descrição deve ter no mínimo :min caracteres.',
            'descricao.max'    => 'A descrição deve ter no máximo :max caracteres.',
        ];
    }

    public function submit(){
        $data = $this->validate();

        $cenCustoData = [
            'nome' => $data['nome'],
            'descricao' => $data['descricao']
        ];

        $service = new CentroCustoService();

        $service->store($cenCustoData);
    
        $this->dispatch('toast-message', 'Entidade salva com sucesso!');
    }
    
    public function render(){
        return view('livewire.centro-custo.create-centro-custo');
    }
}
