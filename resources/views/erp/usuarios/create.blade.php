<x-layouts.erp>
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                CADASTROS &middot; USUÁRIOS
            </p>
            <h1 class="text-2xl font-semibold text-gray-900">Novo Usuário</h1>
            <p class="text-sm text-gray-500 mt-1">Cadastre um novo usuário com acesso ao ERP.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <form
                method="POST"
                action="{{ route('erp.usuarios.store') }}"
                class="space-y-5"
                @if(auth()->user()->isDev())
                x-data="{
                    tipo: '{{ old('tipo', '') }}',
                    open: false,
                    search: '',
                    selected: {{ json_encode(array_map('intval', old('empresa_parametro_ids', []))) }},
                    empresas: {{ $empresas->map(fn ($e) => ['id' => $e->id, 'nome' => $e->razao_social ?? $e->nome_fantasia])->values() }},
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
                @csrf

                <div class="space-y-1.5">
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="email" :value="__('E-mail')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="tipo" :value="__('Tipo de usuário')" />
                    <select
                        id="tipo"
                        name="tipo"
                        required
                        @if(auth()->user()->isDev()) x-model="tipo" @endif
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#313e50] focus:ring-[#313e50]"
                    >
                        <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>Selecione...</option>
                        @foreach (['dev' => 'Desenvolvedor', 'admin' => 'Administrador', 'visualizador' => 'Visualizador', 'pagador' => 'Pagador', 'cobranca' => 'Cobrança'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>

                @if(auth()->user()->isDev())
                    <div
                        x-show="showEmpresas"
                        x-cloak
                        x-transition.opacity.duration.200ms
                        class="space-y-2"
                        @click.outside="open = false"
                    >
                        <div>
                            <x-input-label value="Empresas com acesso" />
                            <p class="text-xs text-gray-500 mt-0.5">Selecione em quais empresas este usuário poderá atuar.</p>
                        </div>

                        <div class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex min-h-[2.75rem] w-full items-center justify-between gap-3 rounded-lg border px-3 py-2 text-left text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#313e50]/20"
                                :class="{{ $errors->has('empresa_parametro_ids') || $errors->has('empresa_parametro_ids.*') ? "'border-red-300 bg-red-50/40'" : "'border-gray-300 bg-white hover:border-gray-400'" }}"
                            >
                                <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5">
                                    <template x-if="selected.length === 0">
                                        <span class="text-gray-400">Selecione uma ou mais empresas...</span>
                                    </template>
                                    <template x-for="id in selected" :key="id">
                                        <span class="inline-flex max-w-full items-center gap-1 rounded-md bg-[#2C394B]/10 px-2 py-0.5 text-xs font-medium text-[#2C394B]">
                                            <span class="truncate" x-text="empresaNome(id)"></span>
                                            <button
                                                type="button"
                                                class="rounded p-0.5 text-[#2C394B]/60 hover:bg-[#2C394B]/10 hover:text-[#2C394B]"
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
                                                :class="isSelected(empresa.id) ? 'bg-[#2C394B]/5 text-[#2C394B]' : 'text-gray-700'"
                                            >
                                                <span
                                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-colors"
                                                    :class="isSelected(empresa.id) ? 'border-[#2C394B] bg-[#2C394B] text-white' : 'border-gray-300 bg-white'"
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

                        <template x-for="id in selected" :key="'input-' + id">
                            <input type="hidden" name="empresa_parametro_ids[]" :value="id">
                        </template>

                        <x-input-error :messages="$errors->get('empresa_parametro_ids')" class="mt-2" />
                        <x-input-error :messages="$errors->get('empresa_parametro_ids.*')" class="mt-2" />
                    </div>
                @endif

                <div class="space-y-1.5">
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="space-y-1.5">
                    <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('erp.usuarios.index') }}" class="text-sm text-gray-500 hover:text-[#313e50] hover:underline">
                        Voltar para listagem
                    </a>
                    <x-primary-button>Cadastrar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>
