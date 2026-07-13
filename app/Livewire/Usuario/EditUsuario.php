<?php

namespace App\Livewire\Usuario;

use App\Models\User;
use App\Models\EmpresaParametro;
use App\Services\UserService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class EditUsuario extends Component
{
    public User $usuario;

    public int $id;

    public string $name = '';

    public string $email = '';

    public string $tipo = '';

    public string $password = '';

    public string $password_confirmation = '';

    public $empresasParametro;

    // Novo atributo para guardar os IDs selecionados
    public array $empresa_parametro_ids = []; 

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->id),
            ],
            'tipo' => ['required', Rule::in(['dev', 'admin', 'visualizador', 'pagador', 'cobranca'])],
            
            'empresa_parametro_ids' => [
                Rule::requiredIf(fn () => auth()->user()->isDev() && $this->tipo !== 'dev'),
                'array'
            ],
            'empresa_parametro_ids.*' => ['integer'],

            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'tipo.required' => 'Selecione o tipo de usuário.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            
            // Nova mensagem de erro
            'empresa_parametro_ids.required_if' => 'Selecione pelo menos uma empresa para este perfil de usuário.',
        ];
    }

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->usuario = User::withTrashed()->findOrFail($id);
        $this->name = $this->usuario->name;
        $this->email = $this->usuario->email;
        $this->tipo = $this->usuario->tipo;
        
        $this->empresasParametro = EmpresaParametro::query()
            ->orderBy('nome_fantasia')
            ->orderBy('razao_social')
            ->get(['id', 'nome_fantasia', 'razao_social']);

        $this->empresa_parametro_ids = $this->usuario->empresas->pluck('id')->toArray();
    }

    public function submit(): void
    {
        $data = $this->validate();

        $service = new UserService();

        $service->update($data, $this->id);

        $this->reset('password', 'password_confirmation');

        $this->dispatch('toast-message', 'Usuário atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.usuario.edit-usuario');
    }
}