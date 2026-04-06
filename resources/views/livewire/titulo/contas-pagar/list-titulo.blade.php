<div x-data="{ mostrarFiltrosAvancados: false }">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Despesas
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Contas a Pagar</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gestão de parcelas e pagamento de seus fornecedores.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('erp.despesa.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors"
                >
                    Nova Despesa
                </a>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Exportar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button
                wire:click="filtrarPorCard('atrasado')"
                class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all"
            >
                <p class="text-xs text-gray-400 uppercase">Vencidos</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{ number_format($parcelas->filter(fn($p) =>$p->status !== 'cancelado' && $p->data_vencimento < now()->startOfDay())->sum('valor'), 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Total do Filtro: R$ {{ number_format($vencidos, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Requer atenção</p>
            </button>

            <button
                wire:click="filtrarPorCard('hoje')"
                class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all"
            >
                <p class="text-xs text-gray-400 uppercase">Hoje</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{ number_format($parcelas->filter(fn($p) => $p->status !== 'cancelado' && $p->data_vencimento === today()->toDateString())->sum('valor'), 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Total do Filtro: R$ {{ number_format($venceHoje, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Vencimentos do dia</p>
            </button>
            <button
                wire:click="filtrarPorCard('aberto')"
                class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all"
            >
                <p class="text-xs text-gray-400 uppercase">Em aberto</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{
                        number_format(
                            $parcelas
                                ->filter(fn($p) =>
                                    $p->status !== 'cancelado' &&
                                    $p->data_vencimento >= now()->startOfDay() &&
                                    $p->valor_pago < $p->valor
                                )
                                ->sum(fn($p) => $p->valor - $p->valor_pago),
                            2, ',', '.'
                        )
                    }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Total do Filtro: R$ {{ number_format($abertos, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Projeção</p>
            </button>
            <button
                wire:click="filtrarPorCard('pago')"
                class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all"
            >
                <p class="text-xs text-gray-400 uppercase">Pagos</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{ number_format($parcelas->sum('valor_pago'), 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Total do Filtro: R$ {{ number_format($pagos, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Período</p>
            </button>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all">

            <!-- HEADER -->
            <div class="p-2 flex flex-col lg:flex-row items-center gap-2">

                <!-- SEARCH -->
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por cliente, descrição ou parcela..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-transparent rounded-lg focus:border-gray-200 focus:ring-0"
                    >
                </div>

                <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

                <!-- FILTROS PRINCIPAIS -->
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">

                    <!-- COMPETÊNCIA -->
                    <select
                        wire:model.live="filtroCompetencia"
                        class="text-sm border-gray-200 rounded-lg px-7 py-2 focus:ring-0"
                    >
                        <option value="todos">Qualquer Vencimento</option>
                        <option value="hoje">Hoje</option>
                        <option value="ontem">Ontem</option>
                        <option value="semana">Semana</option>
                        <option value="mes">Mês</option>
                        <option value="custom">Período Customizado</option>
                    </select>

                    <!-- BLOCO DINÂMICO -->
                    <div class="flex items-center gap-2">

                        <!-- HOJE / ONTEM (navegável futuramente) -->
                        @if(in_array($filtroCompetencia, ['hoje', 'ontem']))
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    wire:click="diaAnterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50"
                                >
                                    ←
                                </button>

                                <span class="text-sm text-gray-600 px-2">
                                    {{ $labelDiaEspecifico ?? 'Hoje' }}
                                </span>

                                <button
                                    type="button"
                                    wire:click="diaPosterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50"
                                >
                                    →
                                </button>
                            </div>
                        @endif

                        <!-- SEMANA -->
                        @if($filtroCompetencia === 'semana')
                            <span class="text-sm text-gray-600 px-2">
                                {{ $labelCompetencia ?? 'Semana atual' }}
                            </span>
                        @endif

                        <!-- MÊS -->
                        @if($filtroCompetencia === 'mes')
                            <div class="flex items-center gap-1">
                                <button wire:click="mesAnterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50">
                                    ←
                                </button>

                                <span class="text-sm text-gray-600 px-2">
                                    {{ $labelMesAno ?? 'Março / 2026' }}
                                </span>

                                <button wire:click="mesPosterior"
                                    class="px-2 py-1 border border-gray-200 rounded hover:bg-gray-50">
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
                                    class="border border-gray-200 rounded px-2 py-1 text-sm"
                                >

                                <span class="text-gray-400 text-xs">até</span>

                                <input
                                    type="date"
                                    wire:model.live="dataFimRange"
                                    class="border border-gray-200 rounded px-2 py-1 text-sm"
                                >
                            </div>
                        @endif

                    </div>

                    <div class="hidden md:block w-px h-4 bg-gray-200"></div>

                    <!-- STATUS -->
                    <select
                        wire:model.live="statusFiltro"
                        class="text-sm border-gray-200 rounded-lg px-30 py-2 focus:ring-0"
                    >
                        <option value="todos">Todos Status</option>
                        <option value="aberto">Em aberto</option>
                        <option value="atrasado">Atrasados</option>
                        <option value="pago">Pagos</option>
                        <option value="parcial">Parcial</option>
                    </select>

                    <!-- AVANÇADO -->
                    <button
                        type="button"
                        @click="mostrarFiltrosAvancados = !mostrarFiltrosAvancados"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition"
                        :class="mostrarFiltrosAvancados ? 'bg-[#313e50] text-white' : 'text-gray-600 hover:bg-gray-50'"
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
                        <label class="text-xs text-gray-600 mb-1 block">Categoria</label>
                        <select wire:model.live="categoriaFiltro"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                            <option value="">Todas</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="text-xs text-gray-600 mb-1 block">Centro de Custo</label>
                        <select wire:model.live="centroCustoFiltro"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                            <option value="">Todos</option>
                            @foreach($centrosCusto as $centro)
                                <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        wire:click="limparFiltros"
                        class="text-xs text-red-600 hover:text-red-700 font-medium"
                    >
                        Limpar filtros
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Vencimento</th>
                            <th class="px-4 py-3 text-left w-1/3">Descrição do Título & Parcela</th>
                            <th class="px-4 py-3 text-left">Cliente / Pagador</th>
                            <th class="px-4 py-3 text-right">Valor (R$)</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($parcelas as $parcela)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium">
                                            {{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}
                                        </span>
                                        @if($parcela->status_calculado === 'atrasado')
                                            <span class="text-[10px] text-red-500 font-medium">
                                                {{ \Carbon\Carbon::parse($parcela->data_vencimento)->diffInDays(now()) }} dias vencidos
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium truncate max-w-[250px]" title="{{ $parcela->titulo->descricao ?? '--' }}">
                                            {{ $parcela->titulo->descricao ?? '--' }}
                                        </span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-gray-500 text-[11px]">
                                                Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count }}
                                            </span>
                                            <span class="text-gray-300 text-[10px]">•</span>
                                            <span class="text-gray-400 text-[10px]" title="ID do Título Pai">
                                                Tít. #{{ $parcela->titulo_financeiro_id }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span 
                                        class="text-gray-700 font-medium truncate max-w-[180px] block" 
                                        title="{{ $parcela->titulo->entidade->razaoSocial ?? $parcela->titulo->entidade->nomeFantasia ?? '--' }}"
                                    >
                                        {{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'Sem entidade' }}
                                    </span>

                                    <span class="text-gray-400 text-sm block">
                                        {{ $parcela->titulo->entidade->cpf_cnpj ?? 'CNPJ não informado' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    {{ number_format($parcela->valor, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @php
                                        // Definindo classes de cores para o status
                                        $statusColors = [
                                            'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'pago' => 'bg-green-50 text-green-700 border-green-200',
                                            'atrasado' => 'bg-red-50 text-red-700 border-red-200',
                                            'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        ];
                                        $color = $statusColors[$parcela->status_calculado] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                        
                                        // Substituição visual se a parcela estiver em aberto mas a data já passou
                                        $displayStatus = $parcela->status_calculado;
                                        if($displayStatus === 'aberto' && \Carbon\Carbon::parse($parcela->data_vencimento)->isPast()) {
                                            $displayStatus = 'atrasado';
                                            $color = $statusColors['atrasado'];
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $color }}">
                                        {{ ucfirst($displayStatus) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right" x-data="{ open: false }">
                                    <button
                                        @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        :class="open ? 'bg-gray-50 ring-2 ring-[#313e50]' : ''"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                        aria-haspopup="menu"
                                        :aria-expanded="open"
                                        id="btn-{{ $parcela->id }}"
                                    >
                                        Ações
                                        <svg class="ml-1.5 w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click="open = false" class="fixed inset-0 z-40"></div>

                                    <template x-teleport="body">
                                        <div
                                            x-show="open" 
                                            @click.away="open = false"
                                            x-anchor.bottom-end="document.getElementById('btn-{{ $parcela->id }}')"
                                            class="z-[100] w-44 bg-white border border-gray-200 rounded-lg shadow-lg"
                                        >
                                            <div class="py-1">
                                                @if($parcela->status !== 'pago')
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 font-medium"
                                                        wire:click="pagarParcela({{ $parcela->id }})"
                                                    >
                                                        Informar Pagamento
                                                    </button>
                                                    <div class="h-px bg-gray-100 my-1"></div>
                                                @endif
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                    wire:click="editarParcela({{ $parcela->id }})"
                                                    @click="open = false"
                                                >
                                                    Editar
                                                </button>
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                    wire:click="editarStatus({{ $parcela->id }})"
                                                    @click="open = false"
                                                >
                                                    Alterar Status 
                                                </button>

                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                    wire:click="detalhesParcela({{ $parcela->id }})"
                                                    @click="open = false"
                                                >
                                                    Detalhes da Parcela
                                                </button>
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                    wire:click="verDetalhesTitulo({{ $parcela->titulo_financeiro_id }})"
                                                >
                                                    Ver Título Completo
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Nenhuma parcela encontrada para os filtros selecionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ $parcelas->firstItem() ?? 0 }}</span> 
                    a <span class="font-medium text-gray-900">{{ $parcelas->lastItem() ?? 0 }}</span> 
                    de <span class="font-medium text-gray-900">{{ $parcelas->total() }}</span> registros
                </p>
                <div class="flex gap-2">
                    <button 
                        @if($parcelas->onFirstPage()) disabled @else wire:click="previousPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $parcelas->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Anterior
                    </button>

                    <button 
                        @if(!$parcelas->hasMorePages()) disabled @else wire:click="nextPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$parcelas->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
    @if($openModalDetalhesParcela && $parcelaSelecionada)
        <livewire:Modais.ContasPagar.DetalhesParcela 
            :parcela-id="$parcelaSelecionada->id" 
            wire:key="modal-detalhes-{{ $parcelaSelecionada->id }}" 
            @fechar-modal.camel="$set('openModalDetalhesParcela', false)" 
        />
    @endif
    @if($openModalDetalhesTitulo && $tituloSelecionado)
        <livewire:Modais.ContasPagar.DetalhesTitulo 
            :titulo-id="$tituloSelecionado->id" 
            wire:key="modal-detalhes-titulo-{{ $tituloSelecionado->id }}" 
            @fechar-modal.camel="$set('openModalDetalhesTitulo', false)" 
        />
    @endif
    @if($openModalPagarParcela && $parcelaParaPagar)
        <livewire:Modais.ContasPagar.PagarParcela 
            :parcela-id="$parcelaParaPagar->id" 
            wire:key="modal-receber-{{ $parcelaParaPagar->id }}" 
        />
    @endif
    @if($openModalEditarParcela && $parcelaParaEditar)
        <livewire:Modais.ContasPagar.EditarParcela 
            :parcela-id="$parcelaParaEditar->id" 
            wire:key="modal-receber-{{ $parcelaParaEditar->id }}" 
        />
    @endif
    @if($parcelaParaEditarStatus && $openModalEditarStatus)
        <livewire:Modais.ContasPagar.EditarStatus
            :parcela-id="$parcelaParaEditarStatus->id" 
            wire:key="modal-receber-{{ $parcelaParaEditarStatus->id }}" 
        />
    @endif

</div>