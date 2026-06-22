<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة دفعة جديدة</h2>
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('payments.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="type" class="mb-1 block text-sm font-medium text-gray-700">النوع <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر النوع</option>
                        <option value="receipt" {{ old('type') === 'receipt' ? 'selected' : '' }}>قبض</option>
                        <option value="payment" {{ old('type') === 'payment' ? 'selected' : '' }}>دفع</option>
                    </select>
                </div>
                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="payment_method" class="mb-1 block text-sm font-medium text-gray-700">طريقة الدفع <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر طريقة الدفع</option>
                         <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>نقداً</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>شيك</option>
                    </select>
                </div>
                <div>
                    <label for="amount" class="mb-1 block text-sm font-medium text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة <span class="text-red-500">*</span></label>
                    <select name="currency_id" id="currency_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies ?? [] as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="exchange_rate" class="mb-1 block text-sm font-medium text-gray-700">سعر الصرف</label>
                    <input type="number" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate', 1) }}" step="0.000001" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div id="account_field">
                    <label for="account_id" class="mb-1 block text-sm font-medium text-gray-700">حساب المصروف</label>
                    <select name="account_id" id="account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر حساب المصروف</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="customer_field">
                    <label for="customer_id" class="mb-1 block text-sm font-medium text-gray-700">العميل</label>
                    <select name="customer_id" id="customer_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="supplier_field">
                    <label for="supplier_id" class="mb-1 block text-sm font-medium text-gray-700">المورد</label>
                    <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر المورد</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="treasury_field">
                    <label for="treasury_id" class="mb-1 block text-sm font-medium text-gray-700">جهة الدفع (الخزينة)</label>
                    <select name="treasury_id" id="treasury_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر جهة الدفع</option>
                        @foreach($treasuries as $treasury)
                            <option value="{{ $treasury->id }}" {{ old('treasury_id') == $treasury->id ? 'selected' : ($loop->first && !old('treasury_id') ? 'selected' : '') }}>{{ $treasury->name }} ({{ $treasury->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="bank_account_field">
                    <label for="bank_account_id" class="mb-1 block text-sm font-medium text-gray-700">جهة الدفع (الحساب البنكي)</label>
                    <select name="bank_account_id" id="bank_account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب البنكي</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>{{ $account->bank_name }} - {{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="cheque_number_field">
                    <label for="check_number" class="mb-1 block text-sm font-medium text-gray-700">رقم الشيك</label>
                    <input type="text" name="check_number" id="check_number" value="{{ old('check_number') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="reference" class="mb-1 block text-sm font-medium text-gray-700">رقم المرجع</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">الحالة</label>
                    <select name="status" id="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    </select>
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">البيان</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="وصف العملية">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الدفعة
                </button>
                <a href="{{ route('payments.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const methodSelect = document.getElementById('payment_method');
            const accountField = document.getElementById('account_field');
            const customerField = document.getElementById('customer_field');
            const supplierField = document.getElementById('supplier_field');
            const treasuryField = document.getElementById('treasury_field');
            const bankAccountField = document.getElementById('bank_account_field');
            const chequeField = document.getElementById('cheque_number_field');

            function toggleFields() {
                const type = typeSelect.value;
                const method = methodSelect.value;

                const isPayment = type === 'payment';
                const showCustomer = type === 'receipt';
                const showTreasury = method === 'cash';
                const showBank = method === 'bank_transfer';
                const showCheque = method === 'check';

                accountField.style.display = isPayment ? 'block' : 'none';
                customerField.style.display = showCustomer ? 'block' : 'none';
                supplierField.style.display = isPayment ? 'block' : 'none';
                treasuryField.style.display = showTreasury ? 'block' : 'none';
                bankAccountField.style.display = showBank ? 'block' : 'none';
                chequeField.style.display = showCheque ? 'block' : 'none';
            }

            typeSelect.addEventListener('change', toggleFields);
            methodSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-app-layout>
