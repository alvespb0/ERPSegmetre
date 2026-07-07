<x-layouts.erp>
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">CONTA</p>
            <h1 class="text-2xl font-semibold text-gray-900">Meu perfil</h1>
            <p class="text-sm text-gray-500 mt-1">Informações da sua conta de acesso.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#2C394B] text-white text-lg font-semibold">
                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                </span>
                <div>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <dl class="divide-y divide-gray-100">
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <dt class="text-sm font-medium text-gray-500">Tipo de usuário</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">{{ $user->tipoLabel() }}</dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <dt class="text-sm font-medium text-gray-500">Autenticação 2FA</dt>
                    <dd class="text-sm sm:col-span-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border {{ $user->two_factor_enabled ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                            {{ $user->two_factor_enabled ? 'Ativo' : 'Inativo' }}
                        </span>
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <dt class="text-sm font-medium text-gray-500">Último acesso</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">
                        @if ($user->last_login_at)
                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                            @if ($user->last_login_ip)
                                <span class="text-gray-400">· {{ $user->last_login_ip }}</span>
                            @endif
                        @else
                            <span class="text-gray-400">Nenhum registro</span>
                        @endif
                    </dd>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <dt class="text-sm font-medium text-gray-500">Conta criada em</dt>
                    <dd class="text-sm text-gray-900 sm:col-span-2">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Alterar senha</h2>
                <p class="text-sm text-gray-500 mt-1">Use uma senha longa e aleatória para manter sua conta segura.</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="px-6 py-5 space-y-5">
                @csrf
                @method('put')

                <div class="space-y-1.5">
                    <x-input-label for="current_password" value="Senha atual" />
                    <x-text-input id="current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" required />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password" value="Nova senha" />
                    <x-text-input id="password" name="password" type="password" class="block w-full" autocomplete="new-password" required />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password_confirmation" value="Confirmar nova senha" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" required />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button>Salvar nova senha</x-primary-button>

                    @if (session('status') === 'password-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 3000)"
                            class="text-sm text-emerald-600"
                        >Senha atualizada com sucesso.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>
