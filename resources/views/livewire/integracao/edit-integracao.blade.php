<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Dev &middot; Integrações
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Editar Integração</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Altere a configuração de conexão e credenciais.
                </p>
            </div>
            <a
                href="{{ route('erp.dev.integracoes.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Integrações
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Dados da Integração</h2>
                        <p class="text-xs text-gray-500 mt-1">Identificação e configuração de conexão.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <h3 class="text-sm font-bold text-red-800 mb-2">Corrija os erros antes de continuar:</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 ml-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Empresa <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model.live="empresaParametroId"
                            >
                                <option value="">Selecione a empresa</option>
                                @foreach ($empresasParametro as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razao_social }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="nome"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Slug</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="slug"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Escopo <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="escopo"
                            >
                                <option value="sistema">Sistema</option>
                                <option value="banco">Banco</option>
                                <option value="fiscal">Fiscal</option>
                                <option value="externo">Externo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tecnologia <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="tecnologia"
                            >
                                <option value="rest">REST</option>
                                <option value="soap">SOAP</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Autenticação <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model.live="autenticacao"
                            >
                                <option value="none">Nenhuma</option>
                                <option value="basic">Basic</option>
                                <option value="bearer">Bearer</option>
                                <option value="oauth2">OAuth2</option>
                                <option value="mtls">mTLS</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Endpoint <span class="text-red-500">*</span></label>
                            <input
                                type="url"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="endpoint"
                            >
                        </div>

                        @if ($autenticacao === 'outro')
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Autenticação específica</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    wire:model="autenticacaoEspecifica"
                                >
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                            <textarea
                                rows="3"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="descricao"
                            ></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]"
                                    wire:model="nativa"
                                >
                                <span class="text-sm text-gray-700">Integração nativa do sistema</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Credenciais</h2>
                        <p class="text-xs text-gray-500 mt-1">Campos exibidos conforme o tipo de autenticação selecionado.</p>
                    </div>

                    @include('livewire.integracao.partials.campos-credenciais', [
                        'modo' => 'edit',
                        'possuiSenha' => $possuiSenha,
                        'possuiClientSecret' => $possuiClientSecret,
                    ])
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
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar alterações</span>
                        <span wire:loading wire:target="submit">Salvando...</span>
                    </button>
                    <a
                        href="{{ route('erp.dev.integracoes.index') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
