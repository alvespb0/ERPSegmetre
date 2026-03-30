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
                    R$ {{  number_format($parcelas->filter(fn($p) => $p->status !== 'cancelado' && $p->data_vencimento >= now()->startOfDay())->sum('valor'), 2, ',', '.') }}
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

                                <td class="px-4 py-3 text-right relative overflow-visible" x-data="{ open: false }">
                                    <button
                                        @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        :class="open ? 'bg-gray-50 ring-2 ring-[#313e50]' : ''"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                        aria-haspopup="menu"
                                        :aria-expanded="open"
                                    >
                                        Ações
                                        <svg class="ml-1.5 w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click="open = false" class="fixed inset-0 z-40"></div>

                                    <div
                                        x-show="open"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 z-50 w-44 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg focus:outline-none"
                                        @click.away="open = false"
                                        style="display: none;"
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
        <div 
            x-data="{ show: @entangle('openModalDetalhesParcela') }"
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
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
                @click="show = false"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div 
                    x-show="show" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-3xl border border-gray-100"
                >
                    @php
                        $titulo = $parcelaSelecionada->titulo;
                        
                        $statusColors = [
                            'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'pago' => 'bg-green-50 text-green-700 border-green-200',
                            'atrasado' => 'bg-red-50 text-red-700 border-red-200',
                            'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                        $corStatus = $statusColors[$parcelaSelecionada->status_calculado] ?? $statusColors['aberto'];
                    @endphp

                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                    Parcela {{ $parcelaSelecionada->numero_parcela }}
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $corStatus }}">
                                    {{ ucfirst($parcelaSelecionada->status_calculado) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-1">
                                Ref: Título #{{ $titulo->id ?? '--' }} &middot; {{ $titulo->descricao ?? 'Sem descrição' }}
                            </p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Valor Original</p>
                                <p class="text-xl font-semibold text-gray-900">
                                    R$ {{ number_format($parcelaSelecionada->valor, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-green-600 uppercase font-medium mb-1">Total Pago</p>
                                <p class="text-xl font-semibold text-green-700">
                                    R$ {{ number_format($parcelaSelecionada->valor_pago, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border {{ $parcelaSelecionada->saldo_devedor > 0 ? 'border-red-100' : 'border-gray-100' }} shadow-sm">
                                <p class="text-xs {{ $parcelaSelecionada->saldo_devedor > 0 ? 'text-red-600' : 'text-gray-500' }} uppercase font-medium mb-1">Saldo a Pagar</p>
                                <p class="text-xl font-semibold {{ $parcelaSelecionada->saldo_devedor > 0 ? 'text-red-700' : 'text-gray-900' }}">
                                    R$ {{ number_format($parcelaSelecionada->saldo_devedor, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Informações do Título</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 border-b border-gray-50 pb-3">
                                    <p class="text-gray-500 text-xs mb-0.5">Cliente / Pagador</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $titulo->entidade->razao_social ?? $titulo->entidade->nome_fantasia ?? 'Não informado' }}
                                        <span class="text-gray-400 font-normal ml-1">({{ $titulo->entidade->cpf_cnpj ?? 'S/N' }})</span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Vencimento da Parcela</p>
                                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($parcelaSelecionada->data_vencimento)->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Emissão do Título</p>
                                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($titulo->data_emissao)->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Número NF</p>
                                    <p class="font-medium text-gray-900">{{ $titulo->numero_nf ?? '--' }}</p>
                                </div>

                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Categoria</p>
                                    <p class="font-medium text-gray-900">{{ $titulo->categoriaFinanceira->nome ?? 'Não classificado' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Centro de Custo</p>
                                    <p class="font-medium text-gray-900">{{ $titulo->centroCusto->nome ?? 'Padrão' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Conta</p>
                                    <p class="font-medium text-gray-900">{{ $titulo->conta->descricao ?? 'Não informada' }}</p>
                                </div>

                                @if($titulo->observacoes)
                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-2">
                                        <p class="text-gray-500 text-xs mb-0.5">Observações</p>
                                        <p class="text-gray-700 bg-gray-50 p-3 rounded-lg text-sm">{{ $titulo->observacoes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Histórico de Pagamentos</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $parcelaSelecionada->movimentacoes->count() }}
                                </span>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm text-left">
                                    <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                        <tr>
                                            <th class="px-4 py-2 font-medium">Data</th>
                                            <th class="px-4 py-2 font-medium">Forma de Pagamento</th>
                                            <th class="px-4 py-2 font-medium text-right">Valor Pago</th>
                                            <th class="px-4 py-2 font-medium text-center w-10">Ações</th> </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($parcelaSelecionada->movimentacoes as $movimentacao)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-600">
                                                    {{ \Carbon\Carbon::parse($movimentacao->data_pagamento)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-900">
                                                    {{ $movimentacao->formaPagamento->nome ?? 'Não especificada' }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-green-600">
                                                    R$ {{ number_format($movimentacao->valor_pago, 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button 
                                                        type="button"
                                                        wire:click="excluirMovimentacao({{ $movimentacao->id }})"
                                                        wire:confirm="Tem certeza que deseja excluir/estornar este pagamento? O saldo devedor da parcela será recalculado."
                                                        class="text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-red-50"
                                                        title="Excluir Pagamento"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                                    Nenhum pagamento registrado para esta parcela ainda.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                        <button 
                            type="button" 
                            @click="show = false" 
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Fechar
                        </button>
                        @if(in_array($parcelaSelecionada->status_calculado, ['aberto', 'atrasado', 'parcial']))
                            <button 
                                type="button" 
                                wire:click="pagarParcela({{ $parcelaSelecionada->id }})"
                                @click="show = false"
                                class="px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                            >
                                Informar Pagamento
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($openModalDetalhesTitulo && $tituloSelecionado)
        <div 
            x-data="{ show: @entangle('openModalDetalhesTitulo') }"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="modal-titulo-title" 
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
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
                @click="show = false"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div 
                    x-show="show" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100"
                >
                    @php
                        $statusColorsTitulo = [
                            'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'pago' => 'bg-green-50 text-green-700 border-green-200',
                            'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                        $corStatusTitulo = $statusColorsTitulo[$tituloSelecionado->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                    @endphp

                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-xl font-semibold text-gray-900" id="modal-titulo-title">
                                    Título #{{ $tituloSelecionado->id }}
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $corStatusTitulo }}">
                                    {{ ucfirst($tituloSelecionado->status) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600 tracking-wider">
                                    {{ $tituloSelecionado->tipo === 'receber' ? 'Receita' : 'Despesa' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-1">
                                {{ $tituloSelecionado->descricao ?? 'Sem descrição' }}
                            </p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Valor Total do Título</p>
                                <p class="text-xl font-semibold text-gray-900">
                                    R$ {{ number_format($tituloSelecionado->valor_total, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Emissão</p>
                                <p class="text-xl font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($tituloSelecionado->data_emissao)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Número NF</p>
                                <p class="text-xl font-semibold text-gray-900">
                                    {{ $tituloSelecionado->numero_nf ?? '--' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Informações Gerais</h4>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 border-b border-gray-50 pb-3">
                                    <p class="text-gray-500 text-xs mb-0.5">Entidade (Cliente / Fornecedor)</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $tituloSelecionado->entidade->razao_social ?? $tituloSelecionado->entidade->nome_fantasia ?? 'Não informado' }}
                                        <span class="text-gray-400 font-normal ml-1">({{ $tituloSelecionado->entidade->cpf_cnpj ?? 'S/N' }})</span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Categoria Financeira</p>
                                    <p class="font-medium text-gray-900">{{ $tituloSelecionado->categoriaFinanceira->nome ?? 'Não classificada' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Centro de Custo</p>
                                    <p class="font-medium text-gray-900">{{ $tituloSelecionado->centroCusto->nome ?? 'Padrão' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs mb-0.5">Conta Bancária Origem/Destino</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $tituloSelecionado->conta->nome ?? 'Não informada' }}
                                        @if($tituloSelecionado->conta)
                                            <span class="text-gray-400 text-[11px] block">{{ $tituloSelecionado->conta->banco->nome ?? '' }} Ag {{ $tituloSelecionado->conta->agencia }} Cta {{ $tituloSelecionado->conta->conta }}</span>
                                        @endif
                                    </p>
                                </div>

                                @if($tituloSelecionado->observacoes)
                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-2">
                                        <p class="text-gray-500 text-xs mb-0.5">Observações</p>
                                        <p class="text-gray-700 bg-gray-50 p-3 rounded-lg text-sm whitespace-pre-line">{{ $tituloSelecionado->observacoes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Composição de Parcelas</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $tituloSelecionado->parcelas->count() }}
                                </span>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm text-left">
                                    <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3 font-medium">Nº</th>
                                            <th class="px-4 py-3 font-medium">Vencimento</th>
                                            <th class="px-4 py-3 font-medium text-right">Valor Original</th>
                                            <th class="px-4 py-3 font-medium text-right">Valor Pago</th>
                                            <th class="px-4 py-3 font-medium text-center">Status</th>
                                            <th class="px-4 py-3 font-medium text-right">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($tituloSelecionado->parcelas->sortBy('numero_parcela') as $parc)
                                            @php
                                                $parcStatusColors = [
                                                    'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'pago' => 'bg-green-50 text-green-700 border-green-200',
                                                    'atrasado' => 'bg-red-50 text-red-700 border-red-200',
                                                    'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                    'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                                                ];
                                                $corParcStatus = $parcStatusColors[$parc->status_calculado] ?? $parcStatusColors['aberto'];
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 text-gray-600 font-medium">
                                                    {{ $parc->numero_parcela }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-900">
                                                    {{ \Carbon\Carbon::parse($parc->data_vencimento)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-900">
                                                    R$ {{ number_format($parc->valor, 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-green-600 font-medium">
                                                    R$ {{ number_format($parc->valor_pago, 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $corParcStatus }}">
                                                        {{ ucfirst($parc->status_calculado) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button 
                                                        wire:click="detalhesParcela({{ $parc->id }})"
                                                        @click="show = false"
                                                        class="text-xs text-[#313e50] hover:text-blue-700 font-medium"
                                                    >
                                                        Ver Parcela
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                                    Nenhuma parcela encontrada para este título.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                        <button 
                            type="button" 
                            @click="show = false" 
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($openModalPagarParcela && $parcelaParaPagar)
        <div 
            x-data="{ show: @entangle('openModalPagarParcela') }"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="modal-pagamento-title" 
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
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
                @click="show = false"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div 
                    x-show="show" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-xl border border-gray-100"
                >
                    
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-xl font-semibold text-gray-900" id="modal-pagamento-title">
                                    Informar Pagamento
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                    Parcela {{ $parcelaParaPagar->numero_parcela }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-1">
                                Ref: Título #{{ $parcelaParaPagar->titulo_financeiro_id }}
                            </p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="salvarPagamento">
                        <div class="p-6 space-y-6">
                            
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                                <p class="text-xs text-gray-500 uppercase font-medium">Saldo Devedor</p>
                                <p class="text-xl font-semibold text-gray-900">
                                    R$ {{ number_format($parcelaParaPagar->saldo_devedor, 2, ',', '.') }}
                                </p>
                            </div>

                            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Detalhes da Baixa</h4>
                                </div>
                                <div class="p-4 space-y-4">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="pagamentoData" class="block text-xs text-gray-500 mb-1">
                                                Data do Pagamento <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="date" 
                                                id="pagamentoData" 
                                                wire:model="pagamentoData" 
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoData') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                            >
                                            @error('pagamentoData') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="pagamentoValor" class="block text-xs text-gray-500 mb-1">
                                                Valor Pago (R$) <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                min="0.01"
                                                id="pagamentoValor" 
                                                wire:model="pagamentoValor" 
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoValor') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                            >
                                            @error('pagamentoValor') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="pagamentoFormaId" class="block text-xs text-gray-500 mb-1">
                                            Forma de Pagamento <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="pagamentoFormaId" 
                                            wire:model="pagamentoFormaId" 
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#313e50] focus:ring-1 focus:ring-[#313e50] outline-none transition-all @error('pagamentoFormaId') border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                        >
                                            <option value="">Selecione uma forma...</option>
                                            @foreach($formasPagamento as $forma)
                                                <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('pagamentoFormaId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                            <button 
                                type="button" 
                                @click="show = false" 
                                class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                            >
                                <svg wire:loading wire:target="salvarPagamento" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Confirmar Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>