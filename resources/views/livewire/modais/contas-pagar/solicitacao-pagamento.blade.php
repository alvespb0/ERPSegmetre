<div>
    <div
        x-data="{ show: true, showNovaSolicitacao: false }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50"
            wire:click="fechar"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            
            <!-- Modal Panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-5xl border border-gray-100 pointer-events-auto"
            >
                @php
                    $statusColors = [
                        'pendente' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'pago'     => 'bg-green-50 text-green-700 border-green-200',
                        'recusado' => 'bg-red-50 text-red-700 border-red-200',
                        'cancelado' => 'bg-orange-50 text-red-700 border-red-200',
                    ];
                    
                    $tipoLabels = [
                        'codigo_barras'  => 'Cód. Barras',
                        'pix'            => 'PIX',
                        'pix_copia_cola' => 'PIX Copia/Cola',
                        'tributo'        => 'Tributo',
                    ];
                @endphp

                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Solicitações de Pagamento
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count ?? '--' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Gerencie as solicitações de pagamento (PIX, Boletos, Tributos) vinculadas a esta parcela.
                        </p>
                    </div>
                    <button wire:click="fechar" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <!-- Toolbar -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Histórico de Solicitações</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $parcela->solicitacoesPagamento->count() ?? 0 }}
                                </span>
                            </div>
                            
                            <button type="button" @click="showNovaSolicitacao = !showNovaSolicitacao" class="text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 flex items-center gap-1 px-2.5 py-1.5 rounded-md shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Nova Solicitação
                            </button>
                        </div>

                        <!-- Form de Cadastro (Hidden por padrão) -->
                        <div x-show="showNovaSolicitacao" x-transition x-cloak class="bg-gray-50 p-4 border-b border-gray-100">
                            <form wire:submit="salvarSolicitacao" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Pagamento *</label>
                                        <select wire:model="tipo" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                            <option value="">Selecione...</option>
                                            <option value="codigo_barras">Código de Barras</option>
                                            <option value="pix">Chave PIX</option>
                                            <option value="pix_copia_cola">PIX Copia e Cola</option>
                                            <option value="tributo">Tributo</option>
                                        </select>
                                        @error('tipo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Identificador (Linha digitável, Chave PIX, etc) *</label>
                                        <input type="text" wire:model="identificador" placeholder="Digite o código ou chave..." class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                        @error('identificador') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Valor (R$) *</label>
                                        <input type="number" step="0.01" wire:model="valor" placeholder="0,00" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                        @error('valor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" @click="showNovaSolicitacao = false" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-1.5 bg-[#313e50] text-white text-xs font-medium rounded-md hover:bg-[#313e50]/90 transition-colors flex items-center gap-2" wire:loading.attr="disabled" wire:target="salvarSolicitacao">
                                        <svg wire:loading wire:target="salvarSolicitacao" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="salvarSolicitacao">Enviar Solicitação</span>
                                        <span wire:loading wire:target="salvarSolicitacao">Processando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Tabela de Registros -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3 font-medium w-16">ID</th>
                                        <th class="px-4 py-3 font-medium">Data / Tipo</th>
                                        <th class="px-4 py-3 font-medium">Identificador</th>
                                        <th class="px-4 py-3 font-medium">Valor</th>
                                        <th class="px-4 py-3 font-medium">Status</th>
                                        <th class="px-4 py-3 font-medium text-center w-24">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($parcela->solicitacoesPagamento ?? [] as $solicitacao)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-gray-500">#{{ $solicitacao->id }}</td>
                                            
                                            <td class="px-4 py-3">
                                                <div class="text-gray-900 font-medium">{{ date('d/m/Y H:i', strtotime($solicitacao->data_solicitacao)) }}</div>
                                                <div class="text-gray-400 text-xs mt-0.5">{{ $tipoLabels[$solicitacao->tipo] ?? $solicitacao->tipo }}</div>
                                            </td>
                                            
                                            <td class="px-4 py-3 text-gray-700">
                                                <span class="line-clamp-1 font-mono text-xs" title="{{ $solicitacao->identificador }}">
                                                    {{ $solicitacao->identificador }}
                                                </span>
                                            </td>
                                            
                                            <td class="px-4 py-3 text-gray-900 font-medium">
                                                R$ {{ number_format($solicitacao->valor, 2, ',', '.') }}
                                            </td>
                                            
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $statusColors[$solicitacao->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                    {{ ucfirst($solicitacao->status) }}
                                                </span>
                                            </td>
                                            
                                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                <!-- Botão de ver comprovante (se existir) -->
                                                @if($solicitacao->comprovante_path)
                                                    <button type="button" wire:click="downloadComprovante({{ $solicitacao->id }})" class="text-gray-400 hover:text-green-600 p-1 rounded hover:bg-green-50" title="Ver Comprovante">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                    </button>
                                                @endif
                                                
                                                <!-- Botão de exclusão (Apenas se pendente) -->
                                                @if($solicitacao->status === 'pendente')
                                                    <button type="button" wire:click="cancelarSolicitacao({{ $solicitacao->id }})" wire:confirm="Deseja realmente cancelar esta solicitação de pagamento?" class="text-gray-400 hover:text-red-600 p-1 rounded hover:bg-red-50" title="Cancelar Solicitação">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                                <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Nenhuma solicitação de pagamento registrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                    <button
                        type="button"
                        wire:click="fechar"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>