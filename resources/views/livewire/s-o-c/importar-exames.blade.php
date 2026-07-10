<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
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
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" 
            wire:click="fechar"
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
                class="relative transform flex flex-col rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-xl border border-gray-100 pointer-events-auto max-h-[calc(100vh-4rem)] overflow-hidden"
            >
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start flex-shrink-0">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Importar Exames
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Informe a categoria financeira e o centro de custo que serão utilizados na importação.
                        </p>
                    </div>
                    
                    <button type="button" wire:click="fechar" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="importar" class="flex flex-col overflow-hidden">
                    
                    <div class="p-6 space-y-6 overflow-y-auto max-h-[calc(100vh-14rem)]">
                        
                        <!-- Resumo dos Valores -->
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Registros Selecionados</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ count($exames) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Valor Total</p>
                                <p class="text-2xl font-bold text-green-700">
                                    R$ {{ number_format(collect($exames)->sum(function ($item) { return (float) str_replace([".", ","], ["", "."], $item['VALOR_TOTAL']); }), 2, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Formulário -->
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-5">
                            <div>
                                <label for="categoria_id" class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                    Categoria Financeira <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="categoria_id"
                                    wire:model="categoria_id"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] transition-colors @error('categoria_id') border-red-300 ring-red-300 bg-red-50 @enderror"
                                >
                                    <option value="">Selecione a categoria...</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">
                                            {{ $categoria->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="centro_custo_id" class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                    Centro de Custo <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="centro_custo_id"
                                    wire:model="centro_custo_id"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] transition-colors @error('centro_custo_id') border-red-300 ring-red-300 bg-red-50 @enderror"
                                >
                                    <option value="">Selecione o centro de custo...</option>
                                    @foreach($centrosCusto as $centro)
                                        <option value="{{ $centro->id }}">
                                            {{ $centro->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('centro_custo_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="fechar"
                            class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Cancelar
                        </button>
                        
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="importar">Importar Exames</span>
                            <span wire:loading wire:target="importar" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>