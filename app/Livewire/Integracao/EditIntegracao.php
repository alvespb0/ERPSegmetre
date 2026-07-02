<?php

namespace App\Livewire\Integracao;

use App\Models\CertificadoDigital;
use App\Models\EmpresaParametro;
use App\Models\Integracao;
use App\Services\IntegracaoService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditIntegracao extends Component
{
    public $id;
    public $integracao;

    public $empresaParametroId;
    public $nome;
    public $slug;
    public $provider;
    public $descricao;
    public $escopo;
    public $tecnologia;
    public $autenticacao;
    public $autenticacaoEspecifica;
    public $endpoint;
    public $nativa = false;

    public $username;
    public $password;
    public $clientId;
    public $clientSecret;
    public $accessToken;
    public $refreshToken;
    public $tokenExpiresAt;
    public $certificadoDigitalId;

    public $possuiSenha;
    public $possuiClientSecret;

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->integracao = Integracao::with(['credenciais.certificadoDigital', 'empresaParametro'])
            ->withTrashed()
            ->findOrFail($id);

        $credenciais = $this->integracao->credenciais;

        $this->empresaParametroId = $this->integracao->empresa_parametro_id;
        $this->nome = $this->integracao->nome;
        $this->slug = $this->integracao->slug;
        $this->provider = $this->integracao->provider;
        $this->descricao = $this->integracao->descricao;
        $this->escopo = $this->integracao->escopo;
        $this->tecnologia = $this->integracao->tecnologia;
        $this->autenticacao = $this->integracao->autenticacao;
        $this->autenticacaoEspecifica = $this->integracao->autenticacao_especifica;
        $this->endpoint = $this->integracao->endpoint;
        $this->nativa = $this->integracao->nativa;

        $this->username = $credenciais?->username;
        $this->clientId = $credenciais?->client_id;
        $this->accessToken = $credenciais?->access_token;
        $this->refreshToken = $credenciais?->refresh_token;
        $this->tokenExpiresAt = $credenciais?->token_expires_at?->format('Y-m-d\TH:i');
        $this->certificadoDigitalId = $credenciais?->certificado_digital_id;
        $this->possuiSenha = ! empty($credenciais?->password_enc);
        $this->possuiClientSecret = ! empty($credenciais?->client_secret_enc);
    }

    public function rules(): array
    {
        return array_merge($this->regrasIntegracao(), $this->regrasCredenciais());
    }

    public function messages(): array
    {
        return [
            'empresaParametroId.required' => 'A empresa vinculada é obrigatória.',
            'empresaParametroId.exists' => 'A empresa selecionada é inválida.',
            'nome.required' => 'O nome da integração é obrigatório.',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres.',
            'slug.max' => 'O slug não pode ter mais que 255 caracteres.',
            'provider.max' => 'O provider não pode ter mais que 255 caracteres.',
            'provider.class' => 'A classe informada no provider não existe.',
            'escopo.required' => 'O escopo é obrigatório.',
            'escopo.in' => 'O escopo selecionado é inválido.',
            'tecnologia.required' => 'A tecnologia é obrigatória.',
            'tecnologia.in' => 'A tecnologia selecionada é inválida.',
            'autenticacao.required' => 'O tipo de autenticação é obrigatório.',
            'autenticacao.in' => 'O tipo de autenticação selecionado é inválido.',
            'endpoint.required' => 'O endpoint é obrigatório.',
            'endpoint.url' => 'Informe uma URL válida para o endpoint.',
            'endpoint.max' => 'O endpoint não pode ter mais que 255 caracteres.',
            'username.required' => 'O usuário é obrigatório para este tipo de autenticação.',
            'password.required' => 'A senha é obrigatória para este tipo de autenticação.',
            'clientId.required' => 'O Client ID é obrigatório para OAuth2.',
            'clientSecret.required' => 'O Client Secret é obrigatório para OAuth2.',
            'accessToken.required' => 'O token de acesso é obrigatório para autenticação Bearer.',
            'certificadoDigitalId.required' => 'O certificado digital é obrigatório para mTLS.',
            'certificadoDigitalId.exists' => 'O certificado selecionado é inválido.',
            'tokenExpiresAt.date' => 'A data de expiração do token é inválida.',
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        $integracaoData = [
            'empresa_parametro_id' => $data['empresaParametroId'],
            'nome' => $data['nome'],
            'slug' => $data['slug'] ?? null,
            'provider' => $data['provider'] ?? null,
            'descricao' => $data['descricao'] ?? null,
            'escopo' => $data['escopo'],
            'tecnologia' => $data['tecnologia'],
            'autenticacao' => $data['autenticacao'],
            'autenticacao_especifica' => $data['autenticacaoEspecifica'] ?? null,
            'endpoint' => $data['endpoint'],
            'nativa' => (bool) $this->nativa,
        ];

        (new IntegracaoService())->update($integracaoData, $this->id, $this->montarCredenciais($data));

        $this->dispatch('toast-message', 'Integração atualizada com sucesso');
        $this->redirect(route('erp.dev.integracoes.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.integracao.edit-integracao', [
            'empresasParametro' => EmpresaParametro::orderBy('razao_social')->get(),
            'certificadosDigitais' => $this->certificadosDisponiveis(),
        ]);
    }

    private function regrasIntegracao(): array
    {
        return [
            'empresaParametroId' => 'required|exists:empresa_parametro,id',
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'provider' => ['nullable', 'string', 'max:255', $this->regraProviderExistente()],
            'descricao' => 'nullable|string',
            'escopo' => 'required|in:sistema,banco,fiscal,externo',
            'tecnologia' => 'required|in:rest,soap',
            'autenticacao' => 'required|in:none,basic,bearer,oauth2,mtls,outro',
            'autenticacaoEspecifica' => 'nullable|string|max:255',
            'endpoint' => 'required|url|max:255',
            'nativa' => 'boolean',
        ];
    }

    private function regrasCredenciais(): array
    {
        return [
            'username' => Rule::requiredIf(fn () => in_array($this->autenticacao, ['basic', 'outro'])),
            'password' => Rule::requiredIf(fn () => in_array($this->autenticacao, ['basic', 'outro']) && ! $this->possuiSenha),
            'clientId' => Rule::requiredIf(fn () => $this->autenticacao === 'oauth2'),
            'clientSecret' => Rule::requiredIf(fn () => $this->autenticacao === 'oauth2' && ! $this->possuiClientSecret),
            'accessToken' => Rule::requiredIf(fn () => $this->autenticacao === 'bearer'),
            'refreshToken' => 'nullable|string',
            'tokenExpiresAt' => 'nullable|date',
            'certificadoDigitalId' => [
                Rule::requiredIf(fn () => $this->autenticacao === 'mtls'),
                'nullable',
                'exists:certificados_digitais,id',
            ],
        ];
    }

    private function montarCredenciais(array $data): ?array
    {
        if ($this->autenticacao === 'none') {
            return null;
        }

        return [
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'client_id' => $data['clientId'] ?? null,
            'client_secret' => $data['clientSecret'] ?? null,
            'access_token' => $data['accessToken'] ?? null,
            'refresh_token' => $data['refreshToken'] ?? null,
            'token_expires_at' => $data['tokenExpiresAt'] ?? null,
            'certificado_digital_id' => $data['certificadoDigitalId'] ?? null,
        ];
    }

    private function certificadosDisponiveis()
    {
        if (! $this->empresaParametroId) {
            return collect();
        }

        return CertificadoDigital::where('empresa_parametro_id', $this->empresaParametroId)
            ->orderBy('nome_certificado')
            ->get();
    }

    private function regraProviderExistente(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value && ! class_exists($value)) {
                $fail('A classe informada no provider não existe.');
            }
        };
    }
}
