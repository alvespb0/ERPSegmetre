<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Configurações &middot; Empresa
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Empresa Base</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Parametrização da empresa utilizada no sistema.
                </p>
            </div>
        </div>

        @if ($empresa)
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-emerald-900">Empresa já configurada!</h2>
                <p class="text-sm text-emerald-700 mt-2 max-w-md mx-auto">
                    Os parâmetros da empresa base já foram cadastrados. Deseja alterar as configurações?
                </p>
                <a
                    href="{{ route('erp.empresa-parametro.update', $idEnc) }}"
                    class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors"
                >
                    Alterar configurações
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-semibold text-gray-900 mb-4">Dados da Empresa</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="md:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Razão Social</dt>
                                <dd class="mt-1 text-gray-900 font-medium">{{ $empresa->razao_social }}</dd>
                            </div>
                            @if ($empresa->nome_fantasia)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">Nome Fantasia</dt>
                                    <dd class="mt-1 text-gray-900">{{ $empresa->nome_fantasia }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">CNPJ</dt>
                                <dd class="mt-1 text-gray-900">
                                    @php
                                        $cnpj = preg_replace('/\D/', '', $empresa->cnpj);
                                        if (strlen($cnpj) === 14) {
                                            $cnpj = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
                                        }
                                    @endphp
                                    {{ $cnpj }}
                                </dd>
                            </div>
                            @if ($empresa->cnae_principal)
                                <div class="md:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase">CNAE Principal</dt>
                                    <dd class="mt-1 text-gray-900">{{ $empresa->cnae_principal }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-sm font-semibold text-gray-900 mb-4">Endereço</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="md:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Logradouro</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ $empresa->logradouro }}{{ $empresa->numero ? ', ' . $empresa->numero : '' }}{{ $empresa->complemento ? ' - ' . $empresa->complemento : '' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Bairro</dt>
                                <dd class="mt-1 text-gray-900">{{ $empresa->bairro }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">CEP</dt>
                                <dd class="mt-1 text-gray-900">{{ $empresa->cep }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Cidade / UF</dt>
                                <dd class="mt-1 text-gray-900">{{ $empresa->cidade }} / {{ $empresa->uf }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($empresa->telefone || $empresa->email_financeiro)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-sm font-semibold text-gray-900 mb-4">Contato</h2>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                @if ($empresa->telefone)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">Telefone</dt>
                                        <dd class="mt-1 text-gray-900">{{ $empresa->telefone }}</dd>
                                    </div>
                                @endif
                                @if ($empresa->email_financeiro)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">E-mail Financeiro</dt>
                                        <dd class="mt-1 text-gray-900">{{ $empresa->email_financeiro }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    @if ($empresa->certificadoDigital)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-sm font-semibold text-gray-900 mb-4">Certificado Digital</h2>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">Nome</dt>
                                    <dd class="mt-1 text-gray-900">{{ $empresa->certificadoDigital->nome_certificado }}</dd>
                                </div>
                                @if ($empresa->certificadoDigital->titular)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">Titular</dt>
                                        <dd class="mt-1 text-gray-900">{{ $empresa->certificadoDigital->titular }}</dd>
                                    </div>
                                @endif
                                @if ($empresa->certificadoDigital->cpf_cnpj)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">CPF/CNPJ</dt>
                                        <dd class="mt-1 text-gray-900">{{ $empresa->certificadoDigital->cpf_cnpj }}</dd>
                                    </div>
                                @endif
                                @if ($empresa->certificadoDigital->numero_serie)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">Número de Série</dt>
                                        <dd class="mt-1 text-gray-900 font-mono text-xs">{{ $empresa->certificadoDigital->numero_serie }}</dd>
                                    </div>
                                @endif
                                @if ($empresa->certificadoDigital->emitido_em)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">Emitido em</dt>
                                        <dd class="mt-1 text-gray-900">{{ $empresa->certificadoDigital->emitido_em->format('d/m/Y') }}</dd>
                                    </div>
                                @endif
                                @if ($empresa->certificadoDigital->vence_em)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase">Vence em</dt>
                                        <dd class="mt-1 {{ $empresa->certificadoDigital->vence_em->isPast() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                            {{ $empresa->certificadoDigital->vence_em->format('d/m/Y') }}
                                            @if ($empresa->certificadoDigital->vence_em->isPast())
                                                <span class="text-xs">(expirado)</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 self-start">
                    <div class="px-3 py-2 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="text-sm font-semibold text-gray-900">Logo</h2>
                    </div>
                    <div class="p-4 flex items-center justify-center min-h-[160px]">
                        @if ($empresa->logo_path)
                            <img
                                src="{{ asset('storage/' . $empresa->logo_path) }}"
                                alt="Logo da empresa"
                                class="max-h-32 max-w-full object-contain"
                            >
                        @else
                            <p class="text-xs text-gray-400 text-center">Nenhuma logo cadastrada.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
