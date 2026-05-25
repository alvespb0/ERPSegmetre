<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Configurações &middot; Empresa
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Edição da Parametrização</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Altere os dados da empresa base utilizada no sistema.
                </p>
            </div>
            <a
                href="{{ route('erp.dev.empresa-parametro.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Voltar
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados da Empresa</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Identificação fiscal e cadastral da empresa.
                            </p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-bold text-red-800">
                                    Por favor, corrija os seguintes erros antes de continuar:
                                </h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 ml-7 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Razão Social <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Clínica Saúde Total LTDA"
                                wire:model="razaoSocial"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome Fantasia</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Saúde Total"
                                wire:model="nomeFantasia"
                            >
                        </div>

                        <div x-data>
                            <label class="block text-xs font-medium text-gray-700 mb-1">CNPJ <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-on:input="
                                        let v = $event.target.value.replace(/\D/g, '');
                                        v = v.slice(0, 14);
                                        if (v.length > 12) {
                                            v = v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
                                        } else if (v.length > 8) {
                                            v = v.replace(/(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
                                        } else if (v.length > 5) {
                                            v = v.replace(/(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
                                        } else if (v.length > 2) {
                                            v = v.replace(/(\d{2})(\d{0,3})/, '$1.$2');
                                        }
                                        $event.target.value = v;
                                    "
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="00.000.000/0000-00"
                                    wire:model="cnpj"
                                >
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Buscar por CNPJ"
                                    wire:click="consultaCnpj"
                                    wire:loading.attr="disabled"
                                    wire:target="consultaCnpj"
                                    :disabled="$wire.cnpj.length !== 18"
                                >
                                    <svg wire:loading.remove wire:target="consultaCnpj" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                    </svg>
                                    <svg wire:loading wire:target="consultaCnpj" class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Inscrição Estadual</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="inscricaoEstadual"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Inscrição Municipal</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="inscricaoMunicipal"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">CNAE Principal</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="cnaePrincipal"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Logo da Empresa</label>
                            @if ($logoPathAtual)
                                <div class="mb-3 p-3 border border-gray-100 rounded-lg bg-gray-50 inline-block">
                                    <img
                                        src="{{ asset('storage/' . $logoPathAtual) }}"
                                        alt="Logo atual"
                                        class="max-h-20 object-contain"
                                    >
                                    <p class="text-[10px] text-gray-400 mt-1">Logo atual</p>
                                </div>
                            @endif
                            <input
                                type="file"
                                accept="image/*"
                                wire:model="logo"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-md bg-white cursor-pointer focus:ring-[#313e50] focus:border-[#313e50]"
                            >
                            <p class="mt-1 text-xs text-gray-400">Deixe em branco para manter a logo atual.</p>
                            @error('logo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <div wire:loading wire:target="logo" class="mt-1 text-xs text-gray-500">Enviando arquivo...</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Contato</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Telefone e e-mail financeiro da empresa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div x-data>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Telefone</label>
                            <input
                                type="text"
                                x-on:input="
                                    let v = $event.target.value.replace(/\D/g, '');
                                    v = v.slice(0, 11);
                                    if (v.length > 10) {
                                        v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                                    } else if (v.length > 6) {
                                        v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                                    } else if (v.length > 2) {
                                        v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                                    } else if (v.length > 0) {
                                        v = v.replace(/(\d{0,2})/, '($1');
                                    }
                                    $event.target.value = v;
                                "
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="(00) 00000-0000"
                                wire:model="telefone"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">E-mail Financeiro</label>
                            <input
                                type="email"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="financeiro@empresa.com.br"
                                wire:model="emailFinanceiro"
                            >
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Endereço</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Endereço fiscal da empresa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Logradouro <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Av. Paulista"
                                wire:model="logradouro"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Número</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: 1000"
                                wire:model="numero"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Complemento</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Sala 1203"
                                wire:model="complemento"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bairro <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="bairro"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">CEP <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="00000-000"
                                wire:model="cep"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cidade <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="cidade"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">UF <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: SP"
                                wire:model="uf"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 self-start">
                <div class="px-3 py-2 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900">Ações</h2>
                </div>

                <div class="p-3 space-y-2">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit,logo"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit,logo">Salvar alterações</span>
                        <span wire:loading wire:target="submit,logo">Salvando...</span>
                    </button>

                    <a
                        href="{{ route('erp.dev.empresa-parametro.index') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
