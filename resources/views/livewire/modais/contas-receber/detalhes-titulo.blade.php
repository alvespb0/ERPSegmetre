<div>
    <div 
        x-data="{ show: true }"
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
            class="fixed inset-0 bg-gray-900/50" 
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalDetalhesTitulo', false), 200)"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100 pointer-events-auto"
            >
                @php
                    $statusColorsTitulo = [
                        'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'pago' => 'bg-green-50 text-green-700 border-green-200',
                        'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];
                    $corStatusTitulo = $statusColorsTitulo[$titulo->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                @endphp

                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-titulo-title">
                                Título #{{ $titulo->id }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $corStatusTitulo }}">
                                {{ ucfirst($titulo->status) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600 tracking-wider">
                                {{ $titulo->tipo === 'receber' ? 'Receita' : 'Despesa' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            {{ $titulo->descricao ?? 'Sem descrição' }}
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.fechar(), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
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
                                R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Emissão</p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($titulo->data_emissao)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Número NF</p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ $titulo->numero_nf ?? '--' }}
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
                                    {{ $titulo->entidade->razao_social ?? $titulo->entidade->nome_fantasia ?? 'Não informado' }}
                                    <span class="text-gray-400 font-normal ml-1">({{ $titulo->entidade->cpf_cnpj ?? 'S/N' }})</span>
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Categoria Financeira</p>
                                <p class="font-medium text-gray-900">{{ $titulo->categoriaFinanceira->nome ?? 'Não classificada' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Centro de Custo</p>
                                <p class="font-medium text-gray-900">{{ $titulo->centroCusto->nome ?? 'Padrão' }}</p>
                            </div>
                            @if($titulo->observacoes)
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-2">
                                    <p class="text-gray-500 text-xs mb-0.5">Observações</p>
                                    <p class="text-gray-700 bg-gray-50 p-3 rounded-lg text-sm whitespace-pre-line">{{ $titulo->observacoes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center min-h-[52px]">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Composição de Parcelas</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $titulo->parcelas->count() }}
                                </span>
                            </div>
                            
                            @if(count($parcelasSelecionadas) > 0)
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-500">
                                        [ {{ count($parcelasSelecionadas) }} selecionada(s) ]
                                    </span>
                                    <div x-data="{ menuOpen: false }" class="relative">
                                        <button 
                                            @click="menuOpen = !menuOpen" 
                                            @click.away="menuOpen = false" 
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
                                        >
                                            Ações
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        
                                        <div 
                                            x-show="menuOpen" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-1.5 w-48 bg-white border border-gray-100 rounded-lg shadow-lg py-1 z-10" 
                                            x-cloak
                                        >
                                            <button 
                                                type="button" 
                                                wire:click="gerarCobrancasLote" 
                                                class="w-full text-left px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                                            >
                                                Gerar Cobranças Bancárias
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3 w-10"></th> <th class="px-4 py-3 font-medium">Nº</th>
                                        <th class="px-4 py-3 font-medium">Vencimento</th>
                                        <th class="px-4 py-3 font-medium text-right">Valor Original</th>
                                        <th class="px-4 py-3 font-medium text-right">Valor Pago</th>
                                        <th class="px-4 py-3 font-medium text-center">Status</th>
                                        <th class="px-4 py-3 font-medium text-center">Cobrança</th> <th class="px-4 py-3 font-medium text-right">Ação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($titulo->parcelas->sortBy('numero_parcela') as $parc)
                                        @php
                                            $parcStatusColors = [
                                                'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'pago' => 'bg-green-50 text-green-700 border-green-200',
                                                'atrasado' => 'bg-red-50 text-red-700 border-red-200',
                                                'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            ];
                                            $corParcStatus = $parcStatusColors[$parc->status_calculado] ?? $parcStatusColors['aberto'];
                                            
                                            // Lógica para desabilitar o checkbox
                                            $desabilitarSelecao = in_array($parc->status_calculado, ['cancelado', 'pago']) || $parc->possui_boleto_ativo;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-center">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model.live="parcelasSelecionadas" 
                                                    value="{{ $parc->id }}"
                                                    @disabled($desabilitarSelecao)
                                                    class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50] disabled:opacity-40 disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors"
                                                >
                                            </td>
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
                                            <td class="px-4 py-3 text-center">
                                                @if($parc->possui_boleto_ativo)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-50 text-blue-600 border border-blue-100" title="Cobrança Bancária Ativa">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </span>
                                                @else
                                                    <span class="text-gray-300" title="Sem cobrança">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button 
                                                    type="button"
                                                    @click="show = false; setTimeout(() => { $wire.$parent.detalhesParcela({{ $parc->id }}); $wire.$parent.set('openModalDetalhesTitulo', false); }, 200)"
                                                    class="text-xs text-[#313e50] hover:text-blue-700 font-medium"
                                                >
                                                    Ver Parcela
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
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
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalDetalhesTitulo', false), 200)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>