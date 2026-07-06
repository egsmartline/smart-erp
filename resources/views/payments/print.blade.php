<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">طباعة المدفوعات والقبض</h2>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2 text-center">
                <span class="text-sm font-medium text-emerald-700">إجمالي القبض: </span>
                <span class="text-lg font-bold text-emerald-600">{{ number_format($totalReceipts, 2) }}</span>
            </div>
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-center">
                <span class="text-sm font-medium text-red-700">إجمالي الصرف: </span>
                <span class="text-lg font-bold text-red-600">{{ number_format($totalPayments, 2) }}</span>
            </div>
            <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-2 text-center">
                <span class="text-sm font-medium text-blue-700">الرصيد: </span>
                <span class="text-lg font-bold {{ $totalReceipts - $totalPayments >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($totalReceipts - $totalPayments, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">رقم</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الشخص</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">طريقة الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $payment->payment_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $payment->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $payment->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $payment->type === 'receipt' ? 'قبض' : 'صرف' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $payment->customer->name ?? $payment->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $payment->notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($payment->payment_method === 'cash') نقداً
                                @elseif($payment->payment_method === 'bank_transfer') تحويل بنكي
                                @elseif($payment->payment_method === 'check') شيك
                                @else {{ $payment->payment_method }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد مدفوعات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 text-center no-print">
        <button onclick="window.print()" class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 transition">
            طباعة
        </button>
        <a href="{{ route('payments.index', request()->except('print')) }}" class="mr-2 rounded-lg bg-gray-600 px-6 py-3 text-sm font-medium text-white hover:bg-gray-700 transition">
            رجوع
        </a>
    </div>
</x-app-layout>
