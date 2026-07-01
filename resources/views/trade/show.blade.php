<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-800">{{ $tradeOperation->operation_number }}</h2>
                @php
                    $statusMap = ['draft' => ['bg-gray-100', 'text-gray-800', 'مسودة'], 'confirmed' => ['bg-blue-100', 'text-blue-800', 'مؤكدة'], 'shipped' => ['bg-amber-100', 'text-amber-800', 'تم الشحن'], 'cleared' => ['bg-purple-100', 'text-purple-800', 'تم التخليص'], 'completed' => ['bg-emerald-100', 'text-emerald-800', 'مكتملة'], 'cancelled' => ['bg-red-100', 'text-red-800', 'ملغية']];
                    [$bg, $text, $label] = $statusMap[$tradeOperation->status] ?? ['bg-gray-100', 'text-gray-800', $tradeOperation->status];
                @endphp
                <span class="inline-flex items-center rounded-full {{ $bg }} px-3 py-1 text-xs font-medium {{ $text }}">{{ $label }}</span>
                @if($tradeOperation->type === 'import')
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">استيراد</span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800">تصدير</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('trade.update-status', $tradeOperation) }}" method="POST" class="flex items-center gap-1">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-2 py-1.5 text-xs">
                        @foreach(['draft' => 'مسودة', 'confirmed' => 'مؤكدة', 'shipped' => 'تم الشحن', 'cleared' => 'تم التخليص', 'completed' => 'مكتملة', 'cancelled' => 'ملغية'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $tradeOperation->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('trade.edit', $tradeOperation) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                <a href="{{ route('trade.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">معلومات أساسية</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">رقم العملية</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->operation_number }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">التاريخ</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->date->format('Y-m-d') }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">الجهة</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->party_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">الدولة</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->country ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">ميناء التحميل</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->port_of_loading ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">ميناء التفريغ</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->port_of_discharge ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Incoterm</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->incoterm ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">القيمة والعملة</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">العملة</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->currency?->code ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">سعر الصرف</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->exchange_rate }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">إجمالي القيمة</dt><dd class="text-sm font-bold text-blue-700">{{ number_format($tradeOperation->total_value, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">إجمالي المصروفات</dt><dd class="text-sm font-bold text-amber-700">{{ number_format($tradeOperation->totalCosts(), 2) }}</dd></div>
                @if($tradeOperation->type === 'export')
                <div class="flex justify-between border-t pt-2"><dt class="text-sm font-bold text-gray-600">صافي الربح</dt><dd class="text-sm font-bold text-emerald-700">{{ number_format($tradeOperation->netProfit(), 2) }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">معلومات الشحن</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">طريقة الشحن</dt><dd class="text-sm font-medium text-gray-900">{{ ['sea' => 'بحري', 'air' => 'جوي', 'land' => 'بري'][$tradeOperation->shipping_method] ?? $tradeOperation->shipping_method ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">رقم الحاوية</dt><dd class="text-sm font-mono text-gray-900">{{ $tradeOperation->container_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">اسم السفينة</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->vessel_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">بوليصة الشحن (B/L)</dt><dd class="text-sm font-mono text-gray-900">{{ $tradeOperation->bill_of_lading_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">ETD</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->etd_date?->format('Y-m-d') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">ETA</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->eta_date?->format('Y-m-d') ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">خطاب الاعتماد (LC)</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">رقم LC</dt><dd class="text-sm font-mono font-medium text-gray-900">{{ $tradeOperation->lc_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">البنك المصدر</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->lc_issuing_bank ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">البنك المستفيد</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->lc_beneficiary_bank ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">النوع</dt><dd class="text-sm font-medium text-gray-900">{{ ['sight' => 'At Sight', 'deferred' => 'Deferred', 'standby' => 'Standby'][$tradeOperation->lc_type] ?? $tradeOperation->lc_type ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">القيمة</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->lc_amount ? number_format($tradeOperation->lc_amount, 2) : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">تاريخ الإصدار</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->lc_issue_date?->format('Y-m-d') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">تاريخ الانتهاء</dt><dd class="text-sm font-medium text-gray-900">{{ $tradeOperation->lc_expiry_date?->format('Y-m-d') ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">المصروفات</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-xs text-gray-500">القيمة الجمركية</div>
                    <div class="text-sm font-bold text-gray-900">{{ $tradeOperation->customs_value ? number_format($tradeOperation->customs_value, 2) : '—' }}</div>
                </div>
                <div class="rounded-lg bg-red-50 p-3 text-center">
                    <div class="text-xs text-gray-500">مبلغ الجمارك</div>
                    <div class="text-sm font-bold text-red-700">{{ $tradeOperation->customs_duty_amount ? number_format($tradeOperation->customs_duty_amount, 2) : '—' }}</div>
                </div>
                <div class="rounded-lg bg-blue-50 p-3 text-center">
                    <div class="text-xs text-gray-500">تكلفة الشحن</div>
                    <div class="text-sm font-bold text-blue-700">{{ $tradeOperation->shipping_cost ? number_format($tradeOperation->shipping_cost, 2) : '—' }}</div>
                </div>
                <div class="rounded-lg bg-amber-50 p-3 text-center">
                    <div class="text-xs text-gray-500">التأمين</div>
                    <div class="text-sm font-bold text-amber-700">{{ $tradeOperation->insurance_cost ? number_format($tradeOperation->insurance_cost, 2) : '—' }}</div>
                </div>
                <div class="rounded-lg bg-purple-50 p-3 text-center">
                    <div class="text-xs text-gray-500">الفحص</div>
                    <div class="text-sm font-bold text-purple-700">{{ $tradeOperation->inspection_cost ? number_format($tradeOperation->inspection_cost, 2) : '—' }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-xs text-gray-500">أخرى</div>
                    <div class="text-sm font-bold text-gray-900">{{ $tradeOperation->other_costs ? number_format($tradeOperation->other_costs, 2) : '—' }}</div>
                </div>
            </div>
        </div>

        @if($tradeOperation->notes)
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">ملاحظات</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $tradeOperation->notes }}</p>
        </div>
        @endif
    </div>
</x-app-layout>