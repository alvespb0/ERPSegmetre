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
            class="fixed inset-0 bg-gray-900/50" 
            @click="show = false; setTimeout(() => $wire.fechar(), 200)"
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
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-3xl border border-gray-100 pointer-events-auto flex flex-col"
            >
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Configuração de Cobrança
                            </h3>
                            @if($configCobranca)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200">
                                    Ativa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200">
                                    Não configurada
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Conta: {{ $conta->nome }} &middot; {{ $conta->banco?->nome ?? 'Banco não informado' }}
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.fechar(), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div> 

                <form wire:submit.prevent="salvar">
                    <div class="p-6 space-y-6">
                        
                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Dados Principais</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Empresa Vinculada (Parâmetro)</label>
                                    <select 
                                        wire:model="empresa_parametro_id"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                        <option value="">Selecione uma empresa...</option>
                                        @if(isset($empresasParametro) && count($empresasParametro) > 0)
                                            @foreach($empresasParametro as $empresa)
                                                <option value="{{ $empresa->id }}">{{ $empresa->razao_social ?? $empresa->nome ?? 'Empresa ' . $empresa->id }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Código do Cedente</label>
                                    <input 
                                        type="text" 
                                        wire:model="codigo_cendente"
                                        placeholder="Ex: 1234567-8"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Carteira</label>
                                    <input 
                                        type="text" 
                                        wire:model="carteira"
                                        placeholder="Ex: 109"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Ambiente de Emissão</label>
                                    <div class="flex gap-4 mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model="ambiente" value="homologacao" name="ambiente" class="text-[#313e50] focus:ring-[#313e50]">
                                            <span class="ml-2 text-sm text-gray-700">Homologação</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model="ambiente" value="producao" name="ambiente" class="text-[#313e50] focus:ring-[#313e50]">
                                            <span class="ml-2 text-sm text-gray-700">Produção</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Controle CNAB e Numeração</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Layout CNAB</label>
                                    <select 
                                        wire:model="layout_cnab"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                        <option value="">Selecione...</option>
                                        <option value="240">CNAB 240</option>
                                        <option value="400">CNAB 400</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Último Nº Remessa</label>
                                    <input 
                                        type="number" 
                                        wire:model="ultimo_numero_remessa"
                                        placeholder="Ex: 0"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Próximo Nosso Número</label>
                                    <input 
                                        type="number" 
                                        wire:model="nosso_numero"
                                        placeholder="Ex: 1"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#313e50] focus:ring-[#313e50] sm:text-sm transition-colors"
                                    >
                                </div>
                            </div>
                        </div> 

                    </div> 

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                        <button 
                            type="button" 
                            @click="show = false; setTimeout(() => $wire.fechar(), 200)"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Cancelar
                        </button>
                        
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm flex items-center gap-2"
                        >
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>