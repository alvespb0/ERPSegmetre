<div>
    <div
        x-data="{ show: true, showUploadParcela: false, showUploadTitulo: false }"
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
            @click="show = false; setTimeout(() => $wire.fechar(), 200)"
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
                    $tipoColors = [
                        'comprovante' => 'bg-green-50 text-green-700 border-green-200',
                        'pix'         => 'bg-teal-50 text-teal-700 border-teal-200',
                        'boleto'      => 'bg-blue-50 text-blue-700 border-blue-200',
                        'fatura'      => 'bg-purple-50 text-purple-700 border-purple-200',
                        'outros'      => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];
                @endphp

                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Central de Anexos
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200">
                                Parcela {{ $parcela->numero_parcela }} / {{ $parcela->titulo->parcelas_count ?? '--' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            Gerencie os arquivos vinculados a esta parcela, seu título e suas movimentações.
                        </p>
                    </div>
                    <button @click="show = false; setTimeout(() => $wire.fechar(), 200)" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Anexos da Parcela</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $parcela->anexos->count() }}
                                </span>
                            </div>
                            
                            <button type="button" @click="showUploadParcela = !showUploadParcela" class="text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 flex items-center gap-1 px-2.5 py-1.5 rounded-md shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Novo Anexo
                            </button>
                        </div>

                        <div x-show="showUploadParcela" x-transition x-cloak class="bg-gray-50 p-4 border-b border-gray-100">
                            <form wire:submit="salvarAnexoParcela" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Arquivo *</label>
                                        <input type="file" wire:model="arquivo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-md bg-white cursor-pointer focus:ring-[#313e50] focus:border-[#313e50]">
                                        @error('arquivo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo *</label>
                                        <select wire:model="tipoAnexo" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                            <option value="">Selecione...</option>
                                            <option value="comprovante">Comprovante</option>
                                            <option value="pix">PIX</option>
                                            <option value="boleto">Boleto</option>
                                            <option value="fatura">Fatura</option>
                                            <option value="outros">Outros</option>
                                        </select>
                                        @error('tipoAnexo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                                        <input type="text" wire:model="descricaoAnexo" placeholder="Opcional" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                        @error('descricaoAnexo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showUploadParcela = false" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-1.5 bg-[#313e50] text-white text-xs font-medium rounded-md hover:bg-[#313e50]/90 transition-colors flex items-center gap-2" wire:loading.attr="disabled" wire:target="salvarAnexoParcela, arquivo">
                                        <svg wire:loading wire:target="salvarAnexoParcela, arquivo" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="salvarAnexoParcela, arquivo">Salvar Anexo</span>
                                        <span wire:loading wire:target="salvarAnexoParcela, arquivo">Enviando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2 font-medium w-16">ID</th>
                                        <th class="px-4 py-2 font-medium">Descrição</th>
                                        <th class="px-4 py-2 font-medium">Tipo</th>
                                        <th class="px-4 py-2 font-medium text-center w-24">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($parcela->anexos as $anexo)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-gray-500">#{{ $anexo->id }}</td>
                                            <td class="px-4 py-3 text-gray-900 font-medium">{{ $anexo->descricao ?? 'Sem descrição' }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $tipoColors[$anexo->tipo] ?? $tipoColors['outros'] }}">
                                                    {{ ucfirst($anexo->tipo) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                <button type="button" wire:click="downloadAnexo({{ $anexo->id }})" class="text-gray-400 hover:text-blue-600 p-1 rounded hover:bg-blue-50" title="Baixar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </button>
                                                <button type="button" wire:click="excluirAnexo({{ $anexo->id }})" wire:confirm="Deseja realmente excluir este anexo?" class="text-gray-400 hover:text-red-600 p-1 rounded hover:bg-red-50" title="Excluir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">Nenhum anexo vinculado diretamente a esta parcela.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($titulo)
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Anexos do Título Origem</h4>
                                <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                    {{ $titulo->anexos->count() ?? 0 }}
                                </span>
                            </div>
                            
                            <button type="button" @click="showUploadTitulo = !showUploadTitulo" class="text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 flex items-center gap-1 px-2.5 py-1.5 rounded-md shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Novo Anexo
                            </button>
                        </div>

                        <div x-show="showUploadTitulo" x-transition x-cloak class="bg-gray-50 p-4 border-b border-gray-100">
                            <form wire:submit="salvarAnexoTitulo" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Arquivo *</label>
                                        <input type="file" wire:model="arquivoTitulo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-md bg-white cursor-pointer focus:ring-[#313e50] focus:border-[#313e50]">
                                        @error('arquivoTitulo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo *</label>
                                        <select wire:model="tipoAnexoTitulo" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                            <option value="">Selecione...</option>
                                            <option value="comprovante">Comprovante</option>
                                            <option value="pix">PIX</option>
                                            <option value="boleto">Boleto</option>
                                            <option value="fatura">Fatura</option>
                                            <option value="outros">Outros</option>
                                        </select>
                                        @error('tipoAnexoTitulo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                                        <input type="text" wire:model="descricaoAnexoTitulo" placeholder="Opcional" class="block w-full text-sm border-gray-200 rounded-md shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2">
                                        @error('descricaoAnexoTitulo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showUploadTitulo = false" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-1.5 bg-[#313e50] text-white text-xs font-medium rounded-md hover:bg-[#313e50]/90 transition-colors flex items-center gap-2" wire:loading.attr="disabled" wire:target="salvarAnexoTitulo, arquivo_titulo">
                                        <svg wire:loading wire:target="salvarAnexoTitulo, arquivo_titulo" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="salvarAnexoTitulo, arquivo_titulo">Salvar Anexo</span>
                                        <span wire:loading wire:target="salvarAnexoTitulo, arquivo_titulo">Enviando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2 font-medium w-16">ID</th>
                                        <th class="px-4 py-2 font-medium">Descrição</th>
                                        <th class="px-4 py-2 font-medium">Tipo</th>
                                        <th class="px-4 py-2 font-medium text-center w-24">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($titulo->anexos as $anexo)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-gray-500">#{{ $anexo->id }}</td>
                                            <td class="px-4 py-3 text-gray-900 font-medium">{{ $anexo->descricao ?? 'Sem descrição' }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $tipoColors[$anexo->tipo] ?? $tipoColors['outros'] }}">
                                                    {{ ucfirst($anexo->tipo) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                <button type="button" wire:click="downloadAnexo({{ $anexo->id }})" wire:loading.attr="disabled" class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-blue-50" title="Baixar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </button>
                                                <button type="button" wire:click="excluirAnexo({{ $anexo->id }})" wire:confirm="Deseja realmente excluir este anexo do título?" wire:loading.attr="disabled" class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50" title="Excluir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">Nenhum anexo vinculado ao título.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Anexos de Pagamentos (Movimentações)</h4>
                            <span class="text-xs font-medium bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                {{ $anexosMovimentacoes ? $anexosMovimentacoes->count() : 0 }}
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-white border-b border-gray-50 text-xs text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2 font-medium w-16">ID</th>
                                        <th class="px-4 py-2 font-medium">Movimentação</th>
                                        <th class="px-4 py-2 font-medium">Descrição</th>
                                        <th class="px-4 py-2 font-medium">Tipo</th>
                                        <th class="px-4 py-2 font-medium text-center w-24">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($anexosMovimentacoes as $anexo)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-gray-500">#{{ $anexo->id }}</td>
                                            <td class="px-4 py-3 text-gray-400 text-xs">Mov. #{{ $anexo->movimentacao_id }}</td>
                                            <td class="px-4 py-3 text-gray-900 font-medium">{{ $anexo->descricao ?? 'Sem descrição' }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $tipoColors[$anexo->tipo] ?? $tipoColors['outros'] }}">
                                                    {{ ucfirst($anexo->tipo) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                                <button type="button" wire:click="downloadAnexo({{ $anexo->id }})" wire:loading.attr="disabled" class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-blue-50" title="Baixar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </button>
                                                <button type="button" wire:click="excluirAnexo({{ $anexo->id }})" wire:confirm="Deseja realmente excluir este anexo da movimentação?" wire:loading.attr="disabled" class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50" title="Excluir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Nenhum anexo de pagamento encontrado.</td>
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
                        @click="show = false; setTimeout(() => $wire.fechar(), 200)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>