@php
    $expenses = [
        ['id' => 'EXP-2024-001', 'supplier' => 'Medical Equipment Supplies', 'category' => 'Equipment', 'issueDate' => '2024-03-01', 'dueDate' => '2024-03-15', 'amount' => 12000, 'status' => 'Paid'],
        ['id' => 'EXP-2024-002', 'supplier' => 'Laboratory Diagnostics', 'category' => 'Lab Services', 'issueDate' => '2024-03-05', 'dueDate' => '2024-03-20', 'amount' => 8500, 'status' => 'Pending'],
        ['id' => 'EXP-2024-003', 'supplier' => 'Office Supplies Co.', 'category' => 'Office Supplies', 'issueDate' => '2024-02-28', 'dueDate' => '2024-03-14', 'amount' => 1200, 'status' => 'Overdue'],
        ['id' => 'EXP-2024-004', 'supplier' => 'Software Solutions Inc', 'category' => 'Software License', 'issueDate' => '2024-03-01', 'dueDate' => '2024-03-31', 'amount' => 5000, 'status' => 'Pending'],
        ['id' => 'EXP-2024-005', 'supplier' => 'Building Management', 'category' => 'Rent', 'issueDate' => '2024-03-01', 'dueDate' => '2024-03-10', 'amount' => 15000, 'status' => 'Paid'],
        ['id' => 'EXP-2024-006', 'supplier' => 'Professional Training', 'category' => 'Training', 'issueDate' => '2024-03-08', 'dueDate' => '2024-03-22', 'amount' => 3500, 'status' => 'Pending'],
        ['id' => 'EXP-2024-007', 'supplier' => 'Utilities Company', 'category' => 'Utilities', 'issueDate' => '2024-03-01', 'dueDate' => '2024-03-15', 'amount' => 2800, 'status' => 'Paid'],
    ];

    $status = request('status', 'all');
    $category = request('category', 'all');
    $search = request('q', '');

    $filtered = collect($expenses)->filter(function ($expense) use ($status, $category, $search) {
        $matchesSearch = $search === ''
            || str_contains(strtolower($expense['supplier']), strtolower($search))
            || str_contains(strtolower($expense['id']), strtolower($search))
            || str_contains(strtolower($expense['category']), strtolower($search));

        $matchesStatus = $status === 'all' || $expense['status'] === $status;
        $matchesCategory = $category === 'all' || $expense['category'] === $category;

        return $matchesSearch && $matchesStatus && $matchesCategory;
    });

    $totalPayable = $filtered->where('status', '!=', 'Paid')->sum('amount');
    $paidAmount = $filtered->where('status', 'Paid')->sum('amount');
    $overdueAmount = $filtered->where('status', 'Overdue')->sum('amount');
@endphp

<x-layouts.erp>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Accounts Payable</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie pagamentos a fornecedores, categorias de despesas e compromissos em aberto.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
            >
                <span class="w-4 h-4 rounded-full bg-white/30"></span>
                Create Expense
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Total Payable</p>
                <p class="text-3xl font-semibold text-gray-900">
                    ${{ number_format($totalPayable, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Pagamentos ainda não quitados.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Paid This Month</p>
                <p class="text-3xl font-semibold text-green-600">
                    ${{ number_format($paidAmount, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Despesas já liquidadas.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Overdue Amount</p>
                <p class="text-3xl font-semibold text-red-600">
                    ${{ number_format($overdueAmount, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Compromissos vencidos que exigem ação.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Buscar por fornecedor, ID da despesa ou categoria..."
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-300"></span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select
                        name="category"
                        class="text-sm rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                        <option value="all" @selected($category === 'all')>All Categories</option>
                        <option value="Equipment" @selected($category === 'Equipment')>Equipment</option>
                        <option value="Lab Services" @selected($category === 'Lab Services')>Lab Services</option>
                        <option value="Office Supplies" @selected($category === 'Office Supplies')>Office Supplies</option>
                        <option value="Software License" @selected($category === 'Software License')>Software License</option>
                        <option value="Rent" @selected($category === 'Rent')>Rent</option>
                        <option value="Training" @selected($category === 'Training')>Training</option>
                        <option value="Utilities" @selected($category === 'Utilities')>Utilities</option>
                    </select>
                    <select
                        name="status"
                        class="text-sm rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                        <option value="all" @selected($status === 'all')>All Status</option>
                        <option value="Paid" @selected($status === 'Paid')>Paid</option>
                        <option value="Pending" @selected($status === 'Pending')>Pending</option>
                        <option value="Overdue" @selected($status === 'Overdue')>Overdue</option>
                    </select>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        <span class="w-4 h-4 rounded-full bg-gray-400"></span>
                        Export
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Expense ID</th>
                            <th class="px-4 py-3 text-left">Supplier</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Issue Date</th>
                            <th class="px-4 py-3 text-left">Due Date</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($filtered as $expense)
                            @php
                                $statusClasses = match ($expense['status']) {
                                    'Paid' => 'bg-green-100 text-green-700 border-green-200',
                                    'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Overdue' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $expense['id'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $expense['supplier'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-[#6c6f7f]/10 text-[#313e50] border-0">
                                        {{ $expense['category'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $expense['issueDate'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $expense['dueDate'] }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    ${{ number_format($expense['amount'], 0, '.', ',') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $statusClasses }}">
                                        {{ $expense['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button class="h-8 w-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                            <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                                        </button>
                                        <button class="h-8 w-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
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
                    Showing {{ $filtered->count() }} of {{ count($expenses) }} expenses
                </p>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 bg-gray-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>

