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
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100 pointer-events-auto flex flex-col max-h-[90vh]"
            >
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start shrink-0">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Conciliar Pagamento Efetuado
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Conciliação
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Selecione a parcela correspondente para vincular este pagamento sem conciliação prévia.
                        </p>
                    </div>
                    <button wire:click="fechar" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors mt-1">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto flex-1 space-y-6">
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Dados do Pagamento Realizado -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center justify-between mb-3 border-b border-gray-50 pb-2">
                            <h2 class="text-sm font-semibold text-gray-900">Dados do Pagamento Realizado</h2>
                            <span class="text-xs text-gray-400 font-mono">ID #{{ str_pad($pagamento->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Identificador / Chave</p>
                                <p class="font-medium text-gray-900 break-all">{{ $pagamento->identificador ?? 'Não informado' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Tipo: <span class="uppercase font-semibold text-purple-700">{{ $tipo ?? 'PIX' }}</span></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Valor Efetuado</p>
                                <p class="font-semibold text-emerald-600 text-base">R$ {{ number_format($pagamento->valor ?? 0, 2, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Pago em: {{ isset($pagamento->data_pagamento) ? \Carbon\Carbon::parse($pagamento->dataPagamento)->format('d/m/Y H:i') : '--/--/----' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Conta Origem</p>
                                <p class="font-medium text-gray-900 line-clamp-2">
                                    {{ $pagamento->conta->banco->nome ?? 'Banco' }} - Ag: {{ $pagamento->conta->agencia ?? '--' }} / Cc: {{ $pagamento->conta->conta ?? '--' }}
                                </p>
                            </div>
                            @if(!empty($pagamento->end_to_end_id))
                                <div class="md:col-span-3 pt-2 border-t border-gray-50">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">End To End ID</p>
                                    <p class="font-mono text-xs text-gray-600 break-all">{{ $pagamento->end_to_end_id }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Filtros de busca -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex flex-col md:flex-row md:items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Buscar despesa / parcela</label>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="busca"
                                    placeholder="Descrição da despesa, fornecedor ou CPF/CNPJ..."
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                >
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer pb-2">
                                <input
                                    type="checkbox"
                                    wire:model.live="filtrarPorValorSemelhante"
                                    class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]"
                                >
                                Apenas valores semelhantes (± R$ {{ number_format($pagamento->valor ?? 0, 2, ',', '.') }})
                            </label>
                        </div>
                    </div>

                    <!-- Lista de parcelas elegíveis -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">Parcelas / Despesas Elegíveis</h2>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Selecione uma parcela pendente para efetuar o vínculo da conciliação.
                                </p>
                            </div>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            @forelse($parcelasElegiveis as $parcela)
                                <label
                                    class="flex items-start gap-3 px-5 py-3 border-b border-gray-50 cursor-pointer hover:bg-gray-50/80 transition-colors {{ $parcela_id == $parcela->id ? 'bg-[#313e50]/5' : '' }}"
                                >
                                    <input
                                        type="radio"
                                        wire:model="parcela_id"
                                        value="{{ $parcela->id }}"
                                        class="mt-1 border-gray-300 text-[#313e50] focus:ring-[#313e50]"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $parcela->titulo->descricao ?? 'Sem Descrição' }}
                                            </p>
                                            <span class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                                Saldo: R$ {{ number_format($parcela->saldo_devedor ?? $parcela->valor, 2, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-gray-500">
                                            <span>
                                                Fornecedor: <strong>{{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'Não informado' }}</strong>
                                            </span>
                                            <span>Parcela: {{ $parcela->numero_parcela ?? 1 }}/{{ $parcela->titulo->parcelas_count ?? 1 }}</span>
                                            <span>Venc: {{ isset($parcela->data_vencimento) ? \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') : '--/--/----' }}</span>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="px-5 py-10 text-center text-sm text-gray-500">
                                    <p>Nenhuma parcela/despesa elegível encontrada.</p>
                                    @if($filtrarPorValorSemelhante)
                                        <button
                                            type="button"
                                            wire:click="$set('filtrarPorValorSemelhante', false)"
                                            class="mt-2 text-[#313e50] hover:underline text-xs font-medium"
                                        >
                                            Exibir todas as parcelas pendentes
                                        </button>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <button
                        type="button"
                        wire:click="fechar"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors shadow-sm cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="submit">Confirmar Conciliação</span>
                        <span wire:loading wire:target="submit">Conciliando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>