<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الضريبة: {{ $tax->name }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('taxes.edit', $tax) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                <a href="{{ route('taxes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    رجوع
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">الكود</div>
            <div class="text-lg font-mono font-bold text-gray-900">{{ $tax->code }}</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">اسم الضريبة</div>
            <div class="text-lg font-bold text-gray-900">{{ $tax->name }}</div>
            @if($tax->name_en)
                <div class="text-sm text-gray-500">{{ $tax->name_en }}</div>
            @endif
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">النسبة / المعدل</div>
            <div class="text-2xl font-bold text-blue-600">{{ $tax->rate }}%</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">معلومات الضريبة</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="text-sm text-gray-500">النوع</div>
                <div class="font-medium text-gray-900">
                    @switch($tax->type)
                        @case('fixed') ثابتة @break
                        @case('percentage') نسبة مئوية @break
                        @case('group') مجموعة @break
                    @endswitch
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-500">نوع المبلغ</div>
                <div class="font-medium text-gray-900">
                    @switch($tax->amount_type)
                        @case('fixed') ثابت @break
                        @case('percent') نسبة مئوية @break
                        @case('group') مجموعة @break
                        @case('division') قسمة @break
                    @endswitch
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-500">حساب المبيعات</div>
                <div class="font-medium text-gray-900">{{ $tax->account->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">حساب المشتريات</div>
                <div class="font-medium text-gray-900">{{ $tax->purchaseAccount->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">مجموعة الضرائب</div>
                <div class="font-medium text-gray-900">{{ $tax->taxGroup->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500">الحالة</div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $tax->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $tax->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
            <div>
                <div class="text-sm text-gray-500">افتراضي</div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $tax->is_default ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $tax->is_default ? 'نعم' : 'لا' }}
                </span>
            </div>
            <div>
                <div class="text-sm text-gray-500">مضمن في السعر</div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $tax->is_included_in_price ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $tax->is_included_in_price ? 'نعم' : 'لا' }}
                </span>
            </div>
        </div>
        @if($tax->description)
            <div class="mt-4">
                <div class="text-sm text-gray-500">الوصف</div>
                <div class="font-medium text-gray-900">{{ $tax->description }}</div>
            </div>
        @endif
    </div>

    @if($tax->childTaxes->count())
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الضرائب الفرعية</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">الاسم</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">النسبة %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tax->childTaxes as $child)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-mono text-xs font-bold text-gray-900">{{ $child->code }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $child->name }}</td>
                                <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">{{ $child->rate }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
