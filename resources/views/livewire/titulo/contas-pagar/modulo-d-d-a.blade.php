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

        <!-- Seletor de Conta e Resumo -->
        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row items-center gap-6 transition-all focus-within:ring-2 focus-within:ring-[#313e50] focus-within:border-transparent hover:shadow-md">
            
            <div class="relative flex-1 w-full flex flex-col">
                <label class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Selecione a Conta Bancária</label>
                <select
                    wire:model.live="selectedConta"
                    class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2 px-3 cursor-pointer hover:bg-gray-100 transition-colors"
                >
                    <option value="">Selecione uma conta configurada...</option>
                    @foreach($contas as $conta)
                        <option value="{{ $conta->id }}">
                            {{ $conta->banco->nome ?? 'Banco' }} - Ag: {{ $conta->agencia }} / Cc: {{ $conta->conta }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="hidden md:block w-px h-10 bg-gray-200"></div>

            <div class="flex gap-6 w-full md:w-auto">
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Boletos</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ count($titulos) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Valor Total</p>
                    <p class="text-2xl font-semibold text-emerald-600">
                        R$ {{ number_format(collect($titulos)->sum('valor'), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Listagem de Títulos DDA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Vencimento</th>
                            <th class="px-4 py-3 text-left">Beneficiário</th>
                            <th class="px-4 py-3 text-left">Linha Digitável</th>
                            <th class="px-4 py-3 text-right">Valor (R$)</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($titulos as $titulo)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Vencimento -->
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ isset($titulo['vencimento']) ? date('d/m/Y', strtotime($titulo['vencimento'])) : '--/--/----' }}
                                </td>
                                
                                <!-- Beneficiário -->
                                <td class="px-4 py-3">
                                    <span class="text-gray-900 font-medium block">
                                        {{ $titulo['nome_beneficiario'] ?? 'Não informado' }}
                                    </span>
                                    <span class="text-xs text-gray-500 block mt-0.5">
                                        Doc: {{ $titulo['documento_beneficiario'] ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                <!-- Linha Digitável -->
                                <td class="px-4 py-3">
                                    <span class="text-gray-600 font-mono text-xs break-all">
                                        {{ $titulo['linha_digitavel'] ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                <!-- Valor -->
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    {{ number_format($titulo['valor'] ?? 0, 2, ',', '.') }}
                                </td>
                                
                                <!-- Ações -->
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        wire:click="cadastrarDespesa('{{ $titulo['linha_digitavel'] ?? '' }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#313e50] text-white text-xs font-medium rounded-lg hover:bg-[#313e50]/90 transition-colors shadow-sm"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Cadastrar Despesa
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                                    @if($selectedConta)
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Nenhum boleto DDA encontrado para esta conta.
                                        </div>
                                    @else
                                        Selecione uma conta bancária acima para buscar os boletos.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Footer Informativo -->
            @if(count($titulos) > 0)
            <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ count($titulos) }}</span> boletos resgatados via integração DDA.
                </p>
            </div>
            @endif
        </div>
    </div>
</div>