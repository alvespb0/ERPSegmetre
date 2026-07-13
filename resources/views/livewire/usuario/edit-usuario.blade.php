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

                        <div class="md:col-span-2" 
                            @if(auth()->user()->isDev())
                            x-data="{
                                tipo: @entangle('tipo'),
                                open: false,
                                search: '',
                                selected: @entangle('empresa_parametro_ids'),
                                empresas: {{ \Illuminate\Support\Js::from($empresasParametro->map(fn ($e) => ['id' => $e->id, 'nome' => $e->razao_social ?? $e->nome_fantasia])->values()) }},
                                get showEmpresas() {
                                    return this.tipo && this.tipo !== 'dev';
                                },
                                get filteredEmpresas() {
                                    const term = this.search.trim().toLowerCase();
                                    if (!term) return this.empresas;
                                    return this.empresas.filter(e => e.nome.toLowerCase().includes(term));
                                },
                                isSelected(id) {
                                    return this.selected.includes(id);
                                },
                                toggle(id) {
                                    if (this.isSelected(id)) {
                                        this.selected = this.selected.filter(item => item !== id);
                                    } else {
                                        this.selected.push(id);
                                    }
                                },
                                remove(id) {
                                    this.selected = this.selected.filter(item => item !== id);
                                },
                                empresaNome(id) {
                                    return this.empresas.find(e => e.id === id)?.nome ?? '';
                                }
                            }"
                            @endif
                        >
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de usuário <span class="text-red-500">*</span></label>
                            <select
                                wire:model.live="tipo"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                            >
                                <option value="" disabled>Selecione...</option>
                                <option value="dev">Desenvolvedor</option>
                                <option value="admin">Administrador</option>
                                <option value="visualizador">Visualizador</option>
                                <option value="pagador">Pagador</option>
                                <option value="cobranca">Cobrança</option>
                            </select>
                            @error('tipo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                            @if(auth()->user()->isDev())
                                <div
                                    x-show="showEmpresas"
                                    x-cloak
                                    x-transition.opacity.duration.200ms
                                    class="mt-4 space-y-2"
                                    @click.outside="open = false"
                                >
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Empresas com acesso <span class="text-red-500">*</span></label>
                                        <p class="text-[11px] text-gray-500 mt-0.5">Selecione em quais empresas este usuário poderá atuar.</p>
                                    </div>

                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="flex min-h-[2.75rem] w-full items-center justify-between gap-3 rounded-lg border px-3 py-2 text-left text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                                            :class="{{ $errors->has('empresa_parametro_ids') ? "'border-red-300 bg-red-50/40'" : "'border-gray-200 bg-white hover:border-gray-300'" }}"
                                        >
                                            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5">
                                                <template x-if="selected.length === 0">
                                                    <span class="text-gray-400">Selecione uma ou mais empresas...</span>
                                                </template>
                                                <template x-for="id in selected" :key="id">
                                                    <span class="inline-flex max-w-full items-center gap-1 rounded-md bg-[#313e50]/10 px-2 py-0.5 text-xs font-medium text-[#313e50]">
                                                        <span class="truncate" x-text="empresaNome(id)"></span>
                                                        <button
                                                            type="button"
                                                            class="rounded p-0.5 text-[#313e50]/60 hover:bg-[#313e50]/20 hover:text-[#313e50]"
                                                            @click.stop="remove(id)"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </template>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown das empresas -->
                                        <div
                                            x-show="open"
                                            x-transition.origin.top.duration.150ms
                                            class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
                                        >
                                            <div class="border-b border-gray-100 p-3">
                                                <div class="relative">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                                    </svg>
                                                    <input
                                                        type="text"
                                                        x-model="search"
                                                        placeholder="Buscar empresa..."
                                                        class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-[#313e50] focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                                                    >
                                                </div>
                                            </div>

                                            <ul class="max-h-56 overflow-y-auto py-1">
                                                <template x-if="filteredEmpresas.length === 0">
                                                    <li class="px-4 py-3 text-sm text-gray-400">Nenhuma empresa encontrada.</li>
                                                </template>
                                                <template x-for="empresa in filteredEmpresas" :key="empresa.id">
                                                    <li>
                                                        <button
                                                            type="button"
                                                            @click="toggle(empresa.id)"
                                                            class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors hover:bg-gray-50"
                                                            :class="isSelected(empresa.id) ? 'bg-[#313e50]/5 text-[#313e50]' : 'text-gray-700'"
                                                        >
                                                            <span
                                                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-colors"
                                                                :class="isSelected(empresa.id) ? 'border-[#313e50] bg-[#313e50] text-white' : 'border-gray-300 bg-white'"
                                                            >
                                                                <svg x-show="isSelected(empresa.id)" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                            </span>
                                                            <span class="truncate" x-text="empresa.nome"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                    @error('empresa_parametro_ids') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
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