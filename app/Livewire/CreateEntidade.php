<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class CreateEntidade extends Component
{
    public $razaoSocial, $nomeFantasia, $tipo, $classificacao, $cnpjcpf;
    public $email, $telefone;
    public $rua, $numero, $complemento, $bairro, $cep, $cidade, $uf;

    public $showContato = false;
    public $showEndereco = false;

    public function submit(){
        $data = $this->validate([
            'razaoSocial' => 'required|string|max:255',
            'nomeFantasia' => 'nullable|string|max:255',
            'cnpjcpf' => 'required|string|max:18|unique:entidade,cpf_cnpj',
            'tipo' => 'required|in:pf,pj',
            'classificacao' => 'required|in:cliente,fornecedor',

            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',

            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:10',
            'cidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|size:2',
        ]);

    }

    public function consultaCnpj(){
        if($this->cnpjcpf != null && strlen($this->cnpjcpf) == 18){
            $response = http::get('https://api.opencnpj.org/'.$this->cnpjcpf);
            
            if($response->ok()){
                $data = $response->json();
                $this->razaoSocial = $data['razao_social'] ?? '';
                $this->nomeFantasia = $data['nome_fantasia'] ?? '';
                $this->email = $data['email'] ?? '';
                $this->telefone = '('. $data['telefones'][0]['ddd'] . ') ' . $data['telefones'][0]['numero'] ?? '';
                $this->rua = $data['logradouro'] ?? '';
                $this->numero = $data['numero'] ?? 'n/a';
                $this->complemento = $data['complemento'] ?? 'n/a';
                $this->bairro = $data['bairro'] ?? '';
                $this->cep = $data['cep'] ?? '';
                $this->cidade = $data['municipio'] ?? '';
                $this->uf = $data['uf'] ?? '';

                $this->dispatch('toast-message', 'CNPJ resgatado com sucesso');
            }else{
                $this->dispatch('toast-error', 'Erro ao resgatar CNPJ');
            }
        }
    }

    public function render()
    {
        return view('livewire.entidades.create-entidade');
    }
}
