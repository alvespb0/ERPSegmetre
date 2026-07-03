<x-layouts.erp>
    <div class="mx-auto max-w-xl">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 space-y-1">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Cadastrar usuário') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ __('Crie uma nova conta de acesso ao ERP.') }}
                </p>
            </div>

            @if (session('status') === 'user-created')
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ __('Usuário criado com sucesso.') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input
                        id="name"
                        class="block mt-1 w-full"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="tipo" :value="__('Tipo de usuário')" />
                    <select
                        id="tipo"
                        name="tipo"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#313e50] focus:ring-[#313e50]"
                    >
                        <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>{{ __('Selecione...') }}</option>
                        @foreach (['dev' => 'Desenvolvedor', 'admin' => 'Administrador', 'visualizador' => 'Visualizador', 'pagador' => 'Pagador', 'cobranca' => 'Cobrança'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input
                        id="password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                    <x-text-input
                        id="password_confirmation"
                        class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        {{ __('Cadastrar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>
