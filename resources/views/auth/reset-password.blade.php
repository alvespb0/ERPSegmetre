<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-1">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('Definir nova senha') }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ __('Crie uma nova senha forte para continuar acessando o painel financeiro da clínica com segurança.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    autofocus
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <x-input-label for="password" :value="__('Password')" />
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

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

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

            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('login') }}"
                    class="text-xs font-medium text-gray-500 hover:text-[#313e50] hover:underline"
                >
                    {{ __('Voltar para o login') }}
                </a>

            <x-primary-button>
                {{ __('Salvar nova senha') }}
            </x-primary-button>
        </div>
        </form>
    </div>
</x-guest-layout>

