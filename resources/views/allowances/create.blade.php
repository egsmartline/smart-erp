<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بدل جديد</h2>
            <a href="{{ route('allowances.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">رجوع</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 max-w-3xl">
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('allowances.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">نوع البدل <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر النوع</option>
                        @foreach(\App\Models\Allowance::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="0.00">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">طريقة الدفع <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="treasury" {{ old('payment_method') == 'treasury' ? 'selected' : '' }}>صرف فوري من الخزينة</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>صرف فوري من الحساب البنكي</option>
                        <option value="payroll" {{ old('payment_method') == 'payroll' ? 'selected' : '' }}>إضافة للراتب</option>
                    </select>
                </div>

                <div id="treasury_div" class="{{ old('payment_method', 'treasury') !== 'treasury' ? 'hidden' : '' }}">
                    <label class="mb-1 block text-sm font-medium text-gray-700">الخزينة <span class="text-red-500">*</span></label>
                    <select name="cash_treasury_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الخزينة</option>
                        @foreach($treasuries as $tre)
                            <option value="{{ $tre->id }}" {{ old('cash_treasury_id') == $tre->id ? 'selected' : '' }}>{{ $tre->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="bank_div" class="{{ old('payment_method') !== 'bank' ? 'hidden' : '' }}">
                    <label class="mb-1 block text-sm font-medium text-gray-700">الحساب البنكي <span class="text-red-500">*</span></label>
                    <select name="bank_account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($bankAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('bank_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="ملاحظات اختيارية">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ</button>
                <a href="{{ route('allowances.index') }}" class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function () {
            document.getElementById('treasury_div').classList.toggle('hidden', this.value !== 'treasury');
            document.getElementById('bank_div').classList.toggle('hidden', this.value !== 'bank');
        });
    </script>
</x-app-layout>
