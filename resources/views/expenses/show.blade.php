<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيان المصروف</h2>
            <div class="flex items-center gap-2">
                @if($expense->status == 'pending')
                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline" onsubmit="return confirm('الموافقة على المصروف؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">موافقة</button>
                    </form>
                    <form action="{{ route('expenses.reject', $expense) }}" method="POST" class="inline" onsubmit="return confirm('رفض المصروف؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition">رفض</button>
                    </form>
                @endif
                <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات المصروف</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الموظف:</span><span class="font-medium">{{ $expense->employee->full_name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التصنيف:</span><span class="font-medium">{{ $expense->category }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المبلغ:</span><span class="font-medium font-mono text-lg">{{ number_format($expense->amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التاريخ:</span><span class="font-medium">{{ $expense->expense_date->format('Y/m/d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($expense->status == 'approved')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">موافق عليها</span>
                    @elseif($expense->status == 'pending')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">قيد المراجعة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">مرفوضة</span>
                    @endif
                </div>
                <div class="flex justify-between"><span class="text-gray-500">الوصف:</span><span class="font-medium">{{ $expense->description }}</span></div>
                @if($expense->notes)
                    <div class="flex justify-between"><span class="text-gray-500">ملاحظات:</span><span class="font-medium">{{ $expense->notes }}</span></div>
                @endif
                @if($expense->approver)
                    <div class="flex justify-between"><span class="text-gray-500">راجعه:</span><span class="font-medium">{{ $expense->approver->name }}</span></div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
