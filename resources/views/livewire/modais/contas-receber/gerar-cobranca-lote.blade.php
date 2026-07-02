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
            wire:click="fecharModal"
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
                class="relative transform flex flex-col rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100 pointer-events-auto max-h-[calc(100vh-4rem)] overflow-hidden"
            >
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start flex-shrink-0">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Cobranças Bancárias em LOTE
                            </h3>
                        </div>
                    </div>
                    
                    <button type="button" wire:click="fecharModal" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="gerar" class="flex flex-col overflow-hidden">
                    
                    <div class="p-6 space-y-6 overflow-y-auto max-h-[calc(100vh-14rem)]">
                        
                        @error('geral')
                            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm font-medium">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                            <label for="conta_select" class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                Conta Bancária Emissora <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="conta_select"
                                wire:change="selectContaCobranca($event.target.value)"
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] transition-colors @error('selectedConta') border-red-300 ring-red-300 bg-red-50 @enderror"
                            >
                                <option value="">Selecione a conta para registrar o boleto...</option>
                                @foreach($contas as $conta)
                                    <option value="{{ $conta->id }}" {{ optional($selectedConta)->id == $conta->id ? 'selected' : '' }}>
                                        {{ $conta->nome }} (Ag: {{ $conta->agencia }} / CC: {{ $conta->conta }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedConta') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        @if($selectedConta)
                            <div class="space-y-6 animate-fade-in">
                                
                                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="modalidade" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                            Modalidade <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            wire:model="modalidade" 
                                            id="modalidade"
                                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] @error('modalidade') border-red-300 ring-red-300 @enderror"
                                        >
                                            <option value="1">01 - Cobrança Simples</option>
                                            <option value="3">03 - Caucionada</option>
                                            <option value="4">04 - Vinculada</option>
                                            <option value="5">05 - Carnê</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                        @error('modalidade') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="especie_documento" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                            Espécie do Documento <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            wire:model="especie_documento" 
                                            id="especie_documento"
                                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] @error('especie_documento') border-red-300 ring-red-300 @enderror"
                                        >
                                            <option value="DM">DM - Duplicata Mercantil</option>
                                            <option value="DS">DS - Duplicata de Prestação de Serviços</option>
                                            <option value="NP">NP - Nota Promissória</option>
                                            <option value="OU">OU - Outros</option>
                                        </select>
                                        @error('especie_documento') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                                        <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Parametrização de Encargos e Prazos</h4>
                                    </div>
                                    
                                    <div class="p-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-4 bg-gray-50/50 p-4 rounded-lg border border-gray-100">
                                                <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide">Juros de Mora</h5>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Juros</label>
                                                    <select wire:model.live="codigo_juros" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#313e50] focus:ring-[#313e50] @error('codigo_juros') border-red-300 ring-red-300 @enderror">
                                                        <option value="0">0 - Isento</option>
                                                        <option value="1">1 - Valor por dia (R$)</option>
                                                        <option value="2">2 - Taxa mensal (%)</option>
                                                    </select>
                                                    @error('codigo_juros') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                </div>
                                                
                                                <div x-data="{ codigoJuros: @entangle('codigo_juros') }" x-show="codigoJuros != 0" class="grid grid-cols-2 gap-3" x-cloak>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Valor / Taxa</label>
                                                        <div class="relative">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-sm" x-text="codigoJuros == 1 ? 'R$' : '%'"></span>
                                                            </div>
                                                            <input type="number" step="0.01" wire:model="valor_juros" class="block w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2 text-sm bg-white focus:border-[#313e50] focus:ring-[#313e50] @error('valor_juros') border-red-300 ring-red-300 @enderror">
                                                        </div>
                                                        @error('valor_juros') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Início (Dias após)</label>
                                                        <input type="number" wire:model="dias_inicio_juros" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:border-[#313e50] focus:ring-[#313e50] @error('dias_inicio_juros') border-red-300 ring-red-300 @enderror">
                                                        @error('dias_inicio_juros') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-4 bg-gray-50/50 p-4 rounded-lg border border-gray-100">
                                                <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide">Multa por Atraso</h5>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Multa</label>
                                                    <select wire:model.live="codigo_multa" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#313e50] focus:ring-[#313e50] @error('codigo_multa') border-red-300 ring-red-300 @enderror">
                                                        <option value="0">0 - Isento</option>
                                                        <option value="1">1 - Valor fixo (R$)</option>
                                                        <option value="2">2 - Percentual (%)</option>
                                                    </select>
                                                    @error('codigo_multa') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                </div>

                                                <div x-data="{ codigoMulta: @entangle('codigo_multa') }" x-show="codigoMulta != 0" class="grid grid-cols-2 gap-3" x-cloak>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Valor / Taxa</label>
                                                        <div class="relative">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-sm" x-text="codigoMulta == 1 ? 'R$' : '%'"></span>
                                                            </div>
                                                            <input type="number" step="0.01" wire:model="valor_multa" class="block w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2 text-sm bg-white focus:border-[#313e50] focus:ring-[#313e50] @error('valor_multa') border-red-300 ring-red-300 @enderror">
                                                        </div>
                                                        @error('valor_multa') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Aplica em (Dias)</label>
                                                        <input type="number" wire:model="dias_inicio_multa" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:border-[#313e50] focus:ring-[#313e50] @error('dias_inicio_multa') border-red-300 ring-red-300 @enderror">
                                                        @error('dias_inicio_multa') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 bg-gray-50/50 p-4 rounded-lg border border-gray-100 flex flex-col md:flex-row md:items-start justify-between gap-4">
                                            <div class="flex-1 pt-1">
                                                <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide mb-1">Validade do Título</h5>
                                                <p class="text-xs text-gray-500">Número de dias corridos que o banco aceitará receber este boleto após o vencimento.</p>
                                            </div>
                                            <div class="w-full md:w-1/3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Prazo Limite para Pagto.</label>
                                                <div class="relative">
                                                    <input type="number" wire:model="dias_limite_pagamento" class="block w-full rounded-lg border border-gray-300 pr-12 px-3 py-2 text-sm bg-white focus:border-[#313e50] focus:ring-[#313e50] @error('dias_limite_pagamento') border-red-300 ring-red-300 @enderror">
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                        <span class="text-gray-400 sm:text-xs">dias</span>
                                                    </div>
                                                </div>
                                                @error('dias_limite_pagamento') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($tipoIntegracao))
                                    @if($tipoIntegracao === 'api')
                                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                            <div class="text-xs text-blue-800">
                                                <strong class="block mb-0.5 text-blue-900">Registro Direto via API</strong>
                                                Esta cobrança será enviada eletronicamente e cadastrada imediatamente no banco {{ optional($selectedConta->banco)->nome }} após a emissão.
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-100 border border-gray-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                            <div class="text-xs text-gray-600">
                                                <strong class="block mb-0.5 text-gray-800">Fluxo por Arquivo de Remessa</strong>
                                                Esta cobrança ficará pendente no sistema até a geração de uma remessa de cobrança (CNAB) para envio ao banco.
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                @foreach($parcelas as $parcela)
                                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                                        <label for="info_complementares" class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                            Instruções / Informações Complementares (Opcional)
                                            <p class="text-gray-500"> Parcela nº{{ $parcela->numero_parcela }} | Vencimento: {{ Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }} </p>
                                        </label>
                                        <textarea wire:model="info_complementares.{{ $parcela->id }}" id="info_complementares" rows="2" placeholder="Ex: Pagável em qualquer banco até o vencimento..." class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-[#313e50] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#313e50] @error('info_complementares') border-red-300 ring-red-300 @enderror"></textarea>
                                        @error('info_complementares') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="fecharModal"
                            class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Cancelar
                        </button>
                        
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            @if(!$selectedConta) disabled @endif
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="gerar">Gerar e Registrar Boleto</span>
                            <span wire:loading wire:target="gerar" class="flex items-center gap-2">
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