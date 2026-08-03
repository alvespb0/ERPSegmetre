<div x-data="{ mostrarFiltrosAvancados: false }">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Recebimentos
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Contas a Receber</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gestão de parcelas e recebimentos dos seus clientes.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('erp.receita.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors"
                >
                    Nova Receita
                </a>
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
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button
                wire:click="filtrarPorCard('atrasado')"
                class="bg-white border border-gray-200 rounded-lg p-4 text-left hover:border-gray-300 hover:shadow-sm transition-all"
            >
                <p class="text-xs text-gray-400 uppercase">Vencidos</p>
                <p class="text-xl font-semibold text-gray-900 mt-1">
                    R$ {{ number_format(
                        $parcelas
                            ->filter(fn($p) => $p->status !== 'cancelado' && $p->status_calculado == 'atrasado')
                            ->sum('valor'),
                        2,
                        ',',
                        '.'
                    ) }}
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
                <p class="text-xs text-gray-400 uppercase">Recebidos</p>
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

            <div class="p-2 flex flex-col lg:flex-row items-center gap-2">

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

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">

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

                    <div class="flex items-center gap-2">

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

                        @if($filtroCompetencia === 'semana')
                            <span class="text-sm text-gray-600 px-2">
                                {{ $labelCompetencia ?? 'Semana atual' }}
                            </span>
                        @endif

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

            <div 
                x-show="mostrarFiltrosAvancados"
                x-collapse
                class="border-t border-gray-100 bg-gray-50/50 p-4"
                style="display:none;"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div
                        class="lg:col-span-2"
                        x-data="{
                            open: false,
                            search: '',
                            selected: @entangle('categoriaFiltro').live,
                            items: {{ \Illuminate\Support\Js::from($categorias->map(fn ($c) => ['id' => $c->id, 'nome' => $c->nome])->values()) }},
                            get filteredItems() {
                                const term = this.search.trim().toLowerCase();
                                if (!term) return this.items;
                                return this.items.filter(i => i.nome.toLowerCase().includes(term));
                            },
                            isSelected(id) {
                                return this.selected.includes(id);
                            },
                            toggle(id) {
                                if (this.isSelected(id)) {
                                    this.selected = this.selected.filter(item => item !== id);
                                } else {
                                    this.selected.push(id);
                                }
                            },
                            remove(id) {
                                this.selected = this.selected.filter(item => item !== id);
                            },
                            itemNome(id) {
                                return this.items.find(i => i.id === id)?.nome ?? '';
                            }
                        }"
                        @click.outside="open = false"
                    >
                        <label class="text-xs text-gray-600 mb-1 block">Categoria</label>
                        <div class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex min-h-[2.75rem] w-full items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm shadow-sm transition-colors hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                            >
                                <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5">
                                    <template x-if="selected.length === 0">
                                        <span class="text-gray-400">Todas as categorias</span>
                                    </template>
                                    <template x-for="id in selected" :key="'cat-' + id">
                                        <span class="inline-flex max-w-full items-center gap-1 rounded-md bg-[#313e50]/10 px-2 py-0.5 text-xs font-medium text-[#313e50]">
                                            <span class="truncate" x-text="itemNome(id)"></span>
                                            <button
                                                type="button"
                                                class="rounded p-0.5 text-[#313e50]/60 hover:bg-[#313e50]/20 hover:text-[#313e50]"
                                                @click.stop="remove(id)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition.origin.top.duration.150ms
                                class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
                            >
                                <div class="border-b border-gray-100 p-3">
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                        </svg>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Buscar categoria..."
                                            class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-[#313e50] focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                                        >
                                    </div>
                                </div>

                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <template x-if="filteredItems.length === 0">
                                        <li class="px-4 py-3 text-sm text-gray-400">Nenhuma categoria encontrada.</li>
                                    </template>
                                    <template x-for="item in filteredItems" :key="'cat-opt-' + item.id">
                                        <li>
                                            <button
                                                type="button"
                                                @click="toggle(item.id)"
                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors hover:bg-gray-50"
                                                :class="isSelected(item.id) ? 'bg-[#313e50]/5 text-[#313e50]' : 'text-gray-700'"
                                            >
                                                <span
                                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-colors"
                                                    :class="isSelected(item.id) ? 'border-[#313e50] bg-[#313e50] text-white' : 'border-gray-300 bg-white'"
                                                >
                                                    <svg x-show="isSelected(item.id)" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                                <span class="truncate" x-text="item.nome"></span>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div
                        class="lg:col-span-2"
                        x-data="{
                            open: false,
                            search: '',
                            selected: @entangle('centroCustoFiltro').live,
                            items: {{ \Illuminate\Support\Js::from($centrosCusto->map(fn ($c) => ['id' => $c->id, 'nome' => $c->nome])->values()) }},
                            get filteredItems() {
                                const term = this.search.trim().toLowerCase();
                                if (!term) return this.items;
                                return this.items.filter(i => i.nome.toLowerCase().includes(term));
                            },
                            isSelected(id) {
                                return this.selected.includes(id);
                            },
                            toggle(id) {
                                if (this.isSelected(id)) {
                                    this.selected = this.selected.filter(item => item !== id);
                                } else {
                                    this.selected.push(id);
                                }
                            },
                            remove(id) {
                                this.selected = this.selected.filter(item => item !== id);
                            },
                            itemNome(id) {
                                return this.items.find(i => i.id === id)?.nome ?? '';
                            }
                        }"
                        @click.outside="open = false"
                    >
                        <label class="text-xs text-gray-600 mb-1 block">Centro de Custo</label>
                        <div class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex min-h-[2.75rem] w-full items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left text-sm shadow-sm transition-colors hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                            >
                                <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5">
                                    <template x-if="selected.length === 0">
                                        <span class="text-gray-400">Todos os centros de custo</span>
                                    </template>
                                    <template x-for="id in selected" :key="'cc-' + id">
                                        <span class="inline-flex max-w-full items-center gap-1 rounded-md bg-[#313e50]/10 px-2 py-0.5 text-xs font-medium text-[#313e50]">
                                            <span class="truncate" x-text="itemNome(id)"></span>
                                            <button
                                                type="button"
                                                class="rounded p-0.5 text-[#313e50]/60 hover:bg-[#313e50]/20 hover:text-[#313e50]"
                                                @click.stop="remove(id)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition.origin.top.duration.150ms
                                class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
                            >
                                <div class="border-b border-gray-100 p-3">
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                        </svg>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Buscar centro de custo..."
                                            class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-[#313e50] focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                                        >
                                    </div>
                                </div>

                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <template x-if="filteredItems.length === 0">
                                        <li class="px-4 py-3 text-sm text-gray-400">Nenhum centro de custo encontrado.</li>
                                    </template>
                                    <template x-for="item in filteredItems" :key="'cc-opt-' + item.id">
                                        <li>
                                            <button
                                                type="button"
                                                @click="toggle(item.id)"
                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors hover:bg-gray-50"
                                                :class="isSelected(item.id) ? 'bg-[#313e50]/5 text-[#313e50]' : 'text-gray-700'"
                                            >
                                                <span
                                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-colors"
                                                    :class="isSelected(item.id) ? 'border-[#313e50] bg-[#313e50] text-white' : 'border-gray-300 bg-white'"
                                                >
                                                    <svg x-show="isSelected(item.id)" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                                <span class="truncate" x-text="item.nome"></span>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
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
                            <th class="px-4 py-3 text-left w-10"></th>
                            <th class="px-4 py-3 text-left">Vencimento</th>
                            <th class="px-4 py-3 text-left w-1/3">Descrição do Título & Parcela</th>
                            <th class="px-4 py-3 text-left">Cliente / Pagador</th>
                            <th class="px-4 py-3 text-right">Valor (R$)</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Cobrança</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($parcelas as $parcela)
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
                                        <!-- Descrição original -->
                                        <span class="text-gray-900 font-medium truncate max-w-[250px]" title="{{ $parcela->titulo->descricao ?? '--' }}">
                                            {{ $parcela->titulo->descricao ?? '--' }}
                                        </span>
                                        
                                        <!-- Info original da Parcela e ID -->
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-gray-500 text-[11px]">
                                                Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count }}
                                            </span>
                                            <span class="text-gray-300 text-[10px]">•</span>
                                            <span class="text-gray-400 text-[10px]" title="ID do Título Pai">
                                                Tít. #{{ $parcela->titulo_financeiro_id }}
                                            </span>
                                        </div>

                                        <!-- Categoria e Centro de Custo (Minimalista) -->
                                        @if(isset($parcela->titulo->categoriaFinanceira) || isset($parcela->titulo->centroCusto))
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                @if(isset($parcela->titulo->categoriaFinanceira))
                                                    <span class="text-gray-400 text-[10px] truncate max-w-[120px]" title="Categoria: {{ $parcela->titulo->categoriaFinanceira->nome }}">
                                                        {{ $parcela->titulo->categoriaFinanceira->nome }}
                                                    </span>
                                                @endif

                                                @if(isset($parcela->titulo->categoriaFinanceira) && isset($parcela->titulo->centroCusto))
                                                    <span class="text-gray-300 text-[10px]">|</span>
                                                @endif

                                                @if(isset($parcela->titulo->centroCusto))
                                                    <span class="text-gray-400 text-[10px] truncate max-w-[120px]" title="Centro de Custo: {{ $parcela->titulo->centroCusto->nome }}">
                                                        {{ $parcela->titulo->centroCusto->nome }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="relative group w-fit max-w-[180px]">
                                        <span
                                            class="text-gray-700 font-medium truncate max-w-[180px] block cursor-help"
                                        >
                                            {{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'Sem entidade' }}
                                        </span>

                                        <div class="absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-all duration-200 group-hover:opacity-100 pointer-events-none shadow-lg">
                                            {{ $parcela->titulo->entidade->razao_social ?? $parcela->titulo->entidade->nome_fantasia ?? 'Sem entidade' }}

                                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                        </div>
                                    </div>

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
                                        $displayStatus = $parcela->status_calculado;
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $color }}">
                                        {{ ucfirst($displayStatus) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center">
                                        @if($parcela->possui_boleto_ativo)
                                            <!-- Com Boleto -->
                                            <div class="relative flex items-center justify-center group">
                                                <button
                                                    type="button"
                                                    wire:click="downloadCobranca({{ $parcela->boleto_ativo->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 rounded-md border shadow-sm transition-all duration-200 cursor-pointer hover:scale-105 active:scale-95 {{ $parcela->boleto_ativo->classes_status }}"
                                                >
                                                    {{-- Ícone normal --}}
                                                    <svg
                                                        wire:loading.remove
                                                        wire:target="downloadCobranca({{ $parcela->boleto_ativo->id }})"
                                                        class="w-4 h-4"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                    >
                                                        <rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.8"/>
                                                        <path stroke-linecap="round" stroke-width="1.5" d="M7 10h10"/>
                                                        <path stroke-linecap="round" stroke-width="1.5" d="M7 14h2"/>
                                                        <path stroke-linecap="round" stroke-width="1.5" d="M11 14h1"/>
                                                        <path stroke-linecap="round" stroke-width="1.5" d="M14 14h3"/>
                                                    </svg>

                                                    {{-- Spinner --}}
                                                    <svg
                                                        wire:loading
                                                        wire:target="downloadCobranca({{ $parcela->boleto_ativo->id }})"
                                                        class="w-4 h-4 animate-spin"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <circle
                                                            class="opacity-25"
                                                            cx="12"
                                                            cy="12"
                                                            r="10"
                                                            stroke="currentColor"
                                                            stroke-width="3"
                                                        ></circle>
                                                        <path
                                                            class="opacity-75"
                                                            fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V1C5.925 1 1 5.925 1 12h3z"
                                                        ></path>
                                                    </svg>
                                                </button>

                                                <div class="absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-all duration-200 group-hover:opacity-100 pointer-events-none shadow-lg">
                                                    <span class="font-semibold">Boleto {{ ucfirst($parcela->boleto_ativo->status) }}</span><br>

                                                    Banco:
                                                    {{ $parcela->boleto_ativo->configuracaoCobranca->conta->banco->numero_banco }}
                                                    -
                                                    {{ $parcela->boleto_ativo->configuracaoCobranca->conta->banco->nome }}
                                                    |
                                                    Cooperativa:
                                                    {{ $parcela->boleto_ativo->configuracaoCobranca->conta->agencia }}

                                                    <div class="mt-1 border-t border-gray-600 pt-1 text-center text-[11px] text-gray-300">
                                                        Clique para baixar o PDF do boleto
                                                    </div>

                                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Sem Boleto -->
                                            <div class="relative flex items-center justify-center group">
                                                <span class="inline-flex items-center justify-center p-1.5 text-gray-300 cursor-help transition-colors group-hover:text-gray-400">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                    </svg>
                                                </span>

                                                <!-- Tooltip Customizado Tailwind -->
                                                <div class="absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 transition-all duration-200 group-hover:opacity-100 pointer-events-none shadow-lg">
                                                    Nenhum boleto vinculado
                                                    <!-- Setinha do Tooltip (Triângulo apontando pra baixo) -->
                                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
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
                                                        wire:click="receberParcela({{ $parcela->id }})"
                                                    >
                                                        Informar Recebimento
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
                                                @if($parcela->status_calculado != 'pago' && $parcela->status_calculado != 'cancelado')
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                        wire:click="editarStatus({{ $parcela->id }})"
                                                        @click="open = false"
                                                    >
                                                        Alterar Status 
                                                    </button>
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
                                                @if(!$parcela->possui_boleto_ativo && $parcela->status_calculado != 'pago')
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                        wire:click="gerarCobrancaParcela({{ $parcela->id }})"
                                                    >
                                                        Gerar Cobranca
                                                    </button>
                                                @elseif($parcela->possui_boleto_ativo)
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                        wire:click="cancelarCobrancaParcela({{ $parcela->id }})"
                                                    >
                                                        Cancelar Cobranca
                                                    </button>
                                                @endif
                                                
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
        <livewire:Modais.ContasReceber.DetalhesParcela 
            :parcela-id="$parcelaSelecionada->id" 
            wire:key="modal-detalhes-{{ $parcelaSelecionada->id }}" 
            @fechar-modal.camel="$set('openModalDetalhesParcela', false)" 
        />
    @endif
    @if($openModalDetalhesTitulo && $tituloSelecionado)
        <livewire:Modais.ContasReceber.DetalhesTitulo 
            :titulo-id="$tituloSelecionado->id" 
            wire:key="modal-detalhes-titulo-{{ $tituloSelecionado->id }}" 
            @fechar-modal.camel="$set('openModalDetalhesTitulo', false)" 
        />
    @endif
    @if($openModalReceberParcela && $parcelaAReceber)
        <livewire:modais.contas-receber.receber-parcela 
            :parcela-id="$parcelaAReceber->id" 
            wire:key="modal-receber-{{ $parcelaAReceber->id }}" 
        />
    @endif
    @if($openModalEditarParcela && $parcelaParaEditar)
        <livewire:Modais.ContasReceber.EditarParcela 
            :parcela-id="$parcelaParaEditar->id" 
            wire:key="modal-editar-{{ $parcelaParaEditar->id }}" 
        />
    @endif
    @if($parcelaParaEditarStatus && $openModalEditarStatus)
        <livewire:Modais.ContasReceber.EditarStatus
            :parcela-id="$parcelaParaEditarStatus->id" 
            wire:key="modal-editar-status-{{ $parcelaParaEditarStatus->id }}" 
        />
    @endif
    @if($parcelaParaAnexos && $openModalAnexos)
        <livewire:Modais.ContasReceber.Anexos
            :parcela-id="$parcelaParaAnexos->id" 
            wire:key="modal-anexos-{{ $parcelaParaAnexos->id }}" 
        />
    @endif
    @if($parcelaParaCobranca && $openModalCobranca)
        <livewire:Modais.ContasReceber.GerarCobranca
            :parcela-id="$parcelaParaCobranca->id" 
            wire:key="modal-cobranca-{{ $parcelaParaCobranca->id }}" 
        />
    @endif
    @if($parcelasCobrancaLote && $openModalCobrancaLote)
        <livewire:Modais.ContasReceber.GerarCobrancaLote
            :parcelasIds="$parcelasCobrancaLote"
            wire:key="modal-cobranca-lote" 
        />
    @endif
    @if($parcelaParaCancelaCobranca && $openModalCancelaCobranca)
        <livewire:Modais.ContasReceber.CancelarCobranca
            :parcela-id="$parcelaParaCancelaCobranca->id" 
            wire:key="modal-cancela-{{ $parcelaParaCancelaCobranca->id }}" 
        />
    @endif

</div>