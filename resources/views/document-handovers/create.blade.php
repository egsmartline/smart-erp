<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">حافظة مستندات جديدة</h2>
            <a href="{{ route('document-handovers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                العودة للقائمة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('document-handovers.store') }}" method="POST">
        @csrf

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الحافظة</label>
                    <input type="text" value="{{ $handoverNumber }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الاستلام <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الشحنة</label>
                    <input type="text" name="shipment_ref" value="{{ old('shipment_ref') }}" placeholder="خاص بشحنة رقم..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">استلمت أنا الموقع أدناه</label>
                    <input type="text" name="receiver_name" value="{{ old('receiver_name') }}" placeholder="اسم المستلم"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">بصفتي</label>
                    <input type="text" name="receiver_role" value="{{ old('receiver_role') }}" placeholder="المسمى الوظيفي"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تابع لشركة / جهة</label>
                    <input type="text" name="receiver_entity" value="{{ old('receiver_entity') }}" placeholder="اسم الشركة / الجهة"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم بطاقة الرقم القومي</label>
                    <input type="text" name="receiver_id_card" value="{{ old('receiver_id_card') }}" placeholder="رقم البطاقة"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الصادرة من</label>
                    <input type="text" name="receiver_id_issuer" value="{{ old('receiver_id_issuer') }}" placeholder="جهة الإصدار"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" name="receiver_address" value="{{ old('receiver_address') }}" placeholder="عنوان المستلم"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">من شركة</label>
                <input type="text" name="company_name" value="{{ old('company_name', $company->name ?? '') }}" placeholder="شركة سمارت لاين"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">بيان المستندات المسلمة</h3>
                <button type="button" onclick="addItem()"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    إضافة مستند
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm" id="items-table">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:40px"><span title="تسليم">تسليم</span></th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:50px">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">اسم المستند <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:100px">عدد النسخ</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">ملاحظات</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        @php
                            $rows = old('items') ?? array_map(fn($name) => ['document_name' => $name, 'copies' => 0, 'notes' => '', 'selected' => true], $defaultItems);
                        @endphp
                        @foreach($rows as $index => $item)
                            <tr class="border-b border-gray-100 item-row">
                                <td class="px-4 py-2 text-center">
                                    <input type="hidden" name="items[{{ $index }}][selected]" value="0">
                                    <input type="checkbox" name="items[{{ $index }}][selected]" value="1" @checked(!empty($item['selected'])) class="h-4 w-4 cursor-pointer accent-emerald-600">
                                </td>
                                <td class="px-4 py-2 text-center text-gray-500 item-index">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">
                                    <input type="text" name="items[{{ $index }}][document_name]" value="{{ $item['document_name'] ?? '' }}"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <input type="number" name="items[{{ $index }}][copies]" value="{{ $item['copies'] ?? 0 }}" min="0" class="w-20 rounded-lg border border-gray-300 px-2 py-2 text-sm text-center focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="items[{{ $index }}][notes]" value="{{ $item['notes'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" onclick="removeItem(this)" class="rounded p-1 text-gray-400 hover:text-red-600 transition cursor-pointer" title="حذف">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="ملاحظات إضافية...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ الحافظة
            </button>
            <a href="{{ route('document-handovers.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>

    <script>
        function reindex() {
            document.querySelectorAll('#items-body .item-row').forEach((row, i) => {
                row.querySelector('.item-index').textContent = i + 1;
                row.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${i}]`);
                });
            });
        }

        function addItem() {
            const tbody = document.getElementById('items-body');
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 item-row';
            tr.innerHTML = `
                <td class="px-4 py-2 text-center">
                    <input type="hidden" name="items[${tbody.children.length}][selected]" value="0">
                    <input type="checkbox" name="items[${tbody.children.length}][selected]" value="1" checked class="h-4 w-4 cursor-pointer accent-emerald-600">
                </td>
                <td class="px-4 py-2 text-center text-gray-500 item-index">${tbody.children.length + 1}</td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${tbody.children.length}][document_name]"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                </td>
                <td class="px-4 py-2 text-center">
                    <input type="number" name="items[${tbody.children.length}][copies]" value="0" min="0" class="w-20 rounded-lg border border-gray-300 px-2 py-2 text-sm text-center focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${tbody.children.length}][notes]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="removeItem(this)" class="rounded p-1 text-gray-400 hover:text-red-600 transition cursor-pointer" title="حذف">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </td>`;
            tbody.appendChild(tr);
            reindex();
        }

        function removeItem(btn) {
            const tbody = document.getElementById('items-body');
            if (tbody.children.length <= 1) {
                alert('يجب إدخال مستند واحد على الأقل');
                return;
            }
            btn.closest('tr').remove();
            reindex();
        }
    </script>
</x-app-layout>