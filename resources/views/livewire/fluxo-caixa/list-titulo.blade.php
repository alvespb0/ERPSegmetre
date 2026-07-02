<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                Financeiro &middot; Fluxo de Caixa
            </p>
            <h1 class="text-2xl font-semibold text-gray-900">Fluxo de Caixa</h1>
            <p class="text-sm text-gray-500 mt-1">
                Visão consolidada de recebimentos e pagamentos no período.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                wire:click="exportar"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exportar 
                @if(count($selecionados ?? []) > 0)
                    <span class="bg-gray-100 text-gray-700 py-0.5 px-2 rounded-full text-xs font-bold">
                        {{ count($selecionados) }}
                    </span>
                @endif
            </button>
            <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Atualizar
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all">
            <p class="text-xs text-gray-400 uppercase">Entradas</p>
            <p class="text-xl font-semibold text-gray-900 mt-1">
                R$ {{ number_format(
                    $recebidos,
                    2, ',', '.'
                ) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Recebimentos Confirmados</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all">
            <p class="text-xs text-gray-400 uppercase">Saídas</p>
            <p class="text-xl font-semibold text-gray-900 mt-1">
                R$ {{ number_format(
                    $pagos,
                    2, ',', '.'
                ) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Pagamentos Confirmados</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all">
            <p class="text-xs text-gray-400 uppercase">Saldo (mês)</p>
            <p class="text-xl font-semibold text-gray-900 mt-1">
                R$ {{ number_format(
                    $recebidos - $pagos,
                    2, ',', '.'
                ) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Entradas - Saídas</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all">
            <p class="text-xs text-gray-400 uppercase">Previsto (próx. dias)</p>
            <p class="text-xl font-semibold text-gray-900 mt-1">
                R$ 
            </p>
            <p class="text-xs text-gray-400 mt-1">Projeção Líquida</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Fluxo no período</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Recebimentos x Pagamentos (dados ilustrativos).
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500/70"></span>
                        Recebimentos
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-rose-500/70"></span>
                        Pagamentos
                    </span>
                </div>
            </div>
        </div>

        <div
            class="p-4 sm:p-6"
            x-data="{
                chart: null,
                init() {
                    const render = () => {
                        const el = this.$refs.chart;
                        if (!el || typeof ApexCharts === 'undefined') return;
                        
                        // Garante que gráficos anteriores sejam destruídos antes de recriar
                        if (this.chart) { this.chart.destroy(); this.chart = null; }

                        this.chart = new ApexCharts(el, {
                            chart: {
                                type: 'area',
                                height: 300,
                                toolbar: { show: true },
                                zoom: { enabled: false },
                                fontFamily: 'inherit',
                            },
                            colors: ['#10b981', '#f43f5e'],
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 2 },
                            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.18, opacityTo: 0.02, stops: [0, 90, 100] } },
                            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                            xaxis: {
                                categories: $wire.chartLabels, // Usando $wire para pegar o estado atual
                                labels: { style: { colors: '#6b7280', fontSize: '11px' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                            },
                            yaxis: {
                                labels: {
                                    style: { colors: '#6b7280', fontSize: '11px' },
                                    formatter: (v) => 'R$ ' + Math.round(v).toLocaleString('pt-BR'),
                                },
                            },
                            tooltip: {
                                theme: 'light',
                                y: { formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) },
                            },
                            legend: { show: true },
                            series: [
                                { name: 'Recebimentos', data: $wire.chartRecebimentos },
                                { name: 'Pagamentos', data: $wire.chartPagamentos },
                            ],
                        });

                        this.chart.render();

                        $wire.$watch('chartLabels', () => {
                            if (this.chart) {
                                this.chart.updateOptions({ xaxis: { categories: $wire.chartLabels } });
                                this.chart.updateSeries([
                                    { name: 'Recebimentos', data: $wire.chartRecebimentos },
                                    { name: 'Pagamentos', data: $wire.chartPagamentos },
                                ]);
                            }
                        });
                    };

                    const ensureApexAndRender = () => {
                        if (typeof ApexCharts !== 'undefined') {
                            render();
                            return;
                        }

                        const existing = document.getElementById('apexcharts-cdn');
                        if (existing) {
                            const timer = setInterval(() => {
                                if (typeof ApexCharts !== 'undefined') {
                                    clearInterval(timer);
                                    render();
                                }
                            }, 100);
                            setTimeout(() => clearInterval(timer), 3000);
                            return;
                        }

                        const s = document.createElement('script');
                        s.id = 'apexcharts-cdn';
                        s.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                        s.onload = () => render();
                        document.head.appendChild(s);
                    };

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', ensureApexAndRender, { once: true });
                    } else {
                        ensureApexAndRender();
                    }
                }
            }"
        >
            <div wire:ignore>
                <div x-ref="chart" class="w-full"></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-2 flex flex-col lg:flex-row items-center gap-2">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Buscar por entidade, descrição ou documento..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-transparent rounded-lg focus:border-gray-200 focus:ring-0"
                >
            </div>

            <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

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

                <select wire:model.live="tipoTitulo" class="text-sm border-gray-200 rounded-lg px-7 py-2 focus:ring-0">
                    <option value="todos">Tipo: Todos</option>
                    <option value="receita">Receita</option>
                    <option value="despesa">Despesa</option>
                </select>

                <select wire:model.live="statusCalculadoParcela" class="text-sm border-gray-200 rounded-lg px-30 py-2 focus:ring-0">
                    <option value="todos">Status: Todos</option>
                    <option value="aberto">Em aberto</option>
                    <option value="atrasado">Atrasados</option>
                    <option value="pago">Pagos</option>
                    <option value="parcial">Parcial</option>
                </select>

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

        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left w-10"></th>
                        <th class="px-4 py-3 text-left">Data</th>
                        <th class="px-4 py-3 text-left w-1/3">Título</th>
                        <th class="px-4 py-3 text-left">Entidade</th>
                        <th class="px-4 py-3 text-center">Tipo</th>
                        <th class="px-4 py-3 text-right">Valor (R$)</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($parcelas as $parcela)
                        @php
                            $tipoColor = $tipoColors[$parcela->titulo->tipo] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            $statusColor = $statusColors[$parcela->status_calculado] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            $tipoLabel = $parcela->titulo->tipo === 'receber' ? 'Receita' : 'Despesa';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ in_array($parcela->id, $selecionados ?? []) ? 'bg-blue-50/50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    value="{{ $parcela->id }}" 
                                    wire:model.live="selecionados"
                                    class="rounded border-gray-300 text-[#313e50] shadow-sm focus:ring-[#313e50] focus:ring-opacity-50"
                                >
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y')}}</span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-medium truncate max-w-[320px]" title="{{ $parcela->titulo->descricao }}">
                                        {{ $parcela->titulo->descricao }}
                                    </span>
                                    <span class="text-gray-400 text-[11px] mt-0.5">
                                        Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-gray-700 font-medium truncate max-w-[220px] block" title="{{ $parcela->titulo->entidade->razao_social }}">
                                    {{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'Sem entidade' }}
                                </span>
                                <span class="text-gray-400 text-sm block">
                                    {{ $parcela->titulo->entidade->cpf_cnpj ?? 'CNPJ não informado' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $tipoColor }}">
                                    {{ $tipoLabel }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                {{ number_format($parcela->valor, 2, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $statusColor }}">
                                    {{ ucfirst($parcela->status_calculado) }}
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
                                            <button
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                wire:click="anexosParcela({{ $parcela->id }})"
                                            >
                                                Anexos
                                            </button>

                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                Nenhum título para exibir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $paginator = $titulos ?? $parcelas ?? null;
        @endphp

        <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            @if($paginator && method_exists($paginator, 'total'))
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
                    a <span class="font-medium text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
                    de <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> registros
                </p>
                <div class="flex gap-2">
                    <button
                        @if($paginator->onFirstPage()) disabled @else wire:click="previousPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $paginator->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Anterior
                    </button>

                    <button
                        @if(!$paginator->hasMorePages()) disabled @else wire:click="nextPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$paginator->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Próximo
                    </button>
                </div>
            @else
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ is_countable($titulosFake ?? []) ? count($titulosFake) : 0 }}</span> registros
                </p>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors text-gray-400 bg-gray-50 cursor-not-allowed" disabled>
                        Anterior
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors text-gray-700 bg-white hover:bg-gray-50" disabled>
                        Próximo
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if($openModalDetalhesParcela && $parcelaSelecionada)
        @if($parcelaSelecionada->titulo->tipo == 'receber')
            <livewire:Modais.ContasReceber.DetalhesParcela 
                :parcela-id="$parcelaSelecionada->id" 
                wire:key="modal-detalhes-{{ $parcelaSelecionada->id }}" 
                @fechar-modal.camel="$set('openModalDetalhesParcela', false)" 
            />
        @else
            <livewire:Modais.ContasPagar.DetalhesParcela 
                :parcela-id="$parcelaSelecionada->id" 
                wire:key="modal-detalhes-{{ $parcelaSelecionada->id }}" 
                @fechar-modal.camel="$set('openModalDetalhesParcela', false)" 
            />
        @endif
    @endif

    @if($openModalDetalhesTitulo && $tituloSelecionado)
        @if($tituloSelecionado->tipo == 'receber')
            <livewire:Modais.ContasReceber.DetalhesTitulo 
                :titulo-id="$tituloSelecionado->id" 
                wire:key="modal-detalhes-titulo-{{ $tituloSelecionado->id }}" 
                @fechar-modal.camel="$set('openModalDetalhesTitulo', false)" 
            />
        @else
            <livewire:Modais.ContasPagar.DetalhesTitulo 
                :titulo-id="$tituloSelecionado->id" 
                wire:key="modal-detalhes-titulo-{{ $tituloSelecionado->id }}" 
                @fechar-modal.camel="$set('openModalDetalhesTitulo', false)" 
            />
        @endif
    @endif

    @if($parcelaParaAnexos && $openModalAnexos)
        @if($parcelaParaAnexos->titulo->tipo == 'receber')
            <livewire:Modais.ContasReceber.Anexos
                :parcela-id="$parcelaParaAnexos->id" 
                wire:key="modal-receber-{{ $parcelaParaAnexos->id }}" 
            />
        @else
            <livewire:Modais.ContasPagar.Anexos
                :parcela-id="$parcelaParaAnexos->id" 
                wire:key="modal-receber-{{ $parcelaParaAnexos->id }}" 
            />
        @endif
    @endif

</div>
