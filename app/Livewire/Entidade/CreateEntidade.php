<?php

namespace App\Livewire\Entidade;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Services\EntidadeService;
use App\Services\ContatoService;
use App\Services\EnderecoEntidadeService;

/**
 * Componente Livewire responsável pela criação de uma nova Entidade.
 * * Este componente gerencia o formulário de cadastro, incluindo validação,
 * inserção dos dados principais (PF/PJ) e criação opcional de contatos 
 * e endereços vinculados. Também possui integração com API externa para 
 * preenchimento automático via CNPJ.
 */
class CreateEntidade extends Component
{
    public $razaoSocial, $nomeFantasia, $tipo, $classificacao, $cnpjcpf;
    public $email, $telefone;
    public $rua, $numero, $complemento, $bairro, $cep, $cidade, $uf;

    public $showContato = false;
    public $showEndereco = false;

    /**
     * Retorna as mensagens de erro personalizadas para a validação.
     *
     * @return array<string, string>
     */
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

    /**
     * Define as regras de validação aplicadas no momento da submissão.
     *
     * @return array<string, string>
     */
    public function rules(){
        return [
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
        ];
    }

    /**
     * Processa a submissão do formulário.
     * * Valida os dados e cria uma nova entidade utilizando o EntidadeService.
     * Caso as opções de contato e/ou endereço estejam ativas, realiza
     * a criação dos respectivos relacionamentos. Dispara um evento de toast ao concluir.
     *
     * @return void
     */
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

        $entidade = $entidadeService->store($entidadeData);

        if($this->showContato === true){
            $contatoService = new ContatoService();

            $contatoData = [
                'entidade_id' => $entidade->id,
                'telefone' => $data['telefone'],
                'email' => $data['email']
            ];

            $contato = $contatoService->store($contatoData);
        }

        if($this->showEndereco === true){
            $enderecoService = new EnderecoEntidadeService();

            $enderecoData = [
                'entidade_id' => $entidade->id,
                'rua' => $data['rua'],
                'bairro' => $data['bairro'],
                'numero' => $data['numero'],
                'cep' => $data['cep'],
                'cidade' => $data['cidade'],
                'uf' => $data['uf'],
                'complemento' => $data['complemento']
            ];

            $endereco = $enderecoService->store($enderecoData);
        }
        
        $this->dispatch('toast-message', 'Entidade salva com sucesso!');
    }

    /**
     * Realiza a consulta do CNPJ informado em uma API pública externa (OpenCNPJ).
     * * Caso a requisição seja bem-sucedida, preenche automaticamente as 
     * propriedades do componente com os dados retornados e dispara um 
     * alerta de sucesso.
     *
     * @return void
     */
    public function consultaCnpj(){
        if($this->cnpjcpf != null && strlen($this->cnpjcpf) == 18){
            $response = http::get('https://api.opencnpj.org/'.$this->cnpjcpf);
            
            if($response->ok()){
                $data = $response->json();
                $this->razaoSocial = $data['razao_social'] ?? '';
                $this->nomeFantasia = $data['nome_fantasia'] ?? '';
                $this->email = $data['email'] ?? '';
                $this->telefone = isset($data['telefones'][0]) ? '('. $data['telefones'][0]['ddd'] . ') ' . $data['telefones'][0]['numero'] : ''; 
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

    /**
     * Renderiza a view do componente Livewire.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.entidades.create-entidade');
    }
}
