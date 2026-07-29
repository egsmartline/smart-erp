<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">الرئيسية</h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">إجمالي المبيعات</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sales'], 2) }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">إجمالي المشتريات</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_purchases'], 2) }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">العملاء</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['customers_count'] }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">الأصناف</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['items_count'] }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">المبيعات الشهرية - {{ $year ?? date('Y') }}</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">أرصدة العملاء المستحقة</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="balanceChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">آخر فواتير البيع</h3>
            <div class="space-y-3">
                @forelse($recentSales as $sale)
                    <a href="{{ route('sales-invoices.show', $sale) }}" class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <div>
                            <div class="font-mono text-xs text-gray-500">{{ $sale->invoice_number }}</div>
                            <div class="font-medium text-gray-900">{{ $sale->customer->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->date?->format('Y/m/d') }}</div>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-gray-900">{{ number_format($sale->total, 2) }}</div>
                            @if($sale->payment_status === 'paid')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">مدفوع</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">جزئي</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">غير مدفوع</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-4">لا توجد فواتير بعد</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">آخر فواتير الشراء</h3>
            <div class="space-y-3">
                @forelse($recentPurchases as $purchase)
                    <a href="{{ route('purchase-invoices.show', $purchase) }}" class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <div>
                            <div class="font-mono text-xs text-gray-500">{{ $purchase->invoice_number }}</div>
                            <div class="font-medium text-gray-900">{{ $purchase->supplier->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $purchase->date?->format('Y/m/d') }}</div>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-gray-900">{{ number_format($purchase->total, 2) }}</div>
                            @if($purchase->payment_status === 'paid')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">مدفوع</span>
                            @elseif($purchase->payment_status === 'partial')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">جزئي</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">غير مدفوع</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-4">لا توجد فواتير بعد</div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const salesLabels = @json($salesChartLabels);
        const salesData = @json($salesChartData);
        const balanceLabels = @json($balanceChartLabels);
        const balanceData = @json($balanceChartData);

        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'المبيعات',
                    data: salesData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } },
                    x: { ticks: { font: { size: 11 } } }
                }
            }
        });

        new Chart(document.getElementById('balanceChart'), {
            type: 'doughnut',
            data: {
                labels: balanceLabels,
                datasets: [{
                    data: balanceData,
                    backgroundColor: [
                        '#3b82f6','#ef4444','#22c55e','#f59e0b','#8b5cf6',
                        '#ec4899','#06b6d4','#f97316','#14b8a6','#6366f1'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { font: { size: 12 }, padding: 12 } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toLocaleString() + ' ج.م' } }
                }
            }
        });
    </script>
</x-app-layout>
