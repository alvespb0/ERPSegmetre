<div x-data="{ mostrarFiltrosAvancados: false }">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Contas a Pagar
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Solicitações de Pagamento</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie, filtre e aprove as solicitações de pagamento da empresa.
                </p>
            </div>
            
            <!-- Loading State & Actions -->
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
            <!-- HEADER -->
            <div class="p-2 flex flex-col xl:flex-row items-center gap-2">
                <!-- SEARCH -->
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por ID, Beneficiário, CPF/CNPJ..."
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
                                    ←
                                </button>
                                <span class="text-sm text-gray-600 px-2 font-medium">
                                    {{ $labelDiaEspecifico ?? 'Hoje' }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="diaPosterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white"
                                >
                                    →
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
                                    ←
                                </button>
                                <span class="text-sm text-gray-600 px-2 font-medium">
                                    {{ $labelMesAno ?? 'Março / 2026' }}
                                </span>
                                <button wire:click="mesPosterior" class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50 bg-white">
                                    →
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

                    <!-- STATUS -->
                    <select
                        wire:model.live="status"
                        class="text-sm bg-white border-gray-200 rounded-lg pl-3 pr-8 py-2 focus:ring-0 cursor-pointer hover:bg-gray-50"
                    >
                        <option value="">Todos Status</option>
                        <option value="pendente">Pendente</option>
                        <option value="pago">Pago</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="recusado">Recusado</option>
                    </select>

                    <!-- AVANÇADO -->
                    <button
                        type="button"
                        @click="mostrarFiltrosAvancados = !mostrarFiltrosAvancados"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition"
                        :class="mostrarFiltrosAvancados ? 'bg-[#313e50] text-white' : 'text-gray-600 hover:bg-gray-50 bg-white border border-transparent'"
                    >
                        Filtros
                    </button>
                    
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
                        <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5 block">Aplicar Data Por</label>
                        <select wire:model.live="tipoFiltroData" class="w-full border-gray-200 bg-white rounded-lg px-3 py-2 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                            <option value="vencimento">Data de Vencimento</option>
                            <option value="solicitacao">Data da Solicitação</option>
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
            @if($solicitacoes->total() > 0)
                <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Total Encontrado</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $solicitacoes->total() }}</p>
                        </div>
                        <div class="w-px h-8 bg-gray-300 hidden md:block"></div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Soma na Página</p>
                            <p class="text-xl font-semibold text-[#313e50]">
                                R$ {{ number_format($solicitacoes->sum('valor'), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Listagem Responsiva (Cards) -->
            <div class="p-4 bg-gray-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($solicitacoes as $solicitacao)
                        @php
                            $statusColors = [
                                'aprovado'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'pendente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                'rejeitado' => 'bg-red-50 text-red-700 border-red-200',
                                'cancelado' => 'bg-gray-50 text-gray-600 border-gray-200',
                                'recusado'  => 'bg-red-50 text-red-700 border-red-200',
                                'pago'      => 'bg-blue-50 text-blue-700 border-blue-200',
                            ];
                            $statusStr = strtolower($solicitacao['status'] ?? 'pendente');
                            $corStatus = $statusColors[$statusStr] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            
                            $isVencido = isset($solicitacao->parcela->data_vencimento) && \Carbon\Carbon::parse($solicitacao->parcela->data_vencimento)->isPast() && $statusStr !== 'pago';
                        @endphp

                        <!-- Card Clickable -->
                        <div 
                            wire:click="abrirDetalhes({{ $solicitacao['id'] }})"
                            class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md hover:border-[#313e50]/40 transition-all cursor-pointer flex flex-col justify-between gap-4 group relative overflow-hidden"
                        >

                            <!-- Cabeçalho do Card: Status e ID/Solicitante -->
                            <div class="flex justify-between items-start border-b border-gray-50 pb-3">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border uppercase tracking-wider {{ $corStatus }}">
                                        {{ $solicitacao['status'] ?? 'Pendente' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-gray-900 block">
                                        #{{ str_pad($solicitacao['id'], 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-[10px] font-medium text-gray-400 block mt-0.5" title="Solicitante">
                                        {{ $solicitacao['solicitante'] ?? 'Não informado' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Corpo do Card: Beneficiário e Categoria -->
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-[#313e50] transition-colors line-clamp-2" title="{{ $solicitacao->parcela->titulo->entidade->razao_social ?? 'Não informado' }}">
                                    {{ $solicitacao->parcela->titulo->entidade->razao_social ?? $solicitacao->parcela->titulo->entidade->nome_fantasia ?? 'Beneficiário Não Informado' }}
                                </h4>
                                <p class="text-[11px] text-gray-500 mt-1 line-clamp-1">
                                    {{ $solicitacao->parcela->titulo->categoriaFinanceira->nome ?? 'Sem Categoria' }}
                                </p>
                            </div>

                            <!-- Rodapé do Card: Datas e Valor -->
                            <div class="flex justify-between items-end pt-3 mt-1 bg-gray-50/50 -mx-4 -mb-4 p-4 border-t border-gray-50">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-1.5" title="Vencimento">
                                        <span class="text-[11px] font-medium {{ $isVencido ? 'text-red-600' : 'text-gray-600' }}">
                                            Vencimento: {{ isset($solicitacao->parcela->data_vencimento) ? date('d/m/Y', strtotime($solicitacao->parcela->data_vencimento)) : '--/--/----' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5" title="Data da Solicitação">
                                        <span class="text-[10px] text-gray-400">
                                            Solicitação: {{ isset($solicitacao->data_solicitacao) ? date('d/m/Y', strtotime($solicitacao->data_solicitacao)) : '--/--/----' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Valor (R$)</p>
                                    <p class="text-base font-bold text-gray-900">
                                        {{ number_format($solicitacao->valor ?? 0, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white border border-dashed border-gray-200 rounded-xl">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm text-gray-500">Nenhuma solicitação encontrada para os filtros aplicados.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Footer Informativo de Paginação -->
            <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4 bg-white">
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ $solicitacoes->firstItem() ?? 0 }}</span> 
                    a <span class="font-medium text-gray-900">{{ $solicitacoes->lastItem() ?? 0 }}</span> 
                    de <span class="font-medium text-gray-900">{{ $solicitacoes->total() }}</span> registros
                </p>
                
                <div class="flex gap-2">
                    <button 
                        @if($solicitacoes->onFirstPage()) disabled @else wire:click="previousPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $solicitacoes->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Anterior
                    </button>

                    <button 
                        @if(!$solicitacoes->hasMorePages()) disabled @else wire:click="nextPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$solicitacoes->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @if($solicitacao_id && $openModalPagamento)
        <livewire:Modais.ContasPagar.PagamentosSolicitados
            :solicitacao-id="$solicitacao_id" 
            wire:key="modal-pagamento-{{ $solicitacao_id }}" 
        />
    @endif
</div>