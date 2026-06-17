<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تحويل مخزون جديد</h2>
            <a href="{{ route('stock-transfers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('stock-transfers.store') }}" method="POST" x-data="transferForm()">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="source_warehouse_id" class="mb-1 block text-sm font-medium text-gray-700">مخزن المصدر <span class="text-red-500">*</span></label>
                    <select name="source_warehouse_id" id="source_warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="destination_warehouse_id" class="mb-1 block text-sm font-medium text-gray-700">مخزن الوجهة <span class="text-red-500">*</span></label>
                    <select name="destination_warehouse_id" id="destination_warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="transfer_date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="transfer_date" id="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-gray-800">بنود التحويل</h3>
                    <button type="button" @click="addLine()" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 transition">+ إضافة صنف</button>
                </div>

                <template x-for="(line, index) in lines" :key="index">
                    <div class="grid grid-cols-12 gap-3 mb-3 p-3 bg-gray-50 rounded-lg items-end">
                        <div class="col-span-6">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الصنف</label>
                            <select :name="'lines['+index+'][item_id]'" x-model="line.item_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="">اختر الصنف</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label class="mb-1 block text-xs font-medium text-gray-600">الكمية</label>
                            <input type="number" :name="'lines['+index+'][quantity]'" x-model.number="line.quantity" step="0.01" min="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <button type="button" @click="removeLine(index)" class="rounded-lg bg-red-500 px-3 py-2 text-sm text-white hover:bg-red-600 transition w-full">حذف</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ التحويل</button>
                <a href="{{ route('stock-transfers.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
        function transferForm() {
            return {
                lines: [{ item_id: '', quantity: 1 }],
                addLine() { this.lines.push({ item_id: '', quantity: 1 }); },
                removeLine(i) { if (this.lines.length > 1) this.lines.splice(i, 1); }
            }
        }
    </script>
</x-app-layout>
