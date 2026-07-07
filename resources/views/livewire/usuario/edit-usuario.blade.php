<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    CADASTROS &middot; USUÁRIOS
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Edição de Usuário</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Atualize os dados de acesso do usuário.
                </p>
            </div>
            <a
                href="{{ route('erp.usuarios.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Listar Usuários
            </a>
        </div>

        <form wire:submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Dados do usuário</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Informações básicas de identificação e perfil de acesso.
                            </p>
                        </div>
                        @if ($usuario->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border border-red-200 bg-red-50 text-red-700">
                                Inativo
                            </span>
                        @endif
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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="Nome completo"
                                wire:model="name"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                            <input
                                type="email"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                placeholder="usuario@empresa.com"
                                wire:model="email"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de usuário <span class="text-red-500">*</span></label>
                            <select
                                wire:model="tipo"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                            >
                                <option value="dev">Desenvolvedor</option>
                                <option value="admin">Administrador</option>
                                <option value="visualizador">Visualizador</option>
                                <option value="pagador">Pagador</option>
                                <option value="cobranca">Cobrança</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Alterar senha</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Deixe em branco para manter a senha atual.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nova senha</label>
                            <input
                                type="password"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                autocomplete="new-password"
                                wire:model="password"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Confirmar nova senha</label>
                            <input
                                type="password"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                                autocomplete="new-password"
                                wire:model="password_confirmation"
                            >
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
                        Revise os dados antes de salvar. Alterações no tipo de usuário afetam as permissões de acesso ao ERP.
                    </p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 disabled:opacity-75 disabled:cursor-wait transition-colors"
                    >
                        <span wire:loading.remove wire:target="submit">Salvar Usuário</span>
                        <span wire:loading wire:target="submit">Salvando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
