<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">التحويلات بين الخزن والحسابات البنكية والحسابات المالية</h2>
            <a href="{{ route('transfers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                تحويل جديد
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">من</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">إلى</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">بواسطة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $t->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @php
                                    $sourceName = '-';
                                    if ($t instanceof \App\Models\TreasuryTransaction) {
                                        if ($t->treasury_id) {
                                            $sourceName = $t->treasury->name ?? '-';
                                        } elseif ($t->target_treasury_id) {
                                            $sourceName = optional(\App\Models\Account::find($t->target_treasury_id))->name ?? 'حساب مالي';
                                        }
                                    } else {
                                        $sourceName = $t->bankAccount->account_name ?? '-';
                                    }
                                @endphp
                                {{ $sourceName }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @php
                                    $targetName = '-';
                                    if ($t->reference_type === 'treasury') {
                                        $targetName = optional(\App\Models\CashTreasury::find($t->reference_id))->name ?? '-';
                                    } elseif ($t->reference_type === 'bank') {
                                        $targetName = optional(\App\Models\BankAccount::find($t->reference_id))->account_name ?? '-';
                                    } elseif ($t->reference_type === 'account') {
                                        $targetName = optional(\App\Models\Account::find($t->reference_id))->name ?? 'حساب مالي';
                                    } else {
                                        $targetName = $t->targetTreasury->name ?? $t->targetBankAccount->account_name ?? '-';
                                    }
                                @endphp
                                {{ $targetName }}
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-emerald-600">{{ number_format($t->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $t->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $t->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('transfers.edit', $t->id) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('transfers.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التحويل؟ سيتم عكس القيود المحاسبية.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد تحويلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
