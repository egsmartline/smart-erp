<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">المدفوعات والقبض</h2>
            <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة دفعة
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="type" class="mb-1 block text-sm font-medium text-gray-700">النوع</label>
                <select name="type" id="type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="receipt" {{ request('type') === 'receipt' ? 'selected' : '' }}>قبض</option>
                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>دفع</option>
                </select>
            </div>
            <div>
                <label for="search" class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الرقم..." class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>
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
                        <th class="px-4 py-3 font-semibold text-gray-700">طريقة الدفع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $payment->payment_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $payment->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $payment->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $payment->type === 'receipt' ? 'قبض' : 'دفع' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $payment->customer->name ?? $payment->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($payment->payment_method === 'cash') نقاط
                                @elseif($payment->payment_method === 'bank_transfer') تحويل بنكي
                                @elseif($payment->payment_method === 'check') شيك
                                @else {{ $payment->payment_method }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($payment->status === 'completed')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">مكتمل</span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">قيد الانتظار</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('payments.show', $payment) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="عرض">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد مدفوعات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $payments->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
