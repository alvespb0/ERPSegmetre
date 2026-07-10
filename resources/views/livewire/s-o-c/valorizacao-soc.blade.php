<div>
    <div class="space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Integrações · SOC
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Valorização de Exames
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Consulte o faturamento de exames do SOC e importe os lançamentos financeiros para o ERP.
                </p>
            </div>
            
            @if(!empty($examesValorizados))
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="importarSelecionados"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Importar Selecionados
                    </button>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all">
            <div class="p-2 flex flex-col lg:flex-row items-center gap-2">
                
                <div class="relative flex-1 w-full px-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-600">Buscar Valorização no SOC</span>
                </div>

                <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    
                    <div class="flex items-center gap-2">
                        <input
                            type="date"
                            wire:model="dataInicio"
                            class="text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-[#313e50] focus:border-[#313e50]"
                        >
                        <span class="text-gray-400 text-xs">até</span>
                        <input
                            type="date"
                            wire:model="dataFim"
                            class="text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-[#313e50] focus:border-[#313e50]"
                        >
                    </div>

                    <div class="hidden md:block w-px h-4 bg-gray-200 mx-1"></div>

                    <button
                        wire:click="getValorizacoes"
                        wire:loading.attr="disabled"
                        wire:target="getValorizacoes"
                        class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors disabled:opacity-70 flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="getValorizacoes">Consultar Período</span>
                        <span wire:loading wire:target="getValorizacoes" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Consultando...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        @if(!empty($examesValorizados))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="min-w-full overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left w-10">
                                    <input
                                        type="checkbox"
                                        wire:model="selecionarTodos"
                                        class="rounded border-gray-300 text-[#313e50] shadow-sm focus:ring-[#313e50] focus:ring-opacity-50"
                                    >
                                </th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-left">Empresa</th>
                                <th class="px-4 py-3 text-left">Unidade</th>
                                <th class="px-4 py-3 text-left">Produto</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                                <th class="px-4 py-3 text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach($examesValorizados as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ in_array($index, $selecionados ?? []) ? 'bg-blue-50/50' : '' }}">
                                    
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            wire:model="selecionados"
                                            value="{{ $index }}"
                                            class="rounded border-gray-300 text-[#313e50] shadow-sm focus:ring-[#313e50] focus:ring-opacity-50"
                                        >
                                    </td>

                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($item['vinculada'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border bg-green-50 text-green-700 border-green-200">
                                                Vinculada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border bg-yellow-50 text-yellow-700 border-yellow-200">
                                                Não vinculada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-gray-900 font-medium truncate max-w-[200px]" title="{{ $item['EMPRESA'] }}">
                                                {{ $item['EMPRESA'] }}
                                            </span>
                                            <span class="text-gray-500 text-[11px] mt-0.5">
                                                Código: {{ $item['CODIGO_EMPRESA'] }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-gray-900 truncate max-w-[200px]" title="{{ $item['UNIDADE'] ?: 'Sem unidade' }}">
                                                {{ $item['UNIDADE'] ?: 'Sem unidade' }}
                                            </span>
                                            @if(!empty($item['CODIGO_UNIDADE']))
                                                <span class="text-gray-500 text-[11px] mt-0.5">
                                                    Código: {{ $item['CODIGO_UNIDADE'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $item['PRODUTO'] }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                        R$ {{ $item['VALOR_TOTAL'] }}
                                    </td>

                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if(!$item['vinculada'])
                                            <button
                                                type="button"
                                                wire:click="vincularEmpresa('{{ $item['CODIGO_EMPRESA'] }}', {{ !empty($item['CODIGO_UNIDADE']) ? $item['CODIGO_UNIDADE'] : null }})"
                                                class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-[#313e50] bg-white border border-[#313e50]/30 rounded-lg hover:bg-[#313e50] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                            >
                                                Vincular Empresa
                                            </button>
                                        @else
                                            <button 
                                                disabled 
                                                class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed"
                                            >
                                                Já Vinculado
                                            </button>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex items-center justify-between text-xs text-gray-500">
                    <p>
                        Mostrando <span class="font-medium text-gray-900">{{ count($examesValorizados) }}</span> registros encontrados no período.
                    </p>
                </div>
            </div>
        @endif

    </div>
</div>