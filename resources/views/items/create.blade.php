<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة صنف جديد</h2>
            <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الصنف <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="sku" class="mb-1 block text-sm font-medium text-gray-700">رمز الصنف (SKU)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="barcode" class="mb-1 block text-sm font-medium text-gray-700">الباركود</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="image" class="mb-1 block text-sm font-medium text-gray-700">صورة الصنف</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 file:mr-2 file:rounded file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label for="category_id" class="mb-1 block text-sm font-medium text-gray-700">التصنيف <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit_id" class="mb-1 block text-sm font-medium text-gray-700">الوحدة <span class="text-red-500">*</span></label>
                    <select name="unit_id" id="unit_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الوحدة</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cost_price" class="mb-1 block text-sm font-medium text-gray-700">سعر الشراء <span class="text-red-500">*</span></label>
                    <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="purchase_currency_id" class="mb-1 block text-sm font-medium text-gray-700">عملة الشراء</label>
                    <select name="purchase_currency_id" id="purchase_currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('purchase_currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="selling_price" class="mb-1 block text-sm font-medium text-gray-700">سعر البيع <span class="text-red-500">*</span></label>
                    <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="sales_currency_id" class="mb-1 block text-sm font-medium text-gray-700">عملة البيع</label>
                    <select name="sales_currency_id" id="sales_currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('sales_currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tax_rate" class="mb-1 block text-sm font-medium text-gray-700">نسبة الضريبة %</label>
                    <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 0) }}" step="0.01" min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="minimum_stock" class="mb-1 block text-sm font-medium text-gray-700">الحد الأدنى للمخزون</label>
                    <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="maximum_stock" class="mb-1 block text-sm font-medium text-gray-700">الحد الأقصى للمخزون</label>
                    <input type="number" name="maximum_stock" id="maximum_stock" value="{{ old('maximum_stock', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="reorder_level" class="mb-1 block text-sm font-medium text-gray-700">مستوى إعادة الطلب</label>
                    <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="opening_stock" class="mb-1 block text-sm font-medium text-gray-700">الرصيد الافتتاحي</label>
                    <input type="number" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="default_warehouse" class="mb-1 block text-sm font-medium text-gray-700">المخزن الافتراضي</label>
                    <select name="default_warehouse" id="default_warehouse" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('default_warehouse') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-6 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_serial_numbers" value="1" {{ old('has_serial_numbers') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">أرقام تسلسلية</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_expiry_date" value="1" {{ old('has_expiry_date') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">تاريخ انتهاء صلاحية</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الصنف
                </button>
                <a href="{{ route('items.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
