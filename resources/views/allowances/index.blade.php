<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">البدلات</h2>
            <a href="{{ route('allowances.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                بدل جديد
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-4 flex flex-wrap gap-4">
            <select name="employee_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الموظفين</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الأنواع</option>
                @foreach(\App\Models\Allowance::TYPES as $key => $label)
                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلّق (للراتب)</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الرقم</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الموظف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">نوع البدل</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الدفع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allowances as $allowance)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $allowance->allowance_number }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $allowance->employee->full_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">{{ $allowance->type_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-bold">{{ number_format($allowance->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $allowance->date?->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($allowance->payment_method === 'treasury')
                                    خزينة
                                @elseif($allowance->payment_method === 'bank')
                                    بنك
                                @else
                                    الرواتب
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($allowance->status === 'paid')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مدفوع</span>
                                @elseif($allowance->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">معلّق</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">في الراتب</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('allowances.destroy', $allowance) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟ سيتم عكس القيد وإرجوع الرصيد.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد بدلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $allowances->links() }}</div>
    </div>
</x-app-layout>
