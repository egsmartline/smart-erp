<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تسوية مخزون جديدة</h2>
            <a href="{{ route('inventory-adjustments.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('inventory-adjustments.store') }}" method="POST" x-data="adjustmentForm()">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="warehouse_id" class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="adjustment_date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="adjustment_date" id="adjustment_date" value="{{ old('adjustment_date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-gray-800">بنود التسوية</h3>
                    <button type="button" @click="addLine()" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 transition">+ إضافة صنف</button>
                </div>

                <template x-for="(line, index) in lines" :key="index">
                    <div class="grid grid-cols-12 gap-3 mb-3 p-3 bg-gray-50 rounded-lg items-end">
                        <div class="col-span-4">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الصنف</label>
                            <select :name="'lines['+index+'][item_id]'" x-model="line.item_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="">اختر الصنف</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الكمية النظرية</label>
                            <input type="number" :name="'lines['+index+'][theoretical_qty]'" x-model.number="line.theoretical_qty" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الكمية الفعلية</label>
                            <input type="number" :name="'lines['+index+'][actual_qty]'" x-model.number="line.actual_qty" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الفرق</label>
                            <input type="text" :value="(line.actual_qty || 0) - (line.theoretical_qty || 0)" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-mono">
                        </div>
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-medium text-gray-600">السبب</label>
                            <input type="text" :name="'lines['+index+'][reason]'" x-model="line.reason" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="removeLine(index)" class="rounded-lg bg-red-500 px-3 py-2 text-sm text-white hover:bg-red-600 transition w-full">حذف</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ التسوية</button>
                <a href="{{ route('inventory-adjustments.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
        function adjustmentForm() {
            return {
                lines: [{ item_id: '', theoretical_qty: 0, actual_qty: 0, reason: '' }],
                addLine() { this.lines.push({ item_id: '', theoretical_qty: 0, actual_qty: 0, reason: '' }); },
                removeLine(i) { if (this.lines.length > 1) this.lines.splice(i, 1); }
            }
        }
    </script>
</x-app-layout>
