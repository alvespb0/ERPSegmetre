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
            
            <!-- Modal Panel -->
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
                                Lançar Despesa via DDA
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Novo Título
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Revise e confirme os dados do boleto eletrônico para registrar a conta a pagar.
                        </p>
                    </div>
                    <button wire:click="fechar" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors mt-1">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Body / Form -->
                <div class="p-6 overflow-y-auto flex-1">
                    
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-bold text-red-800">
                                    Por favor, corrija os seguintes erros antes de continuar:
                                </h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 ml-7 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form-despesa-dda" wire:submit.prevent="submit" class="space-y-6">
                        
                        <!-- Dados Principais -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="mb-4">
                                <h2 class="text-sm font-semibold text-gray-900">Dados Principais</h2>
                                <p class="text-xs text-gray-500 mt-1">Informações básicas extraídas do DDA.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Fornecedor / Recebedor<span class="text-red-500">*</span></label>
                                    <select class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="entidade_id">
                                        <option value="">Selecione o Fornecedor...</option>
                                        @foreach($entidades ?? [] as $entidade)
                                            <option value="{{ $entidade->id }}">{{ $entidade->razao_social ?? $entidade->nome_fantasia }} - {{ $entidade->cpf_cnpj }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Descrição da Despesa <span class="text-red-500">*</span></label>
                                    <input type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" placeholder="Ex.: Mensalidade, Compra de Produto, Prestação de Serviço..." wire:model="descricao">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Valor Total (R$) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" placeholder="0,00" wire:model="valor_total">
                                </div>

                                <div class="md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de Emissão</label>
                                    <input type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="data_emissao">
                                </div>
                            </div>
                        </div>

                        <!-- Condição de Pagamento Simplificada -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="mb-4">
                                <h2 class="text-sm font-semibold text-gray-900">Pagamento</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de Vencimento <span class="text-red-500">*</span></label>
                                    <input type="date" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="data_vencimento">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                                    <select class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="forma_pagamento_id">
                                        <option value="">Selecione...</option>
                                        @foreach($formasPagamento ?? [] as $forma)
                                            <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Classificação -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="mb-4">
                                <h2 class="text-sm font-semibold text-gray-900">Classificação</h2>
                                <p class="text-xs text-gray-500 mt-1">Atribua a categoria de despesa e centro de custo (opcionais).</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                                    <select class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="categoria_financeira_id">
                                        <option value="">Não informada</option>
                                        @foreach($categoriasFinanceira ?? [] as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Centro de Custo</label>
                                    <select class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" wire:model="centro_custo_id">
                                        <option value="">Não informado</option>
                                        @foreach($centrosCusto ?? [] as $centro)
                                            <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="mb-4">
                                <h2 class="text-sm font-semibold text-gray-900">Informações Adicionais</h2>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Número da Nota Fiscal (NF) emitida</label>
                                    <input type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" placeholder="Ex.: 000123" wire:model="numero_nf">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Observações internas</label>
                                    <textarea class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]" placeholder="Detalhes adicionais ou referências desta despesa..." wire:model="observacoes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Footer / Actions -->
                <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <button
                        type="button"
                        wire:click="fechar"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Cancelar
                    </button>
                    
                    <button
                        type="submit"
                        form="form-despesa-dda"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors shadow-sm"
                    >
                        <span wire:loading.remove wire:target="submit">Confirmar Lançamento</span>
                        <span wire:loading wire:target="submit">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processando...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>