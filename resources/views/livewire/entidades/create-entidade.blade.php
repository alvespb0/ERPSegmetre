<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Cadastros &middot; Entidades
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Nova Entidade</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Registre clientes, fornecedores ou qualquer entidade com vínculo financeiro com a clínica.
                </p>
            </div>
            <a
                href="{{ route('erp.entidades.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                Listar Entidades
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados Principais</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações básicas de identificação da entidade.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Razão Social / Nome Completo</label>
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

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Classificação</label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="classificacao"
                            >
                                <option value="">Selecione...</option>
                                <option value="cliente">Cliente</option>
                                <option value="fornecedor">Fornecedor</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="tipo"
                            >
                                <option value="">Selecione...</option>
                                <option value="pf">Pessoa Física (PF)</option>
                                <option value="pj">Pessoa Jurídica (PJ)</option>
                            </select>
                        </div>

                        <div x-data>
                            <label class="block text-xs font-medium text-gray-700 mb-1">CNPJ / CPF</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-on:input="
                                        let v = $event.target.value.replace(/\D/g, '');
                                        if (v.length <= 11) {
                                            // CPF: 000.000.000-00
                                            v = v.slice(0, 11);
                                            if (v.length > 9) {
                                                v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
                                            } else if (v.length > 6) {
                                                v = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
                                            } else if (v.length > 3) {
                                                v = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
                                            }
                                        } else {
                                            // CNPJ: 00.000.000/0000-00
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
                                        }
                                        $event.target.value = v;
                                    "
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="00.000.000/0000-00 ou 000.000.000-00"
                                    wire:model="cnpjcpf"
                                >
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Buscar por CNPJ/CPF"
                                    wire:click="consultaCnpj"
                                    :disabled="$wire.cnpjcpf.length !== 18"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Contato</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Dados conforme model de contato (telefone e e-mail).
                            </p>
                        </div>

                        @if(!$showContato)
                            <button
                                type="button"
                                wire:click="$set('showContato', true)"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                                Adicionar contato
                            </button>
                        @endif
                    </div>

                    @if($showContato)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">E-mail</label>
                                <input
                                    type="email"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="contato@empresa.com.br"
                                    wire:model="email"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Telefone</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="(00) 0000-0000"
                                    wire:model="telefone"
                                >
                            </div>

                            <div class="md:col-span-2 flex justify-end">
                                <button
                                    type="button"
                                    wire:click="$set('showContato', false)"
                                    class="text-xs font-medium text-gray-500 hover:text-gray-700"
                                >
                                    Remover contato
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Endereço</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Campos conforme model de endereço da entidade.
                            </p>
                        </div>

                        @if(!$showEndereco)
                            <button
                                type="button"
                                wire:click="$set('showEndereco', true)"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                                Adicionar endereço
                            </button>
                        @endif
                    </div>

                    @if($showEndereco)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rua</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: Av. Paulista"
                                    wire:model="rua"
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
                                <label class="block text-xs font-medium text-gray-700 mb-1">Bairro</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: Bela Vista"
                                    wire:model="bairro"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">CEP</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="00000-000"
                                    wire:model="cep"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Cidade</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: São Paulo"
                                    wire:model="cidade"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">UF</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: SP"
                                    wire:model="uf"
                                >
                            </div>

                            <div class="md:col-span-2 flex justify-end">
                                <button
                                    type="button"
                                    wire:click="$set('showEndereco', false)"
                                    class="text-xs font-medium text-gray-500 hover:text-gray-700"
                                >
                                    Remover endereço
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

                    <div class="sm:flex-row gap-2 justify-center pt-1">
                        <button
                            type="reset"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                            Limpar
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
                        >
                            <span class="w-4 h-4 rounded-full bg-white/30"></span>
                            Salvar Entidade
                        </button>
                    </div>
            </div>
        </form>
    </div>
</div>
