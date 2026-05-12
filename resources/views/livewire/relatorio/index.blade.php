<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                Administração &middot; Relatórios
            </p>
            <h1 class="text-2xl font-semibold text-gray-900">Relatórios</h1>
            <p class="text-sm text-gray-500 mt-1">
                Escolha um relatório para ver a descrição e seguir para a geração (filtros e exportação em etapa posterior).
            </p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach ($relatorios as $rel)
            <div
                wire:key="relatorio-{{ $rel['id'] }}"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-shadow hover:shadow-md hover:border-gray-300"
            >
                <button
                    type="button"
                    wire:click="togglePainel(@js($rel['id']))"
                    wire:loading.attr="disabled"
                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left transition-colors hover:bg-gray-50/80 disabled:opacity-60"
                    aria-expanded="{{ $painelAberto === $rel['id'] ? 'true' : 'false' }}"
                >
                    <span class="text-sm font-semibold text-gray-900">{{ $rel['titulo'] }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200 {{ $painelAberto === $rel['id'] ? 'rotate-180' : '' }}"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                @if ($painelAberto === $rel['id'])
                    <div class="border-t border-gray-100" wire:key="relatorio-body-{{ $rel['id'] }}">
                        <div class="px-5 py-4 space-y-4">
                            <p>
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Sobre este relatório</span>
                                <span class="block text-sm text-gray-600 mt-1.5 leading-relaxed">{{ $rel['descricao'] }}</span>
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    wire:click="irParaGeracao(@js($rel['id']))"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90 transition-colors disabled:opacity-60"
                                >
                                    Seguir para geração do relatório
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($openModalDre)
        <livewire:relatorio.modais.d-r-e-modal wire:key="modal-dre-filtros" />
    @endif
</div>
