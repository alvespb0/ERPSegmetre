<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Contas a Receber
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Nova Conta a Receber</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Registre uma nova receita, faturamento ou direito de recebimento.
                </p>
            </div>
            <a
                href=""
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Recebimentos
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Dados Principais</h2>
                        <p class="text-xs text-gray-500 mt-1">Informações básicas do título a receber.</p>
                    </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cliente / Pagador <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="entidade_id"
                            >
                                <option value="">Selecione o cliente...</option>
                                @foreach($entidades as $entidade)
                                    <option value="{{ $entidade->id }}">{{ $entidade->razao_social ?? $entidade->nome_fantasia }} - {{ $entidade->cpf_cnpj }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Descrição do Recebimento <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Mensalidade, Venda de Produto, Prestação de Serviço..."
                                wire:model="descricao"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Valor Total (R$) <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="0,00"
                                wire:model="valor_total"
                            >
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Data de Emissão</label>
                            <input
                                type="date"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="data_emissao"
                            >
                            <p class="text-[10px] text-gray-400 mt-1">Se vazio, assume a data de hoje.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Condição de Recebimento</h2>
                        <p class="text-xs text-gray-500 mt-1">Defina a data base, a forma e a quantidade de parcelas.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">1º Vencimento <span class="text-red-500">*</span></label>
                            <input
                                type="date"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model.live="data_vencimento"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Forma</label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="forma_pagamento_id"
                            >
                                <option value="">Selecione...</option>
                                @foreach($formasPagamento as $forma)
                                    <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Parcelas <span class="text-red-500">*</span></label>
                            <select
                                class="w-full max-h-48 overflow-y-auto rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model.live="quantidade_parcelas"
                            >
                                <option value="">Selecione...</option>
                                @for($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ $i }}x</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <button
                                type="button"
                                wire:click="gerarParcelas"
                                wire:target="gerarParcelas"
                                wire:loading.attr="disabled"
                                :disabled="!$wire.valor_total || !$wire.data_vencimento || !$wire.quantidade_parcelas"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors focus:ring-2 focus:ring-gray-200 focus:outline-none whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="gerarParcelas" class="inline-flex items-center gap-1.5">
                                    Gerar Parcelas
                                </span>

                                <span wire:loading wire:target="gerarParcelas" class="inline-flex items-center gap-1.5">
                                    Gerando...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model.live="venda_recorrente" 
                                class="w-4 h-4 rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]"
                            >
                            <span class="text-sm font-medium text-gray-800">Esta é uma venda recorrente</span>
                        </label>

                        @if($venda_recorrente)
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade de Recorrências <span class="text-red-500">*</span></label>
                                    <input
                                        type="number"
                                        min="2"
                                        max="60"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                        placeholder="Ex.: 12"
                                        wire:model="quantidade_recorrencias"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Intervalo <span class="text-red-500">*</span></label>
                                    <select
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                        wire:model="intervalo_recorrencia"
                                    >
                                        <option value="">Selecione o intervalo...</option>
                                        <option value="7d">Semanal (7 dias)</option>
                                        <option value="14d">Quinzenal (14 dias)</option>
                                        <option value="21d">Tri-semanal (21 dias)</option>
                                        <option value="30d">Mensal (30 dias)</option>
                                        <option value="45d">A cada 45 dias</option>
                                        <option value="60d">Bimestral (60 dias)</option>
                                        <option value="90d">Trimestral (90 dias)</option>
                                        <option value="1m">1 Mês</option>
                                        <option value="2m">2 Meses</option>
                                        <option value="3m">3 Meses</option>
                                        <option value="6m">Semestral (6 Meses)</option>
                                        <option value="12m">Anual (12 Meses)</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!empty($parcelas))
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-900">Parcelas</h3>
                                <span class="text-xs text-gray-500">{{ count($parcelas) }} item(s)</span>
                            </div>

                            <div class="space-y-3">
                                @foreach($parcelas as $parcela)
                                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 bg-gray-50/50 p-3">
                                        <div>
                                            <p class="text-xs text-gray-500">Parcela {{ $parcela['parcela_numero'] }}</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                Vencimento.: {{ \Carbon\Carbon::parse($parcela['data_vencimento_parcela'])->format('d/m/Y')}}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Valor</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                R$ {{ number_format((float) $parcela['valor_parcela'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Classificação</h2>
                        <p class="text-xs text-gray-500 mt-1">Atribua a conta, categoria de receita e centro de custo (opcionais).</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="categoria_financeira_id"
                            >
                                <option value="">Não informada</option>
                                @foreach($categoriasFinanceira as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Centro de Custo</label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="centro_custo_id"
                            >
                                <option value="">Não informado</option>
                                @foreach($centrosCusto as $centro)
                                    <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Informações Adicionais</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Número da Nota Fiscal (NF) emitida</label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: 000123"
                                wire:model="numero_nf"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Observações internas</label>
                            <textarea
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Detalhes adicionais ou referências deste recebimento..."
                                wire:model="observacoes"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 self-start">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900">Ações</h2>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-xs text-gray-500 mb-4">
                        Revise os dados antes de salvar. Ao confirmar, este título será registrado nas suas Contas a Receber.
                    </p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar Recebimento</span>
                        <span wire:loading wire:target="submit">Salvando...</span>
                    </button>

                    <button
                        type="reset"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Limpar formulário
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>