<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف حساب العملاء</h2>
            <div class="flex items-center gap-2">
                @if($customer)
                    <a href="{{ route('reports.customer-statement.export', request()->only(['customer_id', 'date_from', 'date_to'])) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                        تصدير Excel
                    </a>
                @endif
                <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">العميل</label>
                <select name="customer_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر العميل</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ $customerId == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">من</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">إلى</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
        </form>
    </div>

    @if($customer)
        <div class="mb-4 text-sm flex flex-wrap items-center gap-x-4 gap-y-1">
            <span class="text-gray-500">العميل: </span>
            <span class="font-semibold">{{ $customer->name }}</span>
        </div>

        @if(!empty($currencyGroups))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($currencyGroups as $code => $group)
                    @php
                        $curCode = $group['currency']?->code ?? 'ج.م';
                        $curName = $group['currency']?->name ?? 'جنيه مصرى';
                    @endphp
                    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800">{{ $curCode }}</span>
                            <span class="text-xs text-gray-500">{{ $curName }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mb-1">إجمالي {{ $curCode }}</div>
                        <div class="text-2xl font-bold {{ $group['total'] > 0 ? 'text-red-600' : ($group['total'] < 0 ? 'text-emerald-600' : 'text-gray-700') }}">
                            {{ number_format($group['total'], 2) }}
                        </div>
                        <div class="mt-2 text-xs text-gray-600 space-y-0.5">
                            <div>الرصيد الافتتاحي: {{ number_format($group['openingBalance'], 2) }}</div>
                            <div>
                                @if($group['total'] > 0)
                                    مدين بـ {{ number_format($group['total'], 2) }}
                                @elseif($group['total'] < 0)
                                    دائن بـ {{ number_format(abs($group['total']), 2) }}
                                @else
                                    الرصيد صفر
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @forelse($currencyGroups as $code => $group)
            @php
                $curCode = $group['currency']?->code ?? 'ج.م';
                $curSym = $group['currency']?->symbol ?? 'ج.م';
            @endphp
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-4">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="rounded-lg bg-blue-100 px-3 py-1 text-sm font-bold text-blue-800">{{ $curCode }}</span>
                    <span class="text-sm text-gray-500">الرصيد الافتتاحي:</span>
                    <span class="font-semibold {{ $group['openingBalance'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($group['openingBalance'], 2) }}</span>
                    <span class="text-sm text-gray-500">إجمالي {{ $curCode }}:</span>
                    <span class="font-bold {{ $group['total'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($group['total'], 2) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-300 bg-gray-50">
                                <th class="px-4 py-3 font-semibold">التاريخ</th>
                                <th class="px-4 py-3 font-semibold">البيان</th>
                                <th class="px-4 py-3 font-semibold">المرجع</th>
                                <th class="px-4 py-3 font-semibold text-left">مدين ({{ $curCode }})</th>
                                <th class="px-4 py-3 font-semibold text-left">دائن ({{ $curCode }})</th>
                                <th class="px-4 py-3 font-semibold">العملة</th>
                                <th class="px-4 py-3 font-semibold text-left">الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBal = $group['openingBalance']; @endphp
                            @if($group['openingBalance'] != 0)
                                <tr class="border-b border-gray-100 bg-gray-50 font-semibold">
                                    <td class="px-4 py-2">—</td>
                                    <td class="px-4 py-2">رصيد افتتاحي</td>
                                    <td class="px-4 py-2">—</td>
                                    <td class="px-4 py-2 text-left font-mono">{{ $group['openingBalance'] > 0 ? number_format($group['openingBalance'], 2) : '-' }}</td>
                                    <td class="px-4 py-2 text-left font-mono">{{ $group['openingBalance'] < 0 ? number_format(abs($group['openingBalance']), 2) : '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-2 text-left font-mono">{{ number_format($runningBal, 2) }}</td>
                                </tr>
                            @endif
                            @foreach($group['transactions'] as $tx)
                                @php $runningBal += $tx['amount']; @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $tx['date']?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tx['badge'] }}">{{ $tx['type'] }}</span></td>
                                    <td class="px-4 py-2 font-mono text-xs">@isset($tx['route'])<a href="{{ route($tx['route'], $tx['id']) }}" class="text-blue-600 hover:text-blue-800 underline">@endisset{{ $tx['reference'] }}@isset($tx['route'])</a>@endisset</td>
                                    <td class="px-4 py-2 text-left font-mono text-red-600">{{ $tx['amount'] > 0 ? number_format($tx['amount'], 2) : '-' }}</td>
                                    <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ $tx['amount'] < 0 ? number_format(abs($tx['amount']), 2) : '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-2 text-left font-mono {{ $runningBal >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($runningBal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                                <td class="px-4 py-3" colspan="3">إجمالي {{ $curCode }}</td>
                                <td class="px-4 py-3 text-left font-mono text-red-600">{{ $group['totalDebit'] != 0 ? number_format($group['totalDebit'], 2) : '-' }}</td>
                                <td class="px-4 py-3 text-left font-mono text-emerald-600">{{ $group['totalCredit'] != 0 ? number_format($group['totalCredit'], 2) : '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $curCode }}</td>
                                <td class="px-4 py-3 text-left font-mono {{ $group['total'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($group['total'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <p class="text-center text-gray-500 py-8">لا توجد حركات في هذه الفترة</p>
            </div>
        @endforelse
    @else
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-center text-gray-500 py-8">اختر عميلاً لعرض البيانات</p>
        </div>
    @endif
</x-app-layout>