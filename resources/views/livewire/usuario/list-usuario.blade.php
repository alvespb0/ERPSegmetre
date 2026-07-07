<div>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    CADASTROS &middot; USUÁRIOS
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Visualize e gerencie os usuários com acesso ao ERP.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('erp.usuarios.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
                >
                    Novo Usuário
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Ativos</p>
                <p class="text-3xl font-semibold text-gray-900">
                    {{ $usuarios->getCollection()->whereNull('deleted_at')->count() }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Usuários com acesso habilitado.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Inativos</p>
                <p class="text-3xl font-semibold text-gray-900">
                    {{ $usuarios->getCollection()->whereNotNull('deleted_at')->count() }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Usuários desativados no sistema.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Com 2FA</p>
                <p class="text-3xl font-semibold text-emerald-600">
                    {{ $usuarios->getCollection()->where('two_factor_enabled', true)->count() }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Autenticação em dois fatores ativa.</p>
            </div>
        </div>

        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-1.5 flex flex-col md:flex-row items-center transition-all focus-within:ring-2 focus-within:ring-[#313e50] focus-within:border-transparent hover:shadow-md">
            <div class="relative flex-1 w-full flex items-center">
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Buscar por nome ou e-mail..."
                    class="w-full pl-10 pr-4 py-2 text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-700 placeholder-gray-400"
                >
            </div>

            <div class="hidden md:block w-px h-6 bg-gray-200 mx-2"></div>

            <div class="flex flex-row items-center w-full md:w-auto gap-1 border-t md:border-t-0 border-gray-100 pt-2 md:pt-0 mt-2 md:mt-0">
                <select
                    name="tipo"
                    class="flex-1 md:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-30 rounded-lg hover:bg-gray-50 transition-colors"
                    wire:model.live="tipo"
                >
                    <option value="todos">Todos os tipos</option>
                    <option value="dev">Desenvolvedor</option>
                    <option value="admin">Administrador</option>
                    <option value="visualizador">Visualizador</option>
                    <option value="pagador">Pagador</option>
                    <option value="cobranca">Cobrança</option>
                </select>

                <select
                    name="status"
                    class="flex-1 md:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-30 rounded-lg hover:bg-gray-50 transition-colors"
                    wire:model.live="status"
                >
                    <option value="todos">Todos os status</option>
                    <option value="ativo">Ativos</option>
                    <option value="inativo">Inativos</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-left">E-mail</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">2FA</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Cadastro</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $usuario->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border border-gray-200 bg-gray-50 text-gray-700">
                                        {{ $usuario->tipoLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $usuario->two_factor_enabled ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                                        {{ $usuario->two_factor_enabled ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $usuario->deleted_at ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                        {{ $usuario->deleted_at ? 'Inativo' : 'Ativo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $usuario->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-right" x-data="{ open: false }">
                                    <button
                                        @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        :class="open ? 'bg-gray-50 ring-2 ring-[#313e50]' : ''"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                        aria-haspopup="menu"
                                        :aria-expanded="open"
                                    >
                                        Ações
                                        <svg class="ml-1.5 w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click="open = false" class="fixed inset-0 z-40"></div>

                                    <div
                                        x-show="open"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 z-50 w-44 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg focus:outline-none"
                                        @click.away="open = false"
                                        style="display: none;"
                                    >
                                        <div class="py-1">
                                            <button
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                wire:click="editarUsuario({{ $usuario->id }})"
                                            >
                                                Editar
                                            </button>

                                            <div class="h-px bg-gray-100 my-1"></div>

                                            @if($usuario->deleted_at)
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50"
                                                    wire:click="ativarUsuario({{ $usuario->id }})"
                                                >
                                                    Reativar
                                                </button>
                                            @elseif($usuario->id !== auth()->id())
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    wire:click="inativarUsuario({{ $usuario->id }})"
                                                >
                                                    Inativar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ $usuarios->firstItem() ?? 0 }}</span>
                    a <span class="font-medium text-gray-900">{{ $usuarios->lastItem() ?? 0 }}</span>
                    de <span class="font-medium text-gray-900">{{ $usuarios->total() }}</span> resultados
                </p>
                <div class="flex gap-2">
                    <button
                        @if($usuarios->onFirstPage()) disabled @else wire:click="previousPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $usuarios->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Anterior
                    </button>
                    <button
                        @if(!$usuarios->hasMorePages()) disabled @else wire:click="nextPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$usuarios->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
