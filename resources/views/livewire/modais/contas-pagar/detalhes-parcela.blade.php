<div>
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
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
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
            @click="show = false; setTimeout(() => $wire.$parent.set('openModalDetalhesParcela', false), 200)"
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
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-3xl border border-gray-100 pointer-events-auto"
            >
                @php
                    $titulo = $parcela->titulo;
                    
                    $statusColors = [
                        'aberto' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'pago' => 'bg-green-50 text-green-700 border-green-200',
                        'atrasado' => 'bg-red-50 text-red-700 border-red-200',
                        'parcial' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'cancelado' => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];
                    $corStatus = $statusColors[$parcela->status_calculado] ?? $statusColors['aberto'];
                @endphp 

                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Parcela {{ $parcela->numero_parcela }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $corStatus }}">
                                {{ ucfirst($parcela->status_calculado) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Ref: Título #{{ $titulo->id ?? '--' }} &middot; {{ $titulo->descricao ?? 'Sem descrição' }}
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
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Valor Original</p>
                            <p class="text-xl font-semibold text-gray-900">
                                R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-green-600 uppercase font-medium mb-1">Total Pago</p>
                            <p class="text-xl font-semibold text-green-700">
                                R$ {{ number_format($parcela->valor_pago, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-xl border {{ $parcela->saldo_devedor > 0 ? 'border-red-100' : 'border-gray-100' }} shadow-sm">
                            <p class="text-xs {{ $parcela->saldo_devedor > 0 ? 'text-red-600' : 'text-gray-500' }} uppercase font-medium mb-1">Saldo a Pagar</p>
                            <p class="text-xl font-semibold {{ $parcela->saldo_devedor > 0 ? 'text-red-700' : 'text-gray-900' }}">
                                R$ {{ number_format($parcela->saldo_devedor, 2, ',', '.') }}
                            </p>
                        </div>
                    </div> 

                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Informações do Título</h4>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 border-b border-gray-50 pb-3">
                                <p class="text-gray-500 text-xs mb-0.5">Cliente / Pagador</p>
                                <p class="font-medium text-gray-900">
                                    {{ $titulo->entidade->razao_social ?? $titulo->entidade->nome_fantasia ?? 'Não informado' }}
                                    <span class="text-gray-400 font-normal ml-1">({{ $titulo->entidade->cpf_cnpj ?? 'S/N' }})</span>
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Vencimento da Parcela</p>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Emissão do Título</p>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($titulo->data_emissao)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Número NF</p>
                                <p class="font-medium text-gray-900">{{ $titulo->numero_nf ?? '--' }}</p>
                            </div> 
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Categoria</p>
                                <p class="font-medium text-gray-900">{{ $titulo->categoriaFinanceira->nome ?? 'Não classificado' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Centro de Custo</p>
                                <p class="font-medium text-gray-900">{{ $titulo->centroCusto->nome ?? 'Padrão' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-0.5">Conta</p>
                                <p class="font-medium text-gray-900">{{ $titulo->conta->descricao ?? 'Não informada' }}</p>
                            </div> 
                            @if($titulo->observacoes)
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-2">
                                    <p class="text-gray-500 text-xs mb-0.5">Observações</p>
                                    <p class="text-gray-700 bg-gray-50 p-3 rounded-lg text-sm">{{ $titulo->observacoes }}</p>
                                </div>
                            @endif
                        </div>
                    </div> 

                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Histórico de Pagamentos</h4>
                            <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                {{ $parcela->movimentacoes->count() }}
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2 font-medium">Data</th>
                                        <th class="px-4 py-2 font-medium">Forma de Pagamento</th>
                                        <th class="px-4 py-2 font-medium text-right">Valor Pago</th>
                                        <th class="px-4 py-2 font-medium text-center w-10">Ações</th> 
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($parcela->movimentacoes as $movimentacao)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ \Carbon\Carbon::parse($movimentacao->data_pagamento)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-900">
                                                {{ $movimentacao->formaPagamento->nome ?? 'Não especificada' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium text-green-600">
                                                R$ {{ number_format($movimentacao->valor_pago, 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button 
                                                    type="button"
                                                    wire:click="excluirMovimentacao({{ $movimentacao->id }})"
                                                    wire:confirm="Tem certeza que deseja excluir/estornar este pagamento? O saldo devedor da parcela será recalculado."
                                                    class="text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-red-50"
                                                    title="Excluir Pagamento"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                                Nenhum pagamento registrado para esta parcela ainda.
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
                        @click="show = false; setTimeout(() => $wire.$parent.set('openModalDetalhesParcela', false), 200)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Fechar
                    </button>
                    @if(in_array($parcela->status_calculado, ['aberto', 'atrasado', 'parcial']))
                        <button 
                            type="button" 
                            @click="show = false; $wire.$parent.pagarParcela({{ $parcela->id }}); setTimeout(() => $wire.fechar(), 200)"
                            class="px-4 py-2 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors shadow-sm"
                        >
                            Informar Pagamento
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>