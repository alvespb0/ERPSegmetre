@php
    $invoices = [
        ['id' => 'INV-2024-001', 'company' => 'TechCorp Industries', 'service' => 'Annual Medical Examination', 'due' => '2024-03-31', 'amount' => 15000, 'status' => 'Paid'],
        ['id' => 'INV-2024-002', 'company' => 'BuildPro Construction', 'service' => 'Audiometry Test (15 employees)', 'due' => '2024-04-05', 'amount' => 4500, 'status' => 'Pending'],
        ['id' => 'INV-2024-003', 'company' => 'SafetyFirst Corp', 'service' => 'Spirometry Examination', 'due' => '2024-03-15', 'amount' => 3200, 'status' => 'Overdue'],
        ['id' => 'INV-2024-004', 'company' => 'Manufacturing Solutions Ltd', 'service' => 'Vision Test - Pre-employment', 'due' => '2024-04-10', 'amount' => 8000, 'status' => 'Pending'],
        ['id' => 'INV-2024-005', 'company' => 'Oil & Gas Services', 'service' => 'Complete Health Package', 'due' => '2024-04-08', 'amount' => 25000, 'status' => 'Pending'],
    ];

    $expenses = [
        ['id' => 'EXP-2024-001', 'supplier' => 'Medical Equipment Supplies', 'category' => 'Equipment', 'due' => '2024-03-15', 'amount' => 12000, 'status' => 'Paid'],
        ['id' => 'EXP-2024-002', 'supplier' => 'Laboratory Diagnostics', 'category' => 'Lab Services', 'due' => '2024-03-20', 'amount' => 8500, 'status' => 'Pending'],
        ['id' => 'EXP-2024-003', 'supplier' => 'Office Supplies Co.', 'category' => 'Office Supplies', 'due' => '2024-03-14', 'amount' => 1200, 'status' => 'Overdue'],
        ['id' => 'EXP-2024-004', 'supplier' => 'Software Solutions Inc', 'category' => 'Software License', 'due' => '2024-03-31', 'amount' => 5000, 'status' => 'Pending'],
    ];

    $recentActivities = [
        ['id' => 1, 'type' => 'invoice', 'description' => 'Invoice INV-2024-008 created for Retail Group SA', 'amount' => 7500, 'date' => '2024-03-12'],
        ['id' => 2, 'type' => 'payment', 'description' => 'Payment received from TechCorp Industries', 'amount' => 15000, 'date' => '2024-03-11'],
        ['id' => 3, 'type' => 'expense', 'description' => 'Paid Medical Equipment Supplies invoice', 'amount' => -12000, 'date' => '2024-03-10'],
        ['id' => 4, 'type' => 'payment', 'description' => 'Payment received from Logistics Express', 'amount' => 6800, 'date' => '2024-03-09'],
    ];

    $totalReceivable = collect($invoices)->where('status', '!=', 'Paid')->sum('amount');
    $totalPayable = collect($expenses)->where('status', '!=', 'Paid')->sum('amount');
    $currentMonthRevenue = collect($invoices)->where('status', 'Paid')->sum('amount');
    $overdueInvoices = collect($invoices)->where('status', 'Overdue');
@endphp

<x-layouts.erp>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">
                Visão geral financeira da clínica e principais indicadores.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Accounts Receivable</p>
                <p class="text-2xl font-semibold text-gray-900">
                    ${{ number_format($totalReceivable, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Valores a receber de empresas contratantes.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Accounts Payable</p>
                <p class="text-2xl font-semibold text-gray-900">
                    ${{ number_format($totalPayable, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Compromissos com fornecedores e despesas recorrentes.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Monthly Revenue</p>
                <p class="text-2xl font-semibold text-gray-900">
                    ${{ number_format($currentMonthRevenue, 0, '.', ',') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Faturamento confirmado por pagamentos recebidos.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Overdue Invoices</p>
                <p class="text-2xl font-semibold text-red-600">
                    {{ $overdueInvoices->count() }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Títulos em atraso que exigem atenção imediata.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900">Upcoming Payments</h2>
                </div>
                <div class="space-y-3">
                    @foreach (collect($invoices)->where('status', 'Pending')->take(5) as $invoice)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $invoice['company'] }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice['service'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">Due: {{ $invoice['due'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($invoice['amount'], 0, '.', ',') }}
                                </p>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[11px] border border-[#5c6672] text-[#313e50] bg-white">
                                    {{ $invoice['status'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900">Overdue Invoices</h2>
                </div>
                <div class="space-y-3">
                    @forelse ($overdueInvoices as $invoice)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $invoice['company'] }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice['service'] }}</p>
                                <p class="text-xs text-red-600 mt-1">Overdue since: {{ $invoice['due'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($invoice['amount'], 0, '.', ',') }}
                                </p>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[11px] bg-red-500 text-white">
                                    {{ $invoice['status'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">
                            Nenhuma fatura em atraso no momento.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-[#313e50]"></span>
                Recent Financial Activities
            </h2>
            <div class="space-y-3">
                @foreach ($recentActivities as $activity)
                    <div class="flex items-center justify-between border-b border-gray-100 last:border-0 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if ($activity['type'] === 'invoice') bg-blue-100
                                @elseif ($activity['type'] === 'payment') bg-green-100
                                @else bg-orange-100 @endif
                            ">
                                <span class="w-4 h-4 rounded-full
                                    @if ($activity['type'] === 'invoice') bg-blue-500
                                    @elseif ($activity['type'] === 'payment') bg-green-500
                                    @else bg-orange-500 @endif
                                "></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['date'] }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold {{ $activity['amount'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $activity['amount'] > 0 ? '+' : '' }}${{ number_format(abs($activity['amount']), 0, '.', ',') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.erp>
