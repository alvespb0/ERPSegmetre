<?php

namespace App\Livewire\TipoConta;

use Livewire\Component;
use App\Services\TipoContaService;

class CreateTipoConta extends Component
{
    public $descricao;

    public function rules(){
        return [
            'descricao' => 'required|string|min:3|max:255'
        ];
    }

    public function messages(){
        return [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string'   => 'A descrição deve ser um texto válido.',
            'descricao.min'      => 'A descrição deve ter no mínimo :min caracteres.',
            'descricao.max'      => 'A descrição deve ter no máximo :max caracteres.',
        ];
    }  

    public function submit(){
        $data = $this->validate();

        $tpContaData = [
            'descricao' => $data['descricao']
        ];

        $service = new TipoContaService();

        $service->store($tpContaData);

        $this->dispatch('toast-message', 'Tipo de conta cadastrada com sucesso');
    }
    
    public function render(){
        return view('livewire.tipo-conta.create-tipo-conta');
    }
}
