<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات المورد: {{ $supplier->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.supplier-statement', ['supplier_id' => $supplier->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">كشف حساب</a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات المورد</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $supplier->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">البريد:</span><span class="font-medium">{{ $supplier->email ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الهاتف:</span><span class="font-medium">{{ $supplier->phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الموبايل:</span><span class="font-medium">{{ $supplier->mobile ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">العنوان:</span><span class="font-medium">{{ $supplier->address ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المدينة:</span><span class="font-medium">{{ $supplier->city ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الرقم الضريبي:</span><span class="font-medium">{{ $supplier->tax_number ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">حد الائتمان:</span><span class="font-medium">{{ number_format($supplier->credit_limit, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">العملات:</span><span class="font-medium">{{ implode(' - ', array_map(fn($g) => $g['currency']?->code ?? 'ج.م', $currencyGroups)) ?: 'ج.م' }}</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الرصيد</h3>
            <div class="space-y-3 text-center py-4">
                @forelse($currencyGroups as $group)
                    @php
                        $bal = $group['openingBalance'];
                        foreach ($group['transactions'] as $tx) { $bal += $tx['amount']; }
                    @endphp
                    <div>
                        <div class="text-2xl font-bold {{ $bal > 0 ? 'text-red-600' : ($bal < 0 ? 'text-emerald-600' : 'text-gray-600') }}">{{ number_format($bal, 2) }}</div>
                        <div class="text-sm text-gray-500">{{ $group['currency']?->code ?? 'ج.م' }}</div>
                    </div>
                @empty
                    <div class="text-lg text-gray-500">0.00</div>
                @endforelse
                <div class="text-sm text-gray-500 mt-2">الرصيد الحالي</div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $supplier->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $supplier->is_active ? 'نشط' : 'غير نشط' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">ملخص</h3>
            <div class="space-y-4">
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="text-2xl font-bold text-blue-700">{{ $supplier->purchaseInvoices->count() }}</div>
                    <div class="text-sm text-blue-600">إجمالي فواتير الشراء</div>
                </div>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $supplier->payments->count() }}</div>
                    <div class="text-sm text-emerald-600">المدفوعات</div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <h3 class="text-lg font-bold text-gray-800">كشف حساب المورد</h3>
        @forelse($currencyGroups as $code => $group)
            @php
                $curCode = $group['currency']?->code ?? 'ج.م';
                $runningBal = $group['openingBalance'];
            @endphp
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <div class="mb-3 flex items-center gap-2">
                    <span class="rounded-lg bg-blue-100 px-3 py-1 text-sm font-bold text-blue-800">{{ $curCode }}</span>
                    <span class="text-sm text-gray-500">الرصيد الافتتاحي:</span>
                    <span class="font-semibold {{ $group['openingBalance'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($group['openingBalance'], 2) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">المرجع</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">العملة</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($group['openingBalance'] != 0)
                                <tr class="border-b border-gray-100 bg-gray-50 font-semibold">
                                    <td class="px-4 py-3">—</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-200 text-gray-700">رصيد افتتاحي</span></td>
                                    <td class="px-4 py-3 font-mono text-xs">—</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format($runningBal, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format($runningBal, 2) }}</td>
                                </tr>
                            @endif
                            @foreach($group['transactions'] as $tx)
                                @php $runningBal += $tx['amount']; @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $tx['date']?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $tx['badge'] }}">{{ $tx['type'] }}</span></td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $tx['reference'] }}</td>
                                    <td class="px-4 py-3 text-left font-mono {{ $tx['amount'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format(abs($tx['amount']), 2) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format($runningBal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <p class="text-center text-gray-500 py-6">لا توجد معاملات</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
