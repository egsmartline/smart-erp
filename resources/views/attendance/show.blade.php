<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">سجل حضور: {{ $employee->full_name }}</h2>
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">وقت الدخول</th>
                        <th class="px-4 py-3 font-semibold">وقت الخروج</th>
                        <th class="px-4 py-3 font-semibold text-center">ساعات العمل</th>
                        <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $att->date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3 font-mono">{{ $att->check_in?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono">{{ $att->check_out?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $att->work_hours ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($att->status == 'present')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">حضور</span>
                                @elseif($att->status == 'late')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">تأخير</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">غياب</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>
</x-app-layout>
