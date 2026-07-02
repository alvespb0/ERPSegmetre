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
                                Cancelar Cobrança Bancária (Boleto)
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count ?? '--' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Ref. Título #{{ $parcela->titulo_financeiro_id }} &middot; Verifique os dados abaixo para confirmar o cancelamento.
                        </p>
                    </div>
                    
                    <button type="button" wire:click="fecharModal" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="cancelar" class="flex flex-col overflow-hidden">
                    
                    <div class="p-6 space-y-6 overflow-y-auto max-h-[calc(100vh-14rem)]">
                        
                        @error('geral')
                            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm font-medium">
                                {{ $message }}
                            </div>
                        @enderror

                        @php
                            $pagador = $parcela->titulo->entidade ?? null;
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Pagador</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $pagador->razao_social ?? $pagador->nome ?? 'Não informado' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">CPF/CNPJ: {{ $pagador->cpf_cnpj ?? 'S/N' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Vencimento da Parcela</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}
                                </p>
                                @php
                                    $diasAtraso = \Carbon\Carbon::parse($parcela->data_vencimento)->diffInDays(now(), false);
                                @endphp
                                @if($diasAtraso > 0)
                                    <span class="text-[10px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100 mt-1 inline-block">{{ $diasAtraso }} dias em atraso</span>
                                @else
                                    <span class="text-[10px] text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100 mt-1 inline-block">No prazo</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Valor da Cobrança</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    R$ {{ number_format($parcela->saldo_devedor, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        @if($boletoAtivo)
                            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Detalhes do Boleto Ativo</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $boletoAtivo->classes_status }}">
                                        {{ ucfirst($boletoAtivo->status) }}
                                    </span>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Nosso Número</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $boletoAtivo->nosso_numero ?? 'Não gerado' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Numero Documento</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $boletoAtivo->numero_documento ?? 'Não gerado' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Data de Registro</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $boletoAtivo->data_registro || $boletoAtivo->created_at ? \Carbon\Carbon::parse($boletoAtivo->data_registro ?? $boletoAtivo->created_at)->format('d/m/Y') : 'Aguardando' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Modalidade / Espécie</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $boletoAtivo->modalidade ?? '--' }} / {{ $boletoAtivo->especie_documento ?? '--' }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 border-t border-gray-100">
                                        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Linha Digitável</p>
                                        <p class="text-sm font-mono font-medium text-gray-900 break-all">
                                            {{ $boletoAtivo->linha_digitavel ?? 'Linha digitável não disponível' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($boletoAtivo->esta_registrado)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                    <div>
                                        <h4 class="text-sm font-semibold text-amber-800">Atenção ao Cancelamento</h4>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="fecharModal"
                            class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Voltar
                        </button>
                        
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="cancelar">Confirmar Cancelamento</span>
                            <span wire:loading wire:target="cancelar" class="flex items-center gap-2">
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