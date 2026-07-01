<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الحساب: {{ $account->name }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('accounts.edit', $account) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                <a href="{{ route('accounts.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">كود الحساب</div>
            <div class="text-lg font-mono font-bold text-gray-900">{{ $account->code }}</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">اسم الحساب</div>
            <div class="text-lg font-bold text-gray-900">{{ $account->name }}</div>
            @if($account->name_en)
                <div class="text-sm text-gray-500">{{ $account->name_en }}</div>
            @endif
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">الرصيد الحالي</div>
            <div class="text-2xl font-bold {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($account->current_balance, 2) }} {{ session('display_currency', 'EGP') === 'EGP' ? 'ج.م' : '$' }}
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">معلومات الحساب</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="text-sm text-gray-500">النوع</div>
                <div class="font-medium text-gray-900">
                    @switch($account->type)
                        @case('assets') أصول @break
                        @case('liabilities') خصوم @break
                        @case('equity') حقوق ملكية @break
                        @case('revenue') إيرادات @break
                        @case('expenses') مصروفات @break
                    @endswitch
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-500">النوع الفرعي</div>
                <div class="font-medium text-gray-900">{{ $account->sub_type ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">الحساب الأب</div>
                <div class="font-medium text-gray-900">
                    @if($account->parent)
                        {{ $account->parent->code }} - {{ $account->parent->name }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-500">الرصيد الافتتاحي</div>
                <div class="font-medium text-gray-900">{{ number_format($account->opening_balance, 2) }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">الحالة</div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $account->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">القيود اليومية المرتبطة</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">رقم القيد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">مدين</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">دائن</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journalLines as $line)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('journal-entries.show', $line->journalEntry) }}" class="text-blue-600 hover:text-blue-800 font-mono text-xs">
                                    {{ $line->journalEntry->entry_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $line->journalEntry->date }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $line->description ?? $line->journalEntry->description }}</td>
                            <td class="px-4 py-3 text-left font-mono {{ $line->debit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-left font-mono {{ $line->credit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-left font-mono {{ $line->running_balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ number_format($line->running_balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد قيود يومية مرتبطة بهذا الحساب</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $journalLines->links() }}
        </div>
    </div>
</x-app-layout>
