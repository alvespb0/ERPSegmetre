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
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Exportar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-red-100 border-l-4 border-l-red-500 relative overflow-hidden">
                <p class="text-sm font-medium text-gray-600 mb-1">Vencidos</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ number_format($totalVencido ?? 0, 2, ',', '.') }}
                </p>
                <p class="text-xs text-red-600 mt-1 font-medium">Requer atenção</p>
                <div class="absolute right-3 top-5 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 12 12 14 14"></polyline></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-orange-100 border-l-4 border-l-orange-400 relative overflow-hidden">
                <p class="text-sm font-medium text-gray-600 mb-1">Vencem Hoje</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ number_format($totalVenceHoje ?? 0, 2, ',', '.') }}
                </p>
                <p class="text-xs text-orange-600 mt-1 font-medium">Cobranças do dia</p>
                <div class="absolute right-3 top-5 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-blue-100 border-l-4 border-l-blue-500 relative overflow-hidden">
                <p class="text-sm font-medium text-gray-600 mb-1">A Receber (Em Aberto)</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ $parcelas->where('status', 'aberto')->pluck('valor')->sum()}}
                </p>
                <p class="text-xs text-blue-600 mt-1 font-medium">Projeção futura</p>
                <div class="absolute right-3 top-5 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-emerald-100 border-l-4 border-l-emerald-500 relative overflow-hidden">
                <p class="text-sm font-medium text-gray-600 mb-1">Recebidos (Período)</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ number_format($totalRecebidoPeriodo ?? 0, 2, ',', '.') }}
                </p>
                <p class="text-xs text-emerald-600 mt-1 font-medium">Entradas confirmadas</p>
                <div class="absolute right-3 top-5 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 transition-all focus-within:ring-2 focus-within:ring-[#313e50] focus-within:border-transparent hover:shadow-md">
            
            <div class="p-1.5 flex flex-col lg:flex-row items-center gap-2">
                <div class="relative flex-1 w-full flex items-center">
                    <svg class="absolute left-3 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por cliente, descrição do título ou nº da parcela..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-700 placeholder-gray-400"
                    >
                </div>

                <div class="hidden lg:block w-px h-6 bg-gray-200 mx-2"></div>

                <div class="flex flex-wrap items-center w-full lg:w-auto gap-2 border-t lg:border-t-0 border-gray-100 pt-2 lg:pt-0">
                    
                    <select
                        wire:model.live="periodoFiltro"
                        class="flex-1 lg:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-30 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <option value="todos">Qualquer Vencimento</option>
                        <option value="hoje">Vencem Hoje</option>
                        <option value="ontem">Venceram Ontem</option>
                        <option value="semana">Esta Semana</option>
                        <optgroup label="Por Mês">
                            <option value="mes_1">Janeiro</option>
                            <option value="mes_2">Fevereiro</option>
                            <option value="mes_3">Março</option>
                            <option value="mes_4">Abril</option>
                            <option value="mes_5">Maio</option>
                            <option value="mes_6">Junho</option>
                            <option value="mes_7">Julho</option>
                            <option value="mes_8">Agosto</option>
                            <option value="mes_9">Setembro</option>
                            <option value="mes_10">Outubro</option>
                            <option value="mes_11">Novembro</option>
                            <option value="mes_12">Dezembro</option>
                        </optgroup>
                        <option value="customizado">Período Customizado...</option>
                    </select>

                    <div class="hidden md:block w-px h-4 bg-gray-200 mx-1"></div>

                    <select
                        wire:model.live="statusFiltro"
                        class="flex-1 lg:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <option value="todos">Todos Status</option>
                        <option value="aberto">Em Aberto</option>
                        <option value="atrasado">Atrasados</option>
                        <option value="pago">Pagos</option>
                        <option value="parcial">Pagos Parcialmente</option>
                    </select>

                    <button
                        type="button"
                        @click="mostrarFiltrosAvancados = !mostrarFiltrosAvancados"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors focus:outline-none"
                        :class="mostrarFiltrosAvancados ? 'bg-[#313e50] text-white' : 'text-gray-600 hover:bg-gray-50'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filtros Avançados
                    </button>
                </div>
            </div>

            <div 
                x-show="mostrarFiltrosAvancados" 
                x-collapse
                class="border-t border-gray-100 bg-gray-50/50 p-4"
                style="display: none;"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    @if($periodoFiltro === 'customizado')
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Vencimento Início</label>
                            <input type="date" wire:model.live="dataInicio" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Vencimento Fim</label>
                            <input type="date" wire:model.live="dataFim" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                        </div>
                    @endif

                    <div class="{{ $periodoFiltro === 'customizado' ? 'lg:col-span-1' : 'lg:col-span-2' }}">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoria Financeira</label>
                        <select wire:model.live="categoriaFiltro" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                            <option value="">Todas as categorias</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="{{ $periodoFiltro === 'customizado' ? 'lg:col-span-1' : 'lg:col-span-2' }}">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Centro de Custo</label>
                        <select wire:model.live="centroCustoFiltro" class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-[#313e50] focus:border-[#313e50]">
                            <option value="">Todos os centros de custo</option>
                            @foreach($centrosCusto as $centro)
                                <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button wire:click="limparFiltros" type="button" class="text-xs font-medium text-red-600 hover:text-red-700 transition-colors">
                        Limpar todos os filtros
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
                                        @if($parcela->status === 'atrasado' || ($parcela->status === 'aberto' && \Carbon\Carbon::parse($parcela->data_vencimento)->isPast()))
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
                                                Parcela {{ $parcela->numero_parcela }}
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
                                        $color = $statusColors[$parcela->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                        
                                        // Substituição visual se a parcela estiver em aberto mas a data já passou
                                        $displayStatus = $parcela->status;
                                        if($displayStatus === 'aberto' && \Carbon\Carbon::parse($parcela->data_vencimento)->isPast()) {
                                            $displayStatus = 'atrasado';
                                            $color = $statusColors['atrasado'];
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $color }}">
                                        {{ ucfirst($displayStatus) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right relative overflow-visible whitespace-nowrap" x-data="{ open: false }">
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
                                                    wire:click="receberParcela({{ $parcela->id }})"
                                                >
                                                    Baixar Recebimento
                                                </button>
                                                <div class="h-px bg-gray-100 my-1"></div>
                                            @endif
                                            
                                            <button
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                wire:click="editarParcela({{ $parcela->id }})"
                                            >
                                                Editar Parcela
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
</div>