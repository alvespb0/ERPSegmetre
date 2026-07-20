<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-titulo-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50" 
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagamento', false), 200)"
        ></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-3xl border border-gray-100 pointer-events-auto"
            >
                @php
                    $statusColorsSolicitacao = [
                        'aprovado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'pendente' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'rejeitado' => 'bg-red-50 text-red-700 border-red-200',
                        'pago' => 'bg-blue-50 text-blue-700 border-blue-200',
                    ];
                    $statusReal = strtolower($solicitacao->status ?? 'pendente');
                    $corStatusSolicitacao = $statusColorsSolicitacao[$statusReal] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                @endphp

                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-titulo-title">
                                Solicitação #{{ str_pad($solicitacao->id ?? 0, 5, '0', STR_PAD_LEFT) }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $corStatusSolicitacao }}">
                                {{ ucfirst($solicitacao->status ?? 'Pendente') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Criada em {{ isset($solicitacao->created_at) ? $solicitacao->created_at->format('d/m/Y \à\s H:i') : '--/--/----' }}
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagamento', false), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    
                    <!-- Grid Superior: Valor, Vencimento e Método -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Valor a ser Pago</p>
                            <p class="text-xl font-semibold text-gray-900">
                                R$ {{ number_format($solicitacao->valor ?? 0, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Método</p>
                            <p class="text-xl font-semibold text-gray-900 uppercase">
                                {{ str_replace('_', ' ', $solicitacao->tipo ?? 'Indefinido') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Vencimento</p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ isset($solicitacao->parcela->data_vencimento) ? \Carbon\Carbon::parse($solicitacao->parcela->data_vencimento)->format('d/m/Y') : '--/--/----' }}
                            </p>
                        </div>
                    </div>

                    <!-- Informações Gerais (Beneficiário e Identificador) -->
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Detalhes do Beneficiário</h4>
                        </div>
                        <div class="p-4 grid grid-cols-1 gap-4 text-sm">
                            
                            <div class="border-b border-gray-50 pb-3">
                                <p class="text-gray-500 text-xs mb-0.5">Entidade (Nome / Razão Social)</p>
                                <p class="font-medium text-gray-900 text-base">
                                    {{ $solicitacao->parcela->titulo->entidade->razao_social ?? $solicitacao->parcela->titulo->entidade->nome_fantasia ?? 'Não informado' }}
                                    <span class="text-gray-400 font-normal ml-1 text-sm">({{ $solicitacao->parcela->titulo->entidade->cpf_cnpj ?? 'S/N' }})</span>
                                </p>
                            </div>
                            
                            <div x-data="{ copied: false }">
                                <p class="text-gray-500 text-xs mb-1">
                                    Identificador (Linha Digitável / Chave PIX)
                                </p>

                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex items-start justify-between gap-2">
                                    <p class="font-mono font-medium text-gray-800 break-all flex-1">
                                        {{ $solicitacao->identificador ?? 'Nenhum identificador registrado.' }}
                                    </p>

                                    @if($solicitacao->identificador)
                                        <button
                                            type="button"
                                            class="flex-shrink-0 p-2 rounded-md hover:bg-gray-200 transition"
                                            @click="
                                                navigator.clipboard.writeText('{{ $solicitacao->identificador }}');
                                                copied = true;
                                                setTimeout(() => copied = false, 2000);
                                            "
                                            :title="copied ? 'Copiado!' : 'Copiar'"
                                        >
                                            <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M10 8h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/>
                                            </svg>

                                            <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <span
                                    x-show="copied"
                                    x-transition
                                    class="text-xs text-green-600 mt-1 inline-block"
                                    style="display:none"
                                >
                                    Copiado para a área de transferência!
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Definição da Conta de Pagamento -->
                    <div class="bg-white rounded-xl border border-blue-100 overflow-hidden shadow-sm relative">
                        <!-- Filete lateral azul -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#313e50]"></div>
                        
                        <div class="px-5 py-3 bg-[#313e50]/5 border-b border-blue-100">
                            <h4 class="text-xs font-semibold text-[#313e50] uppercase tracking-wide">Origem do Pagamento</h4>
                        </div>
                        <div class="p-5">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Conta Bancária *</label>
                            <select 
                                wire:model="conta_selecionada"
                                class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2.5 px-3 cursor-pointer hover:bg-gray-100 transition-colors"
                            >
                                <option value="">Selecione de qual conta o dinheiro irá sair...</option>
                                @foreach($contas ?? [] as $conta)
                                    <option value="{{ $conta->id }}">
                                        {{ $conta->banco->nome ?? 'Banco' }} - Ag: {{ $conta->agencia }} / Cc: {{ $conta->conta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Footer / Ações -->
                <div class="bg-white border-t border-gray-100 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-xl">
                    <button 
                        type="button" 
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagamento', false), 200)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm w-full sm:w-auto"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        wire:click="processarPagamento"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm w-full sm:w-auto disabled:opacity-70"
                    >
                        <svg wire:loading wire:target="processarPagamento" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Aprovar e Pagar
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>