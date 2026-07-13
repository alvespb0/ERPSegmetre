<?php

namespace App\Livewire\Conta;

use Livewire\Component;
use App\Models\Conta;
use App\Services\BancoService;
use App\Services\ContaService;
use App\Services\TipoContaService;
use Illuminate\Validation\Rule; 

class EditConta extends Component
{
    public $nome, $modalidade, $banco_id, $tipo_conta_id, $agencia, $conta;
    public $contaModel, $id;

    public function mount($id){
        $this->id = $id;
        $this->contaModel = Conta::withTrashed()->findOrFail($id);
        $this->nome = $this->contaModel->nome;
        $this->modalidade = $this->contaModel->modalidade;
        $this->banco_id = $this->contaModel->banco_id;
        $this->tipo_conta_id = $this->contaModel->tipo_conta_id;
        $this->agencia = $this->contaModel->agencia ?? null;
        $this->conta = $this->contaModel->conta ?? null;
    }

    public function rules(){
        return [
            'nome' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('conta', 'nome'
                    )
                    ->ignore($this->id)
                    ->where(fn ($q) => 
                        $q->where('empresa_parametro_id', Empresa::id()
                    )
                )
            ],
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

        $service->update($contaData, $this->id);

        $this->dispatch('toast-message', 'Conta atualizada com sucesso');

    }

    public function render(TipoContaService $tpContaService, BancoService $bancoService)
    {
        $bancos = $bancoService->show();
        $tiposConta = $tpContaService->show();

        return view('livewire.conta.edit-conta', ['tiposConta' => $tiposConta, 'bancos' => $bancos]);
    }
}
