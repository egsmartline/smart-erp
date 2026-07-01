<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">{{ $employee->full_name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full {{ $employee->gender == 'female' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-700' }} mb-3">
                @if($employee->gender == 'female')
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="9" r="5"/><path d="M12 14v8m-4-4h8"/></svg>
                @else
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.4 5.4M14 5h5v5"/></svg>
                @endif
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ $employee->full_name }}</h3>
            <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $employee->jobPosition->name ?? '' }}</p>
            <div class="mt-3">
                @if($employee->employment_status == 'active')
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">نشط</span>
                @elseif($employee->employment_status == 'on_leave')
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">في إجازة</span>
                @elseif($employee->employment_status == 'terminated')
                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">منتهي</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">غير نشط</span>
                @endif
            </div>
        </div>

        <div class="lg:col-span-3 rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div x-data="{ activeTab: 'personal' }">
                <div class="flex gap-2 border-b border-gray-200 mb-4">
                    <button @click="activeTab = 'personal'" :class="activeTab == 'personal' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">البيانات الشخصية</button>
                    <button @click="activeTab = 'job'" :class="activeTab == 'job' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">الوظيفة والراتب</button>
                    <button @click="activeTab = 'bank'" :class="activeTab == 'bank' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">البيانات البنكية</button>
                    <button @click="activeTab = 'attendance'" :class="activeTab == 'attendance' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">الحضور</button>
                    <button @click="activeTab = 'leaves'" :class="activeTab == 'leaves' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">الإجازات</button>
                    <button @click="activeTab = 'salary'" :class="activeTab == 'salary' ? 'border-blue-600 text-blue-600' : ''" class="px-4 py-2 text-sm font-medium border-b-2 transition">الرواتب</button>
                </div>

                <div x-show="activeTab == 'personal'" class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-gray-500">الاسم:</span> <span class="font-medium">{{ $employee->full_name }}</span></div>
                        <div><span class="text-gray-500">البريد:</span> <span class="font-medium">{{ $employee->email ?? '-' }}</span></div>
                        <div><span class="text-gray-500">الهاتف:</span> <span class="font-medium">{{ $employee->phone ?? '-' }}</span></div>
                        <div><span class="text-gray-500">تاريخ الميلاد:</span> <span class="font-medium">{{ $employee->birth_date?->format('Y/m/d') ?? '-' }}</span></div>
                        <div><span class="text-gray-500">الجنس:</span> <span class="font-medium">{{ $employee->gender == 'male' ? 'ذكر' : ($employee->gender == 'female' ? 'أنثى' : '-') }}</span></div>
                        <div><span class="text-gray-500">الجنسية:</span> <span class="font-medium">{{ $employee->nationality ?? '-' }}</span></div>
                        <div><span class="text-gray-500">رقم الهوية:</span> <span class="font-medium">{{ $employee->national_id ?? '-' }}</span></div>
                        <div class="col-span-2"><span class="text-gray-500">العنوان:</span> <span class="font-medium">{{ $employee->address ?? '-' }} {{ $employee->city ? '، ' . $employee->city : '' }}</span></div>
                    </div>
                </div>

                <div x-show="activeTab == 'job'" class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-gray-500">الوظيفة:</span> <span class="font-medium">{{ $employee->jobPosition->name ?? '-' }}</span></div>
                        <div><span class="text-gray-500">تاريخ التعيين:</span> <span class="font-medium">{{ $employee->hire_date?->format('Y/m/d') ?? '-' }}</span></div>
                        <div><span class="text-gray-500">نوع العقد:</span> <span class="font-medium">{{ $employee->contract_type ?? '-' }}</span></div>
                        <div><span class="text-gray-500">انتهاء العقد:</span> <span class="font-medium">{{ $employee->contract_end_date?->format('Y/m/d') ?? '-' }}</span></div>
                        <div><span class="text-gray-500">الراتب الإجمالي:</span> <span class="font-medium font-mono">{{ number_format($employee->gross_salary, 2) }}</span></div>
                    </div>
                </div>

                <div x-show="activeTab == 'bank'" class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-gray-500">اسم البنك:</span> <span class="font-medium">{{ $employee->bank_name ?? '-' }}</span></div>
                        <div><span class="text-gray-500">رقم الحساب:</span> <span class="font-medium">{{ $employee->bank_account ?? '-' }}</span></div>
                        <div><span class="text-gray-500">الآيبان:</span> <span class="font-medium">{{ $employee->bank_iban ?? '-' }}</span></div>
                        <div><span class="text-gray-500">جهة الاتصال الطارئ:</span> <span class="font-medium">{{ $employee->emergency_contact_name ?? '-' }}</span></div>
                        <div><span class="text-gray-500">هاتف الطوارئ:</span> <span class="font-medium">{{ $employee->emergency_contact_phone ?? '-' }}</span></div>
                    </div>
                </div>

                <div x-show="activeTab == 'attendance'" class="text-sm">
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
                                @forelse($employee->attendances as $att)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-4 py-2">{{ $att->date->format('Y/m/d') }}</td>
                                        <td class="px-4 py-2">{{ $att->check_in?->format('H:i') ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $att->check_out?->format('H:i') ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center font-mono">{{ $att->work_hours ?? '-' }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($att->status == 'present')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">حضور</span>
                                            @elseif($att->status == 'late')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">تأخير</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">غياب</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد سجلات حضور</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeTab == 'leaves'" class="text-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-4 py-3 font-semibold">النوع</th>
                                    <th class="px-4 py-3 font-semibold">من</th>
                                    <th class="px-4 py-3 font-semibold">إلى</th>
                                    <th class="px-4 py-3 font-semibold text-center">الأيام</th>
                                    <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaves as $leave)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-4 py-2">{{ $leave->type }}</td>
                                        <td class="px-4 py-2">{{ $leave->start_date->format('Y/m/d') }}</td>
                                        <td class="px-4 py-2">{{ $leave->end_date->format('Y/m/d') }}</td>
                                        <td class="px-4 py-2 text-center font-mono">{{ $leave->days }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($leave->status == 'approved')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">موافق عليها</span>
                                            @elseif($leave->status == 'pending')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">قيد المراجعة</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">مرفوضة</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد إجازات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeTab == 'salary'" class="text-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-4 py-3 font-semibold">الشهر</th>
                                    <th class="px-4 py-3 font-semibold text-center">الراتب الأساسي</th>
                                    <th class="px-4 py-3 font-semibold text-center">البدلات</th>
                                    <th class="px-4 py-3 font-semibold text-center">الخصومات</th>
                                    <th class="px-4 py-3 font-semibold text-center">الصافي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->payslips as $ps)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-4 py-2">{{ $ps->payroll->month }}/{{ $ps->payroll->year }}</td>
                                        <td class="px-4 py-2 text-center font-mono">{{ number_format($ps->basic_salary, 2) }}</td>
                                        <td class="px-4 py-2 text-center font-mono text-green-600">{{ number_format($ps->allowances, 2) }}</td>
                                        <td class="px-4 py-2 text-center font-mono text-red-600">{{ number_format($ps->deductions, 2) }}</td>
                                        <td class="px-4 py-2 text-center font-mono font-bold">{{ number_format($ps->net_salary, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد كشف رواتب</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
