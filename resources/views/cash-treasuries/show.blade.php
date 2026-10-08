<x-app-layout>
    <style>
        @media print {
            .print-treasury-card { padding: 0.75rem !important; margin-bottom: 0.75rem !important; }
            .print-treasury-card-inner { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
            .print-treasury-balance { font-size: 1.5rem !important; }
            .print-treasury-label { font-size: 0.75rem !important; }
            .print-treasury-name { font-size: 0.875rem !important; }
        }
    </style>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الخزينة: {{ $treasury->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('cash-treasuries.edit', $treasury) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('cash-treasuries.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6 print-treasury-card">
        <div class="text-center py-6 print-treasury-card-inner">
@php
    $hasFilter = !empty($dateFrom) || !empty($dateTo);
    $balance = $hasFilter ? ($displayBalance ?? $treasury->current_balance) : $treasury->current_balance;
@endphp
            <div class="text-4xl print-treasury-balance font-bold {{ $balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($balance, 2) }} {{ $treasury->currency->code ?? 'ج.م' }}</div>
            <div class="text-sm print-treasury-label text-gray-500 mt-2">{{ $hasFilter ? 'الرصيد في الفترة' : 'الرصيد الحالي' }}</div>
            <div class="mt-2 text-lg print-treasury-name font-bold text-gray-700 print-only">{{ $treasury->name }}</div>
            <div class="mt-4 no-print">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 max-w-xl mx-auto">
                    <input id="waTreasuryPhone" type="text" inputmode="tel" value="{{ $treasury->whatsapp_number ?? '' }}" placeholder="رقم الواتساب الدولي (مثال: 9665xxxxxxxx)" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <button type="button" onclick="sendTreasuryBalanceWhatsapp()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition whitespace-nowrap">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.744-6.307C.001 5.322 5.323 0 11.85 0a11.85 11.85 0 018.385 3.458c2.296 2.296 3.465 5.345 3.465 8.385 0 6.527-5.322 11.85-11.85 11.85a11.8 11.8 0 01-6.306-1.743L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.886-9.885 0-2.654-1.035-5.149-2.914-7.028A9.825 9.825 0 0011.85 1.86C6.402 1.86 1.964 6.298 1.964 11.745c0 1.98.534 3.581 1.509 5.094l-1.016 3.729 3.7-1.77z"/></svg>
                        إرسال بالواتساب
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 mt-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-800">الحركات على الخزينة</h3>
                <form method="GET" action="{{ route('cash-treasuries.show', $treasury) }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">بحث</button>
                    @if($dateFrom || $dateTo)
                        <a href="{{ route('cash-treasuries.show', $treasury) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">مسح الفلتر</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الطرف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">بواسطة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600">{{ $t->date }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ in_array($t->type, ['receipt', 'in', 'opening', 'transfer']) ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $t->type === 'receipt' || $t->type === 'in' ? 'قبض' : ($t->type === 'out' ? 'صرف' : ($t->type === 'transfer' ? 'تحويل' : ($t->type === 'opening' ? 'رصيد افتتاحي' : 'صرف'))) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->description }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->party }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ in_array($t->type, ['receipt', 'in', 'opening', 'transfer']) ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ in_array($t->type, ['receipt', 'in', 'opening', 'transfer']) ? '+' : '-' }}{{ number_format($t->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->user_name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد حركات على هذه الخزينة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dateFrom || $dateTo)
            <div class="border-t border-gray-200 px-6 py-3 bg-gray-50 text-sm text-gray-600">
                عدد النتائج: {{ count($transactions) }} حركة
                @php
                    $totalIn = $transactions->filter(fn($t) => in_array($t->type, ['receipt', 'in', 'opening', 'transfer']))->sum('amount');
                    $totalOut = $transactions->filter(fn($t) => !in_array($t->type, ['receipt', 'in', 'opening', 'transfer']))->sum('amount');
                @endphp
                | قبض: <span class="font-bold text-emerald-600">{{ number_format($totalIn, 2) }}</span>
                | صرف: <span class="font-bold text-red-600">{{ number_format($totalOut, 2) }}</span>
                @if($startBalance !== null)
                    | رصيد الفترة: <span class="font-bold text-blue-600">{{ number_format($startBalance, 2) }} → {{ number_format($displayBalance, 2) }}</span>
                @endif
            </div>
        @endif
    </div>

    @php
        $treasuryWa = [
            'name' => $treasury->name,
            'balance' => (float) $treasury->current_balance,
            'currency' => $treasury->currency->code ?? 'ج.م',
        ];
    @endphp

    <script>
        const waTreasuryPhone = document.getElementById('waTreasuryPhone');
        if (waTreasuryPhone && localStorage.getItem('waTreasuryPhone')) {
            waTreasuryPhone.value = localStorage.getItem('waTreasuryPhone');
        } else if (waTreasuryPhone && waTreasuryPhone.value) {
            localStorage.setItem('waTreasuryPhone', waTreasuryPhone.value);
        }
        if (waTreasuryPhone) {
            waTreasuryPhone.addEventListener('input', () => localStorage.setItem('waTreasuryPhone', waTreasuryPhone.value));
        }

        const treasuryWa = @json($treasuryWa);

        function sendTreasuryBalanceWhatsapp() {
            const phone = document.getElementById('waTreasuryPhone').value.replace(/[^0-9]/g, '');
            if (!phone) { alert('يرجى إدخال رقم الواتساب'); return; }
            const msg = 'السلام عليكم\nرصيد الخزينة ' + treasuryWa.name + ' الحالي هو ' + Number(treasuryWa.balance).toFixed(2) + ' ' + treasuryWa.currency;
            window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(msg), '_blank');
        }
    </script>
</x-app-layout>
