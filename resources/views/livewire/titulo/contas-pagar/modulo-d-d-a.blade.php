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

        <!-- Área de Listagem com Abas (Alpine.js) -->
        <div x-data="{ abaAtiva: 'sem_vinculo' }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
            
            <!-- Overlay de carregamento sobre a tabela -->
            <div wire:loading wire:target="buscarBoletos" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-20 flex items-center justify-center">
            </div>

            <!-- Navegação das Abas -->
            <div class="border-b border-gray-200">
                <nav class="flex px-6 -mb-px gap-6" aria-label="Tabs">
                    <button 
                        @click="abaAtiva = 'sem_vinculo'"
                        :class="abaAtiva === 'sem_vinculo' ? 'border-[#313e50] text-[#313e50]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                    >
                        Boletos Sem Vínculo
                        <span 
                            :class="abaAtiva === 'sem_vinculo' ? 'bg-[#313e50] text-white' : 'bg-gray-100 text-gray-600'"
                            class="py-0.5 px-2.5 rounded-full text-xs font-semibold transition-colors"
                        >
                            {{ count($titulosSemVinculo) }}
                        </span>
                    </button>

                    <button 
                        @click="abaAtiva = 'vinculados'"
                        :class="abaAtiva === 'vinculados' ? 'border-[#313e50] text-[#313e50]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                    >
                        Boletos Vinculados
                        <span 
                            :class="abaAtiva === 'vinculados' ? 'bg-[#313e50] text-white' : 'bg-gray-100 text-gray-600'"
                            class="py-0.5 px-2.5 rounded-full text-xs font-semibold transition-colors"
                        >
                            {{ count($titulosVinculados) }}
                        </span>
                    </button>
                </nav>
            </div>

            <div x-show="abaAtiva === 'sem_vinculo'" style="display: none;" x-transition.opacity>
                
                @if(count($titulosSemVinculo) > 0)
                    <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Valor Total Pendente</p>
                                <p class="text-xl font-semibold text-amber-600">
                                    R$ {{ number_format(collect($titulosSemVinculo)->sum('valor'), 2, ',', '.') }}
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
                            @forelse($titulosSemVinculo as $titulo)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-3.5 font-medium text-gray-900 whitespace-nowrap">
                                        {{ isset($titulo['vencimento']) ? date('d/m/Y', strtotime($titulo['vencimento'])) : '--/--/----' }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-gray-900 font-medium block">
                                            {{ $titulo['nome_beneficiario'] ?? 'Não informado' }}
                                        </span>
                                        <span class="text-[11px] text-gray-500 block mt-0.5">
                                            Doc: {{ $titulo['documento_beneficiario'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-gray-600 font-mono text-xs break-all">
                                            {{ $titulo['linha_digitavel'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-medium text-gray-900 whitespace-nowrap">
                                        {{ number_format($titulo['valor'] ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="text-gray-600 font-medium text-xs">
                                            {{ $titulo['situacao'] ?? 'Não informada' }}
                                        </span>
                                    </td>
                                    <!-- Ação Ativa -->
                                    <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                        <button
                                            type="button"
                                            wire:click="cadastrarDespesa('{{ $titulo['linha_digitavel'] }}')"
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
                                            Nenhum boleto pendente de vínculo.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="abaAtiva === 'vinculados'" style="display: none;" x-transition.opacity>
                
                @if(count($titulosVinculados) > 0)
                    <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Valor Total Vinculado</p>
                                <p class="text-xl font-semibold text-emerald-600">
                                    R$ {{ number_format(collect($titulosVinculados)->sum('valor'), 2, ',', '.') }}
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
                            @forelse($titulosVinculados as $titulo)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-3.5 font-medium text-gray-900 whitespace-nowrap">
                                        {{ isset($titulo['vencimento']) ? date('d/m/Y', strtotime($titulo['vencimento'])) : '--/--/----' }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-gray-900 font-medium block">
                                            {{ $titulo['nome_beneficiario'] ?? 'Não informado' }}
                                        </span>
                                        <span class="text-[11px] text-gray-500 block mt-0.5">
                                            Doc: {{ $titulo['documento_beneficiario'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-gray-600 font-mono text-xs break-all">
                                            {{ $titulo['linha_digitavel'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-medium text-gray-900 whitespace-nowrap">
                                        {{ number_format($titulo['valor'] ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="text-gray-600 font-medium text-xs">
                                            {{ $titulo['situacao'] ?? 'Não informada' }}
                                        </span>
                                    </td>
                                    <!-- Ação Inativa (Já Vinculado) -->
                                    <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                        <button 
                                            disabled 
                                            class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed"
                                        >
                                            Já Cadastrado
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Nenhum boleto já vinculado foi encontrado.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Footer Informativo Geral -->
            @if(count($titulosSemVinculo) > 0 || count($titulosVinculados) > 0)
                <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-3 flex items-center justify-center text-xs text-gray-500">
                    <p>
                        Total processado: <span class="font-medium text-gray-900">{{ count($titulosSemVinculo) + count($titulosVinculados) }}</span> boletos via integração DDA.
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if($openModalDespesa == true)
        <livewire:Modais.ContasPagar.LancarTituloDDA
            :dadosDDA="$dadosDDA" 
            wire:key="modal-despesa" 
        />
    @endif
</div>