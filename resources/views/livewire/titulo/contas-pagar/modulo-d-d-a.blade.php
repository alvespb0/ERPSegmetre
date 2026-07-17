<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Financeiro &middot; Contas a Pagar
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Módulo DDA</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Busque e gerencie boletos eletrônicos emitidos contra o CNPJ da empresa.
                </p>
            </div>
            
            <!-- Loading State -->
            <div class="flex flex-wrap gap-2 min-h-[40px] items-center">
                <div wire:loading wire:target="selectedConta" class="inline-flex items-center gap-2 text-sm font-medium text-[#313e50]">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Buscando boletos no banco...
                </div>
            </div>
        </div>

        <!-- Painel de Filtros -->
        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <form wire:submit="buscarBoletos">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <!-- Conta -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Conta Bancária *</label>
                        <select
                            wire:model="selectedConta"
                            required
                            class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2 px-3 cursor-pointer hover:bg-gray-100 transition-colors"
                        >
                            <option value="">Selecione...</option>
                            @foreach($contas as $conta)
                                <option value="{{ $conta->id }}">
                                    {{ $conta->banco->nome ?? 'Banco' }} - Ag: {{ $conta->agencia }} / Cc: {{ $conta->conta }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Inicial -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Data Inicial</label>
                        <input 
                            type="date" 
                            wire:model="dataInicial"
                            class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2 px-3 hover:bg-gray-100 transition-colors"
                        >
                    </div>

                    <!-- Data Final -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Data Final</label>
                        <input 
                            type="date" 
                            wire:model="dataFinal"
                            class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2 px-3 hover:bg-gray-100 transition-colors"
                        >
                    </div>

                    <!-- Situação -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Situação</label>
                        <select
                            wire:model="situacao"
                            class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2 px-3 cursor-pointer hover:bg-gray-100 transition-colors"
                        >
                            <option value="">Todas</option>
                            <option value="1">Em aberto</option>
                            <option value="2">Agendado</option>
                            <option value="3">Liquidado</option>
                            <option value="4">Baixado</option>
                        </select>
                    </div>
                </div>

                <!-- Ações do Filtro -->
                <div class="mt-5 flex justify-end items-center border-t border-gray-100 pt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2 bg-[#313e50] text-white text-sm font-medium rounded-lg hover:bg-[#313e50]/90 transition-colors shadow-sm disabled:opacity-70"
                        wire:loading.attr="disabled"
                        wire:target="buscarBoletos"
                    >
                        <svg wire:loading wire:target="buscarBoletos" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span wire:loading.remove wire:target="buscarBoletos">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        
                        <span wire:loading.remove wire:target="buscarBoletos">Buscar Boletos</span>
                        <span wire:loading wire:target="buscarBoletos">Buscando no banco...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Área de Listagem e Resumo Integrados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
            
            <!-- Overlay de carregamento sobre a tabela -->
            <div wire:loading wire:target="buscarBoletos" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
            </div>

            <!-- Header da Tabela (Resumo) -->
            @if(count($titulos) > 0)
                <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Boletos Encontrados</p>
                            <p class="text-xl font-semibold text-gray-900">{{ count($titulos) }}</p>
                        </div>
                        <div class="w-px h-8 bg-gray-300 hidden md:block"></div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Valor Total</p>
                            <p class="text-xl font-semibold text-emerald-600">
                                R$ {{ number_format(collect($titulos)->sum('valor'), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Vencimento</th>
                            <th class="px-6 py-3 text-left font-semibold">Beneficiário</th>
                            <th class="px-6 py-3 text-left font-semibold">Linha Digitável</th>
                            <th class="px-6 py-3 text-right font-semibold">Valor (R$)</th>
                            <th class="px-6 py-3 text-center font-semibold">Situação</th>
                            <th class="px-6 py-3 text-center font-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($titulos as $titulo)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <!-- Vencimento -->
                                <td class="px-6 py-3.5 font-medium text-gray-900 whitespace-nowrap">
                                    {{ isset($titulo['vencimento']) ? date('d/m/Y', strtotime($titulo['vencimento'])) : '--/--/----' }}
                                </td>
                                
                                <!-- Beneficiário -->
                                <td class="px-6 py-3.5">
                                    <span class="text-gray-900 font-medium block">
                                        {{ $titulo['nome_beneficiario'] ?? 'Não informado' }}
                                    </span>
                                    <span class="text-[11px] text-gray-500 block mt-0.5">
                                        Doc: {{ $titulo['documento_beneficiario'] ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                <!-- Linha Digitável -->
                                <td class="px-6 py-3.5">
                                    <span class="text-gray-600 font-mono text-xs break-all">
                                        {{ $titulo['linha_digitavel'] ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                <!-- Valor -->
                                <td class="px-6 py-3.5 text-right font-medium text-gray-900 whitespace-nowrap">
                                    {{ number_format($titulo['valor'] ?? 0, 2, ',', '.') }}
                                </td>

                                <!-- Situação (Limpa e Neutra) -->
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="text-gray-600 font-medium text-xs">
                                        {{ $titulo['situacao'] ?? 'Não informada' }}
                                    </span>
                                </td>
                                
                                <!-- Ações (Botão menor e mais leve) -->
                                <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                    <button
                                        type="button"
                                        wire:click="cadastrarDespesa({{ $titulo['linha_digitavel'] }})"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#313e50] bg-white border border-[#313e50]/30 rounded-lg hover:bg-[#313e50] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Cadastrar Despesa
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Nenhum boleto encontrado para os filtros selecionados.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($titulos) > 0)
                <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-3 flex items-center justify-center text-xs text-gray-500">
                    <p>
                        Mostrando <span class="font-medium text-gray-900">{{ count($titulos) }}</span> boletos resgatados via integração DDA.
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if($openModalDespesa == true)
        <livewire:Modais.ContasPagar.LancarTituloDDA
            wire:key="modal-despesa" 
        />
    @endif
</div>