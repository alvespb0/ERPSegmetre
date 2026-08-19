<div x-data="{ mostrarFiltrosAvancados: false }">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Contas a Pagar
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Pagamentos Não Conciliados</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie e concilie os pagamentos realizados que ainda não possuem vínculo com parcelas.
                </p>
            </div>
            
            <!-- Loading State -->
            <div class="flex flex-wrap items-center gap-3">
                <div wire:loading class="inline-flex items-center gap-2 text-sm font-medium text-[#313e50] mr-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processando...
                </div>
            </div>
        </div>

        <!-- Painel de Filtros Integrado -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all">
            <!-- HEADER DO FILTRO -->
            <div class="p-2 flex flex-col xl:flex-row items-center gap-2">
                <!-- SEARCH -->
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por Identificador (Chave Pix/E-mail/CPF), Valor ou ID..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-transparent rounded-lg focus:border-gray-200 focus:ring-0 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                </div>

                <div class="hidden xl:block w-px h-6 bg-gray-200"></div>

                <!-- FILTROS PRINCIPAIS -->
                <div class="flex flex-wrap items-center gap-2 w-full xl:w-auto">
                    <!-- COMPETÊNCIA -->
                    <select
                        wire:model.live="filtroCompetencia"
                        class="text-sm bg-white border-gray-200 rounded-lg px-7 py-2 focus:ring-0 cursor-pointer hover:bg-gray-50"
                    >
                        <option value="todos">Qualquer Data</option>
                        <option value="hoje">Hoje</option>
                        <option value="ontem">Ontem</option>
                        <option value="semana">Semana</option>
                        <option value="mes">Mês</option>
                        <option value="custom">Período Customizado</option>
                    </select>

                    <!-- BLOCO DINÂMICO DE DATAS -->
                    <div class="flex items-center gap-2">
                        <!-- HOJE / ONTEM -->
                        @if(in_array($filtroCompetencia, ['hoje', 'ontem']))
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    wire:click="diaAnterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white"
                                >
                                    &larr;
                                </button>
                                <span class="text-sm text-gray-600 px-2 font-medium">
                                    {{ $labelDiaEspecifico ?? 'Hoje' }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="diaPosterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white"
                                >
                                    &rarr;
                                </button>
                            </div>
                        @endif

                        <!-- SEMANA -->
                        @if($filtroCompetencia === 'semana')
                            <span class="text-sm text-gray-600 px-2 font-medium">
                                {{ $labelCompetencia ?? 'Semana atual' }}
                            </span>
                        @endif

                        <!-- MÊS -->
                        @if($filtroCompetencia === 'mes')
                            <div class="flex items-center gap-1">
                                <button wire:click="mesAnterior" class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white">
                                    &larr;
                                </button>
                                <span class="text-sm text-gray-600 px-2 font-medium">
                                    {{ $labelMesAno ?? 'Agosto / 2026' }}
                                </span>
                                <button wire:click="mesPosterior" class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white">
                                    &rarr;
                                </button>
                            </div>
                        @endif

                        <!-- RANGE CUSTOM -->
                        @if($filtroCompetencia === 'custom')
                            <div class="flex items-center gap-2">
                                <input
                                    type="date"
                                    wire:model.live="dataInicioRange"
                                    class="border border-gray-200 rounded px-2 py-1 text-sm focus:ring-[#313e50] focus:border-[#313e50]"
                                >
                                <span class="text-gray-400 text-xs">até</span>
                                <input
                                    type="date"
                                    wire:model.live="dataFimRange"
                                    class="border border-gray-200 rounded px-2 py-1 text-sm focus:ring-[#313e50] focus:border-[#313e50]"
                                >
                            </div>
                        @endif
                    </div>

                    <div class="hidden md:block w-px h-4 bg-gray-200"></div>

                    <!-- BOTÃO FILTROS AVANÇADOS -->
                    <button
                        type="button"
                        @click="mostrarFiltrosAvancados = !mostrarFiltrosAvancados"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition"
                        :class="mostrarFiltrosAvancados ? 'bg-[#313e50] text-white' : 'text-gray-600 hover:bg-gray-50 bg-white border border-transparent'"
                    >
                        Filtros
                    </button>
                    
                    <!-- BOTÃO LIMPAR -->
                    <button
                        type="button"
                        wire:click="limparFiltros"
                        class="px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"
                        title="Limpar todos os filtros"
                    >
                        Limpar
                    </button>
                </div>
            </div>

            <!-- FILTROS AVANÇADOS -->
            <div 
                x-show="mostrarFiltrosAvancados"
                x-collapse
                class="border-t border-gray-100 bg-gray-50/50 p-4"
                style="display:none;"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5 block">Filtrar Data Por</label>
                        <select wire:model.live="tipoFiltroData" class="w-full border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                            <option value="solicitacao">Data da Solicitação</option>
                            <option value="pagamento">Data do Pagamento</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        wire:click="limparFiltros"
                        class="text-xs text-red-600 hover:text-red-700 font-medium transition-colors"
                    >
                        Resetar filtros avançados
                    </button>
                </div>
            </div>
        </div>

        <!-- Área de Listagem e Resumo Integrados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
            
            <!-- Overlay de carregamento sobre a listagem -->
            <div wire:loading class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center"></div>

            <!-- Header da Listagem (Resumo) -->
            @php $totalRegistros = method_exists($pagamentos, 'total') ? $pagamentos->total() : $pagamentos->count(); @endphp
            @if($totalRegistros > 0)
                <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Total Não Conciliado</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $totalRegistros }}</p>
                        </div>
                        <div class="w-px h-8 bg-gray-300 hidden md:block"></div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Soma Total</p>
                            <p class="text-xl font-semibold text-[#313e50]">
                                R$ {{ number_format($pagamentos->sum('valor'), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Listagem Responsiva (Cards) -->
            <div class="p-4 bg-gray-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($pagamentos as $pagamento)
                        <!-- Card do Pagamento -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between gap-4 group relative overflow-hidden">

                            <!-- Cabeçalho do Card: Tipo / Status e ID -->
                            <div class="flex justify-between items-start border-b border-gray-50 pb-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $pagamento->tipo ?? 'PIX' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        Não Conciliado
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-gray-900 block">
                                        #{{ str_pad($pagamento->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Corpo do Card: Identificador, Conta e Detalhes -->
                            <div class="flex-1 space-y-2">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Identificador / Chave</p>
                                    <h4 class="text-sm font-semibold text-gray-900 break-all line-clamp-2" title="{{ $pagamento->identificador }}">
                                        {{ $pagamento->identificador ?? 'Não Informado' }}
                                    </h4>
                                </div>

                                <div>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Conta Origem</p>
                                    <p class="text-xs text-gray-600 line-clamp-1" title="{{ $pagamento->conta->banco->nome ?? 'Banco' }} - Ag: {{ $pagamento->conta->agencia ?? '' }} / Cc: {{ $pagamento->conta->conta ?? '' }}">
                                        {{ $pagamento->conta->banco->nome ?? 'Banco' }} - Ag: {{ $pagamento->conta->agencia ?? '--' }} / Cc: {{ $pagamento->conta->conta ?? '--' }}
                                    </p>
                                </div>

                                @if(!empty($pagamento->end_to_end_id))
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">End To End ID</p>
                                        <p class="text-[11px] font-mono text-gray-500 truncate" title="{{ $pagamento->end_to_end_id }}">
                                            {{ $pagamento->end_to_end_id }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Datas e Valor -->
                            <div class="pt-3 border-t border-gray-100 flex justify-between items-end">
                                <div class="space-y-1">
                                    <div class="text-[10px] text-gray-500">
                                        <span>Solicitado: </span>
                                        <span class="font-medium text-gray-700">
                                            {{ isset($pagamento->data_solicitacao) ? date('d/m/Y H:i', strtotime($pagamento->data_solicitacao)) : '--/--/----' }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-emerald-600 font-medium">
                                        <span>Pago: </span>
                                        <span>
                                            {{ isset($pagamento->data_pagamento) ? date('d/m/Y H:i', strtotime($pagamento->data_pagamento)) : '--/--/----' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Valor</p>
                                    <p class="text-base font-bold text-gray-900">
                                        R$ {{ number_format($pagamento->valor ?? 0, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Rodapé de Ações: Comprovante & Conciliar -->
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 bg-gray-50/50 -mx-4 -mb-4 p-3">
                                <!-- Download Comprovante -->
                                @if(!empty($pagamento->comprovante_path))
                                    <button
                                        type="button"
                                        wire:click="downloadComprovante({{ $pagamento->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        title="Baixar Comprovante"
                                    >
                                        Comprovante
                                    </button>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Sem comprovante</span>
                                @endif

                                <!-- Botão Conciliar -->
                                <button
                                    type="button"
                                    wire:click="abrirModalConciliacao({{ $pagamento->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-[#313e50] hover:bg-[#252f3d] rounded-lg shadow-sm transition-colors focus:outline-none cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Conciliar
                                </button>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white border border-dashed border-gray-200 rounded-xl">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">Nenhum pagamento sem conciliação encontrado para os filtros aplicados.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Footer Informativo / Paginação -->
            @if(method_exists($pagamentos, 'hasPages'))
                <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4 bg-white">
                    <p>
                        Mostrando <span class="font-medium text-gray-900">{{ $pagamentos->firstItem() ?? 0 }}</span> 
                        a <span class="font-medium text-gray-900">{{ $pagamentos->lastItem() ?? 0 }}</span> 
                        de <span class="font-medium text-gray-900">{{ $pagamentos->total() }}</span> registros
                    </p>
                    
                    <div class="flex gap-2">
                        <button 
                            @if($pagamentos->onFirstPage()) disabled @else wire:click="previousPage" @endif
                            class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $pagamentos->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                        >
                            Anterior
                        </button>

                        <button 
                            @if(!$pagamentos->hasMorePages()) disabled @else wire:click="nextPage" @endif
                            class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$pagamentos->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                        >
                            Próximo
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if($pagamento_selecionado_id && $openModalConciliacao)
        <livewire:Modais.ContasPagar.ConciliacaoPagamento
            :solicitacao-id="$pagamento_selecionado_id" 
            wire:key="modal-conciliacao-{{ $pagamento_selecionado_id }}" 
        />
    @endif
</div>