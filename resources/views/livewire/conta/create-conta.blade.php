<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    CADASTROS &middot; CONTA
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Nova Conta</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Cadastre uma nova conta bancária ou carteira para movimentações financeiras.
                </p>
            </div>
            <a
                href="{{ route('erp.conta.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Contas
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados da Conta</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações principais e de vínculo institucional.
                            </p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-bold text-red-800">
                                    Por favor, corrija os seguintes erros antes de continuar:
                                </h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 ml-7 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome da Conta <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Ex.: Conta Principal Itaú, Caixinha da Recepção..."
                                wire:model="nome"
                                required
                            >
                            <p class="text-[10px] text-gray-400 mt-1">Este nome deve ser único no sistema.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Modalidade <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="modalidade"
                                required
                            >
                                <option value="">Selecione...</option>
                                <option value="pj">Pessoa Jurídica (PJ)</option>
                                <option value="pf">Pessoa Física (PF)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Banco <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="banco_id"
                                required
                            >
                                <option value="">Selecione um banco...</option>
                                @foreach($bancos as $banco)
                                    <option value="{{ $banco->id }}">{{ $banco->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Conta <span class="text-red-500">*</span></label>
                            <select
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                wire:model="tipo_conta_id"
                                required
                            >
                                <option value="">Selecione o tipo...</option>
                                @foreach($tiposConta as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->descricao }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="mb-4">
                            <h2 class="text-sm font-semibold text-gray-900">Dados Bancários</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações de agência e conta (opcionais).
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Agência</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: 0001"
                                    wire:model="agencia"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Número da Conta</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                    placeholder="Ex.: 12345-6"
                                    wire:model="conta"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6 self-start">
                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900">Ações</h2>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-xs text-gray-500 mb-4">
                        Revise os dados da conta antes de salvar. O nome da conta deve ser único para facilitar a identificação nos lançamentos.
                    </p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar Conta</span>
                        <span wire:loading wire:target="submit">Salvando...</span>
                    </button>

                    <button
                        type="reset"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Limpar formulário
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>