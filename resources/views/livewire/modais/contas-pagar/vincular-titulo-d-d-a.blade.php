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
                                Vincular Boleto à Despesa Existente
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200">
                                Vínculo
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Selecione uma parcela já cadastrada para vincular a solicitação de pagamento deste boleto.
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

                    <!-- Dados do Boleto DDA -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h2 class="text-sm font-semibold text-gray-900 mb-3">Dados do Boleto (DDA)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Beneficiário</p>
                                <p class="font-medium text-gray-900">{{ $nomeBeneficiario }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Doc: {{ $documentoBeneficiario }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Valor / Vencimento</p>
                                <p class="font-medium text-gray-900">R$ {{ number_format($valorDDA, 2, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($vencimentoDDA)->format('d/m/Y') }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Linha Digitável</p>
                                <p class="font-mono text-xs text-gray-600 break-all">{{ $linhaDigitavel }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros de busca -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex flex-col md:flex-row md:items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Buscar despesa</label>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="busca"
                                    placeholder="Descrição, fornecedor ou CPF/CNPJ..."
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                >
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer pb-2">
                                <input
                                    type="checkbox"
                                    wire:model.live="filtrarPorFornecedor"
                                    class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]"
                                >
                                Filtrar pelo fornecedor do boleto
                            </label>
                        </div>
                    </div>

                    <!-- Lista de parcelas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                            <h2 class="text-sm font-semibold text-gray-900">Despesas elegíveis</h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Parcelas ativas com saldo devedor suficiente e sem solicitação pendente.
                            </p>
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
                                                {{ $parcela->titulo->descricao }}
                                            </p>
                                            <span class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                                R$ {{ number_format($parcela->saldo_devedor, 2, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-gray-500">
                                            <span>{{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia }}</span>
                                            <span>Parcela {{ $parcela->numero_parcela }}/{{ $parcela->titulo->parcelas_count }}</span>
                                            <span>Venc: {{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="px-5 py-10 text-center text-sm text-gray-500">
                                    <p>Nenhuma despesa elegível encontrada.</p>
                                    @if($filtrarPorFornecedor)
                                        <button
                                            type="button"
                                            wire:click="$set('filtrarPorFornecedor', false)"
                                            class="mt-2 text-[#313e50] hover:underline text-xs font-medium"
                                        >
                                            Exibir todas as despesas
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
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors shadow-sm"
                    >
                        <span wire:loading.remove wire:target="submit">Confirmar Vínculo</span>
                        <span wire:loading wire:target="submit">Vinculando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
