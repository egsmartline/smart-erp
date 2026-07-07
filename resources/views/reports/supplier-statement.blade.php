<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف حساب الموردين</h2>
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">المورد</label>
                <select name="supplier_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر المورد</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        @if($supplier)
            <div class="mb-4 text-sm">
                <span class="text-gray-500">المورد: </span>
                <span class="font-semibold">{{ $supplier->name }}</span>
                <span class="mx-4 text-gray-300">|</span>
                <span class="text-gray-500">الرصيد الافتتاحي: </span>
                <span class="font-semibold {{ $openingBalance >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($openingBalance, 2) }}</span>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">البيان</th>
                        <th class="px-4 py-3 font-semibold">المرجع</th>
                        <th class="px-4 py-3 font-semibold text-left">مدين</th>
                        <th class="px-4 py-3 font-semibold text-left">دائن</th>
                        <th class="px-4 py-3 font-semibold text-left">الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBal = $openingBalance; @endphp
                    @if($openingBalance != 0)
                        <tr class="border-b border-gray-100 bg-gray-50 font-semibold">
                            <td class="px-4 py-2">—</td>
                            <td class="px-4 py-2">رصيد افتتاحي</td>
                            <td class="px-4 py-2">—</td>
                            <td class="px-4 py-2 text-left font-mono">{{ $openingBalance > 0 ? number_format($openingBalance, 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ $openingBalance < 0 ? number_format(abs($openingBalance), 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($runningBal, 2) }}</td>
                        </tr>
                    @endif
                    @forelse($transactions as $tx)
                        @php $runningBal += $tx['amount']; @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $tx['date']?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tx['badge'] }}">{{ $tx['type'] }}</span></td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $tx['reference'] }}</td>
                            <td class="px-4 py-2 text-left font-mono text-red-600">{{ $tx['amount'] > 0 ? number_format($tx['amount'], 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ $tx['amount'] < 0 ? number_format(abs($tx['amount']), 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono {{ $runningBal >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($runningBal, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">اختر مورداً لعرض البيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
