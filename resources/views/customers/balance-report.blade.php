<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تقرير أرصدة العملاء المدينون</h2>
            <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للعملاء</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">

        <div class="mb-6 flex flex-wrap items-center gap-4">
            <div class="rounded-lg bg-red-50 border border-red-200 px-5 py-3">
                <span class="text-sm text-red-700">إجمالي المستحق: </span>
                <span class="text-lg font-bold text-red-800">{{ number_format($totalReceivable, 2) }} ج.م</span>
            </div>
            <div class="rounded-lg bg-blue-50 border border-blue-200 px-5 py-3">
                <span class="text-sm text-blue-700">عدد العملاء: </span>
                <span class="text-lg font-bold text-blue-800">{{ $count }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-3 py-2 font-semibold text-gray-700 text-xs w-[5%]">#</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-xs w-[36%]">اسم العميل</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-xs w-[12%]">التصنيف</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-center text-xs w-[9%]">عدد الفواتير</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-center text-xs w-[9%]">عدد الدفعات</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-left text-xs w-[16%]">المستحق</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-center text-xs w-[8%]">العملة</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-center text-xs w-[15%]">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-3 py-2 font-mono text-xs font-bold text-gray-600">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('customers.show', $customer) }}" class="font-medium text-gray-900 hover:text-blue-600 text-xs">{{ $customer->name }}</a>
                            </td>
                            <td class="px-3 py-2">
                                @if($customer->classification)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $customer->classification === 'gold' ? 'bg-yellow-100 text-yellow-800' : ($customer->classification === 'silver' ? 'bg-gray-100 text-gray-800' : ($customer->classification === 'bronze' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                        {{ $customer->classification === 'gold' ? 'ذهبي' : ($customer->classification === 'silver' ? 'فضي' : ($customer->classification === 'bronze' ? 'برونزي' : 'عادي')) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center font-mono text-xs">{{ $customer->salesInvoices->count() }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs">{{ $customer->payments->count() }}</td>
                            <td class="px-3 py-2 text-left font-mono text-xs font-bold text-red-600">{{ number_format($customer->real_balance, 2) }}</td>
                            <td class="px-3 py-2 text-center text-xs font-medium text-gray-600">{{ $customer->openingBalanceCurrency?->code ?? 'ج.م' }}</td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    كشف حساب
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-gray-500 text-xs">لا يوجد عملاء عليهم مبالغ مستحقة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
