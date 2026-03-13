@php
    $invoices = [
        ['id' => 'INV-2024-001', 'company' => 'TechCorp Industries', 'service' => 'Annual Medical Examination', 'issueDate' => '2024-03-01', 'dueDate' => '2024-03-31', 'amount' => 15000, 'status' => 'Paid'],
        ['id' => 'INV-2024-002', 'company' => 'BuildPro Construction', 'service' => 'Audiometry Test (15 employees)', 'issueDate' => '2024-03-05', 'dueDate' => '2024-04-05', 'amount' => 4500, 'status' => 'Pending'],
        ['id' => 'INV-2024-003', 'company' => 'SafetyFirst Corp', 'service' => 'Spirometry Examination', 'issueDate' => '2024-02-15', 'dueDate' => '2024-03-15', 'amount' => 3200, 'status' => 'Overdue'],
        ['id' => 'INV-2024-004', 'company' => 'Manufacturing Solutions Ltd', 'service' => 'Vision Test - Pre-employment', 'issueDate' => '2024-03-10', 'dueDate' => '2024-04-10', 'amount' => 8000, 'status' => 'Pending'],
        ['id' => 'INV-2024-005', 'company' => 'Oil & Gas Services', 'service' => 'Complete Health Package', 'issueDate' => '2024-03-08', 'dueDate' => '2024-04-08', 'amount' => 25000, 'status' => 'Pending'],
        ['id' => 'INV-2024-006', 'company' => 'Logistics Express', 'service' => 'Drug Testing Program', 'issueDate' => '2024-02-28', 'dueDate' => '2024-03-28', 'amount' => 6800, 'status' => 'Paid'],
        ['id' => 'INV-2024-007', 'company' => 'Chemical Industries Inc', 'service' => 'Toxicology Screening', 'issueDate' => '2024-02-10', 'dueDate' => '2024-03-10', 'amount' => 5200, 'status' => 'Overdue'],
        ['id' => 'INV-2024-008', 'company' => 'Retail Group SA', 'service' => 'Ergonomic Assessment', 'issueDate' => '2024-03-12', 'dueDate' => '2024-04-12', 'amount' => 7500, 'status' => 'Pending'],
    ];

    $status = request('status', 'all');
    $search = request('q', '');

    $filtered = collect($invoices)->filter(function ($invoice) use ($status, $search) {
        $matchesSearch = $search === ''
            || str_contains(strtolower($invoice['company']), strtolower($search))
            || str_contains(strtolower($invoice['id']), strtolower($search))
            || str_contains(strtolower($invoice['service']), strtolower($search));

        $matchesStatus = $status === 'all' || $invoice['status'] === $status;

        return $matchesSearch && $matchesStatus;
    });

    $totalReceivable = $filtered->where('status', '!=', 'Paid')->sum('amount');
    $paidAmount = $filtered->where('status', 'Paid')->sum('amount');
    $overdueAmount = $filtered->where('status', 'Overdue')->sum('amount');
@endphp

<x-layouts.erp>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Accounts Receivable</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie faturas emitidas, acompanhe recebimentos e valores em aberto.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#313e50] text-white text-sm font-medium hover:bg-[#313e50]/90"
            >
                <span class="w-4 h-4 rounded-full bg-white/30"></span>
                Create Invoice
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Total Receivable</p>
                <p class="text-3xl font-semibold text-gray-900">
                    ${{ number_format($totalReceivable, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Pagamentos ainda não recebidos.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Paid This Month</p>
                <p class="text-3xl font-semibold text-green-600">
                    ${{ number_format($paidAmount, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Receita já confirmada.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-2">Overdue Amount</p>
                <p class="text-3xl font-semibold text-red-600">
                    ${{ number_format($overdueAmount, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Valores em atraso que exigem ação.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Buscar por empresa, número da fatura ou serviço..."
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#313e50] focus:border-[#313e50]"
                    >
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-300"></span>
                </div>
                <div class="flex gap-2">
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
                            <th class="px-4 py-3 text-left">Invoice ID</th>
                            <th class="px-4 py-3 text-left">Company</th>
                            <th class="px-4 py-3 text-left">Service / Exam</th>
                            <th class="px-4 py-3 text-left">Issue Date</th>
                            <th class="px-4 py-3 text-left">Due Date</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($filtered as $invoice)
                            @php
                                $statusClasses = match ($invoice['status']) {
                                    'Paid' => 'bg-green-100 text-green-700 border-green-200',
                                    'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Overdue' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $invoice['id'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $invoice['company'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
                                    {{ $invoice['service'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $invoice['issueDate'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $invoice['dueDate'] }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    ${{ number_format($invoice['amount'], 0, '.', ',') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border {{ $statusClasses }}">
                                        {{ $invoice['status'] }}
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
                    Showing {{ $filtered->count() }} of {{ count($invoices) }} invoices
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

