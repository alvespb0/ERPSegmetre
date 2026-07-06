<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-1">
            <h2 class="text-xl font-semibold text-gray-900">Configurar autenticação em dois fatores</h2>
            <p class="text-sm text-gray-500">
                Escaneie o QR Code no Google Authenticator e confirme com o código de 6 dígitos.
            </p>
        </div>

        <div class="flex justify-center rounded-lg border border-gray-100 bg-white p-4">
            {!! $qrCodeSvg !!}
        </div>

        <p class="text-xs text-center text-gray-400 break-all">
            Chave manual: <span class="font-mono text-gray-600">{{ $secret }}</span>
        </p>

        <form method="POST" action="{{ route('two-factor.setup.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <x-input-label for="code" value="Código do autenticador" />
                <x-text-input
                    id="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="6"
                    class="block w-full text-center text-lg tracking-widest"
                    required
                    autofocus
                    autocomplete="one-time-code"
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                Confirmar e continuar
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700 hover:underline">
                Sair
            </button>
        </form>
    </div>
</x-guest-layout>
