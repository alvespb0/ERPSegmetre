@php
    $escopos = [
        'sistema' => 'Sistema',
        'banco' => 'Banco',
        'fiscal' => 'Fiscal',
        'externo' => 'Externo',
    ];
    $autenticacoes = [
        'none' => 'Nenhuma',
        'basic' => 'Basic',
        'bearer' => 'Bearer',
        'oauth2' => 'OAuth2',
        'mtls' => 'mTLS',
        'outro' => 'Outro',
    ];
@endphp

<div>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Dev &middot; Integrações
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Integrações</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie conexões com APIs, bancos, serviços fiscais e sistemas externos.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('erp.dev.integracoes.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
                >
                    Nova Integração
                </a>
            </div>
        </div>

        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-1.5 flex flex-col md:flex-row items-center transition-all focus-within:ring-2 focus-within:ring-[#313e50] focus-within:border-transparent hover:shadow-md">
            <div class="relative flex-1 w-full flex items-center">
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Buscar por nome, slug ou endpoint..."
                    class="w-full pl-10 pr-4 py-2 text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-700 placeholder-gray-400"
                >
            </div>

            <div class="hidden md:block w-px h-6 bg-gray-200 mx-2"></div>

            <div class="flex flex-row items-center w-full md:w-auto gap-1 border-t md:border-t-0 border-gray-100 pt-2 md:pt-0 mt-2 md:mt-0">
                <select
                    class="flex-1 md:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-12 rounded-lg hover:bg-gray-50 transition-colors"
                    wire:model.live="escopo"
                >
                    <option value="todos">Todos os escopos</option>
                    @foreach ($escopos as $valor => $label)
                        <option value="{{ $valor }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select
                    class="flex-1 md:flex-none text-sm bg-transparent border-transparent focus:border-transparent focus:ring-0 outline-none text-gray-600 cursor-pointer py-2 px-12 rounded-lg hover:bg-gray-50 transition-colors"
                    wire:model.live="status"
                >
                    <option value="todos">Todos os status</option>
                    <option value="ativo">Ativas</option>
                    <option value="inativo">Inativas</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-left">Slug</th>
                            <th class="px-4 py-3 text-left">Escopo</th>
                            <th class="px-4 py-3 text-left">Tecnologia</th>
                            <th class="px-4 py-3 text-left">Autenticação</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($integracoes as $integracao)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $integracao->nome }}</div>
                                    @if ($integracao->nativa)
                                        <span class="inline-flex mt-1 items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">Nativa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $integracao->slug }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $escopos[$integracao->escopo] ?? $integracao->escopo }}</td>
                                <td class="px-4 py-3 uppercase text-xs text-gray-600">{{ $integracao->tecnologia }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $autenticacoes[$integracao->autenticacao] ?? $integracao->autenticacao }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $integracao->deleted_at ? 'text-red-600 border-red-200 bg-red-50' : 'text-emerald-700 border-emerald-200 bg-emerald-50' }}">
                                        {{ $integracao->deleted_at ? 'Inativa' : 'Ativa' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right relative overflow-visible" x-data="{ open: false }">
                                    <button
                                        @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        :class="open ? 'bg-gray-50 ring-2 ring-[#313e50]' : ''"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#313e50] transition-all"
                                    >
                                        Ações
                                        <svg class="ml-1.5 w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click="open = false" class="fixed inset-0 z-40"></div>

                                    <div
                                        x-show="open"
                                        x-transition
                                        class="absolute right-0 z-50 w-44 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg focus:outline-none"
                                        @click.away="open = false"
                                        style="display: none;"
                                    >
                                        <div class="py-1">
                                            <button
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#313e50]"
                                                wire:click="editarIntegracao({{ $integracao->id }})"
                                            >
                                                Editar
                                            </button>
                                            <div class="h-px bg-gray-100 my-1"></div>
                                            @if ($integracao->deleted_at)
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50"
                                                    wire:click="ativarIntegracao({{ $integracao->id }})"
                                                >
                                                    Ativar
                                                </button>
                                            @else
                                                <button
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    wire:click="inativarIntegracao({{ $integracao->id }})"
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
                                    Nenhuma integração encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                <p>
                    Mostrando <span class="font-medium text-gray-900">{{ $integracoes->firstItem() ?? 0 }}</span>
                    a <span class="font-medium text-gray-900">{{ $integracoes->lastItem() ?? 0 }}</span>
                    de <span class="font-medium text-gray-900">{{ $integracoes->total() }}</span> resultados
                </p>
                <div class="flex gap-2">
                    <button
                        @if($integracoes->onFirstPage()) disabled @else wire:click="previousPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ $integracoes->onFirstPage() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Anterior
                    </button>
                    <button
                        @if(!$integracoes->hasMorePages()) disabled @else wire:click="nextPage" @endif
                        class="px-3 py-1.5 rounded-lg border border-gray-200 transition-colors {{ !$integracoes->hasMorePages() ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                    >
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
