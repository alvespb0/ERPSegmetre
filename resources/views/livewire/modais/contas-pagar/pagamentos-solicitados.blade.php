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
                                    <p class="font-mono font-medium text-gray-800 break-all flex-1 mt-0.5">
                                        {{ $solicitacao->identificador ?? 'Nenhum identificador registrado.' }}
                                    </p>

                                    @if($solicitacao->identificador)
                                        <button
                                            type="button"
                                            class="flex-shrink-0 p-1.5 rounded-md hover:bg-gray-200 transition text-gray-500 hover:text-gray-800"
                                            @click="
                                                navigator.clipboard.writeText('{{ $solicitacao->identificador }}');
                                                copied = true;
                                                setTimeout(() => copied = false, 2000);
                                            "
                                            :title="copied ? 'Copiado!' : 'Copiar'"
                                        >
                                            <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M10 8h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/>
                                            </svg>
                                            <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <span x-show="copied" x-transition class="text-[10px] font-medium text-green-600 mt-1 inline-block" style="display:none">
                                    Copiado para a área de transferência!
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Definição da Conta de Pagamento & Saldo -->
                    <div class="bg-white rounded-xl border border-blue-100 overflow-hidden shadow-sm relative">                        
                        <div class="px-5 py-3 bg-[#313e50]/5 border-b border-blue-100">
                            <h4 class="text-xs font-semibold text-[#313e50] uppercase tracking-wide">Origem do Pagamento</h4>
                        </div>
                        <div class="p-5 space-y-4">
                            
                            <!-- Seletor de Contas -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Conta Bancária *</label>
                                <select 
                                    wire:model.live="selected_conta"
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

                            <!-- Estado de Loading da Consulta de Saldo -->
                            <div wire:loading wire:target="selected_conta" class="w-full">
                                <div class="flex items-center gap-2 text-sm text-gray-500 px-2 py-1">
                                    <svg class="animate-spin h-4 w-4 text-[#313e50]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Consultando saldos disponíveis...
                                </div>
                            </div>

                            <!-- Display do Saldo (Mostra apenas após carregar) -->
                            @if($selected_conta && isset($saldo))
                                <div wire:loading.remove wire:target="selected_conta" class="bg-gray-50 border border-gray-100 rounded-xl p-4 transition-all">
                                    
                                    <!-- Grid de Valores Financeiros -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:divide-x divide-gray-200">
                                        
                                        <!-- Saldo Atual -->
                                        <div class="md:px-3 first:pl-0">
                                            <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Saldo Conta</p>
                                            <p class="text-base font-bold {{ $saldo < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                R$ {{ number_format($saldo, 2, ',', '.') }}
                                            </p>
                                        </div>

                                        <!-- Limite -->
                                        @if(isset($limite))
                                            <div class="md:px-3">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Limite Disp.</p>
                                                <p class="text-base font-medium text-gray-700">
                                                    R$ {{ number_format($limite, 2, ',', '.') }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Valor Bloqueado -->
                                        @if(isset($bloqueado))
                                            <div class="md:px-3 col-span-2 md:col-span-1">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Bloqueado</p>
                                                <p class="text-base font-medium text-amber-600">
                                                    R$ {{ number_format($bloqueado, 2, ',', '.') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Alerta se o saldo for insuficiente -->
                                    @if($saldo < $solicitacao->valor)
                                        <div class="mt-4 pt-3 border-t border-gray-200 flex items-start gap-2.5 text-amber-700 bg-amber-50/50 p-2.5 rounded-lg border border-amber-100/50">
                                            <p class="text-[11px] font-medium leading-tight">
                                                O saldo disponível (R$ {{ number_format($saldo, 2, ',', '.') }}) é inferior ao valor da solicitação. Esta transação pode ser rejeitada pelo banco.
                                            </p>
                                        </div>
                                    @endif

                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- NOVO BLOCO: Retorno da Consulta de Despesa -->
                    @if(!empty($consultaDespesaRet))
                        @php
                            $valorFinalRet = $consultaDespesaRet['valor_final'] ?? $consultaDespesaRet['valor_boleto'] ?? 0;
                            $valorSolicitacao = $solicitacao->valor ?? 0;
                            $valorDivergente = $valorFinalRet != $valorSolicitacao;

                            $vencRet = $consultaDespesaRet['vencimento_boleto'] ?? null;
                            $vencSol = $solicitacao->parcela->data_vencimento ?? null;
                            $vencSolFormat = $vencSol ? \Carbon\Carbon::parse($vencSol)->format('Y-m-d') : null;
                            $vencDivergente = $vencRet && $vencSolFormat && $vencRet !== $vencSolFormat;
                        @endphp

                        <div class="bg-white rounded-xl border border-[#313e50]/20 overflow-hidden shadow-sm relative">                            
                            <div class="px-5 py-3 bg-[#313e50]/5 border-b border-[#313e50]/10">
                                <h4 class="text-xs font-semibold text-[#313e50] uppercase tracking-wide">Dados Retornados do Banco</h4>
                            </div>
                            <div class="p-5 space-y-4 text-sm">
                                
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Beneficiário Encontrado</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $consultaDespesaRet['razao_social_beneficiario'] ?? $consultaDespesaRet['nome_fantasia_beneficiario'] ?? 'N/A' }}
                                        <span class="text-gray-500 font-normal ml-1">({{ $consultaDespesaRet['cpf_cnpj_beneficiario'] ?? 'N/A' }})</span>
                                    </p>
                                    <p class="text-gray-500 text-xs mt-1">Instituição: {{ $consultaDespesaRet['banco_beneficiario'] ?? 'N/A' }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3 border-t border-gray-100">
                                    <!-- Coluna Solicitação -->
                                    <div>
                                        <p class="font-semibold text-gray-700 mb-2">Dados da Solicitação</p>
                                        <p class="text-gray-600">Valor: R$ {{ number_format($valorSolicitacao, 2, ',', '.') }}</p>
                                        <p class="text-gray-600">Vencimento: {{ $vencSol ? \Carbon\Carbon::parse($vencSol)->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                    
                                    <!-- Coluna Retorno -->
                                    <div>
                                        <p class="font-semibold text-gray-700 mb-2">Dados do Título</p>
                                        <p class="font-medium {{ $valorDivergente ? 'text-red-600' : 'text-gray-900' }}">
                                            Valor Cobrado: R$ {{ number_format($valorFinalRet, 2, ',', '.') }}
                                        </p>
                                        <p class="font-medium {{ $vencDivergente ? 'text-red-600' : 'text-gray-900' }}">
                                            Vencimento: {{ $vencRet ? \Carbon\Carbon::parse($vencRet)->format('d/m/Y') : 'N/A' }}
                                        </p>

                                        @if(isset($consultaDespesaRet['valor_multa']) && $consultaDespesaRet['valor_multa'] > 0)
                                            <p class="text-gray-500 text-xs mt-1">Multa/Juros: R$ {{ number_format($consultaDespesaRet['valor_multa'], 2, ',', '.') }}</p>
                                        @endif
                                        @if(isset($consultaDespesaRet['valor_abatimento']) && $consultaDespesaRet['valor_abatimento'] > 0)
                                            <p class="text-gray-500 text-xs mt-1">Abatimento: R$ {{ number_format($consultaDespesaRet['valor_abatimento'], 2, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if($valorDivergente || $vencDivergente)
                                    <div class="mt-2 pt-3 border-t border-gray-200 flex items-start gap-2.5 text-amber-700 bg-amber-50/50 p-2.5 rounded-lg border border-amber-100/50">
                                        <p class="text-[11px] font-medium leading-tight">
                                            <strong>Atenção:</strong> Há divergências entre os dados lançados no sistema e os dados retornados pela instituição financeira. Verifique antes de confirmar o pagamento.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Footer / Ações -->
                <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <button 
                        type="button" 
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalPagamento', false), 200)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Cancelar
                    </button>
                    
                    @if(empty($consultaDespesaRet))
                        <!-- Botão Aprovar (Faz a consulta) -->
                        <button 
                            type="button" 
                            wire:click="consultaDespesa"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-70"
                        >
                            <svg wire:loading wire:target="consultaDespesa" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="consultaDespesa">Consultar Título</span>
                            <span wire:loading wire:target="consultaDespesa">Consultando...</span>
                        </button>
                    @else
                        <button 
                            type="button" 
                            wire:click="processarPagamento"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-70"
                        >
                            <svg wire:loading wire:target="processarPagamento" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="processarPagamento">Processar Pagamento</span>
                            <span wire:loading wire:target="processarPagamento">Processando...</span>
                        </button>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>