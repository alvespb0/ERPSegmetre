@php
    $cards = [
        [
            'label' => 'Entradas (mês)',
            'valor' => 128450.35,
            'hint'  => 'Recebimentos confirmados',
        ],
        [
            'label' => 'Saídas (mês)',
            'valor' => 94620.10,
            'hint'  => 'Pagamentos confirmados',
        ],
        [
            'label' => 'Saldo (mês)',
            'valor' => 33830.25,
            'hint'  => 'Entradas - Saídas',
        ],
        [
            'label' => 'Previsto (próx. 7 dias)',
            'valor' => 17890.00,
            'hint'  => 'Projeção líquida',
        ],
    ];

    $chartLabels = ['01/04', '02/04', '03/04', '04/04', '05/04', '06/04', '07/04', '08/04', '09/04', '10/04', '11/04', '12/04'];
    $chartRecebimentos = [5400, 8200, 3100, 9100, 4800, 12000, 7600, 6600, 9800, 4300, 8700, 10500];
    $chartPagamentos   = [4200, 6100, 5200, 7300, 3900, 8800, 6400, 7100, 5600, 6200, 6900, 7400];

    $titulosFake = [
        [
            'data' => '07/04/2026',
            'descricao' => 'Mensalidade - Plano Premium',
            'entidade' => 'Clínica São Lucas',
            'documento' => '12.345.678/0001-90',
            'tipo' => 'receita',
            'status' => 'pago',
            'valor' => 3200.00,
        ],
        [
            'data' => '07/04/2026',
            'descricao' => 'Fornecedor - Material de limpeza',
            'entidade' => 'Higiene Brasil Ltda',
            'documento' => '45.987.123/0001-10',
            'tipo' => 'despesa',
            'status' => 'aberto',
            'valor' => 860.50,
        ],
        [
            'data' => '06/04/2026',
            'descricao' => 'Convênio - Repasse',
            'entidade' => 'Saúde Mais',
            'documento' => '09.111.222/0001-33',
            'tipo' => 'receita',
            'status' => 'parcial',
            'valor' => 12450.00,
        ],
        [
            'data' => '05/04/2026',
            'descricao' => 'Aluguel - Unidade Centro',
            'entidade' => 'Imóveis Central',
            'documento' => '33.444.555/0001-77',
            'tipo' => 'despesa',
            'status' => 'pago',
            'valor' => 5800.00,
        ],
        [
            'data' => '03/04/2026',
            'descricao' => 'Serviços - Contabilidade',
            'entidade' => 'Contábil & Associados',
            'documento' => '22.333.444/0001-55',
            'tipo' => 'despesa',
            'status' => 'atrasado',
            'valor' => 1350.00,
        ],
        [
            'data' => '02/04/2026',
            'descricao' => 'Pacote de consultas',
            'entidade' => 'Paciente Particular',
            'documento' => 'CPF não informado',
            'tipo' => 'receita',
            'status' => 'aberto',
            'valor' => 420.00,
        ],
    ];

    $statusColors = [
        'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
        'pago' => 'bg-green-50 text-green-700 border-green-200',
        'atrasado' => 'bg-red-50 text-red-700 border-red-200',
        'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    ];

    $tipoColors = [
        'receita' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'despesa' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];
@endphp

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
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors"
            >
                Exportar
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
        @foreach($cards as $card)
            <div class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all">
                <p class="text-xs text-gray-400 uppercase">{{ $card['label'] }}</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{ number_format($card['valor'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $card['hint'] }}</p>
            </div>
        @endforeach
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
                        if (this.chart) { this.chart.destroy(); this.chart = null; }

                        this.chart = new ApexCharts(el, {
                            chart: {
                                type: 'area',
                                height: 300,
                                toolbar: { show: false },
                                zoom: { enabled: false },
                                fontFamily: 'inherit',
                            },
                            colors: ['#10b981', '#f43f5e'],
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 2 },
                            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.18, opacityTo: 0.02, stops: [0, 90, 100] } },
                            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                            xaxis: {
                                categories: @js($chartLabels),
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
                            legend: { show: false },
                            series: [
                                { name: 'Recebimentos', data: @js($chartRecebimentos) },
                                { name: 'Pagamentos', data: @js($chartPagamentos) },
                            ],
                        });

                        this.chart.render();
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
                    placeholder="Buscar por entidade, descrição ou documento..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-transparent rounded-lg focus:border-gray-200 focus:ring-0"
                >
            </div>

            <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <select class="text-sm border-gray-200 rounded-lg px-7 py-2 focus:ring-0">
                    <option>Competência: Abril/2026</option>
                    <option>Competência: Março/2026</option>
                    <option>Competência: Fevereiro/2026</option>
                </select>

                <select class="text-sm border-gray-200 rounded-lg px-7 py-2 focus:ring-0">
                    <option value="todos">Tipo: Todos</option>
                    <option value="receita">Receita</option>
                    <option value="despesa">Despesa</option>
                </select>

                <select class="text-sm border-gray-200 rounded-lg px-30 py-2 focus:ring-0">
                    <option value="todos">Status: Todos</option>
                    <option value="aberto">Em aberto</option>
                    <option value="atrasado">Atrasados</option>
                    <option value="pago">Pagos</option>
                    <option value="parcial">Parcial</option>
                </select>
            </div>
        </div>

        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
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
                    @forelse($titulosFake as $row)
                        @php
                            $tipoColor = $tipoColors[$row['tipo']] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            $statusColor = $statusColors[$row['status']] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            $tipoLabel = $row['tipo'] === 'receita' ? 'Receita' : 'Despesa';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-gray-900 font-medium">{{ $row['data'] }}</span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-medium truncate max-w-[320px]" title="{{ $row['descricao'] }}">
                                        {{ $row['descricao'] }}
                                    </span>
                                    <span class="text-gray-400 text-[11px] mt-0.5">
                                        Lançamento ilustrativo
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-gray-700 font-medium truncate max-w-[220px] block" title="{{ $row['entidade'] }}">
                                    {{ $row['entidade'] }}
                                </span>
                                <span class="text-gray-400 text-sm block">
                                    {{ $row['documento'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $tipoColor }}">
                                    {{ $tipoLabel }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                {{ number_format($row['valor'], 2, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $statusColor }}">
                                    {{ ucfirst($row['status']) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                >
                                    Detalhes
                                </button>
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

        <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <p>
                Mostrando <span class="font-medium text-gray-900">{{ count($titulosFake) }}</span> registros (fake)
            </p>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors text-gray-400 bg-gray-50 cursor-not-allowed" disabled>
                    Anterior
                </button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors text-gray-700 bg-white hover:bg-gray-50">
                    Próximo
                </button>
            </div>
        </div>
    </div>

</div>
