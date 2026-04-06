<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-status-title" 
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
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarStatus', false), 200)"
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
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-status-title">
                                Alterar Status Administrativo
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600 tracking-wider">
                                Título #{{ $parcela->titulo_financeiro_id }} &middot; Parcela {{ $parcela->numero_parcela }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Modifique a situação administrativa desta parcela ou do título completo.
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarStatus', false), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <form id="form-alterar-status" wire:submit.prevent="salvarStatus" class="p-6 space-y-6">
                        
                        <div class="bg-gray-100/50 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-100/80 border-b border-gray-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Resumo da Obrigação</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">Entidade</label>
                                    <input type="text" disabled value="{{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'N/A' }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Status Atual</label>
                                    <input type="text" disabled value="{{ ucfirst($parcela->status ?? 'Pendente') }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Vencimento</label>
                                    <input type="text" disabled value="{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Valor da Parcela (R$)</label>
                                    <input type="text" disabled value="{{ number_format($parcela->valor, 2, ',', '.') }}" class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Definições de Status</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <div>
                                    <label for="novoStatus" class="block text-xs text-gray-700 font-medium mb-1">
                                        Novo Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="novoStatus" 
                                        wire:model.live="novoStatus" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('novoStatus') border-red-300 @enderror"
                                    >
                                        <option value="">Selecione o status...</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="cancelado">Cancelado</option>
                                        <option value="renegociado">Renegociado</option>
                                        <option value="suspenso">Suspenso</option>
                                        </select>
                                    @error('novoStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="escopoStatus" class="block text-xs text-gray-700 font-medium mb-1">
                                        Aplicar status em: <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="escopoStatus" 
                                        wire:model.live="escopoStatus" 
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('escopoStatus') border-red-300 @enderror"
                                    >
                                        <option value="parcela">Apenas nesta Parcela ({{ $parcela->numero_parcela }})</option>
                                        <option value="titulo">No Título Completo (Todas as parcelas)</option>
                                    </select>
                                    @error('escopoStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($escopoStatus === 'parcela' && $novoStatus === 'cancelado')
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 space-y-4 shadow-sm">
                                    
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5">
                                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-amber-900">
                                                O que deseja fazer com o valor desta parcela?
                                            </h4>
                                            <p class="text-xs text-amber-700 mt-0.5">
                                                Essa ação impactará o valor total do título ou das demais parcelas.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        
                                        <!-- Opção: Desconto -->
                                        <label 
                                            class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all"
                                            :class="$wire.tipoAjuste === 'desconto' 
                                                ? 'border-[#313e50] bg-white shadow-md ring-1 ring-[#313e50]' 
                                                : 'border-gray-200 bg-white hover:border-gray-300'"
                                        >
                                            <input type="radio" wire:model="tipoAjuste" value="desconto" class="hidden">

                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-semibold text-gray-800">
                                                    Descontar do título
                                                </span>

                                                <div 
                                                    class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                    :class="$wire.tipoAjuste === 'desconto' 
                                                        ? 'border-[#313e50]' 
                                                        : 'border-gray-300'"
                                                >
                                                    <div 
                                                        class="w-2 h-2 rounded-full bg-[#313e50]"
                                                        x-show="$wire.tipoAjuste === 'desconto'"
                                                    ></div>
                                                </div>
                                            </div>

                                            <p class="text-xs text-gray-500 leading-relaxed">
                                                Remove o valor desta parcela do total do título. 
                                                As demais parcelas não serão alteradas.
                                            </p>
                                        </label>
                                        <label 
                                            class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all"
                                            :class="$wire.tipoAjuste === 'redistribuir' 
                                                ? 'border-[#313e50] bg-white shadow-md ring-1 ring-[#313e50]' 
                                                : 'border-gray-200 bg-white hover:border-gray-300'"
                                        >
                                            <input type="radio" wire:model="tipoAjuste" value="redistribuir" class="hidden">

                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-semibold text-gray-800">
                                                    Redistribuir valor
                                                </span>

                                                <div 
                                                    class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                    :class="$wire.tipoAjuste === 'redistribuir' 
                                                        ? 'border-[#313e50]' 
                                                        : 'border-gray-300'"
                                                >
                                                    <div 
                                                        class="w-2 h-2 rounded-full bg-[#313e50]"
                                                        x-show="$wire.tipoAjuste === 'redistribuir'"
                                                    ></div>
                                                </div>
                                            </div>

                                            <p class="text-xs text-gray-500 leading-relaxed">
                                                Divide o valor desta parcela entre as demais parcelas em aberto,
                                                ajustando automaticamente seus valores.
                                            </p>
                                        </label>

                                    </div>

                                    @error('tipoAjuste') 
                                        <span class="text-xs text-red-500 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="shrink-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl z-10">
                    <button 
                        type="button" 
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalEditarStatus', false), 200)" 
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        form="form-alterar-status"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                    >
                        <svg wire:loading wire:target="salvarStatus" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Confirmar Novo Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>