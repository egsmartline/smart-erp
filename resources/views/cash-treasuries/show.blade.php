<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الخزينة: {{ $treasury->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('cash-treasuries.edit', $treasury) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('cash-treasuries.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="text-center py-6">
            <div class="text-4xl font-bold {{ $treasury->current_balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency->code ?? 'ج.م' }}</div>
            <div class="text-sm text-gray-500 mt-2">الرصيد الحالي</div>
            <div class="mt-2 text-lg font-bold text-gray-700 print-only hidden">{{ $treasury->name }}</div>
            @if($treasury->whatsapp_number)
                @php
                    $currencyCode = $treasury->currency->code ?? 'ج.م';
                    $waMsg = $treasury->whatsapp_message
                        ? str_replace(['{name}', '{balance}', '{currency}'], [$treasury->name, number_format($treasury->current_balance, 2), $currencyCode], $treasury->whatsapp_message)
                        : 'السلام عليكم رصيد الخزينة ' . $treasury->name . ' الحالي هو ' . number_format($treasury->current_balance, 2) . ' ' . $currencyCode;
                @endphp
                <div class="mt-4 no-print">
                    <a href="https://api.whatsapp.com/send?phone={{ $treasury->whatsapp_number }}&amp;text={{ urlencode($waMsg) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        إرسال عبر واتساب
                    </a>
                    <button onclick="var msg=this.getAttribute('data-msg');navigator.clipboard.writeText(msg);this.innerText='تم النسخ!'" data-msg="{{ $waMsg }}" class="mr-2 rounded-lg bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300 transition">
                        نسخ الرسالة
                    </button>
                    <p class="mt-3 text-xs text-gray-500 bg-gray-50 rounded-lg p-2 border border-gray-100">{{ $waMsg }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Transactions -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 mt-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-800">الحركات على الخزينة</h3>
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
    </div>
</x-app-layout>
