<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-editar-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarParcela', false), 200)"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform flex flex-col rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-3xl border border-gray-100 max-h-[90vh] pointer-events-auto"
            >
                <div class="shrink-0 bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start rounded-t-xl z-10">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-editar-title">
                                Editar Parcela e Título
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600 tracking-wider">
                                Título #{{ $parcela->titulo_financeiro_id }} &middot; Parcela {{ $parcela->numero_parcela }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Ajuste os dados de vencimento e classificação financeira.
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarParcela', false), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <form id="form-editar-parcela" wire:submit.prevent="salvarEdicao" class="p-6 space-y-6">
                        
                        <div class="bg-gray-100/50 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-100/80 border-b border-gray-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Informações Bloqueadas (Geração Original)</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">Entidade</label>
                                    <input type="text" disabled value="{{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'N/A' }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Tipo do Título</label>
                                    <input type="text" disabled value="{{ ucfirst($parcela->titulo->tipo) }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Valor do Título (R$)</label>
                                    <input type="text" disabled value="{{ number_format($parcela->titulo->valor_total, 2, ',', '.') }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Valor da Parcela (R$)</label>
                                    <input type="text" disabled value="{{ number_format($parcela->valor, 2, ',', '.') }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Dados da Parcela</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="editDataVencimento" class="block text-xs text-gray-700 font-medium mb-1">
                                        Data de Vencimento <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="editDataVencimento" 
                                        wire:model="editDataVencimento" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('editDataVencimento') border-red-300 @enderror"
                                    >
                                    @error('editDataVencimento') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Classificação do Título Pai</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                
                                <div class="md:col-span-2">
                                    <label for="editDescricao" class="block text-xs text-gray-700 font-medium mb-1">
                                        Descrição / Referência <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="editDescricao" 
                                        wire:model="editDescricao" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('editDescricao') border-red-300 @enderror"
                                    >
                                    @error('editDescricao') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="editDataEmissao" class="block text-xs text-gray-700 font-medium mb-1">
                                        Data de Emissão <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="editDataEmissao" 
                                        wire:model="editDataEmissao" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('editDataEmissao') border-red-300 @enderror"
                                    >
                                    @error('editDataEmissao') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="editNumeroNf" class="block text-xs text-gray-700 font-medium mb-1">Número NF</label>
                                    <input 
                                        type="text" 
                                        id="editNumeroNf" 
                                        wire:model="editNumeroNf" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all"
                                    >
                                </div>

                                <div>
                                    <label for="editCategoriaId" class="block text-xs text-gray-700 font-medium mb-1">Categoria Financeira</label>
                                    <select 
                                        id="editCategoriaId" 
                                        wire:model="editCategoriaId" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all"
                                    >
                                        <option value="">Selecione...</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="editCentroCustoId" class="block text-xs text-gray-700 font-medium mb-1">Centro de Custo</label>
                                    <select 
                                        id="editCentroCustoId" 
                                        wire:model="editCentroCustoId" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all"
                                    >
                                        <option value="">Selecione...</option>
                                        @foreach($centrosCusto as $centro)
                                            <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label for="editObservacoes" class="block text-xs text-gray-700 font-medium mb-1">Observações Gerais</label>
                                    <textarea 
                                        id="editObservacoes" 
                                        wire:model="editObservacoes" 
                                        rows="1" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all resize-none"
                                    ></textarea>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="shrink-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl z-10">
                    <button 
                        type="button" 
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarParcela', false), 200)" 
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        form="form-editar-parcela"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                    >
                        <svg wire:loading wire:target="salvarEdicao" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>