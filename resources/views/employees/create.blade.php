<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة موظف جديد</h2>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">البيانات الشخصية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الاسم الأول <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">اسم العائلة <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الجنس</label>
                    <select name="gender" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الجنسية</label>
                    <input type="text" name="nationality" value="{{ old('nationality') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الهوية</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">المدينة</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الوظيفة</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الوظيفة <span class="text-red-500">*</span></label>
                    <select name="job_position_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر الوظيفة</option>
                        @foreach($jobPositions as $jp)
                            <option value="{{ $jp->id }}" {{ old('job_position_id') == $jp->id ? 'selected' : '' }}>{{ $jp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ التعيين <span class="text-red-500">*</span></label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">نوع العقد</label>
                    <select name="contract_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="full_time" {{ old('contract_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                        <option value="part_time" {{ old('contract_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                        <option value="contract" {{ old('contract_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">انتهاء العقد</label>
                    <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الحالة</label>
                    <select name="employment_status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="active" {{ old('employment_status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('employment_status', 'active') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        <option value="terminated" {{ old('employment_status', 'active') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                        <option value="on_leave" {{ old('employment_status', 'active') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الراتب الإجمالي <span class="text-red-500">*</span></label>
                    <input type="number" name="gross_salary" step="0.01" min="0" value="{{ old('gross_salary') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">البيانات البنكية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">اسم البنك</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الحساب</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الآيبان</label>
                    <input type="text" name="bank_iban" value="{{ old('bank_iban') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">جهة الاتصال الطارئ</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">اسم جهة الاتصال</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">هاتف جهة الاتصال</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">ملاحظات</h3>
            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ الموظف</button>
            <a href="{{ route('employees.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>
