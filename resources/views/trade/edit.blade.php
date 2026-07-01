<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل العملية {{ $tradeOperation->operation_number }}</h2>
            <a href="{{ route('trade.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('trade.update', $tradeOperation) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="type" value="{{ $tradeOperation->type }}">

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات أساسية</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم العملية</label>
                    <input type="text" value="{{ $tradeOperation->operation_number }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-500" readonly>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ</label>
                    <input type="date" name="date" value="{{ old('date', $tradeOperation->date->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الجهة</label>
                    <input type="text" name="party_name" value="{{ old('party_name', $tradeOperation->party_name) }}" placeholder="اسم الطرف (عميل/مورد)" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الدولة</label>
                    <input type="text" name="country" value="{{ old('country', $tradeOperation->country) }}" placeholder="دولة الطرف" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ميناء التحميل</label>
                    <input type="text" name="port_of_loading" value="{{ old('port_of_loading', $tradeOperation->port_of_loading) }}" placeholder="ميناء الشحن" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ميناء التفريغ</label>
                    <input type="text" name="port_of_discharge" value="{{ old('port_of_discharge', $tradeOperation->port_of_discharge) }}" placeholder="ميناء الوصول" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Incoterm</label>
                    <select name="incoterm" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر...</option>
                        @foreach(['FOB', 'CIF', 'CFR', 'EXW', 'DAP', 'DDP', 'FCA', 'CPT', 'CIP', 'FAS'] as $term)
                            <option value="{{ $term }}" {{ old('incoterm', $tradeOperation->incoterm) === $term ? 'selected' : '' }}>{{ $term }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">القيمة والعملة</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر...</option>
                        @foreach($currencies as $cur)
                            <option value="{{ $cur->id }}" {{ old('currency_id', $tradeOperation->currency_id) == $cur->id ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">سعر الصرف</label>
                    <input type="number" name="exchange_rate" value="{{ old('exchange_rate', $tradeOperation->exchange_rate ?? 1) }}" step="0.0001" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">إجمالي القيمة</label>
                    <input type="number" name="total_value" value="{{ old('total_value', $tradeOperation->total_value) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الشحن</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">طريقة الشحن</label>
                    <select name="shipping_method" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر...</option>
                        <option value="sea" {{ old('shipping_method', $tradeOperation->shipping_method) === 'sea' ? 'selected' : '' }}>بحري</option>
                        <option value="air" {{ old('shipping_method', $tradeOperation->shipping_method) === 'air' ? 'selected' : '' }}>جوي</option>
                        <option value="land" {{ old('shipping_method', $tradeOperation->shipping_method) === 'land' ? 'selected' : '' }}>بري</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الحاوية</label>
                    <input type="text" name="container_number" value="{{ old('container_number', $tradeOperation->container_number) }}" placeholder="رقم الحاوية" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">اسم السفينة</label>
                    <input type="text" name="vessel_name" value="{{ old('vessel_name', $tradeOperation->vessel_name) }}" placeholder="اسم السفينة/الرحلة" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم بوليصة الشحن (B/L)</label>
                    <input type="text" name="bill_of_lading_number" value="{{ old('bill_of_lading_number', $tradeOperation->bill_of_lading_number) }}" placeholder="رقم بوليصة الشحن" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ المغادرة (ETD)</label>
                    <input type="date" name="etd_date" value="{{ old('etd_date', $tradeOperation->etd_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الوصول (ETA)</label>
                    <input type="date" name="eta_date" value="{{ old('eta_date', $tradeOperation->eta_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">خطاب الاعتماد (LC)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم LC</label>
                    <input type="text" name="lc_number" value="{{ old('lc_number', $tradeOperation->lc_number) }}" placeholder="رقم خطاب الاعتماد" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">البنك المصدر</label>
                    <input type="text" name="lc_issuing_bank" value="{{ old('lc_issuing_bank', $tradeOperation->lc_issuing_bank) }}" placeholder="البنك المصدر" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">البنك المستفيد</label>
                    <input type="text" name="lc_beneficiary_bank" value="{{ old('lc_beneficiary_bank', $tradeOperation->lc_beneficiary_bank) }}" placeholder="البنك المستفيد" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">نوع LC</label>
                    <select name="lc_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر...</option>
                        <option value="sight" {{ old('lc_type', $tradeOperation->lc_type) === 'sight' ? 'selected' : '' }}>At Sight</option>
                        <option value="deferred" {{ old('lc_type', $tradeOperation->lc_type) === 'deferred' ? 'selected' : '' }}>Deferred Payment</option>
                        <option value="standby" {{ old('lc_type', $tradeOperation->lc_type) === 'standby' ? 'selected' : '' }}>Standby</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">قيمة LC</label>
                    <input type="number" name="lc_amount" value="{{ old('lc_amount', $tradeOperation->lc_amount) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الإصدار</label>
                    <input type="date" name="lc_issue_date" value="{{ old('lc_issue_date', $tradeOperation->lc_issue_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الانتهاء</label>
                    <input type="date" name="lc_expiry_date" value="{{ old('lc_expiry_date', $tradeOperation->lc_expiry_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">المصروفات</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">القيمة الجمركية</label>
                    <input type="number" name="customs_value" value="{{ old('customs_value', $tradeOperation->customs_value) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">مبلغ الجمارك</label>
                    <input type="number" name="customs_duty_amount" value="{{ old('customs_duty_amount', $tradeOperation->customs_duty_amount) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تكلفة الشحن</label>
                    <input type="number" name="shipping_cost" value="{{ old('shipping_cost', $tradeOperation->shipping_cost) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">التأمين</label>
                    <input type="number" name="insurance_cost" value="{{ old('insurance_cost', $tradeOperation->insurance_cost) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تكلفة الفحص</label>
                    <input type="number" name="inspection_cost" value="{{ old('inspection_cost', $tradeOperation->inspection_cost) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">مصاريف أخرى</label>
                    <input type="number" name="other_costs" value="{{ old('other_costs', $tradeOperation->other_costs) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ملاحظات</h3>
            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="ملاحظات إضافية...">{{ old('notes', $tradeOperation->notes) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ العملية
            </button>
            <a href="{{ route('trade.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>