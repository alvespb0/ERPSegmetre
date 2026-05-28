<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    CADASTROS &middot; BANCO
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Edição de Banco</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Edite o banco.
                </p>
            </div>
            <a
                href="{{ route('erp.banco.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Bancos
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados da Instituição</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações básicas para identificação do banco.
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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome do Banco <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Banco do Brasil, Itaú..."
                                wire:model="nome"
                            >
                        </div>

                        <div class="md:col-span-2" x-data>
                            <label class="block text-xs font-medium text-gray-700 mb-1">CNPJ / CPF <span class="text-red-500">*</span></label>
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
                                wire:model="cnpj"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Número do Banco</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: 356"
                                wire:model="numero_banco"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 self-start">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900">Ações</h2>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-xs text-gray-500 mb-4">
                        Revise os dados antes de salvar. Certifique-se de que o documento (CNPJ/CPF) está correto, pois ele será validado pelo sistema.
                    </p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar Banco</span>
                        <span wire:loading wire:target="submit">Salvando...</span>
                    </button>

                    <button
                        type="reset"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Limpar formulário
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>