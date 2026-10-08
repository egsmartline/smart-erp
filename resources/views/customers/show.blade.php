<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات العميل: {{ $customer->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات العميل</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $customer->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">البريد:</span><span class="font-medium">{{ $customer->email ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الهاتف:</span><span class="font-medium">{{ $customer->phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الموبايل:</span><span class="font-medium">{{ $customer->mobile ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">العنوان:</span><span class="font-medium">{{ $customer->address ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المدينة:</span><span class="font-medium">{{ $customer->city ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الدولة:</span><span class="font-medium">{{ $customer->country ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الرقم الضريبي:</span><span class="font-medium">{{ $customer->tax_number ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">حد الائتمان:</span><span class="font-medium">{{ number_format($customer->credit_limit, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">عملة الرصيد:</span><span class="font-medium">{{ $customer->openingBalanceCurrency?->code ?? 'ج.م' }}</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الرصيد</h3>
                <div class="text-center py-6">
                    @php $shownBalances = $currencyBalances ?? []; @endphp
                    @forelse($shownBalances as $curCode => $amount)
                        <div class="mb-2">
                            <div class="text-3xl font-bold {{ $amount > 0 ? 'text-red-600' : ($amount < 0 ? 'text-emerald-600' : 'text-gray-600') }}">
                                {{ number_format($amount, 2) }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">{{ $curCode }} — {{ $amount >= 0 ? 'مدين' : 'دائن' }}</div>
                        </div>
                    @empty
                        <div class="text-3xl font-bold text-gray-600">0.00</div>
                        <div class="text-sm text-gray-500 mt-1">ج.م</div>
                    @endforelse
                    <div class="text-sm text-gray-500 mt-2">الرصيد الحالي</div>
                </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">التصنيف:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($customer->classification ?? '') === 'gold' ? 'bg-yellow-100 text-yellow-800' : (($customer->classification ?? '') === 'silver' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $customer->classification === 'gold' ? 'ذهبي' : ($customer->classification === 'silver' ? 'فضي' : ($customer->classification === 'bronze' ? 'برونزي' : 'عادي')) }}
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">ملخص</h3>
            <div class="space-y-4">
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="text-2xl font-bold text-blue-700">{{ $customer->salesInvoices->count() }}</div>
                    <div class="text-sm text-blue-600">إجمالي الفواتير</div>
                </div>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $customer->payments->count() }}</div>
                    <div class="text-sm text-emerald-600">المدفوعات</div>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ activeTab: 'statement' }" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-1 border-b border-gray-200 mb-4">
            <button @click="activeTab = 'statement'" :class="activeTab === 'statement' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition">كشف حساب العميل</button>
            <button @click="activeTab = 'receivables'" :class="activeTab === 'receivables' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition">
                تقرير المستحقات
                <span class="mr-1 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $receivableCustomers->count() }}</span>
            </button>
        </div>

        {{-- Tab 1: Customer Statement --}}
        <div x-show="activeTab === 'statement'">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">المرجع</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">العملة</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $openingBal = (float) ($customer->opening_balance ?? 0);
                            $openingAmount = $customer->opening_balance_type === 'credit' ? -$openingBal : $openingBal;
                            $openingCurId = (int) ($customer->opening_balance_currency_id ?: $baseCurrencyId);
                            $transactions = collect();

                            foreach ($customer->salesInvoices as $inv) {
                                $transactions->push([
                                    'currency_id' => (int) ($inv->currency_id ?: $baseCurrencyId),
                                    'date' => $inv->date,
                                    'type' => 'invoice',
                                    'type_label' => 'فاتورة بيع',
                                    'badge_class' => 'bg-blue-100 text-blue-800',
                                    'reference' => $inv->invoice_number,
                                    'amount' => (float) $inv->total,
                                    'sort' => $inv->date->format('Y-m-d') . '|' . $inv->created_at,
                                ]);
                            }

                            foreach ($customer->payments as $pay) {
                                $amount = (float) $pay->amount;
                                if ($pay->type === 'receipt') $amount = -$amount;
                                $transactions->push([
                                    'currency_id' => (int) ($pay->currency_id ?: $baseCurrencyId),
                                    'date' => $pay->date,
                                    'type' => 'payment',
                                    'type_label' => $pay->payment_method === 'bank_transfer' ? 'تحويل بنكي' : ($pay->payment_method === 'check' ? 'شيك' : 'نقداً'),
                                    'badge_class' => 'bg-emerald-100 text-emerald-800',
                                    'reference' => $pay->payment_number,
                                    'amount' => $amount,
                                    'sort' => ($pay->date ? $pay->date->format('Y-m-d') : '0000-00-00') . '|' . $pay->created_at,
                                ]);
                            }

                            foreach ($customer->discountNotes as $dn) {
                                $transactions->push([
                                    'currency_id' => $baseCurrencyId,
                                    'date' => $dn->date,
                                    'type' => 'discount',
                                    'type_label' => 'إشعار خصم',
                                    'badge_class' => 'bg-orange-100 text-orange-800',
                                    'reference' => $dn->note_number,
                                    'amount' => -(float) $dn->amount,
                                    'sort' => ($dn->date ? $dn->date->format('Y-m-d') : '0000-00-00') . '|' . $dn->created_at,
                                ]);
                            }

                            $transactions = $transactions->sortBy('sort');

                            $txGroups = $transactions->groupBy('currency_id');
                            if ($openingAmount != 0 && !$txGroups->has($openingCurId)) {
                                $txGroups->put($openingCurId, collect());
                            }
                            if ($txGroups->isEmpty()) {
                                $txGroups->put($baseCurrencyId, collect());
                            }
                            $txGroups = $txGroups->sortBy(fn($v, $k) => $k == $baseCurrencyId ? 0 : 1)->sortBy(fn($v, $k) => (int) $k);
                        @endphp

                        @php $anyRow = false; @endphp
                        @foreach($txGroups as $curId => $groupTxs)
                            @php
                                $curCode = $currencyCodes[$curId] ?? '—';
                                $groupTxs = $groupTxs->sortBy('sort');
                                $runningBalance = ($curId == $openingCurId) ? $openingAmount : 0.0;
                                $groupNet = 0.0;
                            @endphp
                            <tr class="bg-slate-800 text-white">
                                <td colspan="6" class="px-4 py-2 text-sm font-bold">
                                    كشف حساب <span class="rounded bg-white/20 px-2 py-0.5">{{ $curCode }}</span>
                                    <span class="mr-2 text-xs font-normal text-slate-300">({{ $groupTxs->count() }} حركة)</span>
                                </td>
                            </tr>

                            @if($curId == $openingCurId && $openingAmount != 0)
                                @php $anyRow = true; @endphp
                                <tr class="border-b border-gray-100 bg-gray-50 font-semibold">
                                    <td class="px-4 py-3">—</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-200 text-gray-700">رصيد افتتاحي</span></td>
                                    <td class="px-4 py-3 font-mono text-xs">—</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format(abs($openingAmount), 2) }}</td>
                                    <td class="px-4 py-3 text-center text-xs font-medium text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @endif

                            @if($groupTxs->isEmpty() && !($curId == $openingCurId && $openingAmount != 0))
                                <tr><td colspan="6" class="px-4 py-5 text-center text-gray-500">لا توجد معاملات بعملة {{ $curCode }}</td></tr>
                            @endif

                            @foreach($groupTxs as $tx)
                                @php $runningBalance += $tx['amount']; $groupNet += $tx['amount']; $anyRow = true; @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $tx['date']?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $tx['badge_class'] }}">{{ $tx['type_label'] }}</span></td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $tx['reference'] }}</td>
                                    <td class="px-4 py-3 text-left font-mono {{ $tx['amount'] >= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format(abs($tx['amount']), 2) }}</td>
                                    <td class="px-4 py-3 text-center text-xs font-medium text-gray-600">{{ $curCode }}</td>
                                    <td class="px-4 py-3 text-left font-mono">{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @endforeach

                            <tr class="border-t-2 border-gray-300 bg-gray-100 font-bold text-gray-800">
                                <td class="px-4 py-3" colspan="3">إجمالي رصيد {{ $curCode }}</td>
                                <td class="px-4 py-3 text-left font-mono">{{ number_format($groupNet + (($curId == $openingCurId) ? $openingAmount : 0), 2) }}</td>
                                <td class="px-4 py-3 text-center text-xs">{{ $curCode }}</td>
                                <td class="px-4 py-3 text-left font-mono text-base">{{ number_format($runningBalance, 2) }}</td>
                            </tr>
                        @endforeach
                        @if(!$anyRow)
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">لا توجد معاملات</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 2: Receivables Report --}}
        <div x-show="activeTab === 'receivables'">
            <div class="mb-4 flex items-center gap-4">
                <div class="rounded-lg bg-red-50 border border-red-200 px-5 py-3">
                    <span class="text-sm text-red-700">إجمالي المستحق: </span>
                    @forelse($receivableTotals ?? [] as $curCode => $amount)
                        <span class="text-lg font-bold text-red-800">{{ number_format($amount, 2) }} {{ $curCode }}</span>
                        {{ !$loop->last ? ' ·' : '' }}
                    @empty
                        <span class="text-lg font-bold text-red-800">0.00 ج.م</span>
                    @endforelse
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-200 px-5 py-3">
                    <span class="text-sm text-blue-700">عدد العملاء: </span>
                    <span class="text-lg font-bold text-blue-800">{{ $receivableCustomers->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">اسم العميل</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">الهاتف</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">التصنيف</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">فواتير</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">دفعات</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">المستحق</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">العملة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivableCustomers as $c)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition {{ $c->id === $customer->id ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('customers.show', $c) }}" class="font-medium {{ $c->id === $customer->id ? 'text-yellow-700 font-bold' : 'text-gray-900 hover:text-blue-600' }}">{{ $c->name }} {{ $c->id === $customer->id ? '(هذا العميل)' : '' }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $c->phone ?? $c->mobile ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($c->classification)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $c->classification === 'gold' ? 'bg-yellow-100 text-yellow-800' : ($c->classification === 'silver' ? 'bg-gray-100 text-gray-800' : ($c->classification === 'bronze' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ $c->classification === 'gold' ? 'ذهبي' : ($c->classification === 'silver' ? 'فضي' : ($c->classification === 'bronze' ? 'برونزي' : 'عادي')) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-sm">{{ $c->salesInvoices->count() }}</td>
                                <td class="px-4 py-3 text-center font-mono text-sm">{{ $c->payments->count() }}</td>
                                <td class="px-4 py-3 text-left font-mono text-sm font-bold text-red-600">
                                    @php $rowBalances = collect($c->currency_balances)->filter(fn($v) => $v > 0.009); @endphp
                                    @foreach($rowBalances as $curCode => $amount)
                                        <div class="whitespace-nowrap">{{ number_format($amount, 2) }} <span class="text-xs font-semibold">{{ $curCode }}</span></div>
                                    @endforeach
                                    @if($rowBalances->isEmpty())
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs font-medium text-gray-600">
                                    {{ $rowBalances->keys()->implode('، ') ?: ($c->openingBalanceCurrency?->code ?? 'ج.م') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">لا يوجد عملاء عليهم مبالغ مستحقة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
