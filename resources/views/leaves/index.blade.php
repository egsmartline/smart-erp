<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الإجازات</h2>
            <a href="{{ route('leaves.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                طلب إجازة
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <select name="employee_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الموظفين</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الموظف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">من</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">إلى</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الأيام</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $leave->employee->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $leave->leave_type }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $leave->date_from->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $leave->date_to->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $leave->total_days }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($leave->status == 'approved')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">موافق عليها</span>
                                @elseif($leave->status == 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">قيد المراجعة</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">مرفوضة</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    @if($leave->status == 'pending')
                                        <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="inline" onsubmit="return confirm('الموافقة على الإجازة؟')">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-green-600 hover:bg-green-50 transition cursor-pointer" title="موافقة">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="inline" onsubmit="return confirm('رفض الإجازة؟')">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-red-600 hover:bg-red-50 transition cursor-pointer" title="رفض">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد إجازات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $leaves->links() }}</div>
    </div>
</x-app-layout>
