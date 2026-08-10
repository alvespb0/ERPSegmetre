<div>
    <div
        x-data="{ show: true, tipoPix: @entangle('tipo_pix').live }"
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
                class="relative transform overflow-hidden rounded-xl bg-gray-50 text-left shadow-xl transition-all sm:my-8 w-full max-w-2xl border border-gray-100 pointer-events-auto"
            >

                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                Transferência PIX
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 uppercase tracking-wider">
                                Pagamento Instantâneo
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 line-clamp-1">
                            {{ $consultaPixRet ? 'Confira os dados do destinatário antes de concluir.' : 'Selecione a conta de origem para validar o saldo e informe os dados para o PIX.' }}
                        </p>
                    </div>
                    <button wire:click="fechar" type="button" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Form / Body -->
                <form wire:submit="{{ $consultaPixRet ? 'executarPix' : 'iniciaPix' }}">
                    <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                        <!-- ETAPA 1: Seleção da Conta de Origem -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                                    Conta de Origem *
                                </label>
                                <select 
                                    wire:model.live="selected_conta"
                                    @if($consultaPixRet) disabled @endif
                                    class="w-full text-sm bg-gray-50 border-gray-200 focus:border-[#313e50] focus:ring-[#313e50] outline-none text-gray-700 rounded-lg py-2.5 px-3 cursor-pointer hover:bg-gray-100 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    <option value="">Selecione de qual conta o dinheiro irá sair...</option>
                                    @foreach($contas ?? [] as $conta)
                                        <option value="{{ $conta->id }}">
                                            {{ $conta->banco->nome ?? 'Banco' }} - Ag: {{ $conta->agencia }} / Cc: {{ $conta->conta }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selected_conta') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Indicator de carregamento de saldo -->
                            <div wire:loading wire:target="selected_conta" class="py-3 text-center text-sm text-[#313e50] font-medium">
                                <div class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-[#313e50]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Consultando saldo e disponibilidade...
                                </div>
                            </div>

                            <!-- Display do Saldo -->
                            @if($selected_conta && isset($saldo))
                                <div wire:loading.remove wire:target="selected_conta" class="bg-gray-50 border border-gray-100 rounded-xl p-4 transition-all">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:divide-x divide-gray-200">
                                        <div class="md:px-3 first:pl-0">
                                            <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Saldo Conta</p>
                                            <p class="text-base font-bold {{ $saldo < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                R$ {{ number_format($saldo, 2, ',', '.') }}
                                            </p>
                                        </div>
                                        @if(isset($limite))
                                            <div class="md:px-3">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Limite Disp.</p>
                                                <p class="text-base font-medium text-gray-700">
                                                    R$ {{ number_format($limite, 2, ',', '.') }}
                                                </p>
                                            </div>
                                        @endif
                                        @if(isset($bloqueado))
                                            <div class="md:px-3 col-span-2 md:col-span-1">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wide mb-1">Bloqueado</p>
                                                <p class="text-base font-medium text-amber-600">
                                                    R$ {{ number_format($bloqueado, 2, ',', '.') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- ETAPA 2: Seleção do Tipo de PIX -->
                        @if($selected_conta)
                            <div x-transition class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                        Tipo de PIX *
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button 
                                            type="button"
                                            @click="if(!@js((bool)$consultaPixRet)) $wire.set('tipo_pix', 'chave')"
                                            :class="tipoPix === 'chave' ? 'bg-[#313e50]/5 text-[#313e50] border-[#313e50]' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                                            class="flex items-center justify-center p-3 rounded-lg border-2 font-medium text-sm transition-all outline-none"
                                            @if($consultaPixRet) disabled class="opacity-60 cursor-not-allowed" @else class="cursor-pointer" @endif
                                        >
                                            <span>Chave DICT</span>
                                        </button>

                                        <button 
                                            type="button"
                                            @click="if(!@js((bool)$consultaPixRet)) $wire.set('tipo_pix', 'copia_cola')"
                                            :class="tipoPix === 'copia_cola' ? 'bg-[#313e50]/5 text-[#313e50] border-[#313e50]' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                                            class="flex items-center justify-center p-3 rounded-lg border-2 font-medium text-sm transition-all outline-none"
                                            @if($consultaPixRet) disabled class="opacity-60 cursor-not-allowed" @else class="cursor-pointer" @endif
                                        >
                                            <span>PIX Copia e Cola</span>
                                        </button>
                                    </div>
                                    @error('tipo_pix') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Campos de Valor e Chave -->
                                <div x-show="tipoPix && tipoPix !== ''" x-transition x-cloak class="pt-4 border-t border-gray-100 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Campo Chave ou Código -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">
                                                <span x-text="tipoPix === 'copia_cola' ? 'Código PIX Copia e Cola *' : 'Chave PIX (CPF/CNPJ, Email, Tel, Aleatória) *'"></span>
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="identificador"
                                                @if($consultaPixRet) disabled @endif
                                                :placeholder="tipoPix === 'copia_cola' ? 'Cole o código Copia e Cola aqui...' : 'Digite a chave Pix...'"
                                                class="block w-full text-sm border-gray-200 rounded-lg shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2 disabled:bg-gray-100 disabled:opacity-75"
                                            >
                                            @error('identificador') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Campo Valor -->
                                        <div class="md:col-span-1">
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">
                                                Valor (R$) *
                                            </label>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                wire:model="valor" 
                                                @if($consultaPixRet) disabled @endif
                                                placeholder="0,00" 
                                                class="block w-full text-sm border-gray-200 rounded-lg shadow-sm focus:border-[#313e50] focus:ring-[#313e50] py-2 disabled:bg-gray-100 disabled:opacity-75"
                                            >
                                            @error('valor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- ETAPA 3: Dados Consultados do Destinatário (Clean & Sem cores extras) -->
                        @if($consultaPixRet)
                            <div x-transition class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                                <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                    <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Dados do Destinatário
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click="$set('consultaPixRet', null)" 
                                        class="text-xs font-medium text-[#313e50] hover:underline cursor-pointer"
                                    >
                                        Alterar dados
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Nome / Razão Social</p>
                                        <p class="font-semibold text-gray-800">{{ $consultaPixRet['proprietario']['nome'] ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-semibold">CPF / CNPJ</p>
                                        <p class="font-medium text-gray-700">{{ $consultaPixRet['proprietario']['identificador'] ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Chave PIX</p>
                                        <p class="font-medium text-gray-700 break-all">
                                            {{ $consultaPixRet['chave'] ?? '-' }}
                                            @if(isset($consultaPixRet['tipo']))
                                                <span class="text-xs text-gray-400">({{ $consultaPixRet['tipo'] }})</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Valor</p>
                                        <p class="font-bold text-[#313e50] text-base">
                                            R$ {{ number_format((float) ($valor ?? 0), 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Footer -->
                    <div class="bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                        <button
                            type="button"
                            wire:click="fechar"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm cursor-pointer"
                        >
                            Cancelar
                        </button>

                        @if(empty($consultaPixRet))
                            <!-- Botão Etapa 1: Validar / Consultar -->
                            <button
                                type="submit"
                                x-show="tipoPix && tipoPix !== ''"
                                x-transition
                                wire:loading.attr="disabled"
                                wire:target="iniciaPix"
                                class="px-5 py-2 bg-[#313e50] text-white text-sm font-medium rounded-lg hover:bg-[#313e50]/90 transition-colors shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <svg wire:loading wire:target="iniciaPix" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="iniciaPix">Validar PIX</span>
                                <span wire:loading wire:target="iniciaPix">Consultando...</span>
                            </button>
                        @else
                            <!-- Botão Etapa 2: Confirmar -->
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="executarPix"
                                class="px-5 py-2 bg-[#313e50] text-white text-sm font-medium rounded-lg hover:bg-[#313e50]/90 transition-colors shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <svg wire:loading wire:target="executarPix" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="executarPix">Confirmar PIX</span>
                                <span wire:loading wire:target="executarPix">Processando...</span>
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>