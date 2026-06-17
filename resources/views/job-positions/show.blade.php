<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الوظيفة: {{ $position->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('job-positions.edit', $position) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('job-positions.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الوظيفة</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $position->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الكود:</span><span class="font-medium font-mono">{{ $position->code ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">القسم:</span><span class="font-medium">{{ $position->department->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحد الأدنى:</span><span class="font-medium font-mono">{{ $position->min_salary ? number_format($position->min_salary, 2) : '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحد الأقصى:</span><span class="font-medium font-mono">{{ $position->max_salary ? number_format($position->max_salary, 2) : '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($position->is_active)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                    @endif
                </div>
                @if($position->description)
                    <div class="pt-2 border-t border-gray-100"><span class="text-gray-500">الوصف:</span><p class="mt-1 text-gray-700">{{ $position->description }}</p></div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">الموظفون في هذه الوظيفة</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الموظف</th>
                        <th class="px-4 py-3 font-semibold">القسم</th>
                        <th class="px-4 py-3 font-semibold">تاريخ التعيين</th>
                        <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($position->employees as $emp)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3"><a href="{{ route('employees.show', $emp) }}" class="hover:text-blue-600 font-medium">{{ $emp->full_name }}</a></td>
                            <td class="px-4 py-3 text-gray-600">{{ $emp->department->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $emp->hire_date?->format('Y/m/d') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($emp->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا يوجد موظفون في هذه الوظيفة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
