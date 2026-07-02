<?php

namespace App\Livewire\Conta;

use Livewire\Component;
use App\Services\BancoService;
use App\Services\ContaService;
use App\Services\TipoContaService;

class CreateConta extends Component
{
    public $nome, $modalidade, $banco_id, $tipo_conta_id, $agencia, $conta;

    public function rules(){
        return [
            'nome' => 'required|string|min:2|max:255|unique:conta,nome',
            'modalidade' => 'required|in:pj,pf',
            'banco_id' => 'required|exists:banco,id',
            'tipo_conta_id' => 'required|exists:tipo_conta,id',
            'agencia' => 'nullable|string|min:2|max:10',
            'conta' => 'nullable|string|min:2|max:30'
        ];
    }

    public function messages(){
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.min' => 'O nome deve ter pelo menos :min caracteres.',
            'nome.max' => 'O nome não pode ultrapassar :max caracteres.',
            'nome.unique' => 'Já existe uma conta cadastrada com esse nome.',

            'modalidade.required' => 'O campo modalidade é obrigatório.',
            'modalidade.in' => 'A modalidade deve ser "pj" ou "pf".',

            'banco_id.required' => 'O campo banco é obrigatório.',
            'banco_id.exists' => 'O banco selecionado é inválido.',

            'tipo_conta_id.required' => 'O tipo de conta é obrigatório.',
            'tipo_conta_id.exists' => 'O tipo de conta selecionado é inválido.',

            'agencia.string' => 'A agência deve ser um texto válido.',
            'agencia.min' => 'A agência deve ter pelo menos :min caracteres.',
            'agencia.max' => 'A agência não pode ultrapassar :max caracteres.',

            'conta.string' => 'A conta deve ser um texto válido.',
            'conta.min' => 'A conta deve ter pelo menos :min caracteres.',
            'conta.max' => 'A conta não pode ultrapassar :max caracteres.',
        ];
    }

    public function submit(){
        $data = $this->validate();

        $contaData = [
            'banco_id' => $data['banco_id'],
            'tipo_conta_id' => $data['tipo_conta_id'],
            'nome' => $data['nome'],
            'modalidade' => $data['modalidade'],
            'agencia' => $data['agencia'] ?? null,
            'conta' => $data['conta'] ?? null
        ];

        $service = new ContaService();

        $service->store($contaData);

        $this->dispatch('toast-message', 'Conta cadastrada com sucesso');
    }
    
    public function render(TipoContaService $tpContaService, BancoService $bancoService){
        $bancos = $bancoService->show();
        $tiposConta = $tpContaService->show();
        return view('livewire.conta.create-conta', ['tiposConta' => $tiposConta, 'bancos' => $bancos]);
    }
}
