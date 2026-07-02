<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Cadastros &middot; Centros de Custo
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Edição de Centro de Custo</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Editar Centro de Custo
                </p>
            </div>
            <a
                href="{{route('erp.centro-custo.index')}}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Centros de Custo
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados do Centro de Custo</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações básicas de identificação.
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

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Administrativo, Marketing, Operacional..."
                                wire:model="nome"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                            <textarea
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50] resize-y"
                                rows="4"
                                placeholder="Descreva a finalidade deste centro de custo..."
                                wire:model="descricao"
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 h-fit">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900">Ações</h2>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-xs text-gray-500 mb-4">
                        Revise os dados antes de salvar. Certifique-se de que o nome escolhido já não esteja em uso.
                    </p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar Centro de Custo</span>
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