<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-pagamento-title" 
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
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagarParcela', false), 200)"
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
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-xl border border-gray-100 pointer-events-auto"
            >
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-pagamento-title">
                                Informar Pagamento
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Parcela {{ $parcela->numero_parcela }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Ref: Título #{{ $parcela->titulo_financeiro_id }}
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagarParcela', false), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="salvarPagamento">
                    <div class="p-6 space-y-6">
                        
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                            <p class="text-xs text-gray-500 uppercase font-medium">Saldo Devedor</p>
                            <p class="text-xl font-semibold text-gray-900">
                                R$ {{ number_format($parcela->saldo_devedor, 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Detalhes da Baixa</h4>
                            </div>
                            <div class="p-4 space-y-4">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="pagamentoData" class="block text-xs text-gray-500 mb-1">
                                            Data do Pagamento <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="pagamentoData" 
                                            wire:model="pagamentoData" 
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoData') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                        >
                                        @error('pagamentoData') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="pagamentoValor" class="block text-xs text-gray-500 mb-1">
                                            Valor Pago (R$) <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0.01"
                                            id="pagamentoValor" 
                                            wire:model="pagamentoValor" 
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoValor') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                        >
                                        @error('pagamentoValor') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="pagamentoFormaId" class="block text-xs text-gray-500 mb-1">
                                        Forma de Pagamento <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="pagamentoFormaId" 
                                        wire:model="pagamentoFormaId" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoFormaId') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                    >
                                        <option value="">Selecione uma forma...</option>
                                        @foreach($formasPagamento as $forma)
                                            <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('pagamentoFormaId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                        <button 
                            type="button" 
                            @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagarParcela', false), 200)" 
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                        >
                            <svg wire:loading wire:target="salvarPagamento" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Confirmar Pagamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>