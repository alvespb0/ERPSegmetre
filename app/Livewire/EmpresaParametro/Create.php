<?php

namespace App\Livewire\EmpresaParametro;

use App\Models\EmpresaParametro;
use App\Services\EmpresaParametroService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $razaoSocial;
    public $nomeFantasia;
    public $cnpj;
    public $inscricaoEstadual;
    public $inscricaoMunicipal;
    public $cnaePrincipal;
    public $cep;
    public $logradouro;
    public $bairro;
    public $numero;
    public $complemento;
    public $cidade;
    public $uf;
    public $telefone;
    public $emailFinanceiro;
    public $logo;

    public function messages(): array
    {
        return [
            'razaoSocial.required' => 'O campo Razão Social é obrigatório.',
            'razaoSocial.max' => 'A Razão Social não pode ter mais que 255 caracteres.',
            'nomeFantasia.max' => 'O Nome Fantasia não pode ter mais que 255 caracteres.',
            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.max' => 'O CNPJ não pode ter mais que 18 caracteres.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.max' => 'O CEP não pode ter mais que 10 caracteres.',
            'logradouro.required' => 'O campo Logradouro é obrigatório.',
            'logradouro.max' => 'O Logradouro não pode ter mais que 255 caracteres.',
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'bairro.max' => 'O Bairro não pode ter mais que 255 caracteres.',
            'cidade.required' => 'O campo Cidade é obrigatório.',
            'cidade.max' => 'A Cidade não pode ter mais que 255 caracteres.',
            'uf.required' => 'O campo UF é obrigatório.',
            'uf.size' => 'A UF deve conter exatamente 2 letras (ex: SP).',
            'telefone.max' => 'O telefone não pode ter mais que 20 caracteres.',
            'emailFinanceiro.email' => 'Informe um e-mail válido.',
            'emailFinanceiro.max' => 'O e-mail não pode ter mais que 255 caracteres.',
            'logo.image' => 'O arquivo deve ser uma imagem.',
            'logo.max' => 'A logo não pode ter mais que 2MB.',
        ];
    }

    public function rules(): array
    {
        return [
            'razaoSocial' => 'required|string|max:255',
            'nomeFantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18',
            'inscricaoEstadual' => 'nullable|string|max:255',
            'inscricaoMunicipal' => 'nullable|string|max:255',
            'cnaePrincipal' => 'nullable|string|max:255',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'cidade' => 'required|string|max:255',
            'uf' => 'required|string|size:2',
            'telefone' => 'nullable|string|max:20',
            'emailFinanceiro' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        $cnpjCru = preg_replace('/\D/', '', $data['cnpj']);
        $telefoneCru = ! empty($data['telefone'])
            ? preg_replace('/\D/', '', $data['telefone'])
            : null;

        if (EmpresaParametro::where('cnpj', $cnpjCru)->exists()) {
            $this->addError('cnpj', 'Este CNPJ já está cadastrado.');

            return;
        }

        $empresaData = [
            'razao_social' => $data['razaoSocial'],
            'nome_fantasia' => $data['nomeFantasia'],
            'cnpj' => $cnpjCru,
            'inscricao_estadual' => $data['inscricaoEstadual'],
            'inscricao_municipal' => $data['inscricaoMunicipal'],
            'cnae_principal' => $data['cnaePrincipal'],
            'cep' => $data['cep'],
            'logradouro' => $data['logradouro'],
            'bairro' => $data['bairro'],
            'numero' => $data['numero'],
            'complemento' => $data['complemento'],
            'cidade' => $data['cidade'],
            'uf' => strtoupper($data['uf']),
            'telefone' => $telefoneCru,
            'email_financeiro' => $data['emailFinanceiro'],
            'logo_path' => null,
        ];

        if ($this->logo) {
            $fileName = Str::uuid() . '.' . $this->logo->getClientOriginalExtension();
            $empresaData['logo_path'] = $this->logo->storeAs('empresa/logos', $fileName, 'public');
        }

        (new EmpresaParametroService())->store($empresaData);

        $this->reset();
        $this->dispatch('toast-message', 'Parametrização da empresa salva com sucesso!');
    }

    public function consultaCnpj(): void
    {
        if ($this->cnpj != null && strlen($this->cnpj) == 18) {
            $response = Http::get('https://api.opencnpj.org/'.$this->cnpj);

            if ($response->ok()) {
                $data = $response->json();
                $this->razaoSocial = $data['razao_social'] ?? '';
                $this->nomeFantasia = $data['nome_fantasia'] ?? '';
                $this->emailFinanceiro = $data['email'] ?? '';
                $this->telefone = isset($data['telefones'][0]) ? '('. $data['telefones'][0]['ddd'] . ') ' . $data['telefones'][0]['numero'] : '';
                $this->logradouro = $data['logradouro'] ?? '';
                $this->numero = $data['numero'] ?? 'n/a';
                $this->complemento = $data['complemento'] ?? 'n/a';
                $this->bairro = $data['bairro'] ?? '';
                $this->cep = $data['cep'] ?? '';
                $this->cidade = $data['municipio'] ?? '';
                $this->uf = $data['uf'] ?? '';
                $this->cnaePrincipal = $data['cnae_principal'] ?? '';

                $this->dispatch('toast-message', 'CNPJ resgatado com sucesso');
            } else {
                $this->dispatch('toast-error', 'Erro ao resgatar CNPJ');
            }
        }
    }

    public function render()
    {
        return view('livewire.empresa-parametro.create');
    }
}
