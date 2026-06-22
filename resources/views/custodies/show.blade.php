<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل العهدة: {{ $custody->custody_number }}</h2>
            <div class="flex items-center gap-2">
                @if($custody->status !== 'settled')
                <a href="{{ route('custodies.settle', $custody) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تسوية العهدة
                </a>
                @endif
                <a href="{{ route('custodies.edit', $custody) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('custodies.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-500">رقم العهدة</p>
                <p class="font-mono font-medium text-gray-900">{{ $custody->custody_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">الموظف</p>
                <p class="font-medium text-gray-900">{{ $custody->employee->full_name ?? $custody->employee->first_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">المبلغ</p>
                <p class="font-mono font-bold text-gray-900">{{ number_format($custody->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">المردود</p>
                <p class="font-mono text-gray-900">{{ number_format($custody->returned_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">التاريخ</p>
                <p class="text-gray-900">{{ $custody->date instanceof \Carbon\Carbon ? $custody->date->format('Y-m-d') : $custody->date }}</p>
            </div>
            @if($custody->settlement_date)
            <div>
                <p class="text-xs text-gray-500">تاريخ التسوية</p>
                <p class="text-gray-900">{{ $custody->settlement_date instanceof \Carbon\Carbon ? $custody->settlement_date->format('Y-m-d') : $custody->settlement_date }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-gray-500">الحالة</p>
                @if($custody->status == 'active')
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">نشطة</span>
                @elseif($custody->status == 'partial')
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مرتجعة جزئياً</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مصفاة</span>
                @endif
            </div>
            @if($custody->treasury)
            <div>
                <p class="text-xs text-gray-500">الخزينة</p>
                <p class="text-gray-900">{{ $custody->treasury->name }}</p>
            </div>
            @endif
            @if($custody->currency)
            <div>
                <p class="text-xs text-gray-500">العملة</p>
                <p class="text-gray-900">{{ $custody->currency->name }} ({{ $custody->currency->code }})</p>
            </div>
            @endif
            @if($custody->account)
            <div>
                <p class="text-xs text-gray-500">الحساب المحاسبي</p>
                <p class="text-gray-900">{{ $custody->account->name }}</p>
            </div>
            @endif
            @if($custody->user)
            <div>
                <p class="text-xs text-gray-500">بواسطة</p>
                <p class="text-gray-900">{{ $custody->user->name }}</p>
            </div>
            @endif
            @if($custody->description)
            <div class="md:col-span-2 lg:col-span-3">
                <p class="text-xs text-gray-500">الوصف</p>
                <p class="text-gray-900">{{ $custody->description }}</p>
            </div>
            @endif
            @if($custody->notes)
            <div class="md:col-span-2 lg:col-span-3">
                <p class="text-xs text-gray-500">ملاحظات</p>
                <p class="text-gray-900">{{ $custody->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
