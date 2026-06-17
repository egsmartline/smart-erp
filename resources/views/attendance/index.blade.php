<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الحضور والانصراف</h2>
            <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                تسجيل يدوي
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
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الموظف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">وقت الدخول</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">وقت الخروج</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">ساعات العمل</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $att->employee->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $att->date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 font-mono">{{ $att->check_in?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono">{{ $att->check_out?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $att->work_hours ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($att->status == 'present')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">حضور</span>
                                @elseif($att->status == 'late')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">تأخير</span>
                                @elseif($att->status == 'excused')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">إذن</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">غياب</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!$att->check_out)
                                    <form action="{{ route('attendance.check-out', $att) }}" method="POST" class="inline" onsubmit="return confirm('تسجيل الانصراف؟')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 transition cursor-pointer">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            انصراف
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>
</x-app-layout>
