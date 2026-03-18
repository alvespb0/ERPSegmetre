<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Entidade;
use App\Services\EntidadeService;
use App\Services\ContatoService;
use App\Services\EnderecoEntidadeService;
use Illuminate\Validation\Rule; 

class EditEntidade extends Component
{
    public $id;
    public $entidade;
    public $razaoSocial, $nomeFantasia, $tipo, $classificacao, $cnpjcpf;
    public $email, $telefone;
    public $rua, $numero, $complemento, $bairro, $cep, $cidade, $uf;
    public $showContato;
    public $showEndereco;

    public function mount($id){
        $this->id = $id;
        $this->entidade = Entidade::findOrFail($id);

        $this->razaoSocial = $this->entidade->razao_social;
        $this->nomeFantasia = $this->entidade->nome_fantasia;
        $this->cnpjcpf = $this->entidade->cpf_cnpj;
        $this->tipo = $this->entidade->tipo;
        $this->classificacao = $this->entidade->classificacao;

        $contatos = $this->entidade->contatos?->first();
        $this->email = $contatos?->email ?? '';
        $this->telefone = $contatos?->telefone ?? '';

        $endereco = $this->entidade->enderecos?->first();
        $this->rua = $endereco?->rua ?? '';
        $this->numero = $endereco?->numero ?? '';
        $this->complemento = $endereco?->complemento ?? '';
        $this->bairro = $endereco?->bairro ?? '';
        $this->cep = $endereco?->cep ?? '';
        $this->cidade = $endereco?->cidade ?? '';
        $this->uf = $endereco?->uf ?? '';

        $this->showContato = $this->entidade->contatos->first() ? true : false;
        $this->showEndereco = $this->entidade->enderecos->first() ? true : false;
    }

    public function messages(){
        return [
            // Razão Social
            'razaoSocial.required' => 'O campo Razão Social / Nome Completo é obrigatório.',
            'razaoSocial.max' => 'A Razão Social não pode ter mais que 255 caracteres.',

            // Nome Fantasia
            'nomeFantasia.max' => 'O Nome Fantasia não pode ter mais que 255 caracteres.',

            // CNPJ/CPF
            'cnpjcpf.required' => 'O campo CNPJ / CPF é obrigatório.',
            'cnpjcpf.max' => 'O CNPJ / CPF não pode ter mais que 18 caracteres.',
            'cnpjcpf.unique' => 'Este CNPJ ou CPF já está cadastrado no sistema.',

            // Tipo e Classificação
            'tipo.required' => 'Por favor, selecione o Tipo (Pessoa Física ou Jurídica).',
            'tipo.in' => 'O Tipo selecionado é inválido.',
            'classificacao.required' => 'Por favor, selecione a Classificação (Cliente ou Fornecedor).',
            'classificacao.in' => 'A Classificação selecionada é inválida.',

            // Contato
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais que 255 caracteres.',
            'telefone.max' => 'O telefone não pode ter mais que 20 caracteres.',

            // Endereço
            'rua.max' => 'O campo Rua não pode ter mais que 255 caracteres.',
            'numero.max' => 'O campo Número não pode ter mais que 20 caracteres.',
            'complemento.max' => 'O Complemento não pode ter mais que 255 caracteres.',
            'bairro.max' => 'O Bairro não pode ter mais que 255 caracteres.',
            'cep.max' => 'O CEP não pode ter mais que 10 caracteres.',
            'cidade.max' => 'A Cidade não pode ter mais que 255 caracteres.',
            'uf.size' => 'A UF deve conter exatamente 2 letras (ex: SP).',
        ];
    }

    public function rules(){
        return [
            'razaoSocial' => 'required|string|max:255',
            'nomeFantasia' => 'nullable|string|max:255',
            'cnpjcpf' => [
                'required',
                'string',
                'max:18',
                Rule::unique('entidade', 'cpf_cnpj')->ignore($this->id),
            ],
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
        ];
    }

    public function submit(){
        $data = $this->validate();

        $entidadeData = [
            'razao_social' => $data['razaoSocial'],
            'nome_fantasia' => $data['nomeFantasia'],
            'cpf_cnpj' => $data['cnpjcpf'],
            'tipo' => $data['tipo'],
            'classificacao' => $data['classificacao']
        ];

        $entidadeService = new EntidadeService();

        $entidade = $entidadeService->update($entidadeData, $this->entidade->id);

        if($this->showContato === true){
            $contatoService = new ContatoService();

            $contatoData = [
                'telefone' => $data['telefone'],
                'email' => $data['email']
            ];

            $contato = $contatoService->updateOrCreate($contatoData, $this->entidade->contatos->first()->id);
        }else{
            $this->entidade->contatos()->delete();
        }

        if($this->showEndereco === true){
            $enderecoService = new EnderecoEntidadeService();

            $enderecoData = [
                'rua' => $data['rua'],
                'bairro' => $data['bairro'],
                'numero' => $data['numero'],
                'cep' => $data['cep'],
                'cidade' => $data['cidade'],
                'uf' => $data['uf'],
                'complemento' => $data['complemento']
            ];

            $contato = $enderecoService->updateOrCreate($enderecoData, $this->entidade->enderecos->first()->id);
        }else{
            $this->entidade->enderecos()->delete();
        }
        
        $this->dispatch('toast-message', 'Entidade atualizada com sucesso!');
    }

    public function render()
    {
        return view('livewire.entidades.edit-entidade');
    }
}
