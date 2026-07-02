@php
    $escopos = [
        'sistema' => 'Sistema',
        'banco' => 'Banco',
        'fiscal' => 'Fiscal',
        'externo' => 'Externo',
    ];
    $autenticacoes = [
        'none' => 'Nenhuma',
        'basic' => 'Basic',
        'bearer' => 'Bearer',
        'oauth2' => 'OAuth2',
        'mtls' => 'mTLS',
        'outro' => 'Outro',
    ];
@endphp

@if ($autenticacao === 'none')
    <p class="text-xs text-gray-500">Nenhuma credencial necessária para este tipo de autenticação.</p>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if (in_array($autenticacao, ['basic', 'outro']))
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Usuário</label>
                <input
                    type="text"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="username"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Senha</label>
                <input
                    type="password"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="password"
                    autocomplete="new-password"
                    placeholder="{{ ($modo ?? 'create') === 'edit' && ($possuiSenha ?? false) ? 'Deixe em branco para manter a senha atual' : '' }}"
                >
                @if (($modo ?? 'create') === 'edit' && ($possuiSenha ?? false))
                    <p class="mt-1 text-xs text-gray-400">Senha já cadastrada. Informe apenas se for alterar.</p>
                @endif
            </div>
        @endif

        @if ($autenticacao === 'bearer')
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Access Token</label>
                <textarea
                    rows="3"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="accessToken"
                ></textarea>
            </div>
        @endif

        @if ($autenticacao === 'oauth2')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Client ID</label>
                <input
                    type="text"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="clientId"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Client Secret</label>
                <input
                    type="password"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="clientSecret"
                    autocomplete="new-password"
                    placeholder="{{ ($modo ?? 'create') === 'edit' && ($possuiClientSecret ?? false) ? 'Deixe em branco para manter o secret atual' : '' }}"
                >
                @if (($modo ?? 'create') === 'edit' && ($possuiClientSecret ?? false))
                    <p class="mt-1 text-xs text-gray-400">Client secret já cadastrado. Informe apenas se for alterar.</p>
                @endif
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Access Token</label>
                <textarea
                    rows="2"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="accessToken"
                ></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Refresh Token</label>
                <textarea
                    rows="2"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="refreshToken"
                ></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Token expira em</label>
                <input
                    type="datetime-local"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="tokenExpiresAt"
                >
            </div>
        @endif

        @if ($autenticacao === 'mtls')
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Certificado Digital</label>
                <select
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    wire:model="certificadoDigitalId"
                >
                    <option value="">Selecione um certificado</option>
                    @forelse ($certificadosDigitais as $certificado)
                        <option value="{{ $certificado->id }}">
                            {{ $certificado->nome_certificado }}
                            @if ($certificado->vence_em)
                                (vence {{ $certificado->vence_em->format('d/m/Y') }})
                            @endif
                        </option>
                    @empty
                        <option value="" disabled>Nenhum certificado cadastrado para esta empresa</option>
                    @endforelse
                </select>
            </div>
        @endif
    </div>
@endif
