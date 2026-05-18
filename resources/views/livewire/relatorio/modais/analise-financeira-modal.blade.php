<div
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="analise-financeira-modal-titulo"
>
    <div
        class="fixed inset-0 bg-gray-900/50"
        wire:click="fechar"
        aria-hidden="true"
    ></div>

    <div class="flex min-h-full items-end justify-center p-0 pointer-events-none sm:items-center sm:p-6">
        <div
            class="relative z-10 flex max-h-[min(92vh,880px)] w-full max-w-5xl flex-col rounded-t-2xl border border-gray-200 bg-white shadow-xl pointer-events-auto sm:rounded-2xl"
            wire:click.stop
        >
            <header class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Relatório</p>
                    <h2 id="analise-financeira-modal-titulo" class="text-lg font-semibold text-gray-900">Análise financeira — filtros de geração</h2>
                    <p class="mt-1 text-sm text-gray-500">Selecione o período e os blocos de indicadores. A exportação será conectada na próxima etapa.</p>
                </div>
                <button
                    type="button"
                    wire:click="fechar"
                    class="shrink-0 rounded-lg px-2 py-1 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                >
                    Fechar
                </button>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
                <div class="space-y-6">
                    <section class="space-y-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Período</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="af-inicio" class="block text-xs font-medium text-gray-600">Data inicial</label>
                                <input
                                    id="af-inicio"
                                    type="date"
                                    wire:model.live="dataInicio"
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-[#313e50] focus:outline-none focus:ring-1 focus:ring-[#313e50]"
                                >
                                @error('dataInicio')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="af-fim" class="block text-xs font-medium text-gray-600">Data final</label>
                                <input
                                    id="af-fim"
                                    type="date"
                                    wire:model.live="dataFim"
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-[#313e50] focus:outline-none focus:ring-1 focus:ring-[#313e50]"
                                >
                                @error('dataFim')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model.live="compararPeriodoAnterior" class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]">
                            Comparar com o período anterior de mesma duração
                        </label>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Indicadores</h3>
                        <p class="text-xs text-gray-500">Escolha os blocos que devem compor o relatório.</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2.5 text-sm text-gray-700">
                                <input type="checkbox" wire:model.live="incluirMargens" class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]">
                                Margens (bruta, operacional e líquida)
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2.5 text-sm text-gray-700">
                                <input type="checkbox" wire:model.live="incluirComposicao" class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]">
                                Composição de receitas e despesas
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2.5 text-sm text-gray-700">
                                <input type="checkbox" wire:model.live="incluirEvolucao" class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]">
                                Evolução de resultados no período
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2.5 text-sm text-gray-700">
                                <input type="checkbox" wire:model.live="incluirLiquidez" class="rounded border-gray-300 text-[#313e50] focus:ring-[#313e50]">
                                Indicadores de liquidez e endividamento
                            </label>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">Apresentação</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="af-visao" class="block text-xs font-medium text-gray-600">Visão</label>
                                <select
                                    id="af-visao"
                                    wire:model.live="visao"
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-[#313e50] focus:outline-none focus:ring-1 focus:ring-[#313e50]"
                                >
                                    <option value="consolidado">Consolidado</option>
                                    <option value="centro_custo">Por centro de custo</option>
                                </select>
                            </div>
                            <div>
                                <label for="af-formato" class="block text-xs font-medium text-gray-600">Formato de saída</label>
                                <select
                                    id="af-formato"
                                    wire:model.live="formatoSaida"
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-[#313e50] focus:outline-none focus:ring-1 focus:ring-[#313e50]"
                                >
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <footer class="flex shrink-0 flex-col-reverse gap-2 border-t border-gray-100 px-5 py-4 sm:flex-row sm:justify-end sm:gap-3 sm:px-6">
                <button
                    type="button"
                    wire:click="fechar"
                    class="inline-flex justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    wire:click="gerar"
                    wire:loading.attr="disabled"
                    class="inline-flex justify-center rounded-xl bg-[#313e50] px-4 py-2 text-sm font-medium text-white hover:bg-[#313e50]/90 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="gerar">Gerar relatório</span>
                    <span wire:loading wire:target="gerar">Processando…</span>
                </button>
            </footer>
        </div>
    </div>
</div>
