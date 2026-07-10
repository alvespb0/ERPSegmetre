<div>
    <div class="space-y-6">

        <div class="flex items-center justify-between">
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
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">
                        Consulta
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Informe o período para consultar o faturamento no SOC.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Data Inicial
                    </label>

                    <input
                        type="date"
                        wire:model="dataInicio"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Data Final
                    </label>

                    <input
                        type="date"
                        wire:model="dataFim"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                </div>

                <div class="flex items-end">
                    <button
                        wire:click="getValorizacoes"
                        wire:loading.attr="disabled"
                        wire:target="getValorizacoes"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="getValorizacoes">
                            Buscar Valorização
                        </span>

                        <span wire:loading wire:target="getValorizacoes">
                            Consultando...
                        </span>
                    </button>
                </div>

            </div>

        </div>

        @if(!empty($examesValorizados))

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">
                            Resultado da Consulta
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ count($examesValorizados) }} registro(s) encontrados.
                        </p>
                    </div>

                    <button
                        wire:click="importarSelecionados"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm hover:bg-[#313e50]/90"
                    >
                        Importar Selecionados
                    </button>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-100">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        wire:model="selecionarTodos"
                                    >
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Status
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Empresa
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Unidade
                                </th>

                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Vidas
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Valor
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            @foreach($examesValorizados as $index => $item)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3">

                                        <input
                                            type="checkbox"
                                            wire:model="selecionados"
                                            value="{{ $index }}"
                                        >

                                    </td>

                                    <td class="px-4 py-3">

                                        @if($item['vinculada'])

                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Vinculada
                                            </span>

                                        @else

                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                                                Não vinculada
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item['EMPRESA'] }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Código {{ $item['CODIGO_EMPRESA'] }}
                                        </div>

                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="text-sm text-gray-900">
                                            {{ $item['UNIDADE'] ?: 'Sem unidade' }}
                                        </div>

                                        @if(!empty($item['CODIGO_UNIDADE']))
                                            <div class="text-xs text-gray-500">
                                                Código {{ $item['CODIGO_UNIDADE'] }}
                                            </div>
                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-center text-sm text-gray-700">
                                        {{ $item['QUANTIDADE_VIDAS'] }}
                                    </td>

                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        R$ {{ $item['VALOR_TOTAL'] }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @endif

    </div>
</div>