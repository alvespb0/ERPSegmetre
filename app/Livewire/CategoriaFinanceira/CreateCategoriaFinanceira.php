<?php

namespace App\Livewire\CategoriaFinanceira;

use Livewire\Component;
use App\Services\CategoriaFinanceiraService;

class CreateCategoriaFinanceira extends Component
{
    public $nome, $tipo, $descricao;

    public function rules(){
        return [
            'nome' => 'required|string|min:3|max:255|unique:categoria_financeira,nome',
            'descricao' => 'nullable|string|min:3|max:255',
            'tipo' => 'required|in:receita,despesa'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.min' => 'O nome deve ter no mínimo :min caracteres.',
            'nome.max' => 'O nome deve ter no máximo :max caracteres.',
            'nome.unique' => 'Já existe uma categoria financeira com esse nome.',

            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.min' => 'A descrição deve ter no mínimo :min caracteres.',
            'descricao.max' => 'A descrição deve ter no máximo :max caracteres.',

            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser receita ou despesa.',
        ];
    }

    public function submit(){
        $data = $this->validate();

        $catFinData = [
            'nome' => $data['nome'],
            'descricao' => $data['descricao'],
            'tipo' => $data['tipo']
        ];

        $service = new CategoriaFinanceiraService();

        $service->store($catFinData);

        $this->dispatch('toast-message', 'Categoria Financeira salva com sucesso!');
    }

    public function render(){
        return view('livewire.categoria-financeira.create-categoria-financeira');
    }
}
