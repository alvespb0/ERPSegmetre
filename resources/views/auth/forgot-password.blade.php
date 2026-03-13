<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-1">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('Esqueceu sua senha?') }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ __('Informe o e-mail cadastrado e enviaremos um link seguro para você definir uma nova senha de acesso ao ERP da clínica.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-2" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('login') }}"
                    class="text-xs font-medium text-gray-500 hover:text-[#313e50] hover:underline"
                >
                    {{ __('Voltar para o login') }}
                </a>

                <x-primary-button>
                    {{ __('Enviar link de redefinição') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

