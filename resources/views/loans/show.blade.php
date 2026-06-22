<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيان السلفة</h2>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات السلفة</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الموظف:</span><span class="font-medium">{{ $loan->employee->full_name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الخزينة:</span><span class="font-medium">{{ $loan->treasury->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المبلغ:</span><span class="font-medium font-mono text-lg">{{ number_format($loan->amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">القسط الشهري:</span><span class="font-medium font-mono">{{ number_format($loan->monthly_deduction, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المتبقي:</span><span class="font-medium font-mono">{{ number_format($loan->remaining_amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">من تاريخ:</span><span class="font-medium">{{ $loan->start_date?->format('Y/m/d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">إلى تاريخ:</span><span class="font-medium">{{ $loan->end_date?->format('Y/m/d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($loan->status == 'active')
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">نشطة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مكتملة</span>
                    @endif
                </div>
                @if($loan->reason)
                    <div class="flex justify-between"><span class="text-gray-500">السبب:</span><span class="font-medium">{{ $loan->reason }}</span></div>
                @endif
                @if($loan->notes)
                    <div class="flex justify-between"><span class="text-gray-500">ملاحظات:</span><span class="font-medium">{{ $loan->notes }}</span></div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
