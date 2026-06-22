<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الحساب البنكي: {{ $bankAccount->bank_name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="text-center py-6">
            <div class="text-4xl font-bold {{ $bankAccount->current_balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($bankAccount->current_balance, 2) }} ج.م</div>
            <div class="text-sm text-gray-500 mt-2">الرصيد الحالي</div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 mt-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-800">الحركات على الحساب البنكي</h3>
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
                    @forelse($payments as $p)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600">{{ $p->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $p->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $p->type === 'receipt' ? 'قبض' : 'صرف' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->notes ?? $p->payment_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->customer->name ?? $p->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ $p->type === 'receipt' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $p->type === 'receipt' ? '+' : '-' }}{{ number_format($p->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد حركات على هذا الحساب البنكي</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
