<x-layouts.erp>
    <div class="mx-auto max-w-xl">
        <div class="mb-6">
            <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                CADASTROS &middot; USUÁRIOS
            </p>
            <h1 class="text-2xl font-semibold text-gray-900">Novo Usuário</h1>
            <p class="text-sm text-gray-500 mt-1">Cadastre um novo usuário com acesso ao ERP.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <form method="POST" action="{{ route('erp.usuarios.store') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="email" :value="__('E-mail')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="tipo" :value="__('Tipo de usuário')" />
                    <select id="tipo" name="tipo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#313e50] focus:ring-[#313e50]">
                        <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>Selecione...</option>
                        @foreach (['dev' => 'Desenvolvedor', 'admin' => 'Administrador', 'visualizador' => 'Visualizador', 'pagador' => 'Pagador', 'cobranca' => 'Cobrança'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('erp.usuarios.index') }}" class="text-sm text-gray-500 hover:text-[#313e50] hover:underline">
                        Voltar para listagem
                    </a>
                    <x-primary-button>Cadastrar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>
