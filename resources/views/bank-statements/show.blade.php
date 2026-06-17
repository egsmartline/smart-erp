<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف الحساب: {{ $bankStatement->statement_number }}</h2>
            <div class="flex items-center gap-3">
                @if($bankStatement->state === 'draft')
                    <form action="{{ route('bank-statements.post', $bankStatement) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من ترحيل الكشف؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            ترحيل
                        </button>
                    </form>
                @endif
                <a href="{{ route('bank-statements.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    رجوع
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">رقم الكشف</div>
            <div class="text-lg font-mono font-bold text-gray-900">{{ $bankStatement->statement_number }}</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">الحساب البنكي</div>
            <div class="text-lg font-bold text-gray-900">{{ $bankStatement->bankAccount->account_name ?? '-' }}</div>
            <div class="text-sm text-gray-500">{{ $bankStatement->bankAccount->bank_name ?? '' }}</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">الحالة</div>
            <div>
                @switch($bankStatement->state)
                    @case('draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @break
                    @case('posted')
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">مرحل</span>
                        @break
                    @case('reconciled')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">موفق</span>
                        @break
                @endswitch
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">تفاصيل الأرصدة</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-lg bg-gray-50 p-4 text-center">
                <div class="text-sm text-gray-500 mb-1">الرصيد الافتتاحي</div>
                <div class="text-xl font-bold text-gray-900">{{ number_format($bankStatement->start_balance, 2) }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 text-center">
                <div class="text-sm text-gray-500 mb-1">الرصيد الختامي</div>
                <div class="text-xl font-bold text-blue-600">{{ number_format($bankStatement->end_balance, 2) }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 text-center">
                <div class="text-sm text-gray-500 mb-1">فرق التسوية</div>
                <div class="text-xl font-bold {{ $bankStatement->balance_difference >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ number_format($bankStatement->balance_difference, 2) }}
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-500">التاريخ:</span>
                <span class="font-medium text-gray-900">{{ $bankStatement->date->format('Y-m-d') }}</span>
            </div>
            @if($bankStatement->journal)
                <div>
                    <span class="text-sm text-gray-500">دفتر اليومية:</span>
                    <span class="font-medium text-gray-900">{{ $bankStatement->journal->code }} - {{ $bankStatement->journal->name }}</span>
                </div>
            @endif
            @if($bankStatement->notes)
                <div class="md:col-span-2">
                    <span class="text-sm text-gray-500">ملاحظات:</span>
                    <span class="font-medium text-gray-900">{{ $bankStatement->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بنود الكشف</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">نوع الدفع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">موفق</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankStatement->lines as $line)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600">{{ $line->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $line->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $line->payment_type ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ $line->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($line->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">
                                {{ number_format($line->balance, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($line->is_reconciled)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">موفق</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد بنود في هذا الكشف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
