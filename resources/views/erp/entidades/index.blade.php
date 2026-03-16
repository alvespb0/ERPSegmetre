@php
    $filtroTipo = request('tipo', 'todos');
    $filtroStatus = request('status', 'todos');
    $busca = request('q', '');

    $entidades = [
        [
            'codigo' => 'CLI-0001',
            'nome' => 'TechCorp Indústria de Tecnologia LTDA',
            'fantasia' => 'TechCorp',
            'tipo' => 'Cliente',
            'documento' => '12.345.678/0001-90',
            'segmento' => 'Indústria',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'contato' => 'Ana Souza',
            'email' => 'ana.souza@techcorp.com',
            'telefone' => '(11) 4000-2000',
            'status' => 'Ativo',
        ],
        [
            'codigo' => 'CLI-0002',
            'nome' => 'BuildPro Engenharia e Construções S/A',
            'fantasia' => 'BuildPro',
            'tipo' => 'Cliente',
            'documento' => '98.765.432/0001-10',
            'segmento' => 'Construção',
            'cidade' => 'Campinas',
            'uf' => 'SP',
            'contato' => 'Carlos Lima',
            'email' => 'carlos.lima@buildpro.com.br',
            'telefone' => '(19) 3555-9000',
            'status' => 'Ativo',
        ],
        [
            'codigo' => 'FOR-0001',
            'nome' => 'Medical Equipment Supplies LTDA',
            'fantasia' => 'MedSupplies',
            'tipo' => 'Fornecedor',
            'documento' => '45.678.912/0001-55',
            'segmento' => 'Saúde',
            'cidade' => 'Curitiba',
            'uf' => 'PR',
            'contato' => 'Juliana Costa',
            'email' => 'juliana.costa@medsupplies.com',
            'telefone' => '(41) 3200-8080',
            'status' => 'Ativo',
        ],
        [
            'codigo' => 'CLI-0003',
            'nome' => 'Logistics Express Transportes Rápidos LTDA',
            'fantasia' => 'Logistics Express',
            'tipo' => 'Cliente',
            'documento' => '23.456.789/0001-77',
            'segmento' => 'Logística',
            'cidade' => 'Guarulhos',
            'uf' => 'SP',
            'contato' => 'Marcos Pereira',
            'email' => 'marcos.pereira@logisticsexpress.com',
            'telefone' => '(11) 3789-1212',
            'status' => 'Ativo',
        ],
        [
            'codigo' => 'FOR-0002',
            'nome' => 'Office Supplies Co. Comércio de Papéis',
            'fantasia' => 'Office Supplies',
            'tipo' => 'Fornecedor',
            'documento' => '56.890.123/0001-44',
            'segmento' => 'Comércio',
            'cidade' => 'Santo André',
            'uf' => 'SP',
            'contato' => 'Fernanda Dias',
            'email' => 'fernanda.dias@officesupplies.com',
            'telefone' => '(11) 3255-6677',
            'status' => 'Inativo',
        ],
    ];

    $filtradas = collect($entidades)->filter(function ($entidade) use ($filtroTipo, $filtroStatus, $busca) {
        $matchBusca = $busca === ''
            || str_contains(strtolower($entidade['nome']), strtolower($busca))
            || str_contains(strtolower($entidade['fantasia']), strtolower($busca))
            || str_contains(strtolower($entidade['codigo']), strtolower($busca))
            || str_contains(strtolower($entidade['documento']), strtolower($busca))
            || str_contains(strtolower($entidade['contato']), strtolower($busca));

        $matchTipo = $filtroTipo === 'todos' || strtolower($entidade['tipo']) === strtolower($filtroTipo);
        $matchStatus = $filtroStatus === 'todos' || strtolower($entidade['status']) === strtolower($filtroStatus);

        return $matchBusca && $matchTipo && $matchStatus;
    });

    $totalClientes = collect($entidades)->where('tipo', 'Cliente')->count();
    $totalFornecedores = collect($entidades)->where('tipo', 'Fornecedor')->count();
    $totalAtivos = collect($entidades)->where('status', 'Ativo')->count();
@endphp

<x-layouts.erp>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    Cadastros &middot; Entidades
                </p>
                <h1 class="text-2xl font-semibold text-gray-900">Entidades</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Visualize e gerencie clientes, fornecedores e demais entidades vinculadas à clínica.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('erp.entidades.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
                >
                    <span class="w-4 h-4 rounded-full bg-white/30"></span>
                    Nova Entidade
                </a>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                    Exportar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Clientes</p>
                <p class="text-3xl font-semibold text-gray-900">
                    {{ $totalClientes }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Entidades do tipo cliente ativas ou inativas.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Fornecedores</p>
                <p class="text-3xl font-semibold text-gray-900">
                    {{ $totalFornecedores }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Parceiros responsáveis pelo fornecimento de serviços ou insumos.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Entidades Ativas</p>
                <p class="text-3xl font-semibold text-emerald-600">
                    {{ $totalAtivos }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Entidades disponíveis para uso em lançamentos financeiros.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ $busca }}"
                        placeholder="Buscar por nome, fantasia, documento, código ou contato..."
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-300"></span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select
                        name="tipo"
                        class="text-sm rounded-lg border border-gray-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                        <option value="todos" @selected($filtroTipo === 'todos')>Todos os tipos</option>
                        <option value="Cliente" @selected($filtroTipo === 'Cliente')>Clientes</option>
                        <option value="Fornecedor" @selected($filtroTipo === 'Fornecedor')>Fornecedores</option>
                    </select>

                    <select
                        name="status"
                        class="text-sm rounded-lg border border-gray-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                        <option value="todos" @selected($filtroStatus === 'todos')>Todos os status</option>
                        <option value="Ativo" @selected($filtroStatus === 'Ativo')>Ativos</option>
                        <option value="Inativo" @selected($filtroStatus === 'Inativo')>Inativos</option>
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        <span class="w-4 h-4 rounded-full bg-gray-400"></span>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Código</th>
                            <th class="px-4 py-3 text-left">Nome / Fantasia</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">Documento</th>
                            <th class="px-4 py-3 text-left">Segmento</th>
                            <th class="px-4 py-3 text-left">Localização</th>
                            <th class="px-4 py-3 text-left">Contato</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($filtradas as $entidade)
                            @php
                                $statusClasses = $entidade['status'] === 'Ativo'
                                    ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                                    : 'bg-gray-100 text-gray-600 border-gray-200';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $entidade['codigo'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium truncate max-w-xs">
                                            {{ $entidade['nome'] }}
                                        </span>
                                        <span class="text-[11px] text-gray-500">
                                            {{ $entidade['fantasia'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-[#6c6f7f]/10 text-[#313e50] border-0">
                                        {{ $entidade['tipo'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $entidade['documento'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $entidade['segmento'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $entidade['cidade'] }} / {{ $entidade['uf'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 text-sm">
                                            {{ $entidade['contato'] }}
                                        </span>
                                        <span class="text-[11px] text-gray-500">
                                            {{ $entidade['email'] }} &middot; {{ $entidade['telefone'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $statusClasses }}">
                                        {{ $entidade['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button class="h-8 w-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50" title="Detalhes">
                                            <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                                        </button>
                                        <button class="h-8 w-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50" title="Editar">
                                            <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex items-center justify-between text-xs text-gray-500">
                <p>
                    Exibindo {{ $filtradas->count() }} de {{ count($entidades) }} entidades cadastradas (dados simulados).
                </p>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 bg-gray-50" disabled>
                        Anterior
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

