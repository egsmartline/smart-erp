<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة دفعات متعددة</h2>
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">رجوع</a>
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
        <div class="mb-4 flex items-center gap-3">
            <button type="button" id="addRowBtn" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة سطر
            </button>
            <span class="text-sm text-gray-500" id="rowCount">عدد الدفعات: 1</span>
        </div>

        <form action="{{ route('payments.bulk-store') }}" method="POST" id="bulkForm">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse" id="paymentsTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 w-20">النوع</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 w-24">التاريخ</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500">العميل / المورد</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 w-24">طريقة الدفع</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500">جهة الدفع</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 w-20">المبلغ</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 w-28">البيان</th>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="paymentsBody">
                        <tr class="payment-row border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-2 py-1.5">
                                <select name="payments[0][type]" class="payment-type w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                    <option value="receipt">قبض</option>
                                    <option value="payment">صرف</option>
                                </select>
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="date" name="payments[0][date]" value="{{ date('Y-m-d') }}" required class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </td>
                            <td class="px-2 py-1.5">
                                <select name="payments[0][customer_id]" class="payment-customer w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="">-- عميل --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <select name="payments[0][supplier_id]" class="payment-supplier w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" style="display:none">
                                    <option value="">-- مورد --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="payments[0][currency_id]" value="1">
                                <input type="hidden" name="payments[0][exchange_rate]" value="1">
                            </td>
                            <td class="px-2 py-1.5">
                                <select name="payments[0][payment_method]" class="payment-method w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                    <option value="cash">نقداً</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </td>
                            <td class="px-2 py-1.5">
                                <select name="payments[0][treasury_id]" class="payment-treasury w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="">-- خزينة --</option>
                                    @foreach($treasuries as $treasury)
                                        <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                                    @endforeach
                                </select>
                                <select name="payments[0][bank_account_id]" class="payment-bank w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" style="display:none">
                                    <option value="">-- بنك --</option>
                                    @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="payments[0][check_number]" class="payment-check w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="رقم الشيك" style="display:none">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="number" name="payments[0][amount]" step="0.01" min="0.01" required class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="المبلغ">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="text" name="payments[0][notes]" class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="بيان">
                            </td>
                            <td class="px-2 py-1.5 text-center">
                                <button type="button" class="remove-row text-red-500 hover:text-red-700 p-1" disabled>
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الدفعات
                </button>
                <a href="{{ route('payments.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let rowIndex = 1;

            function reindexRows() {
                const rows = document.querySelectorAll('#paymentsBody .payment-row');
                document.getElementById('rowCount').textContent = 'عدد الدفعات: ' + rows.length;
                rows.forEach((row, idx) => {
                    row.querySelectorAll('[name]').forEach(el => {
                        const name = el.getAttribute('name');
                        if (name) {
                            el.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
                        }
                    });
                    const removeBtn = row.querySelector('.remove-row');
                    if (removeBtn) {
                        removeBtn.disabled = rows.length <= 1;
                    }
                });
            }

            function toggleFields(row) {
                const type = row.querySelector('.payment-type').value;
                const method = row.querySelector('.payment-method').value;

                const customer = row.querySelector('.payment-customer');
                const supplier = row.querySelector('.payment-supplier');
                const treasury = row.querySelector('.payment-treasury');
                const bank = row.querySelector('.payment-bank');
                const check = row.querySelector('.payment-check');

                if (type === 'receipt') {
                    customer.style.display = '';
                    supplier.style.display = 'none';
                    supplier.value = '';
                } else {
                    customer.style.display = 'none';
                    customer.value = '';
                    supplier.style.display = '';
                }

                [treasury, bank, check].forEach(el => { el.style.display = 'none'; el.value = ''; });

                if (method === 'cash') {
                    treasury.style.display = '';
                } else if (method === 'bank_transfer') {
                    bank.style.display = '';
                } else if (method === 'check') {
                    check.style.display = '';
                }
            }

            function createRow() {
                const template = document.querySelector('#paymentsBody .payment-row');
                const newRow = template.cloneNode(true);

                newRow.querySelectorAll('input:not([type="hidden"]), select').forEach(el => {
                    el.value = '';
                    if (el.type === 'date') el.value = '{{ date('Y-m-d') }}';
                });

                const currencyHidden = newRow.querySelector('input[name$="[currency_id]"]');
                if (currencyHidden) currencyHidden.value = '1';
                const rateHidden = newRow.querySelector('input[name$="[exchange_rate]"]');
                if (rateHidden) rateHidden.value = '1';

                newRow.querySelector('.payment-type').value = 'receipt';
                newRow.querySelector('.payment-method').value = 'cash';
                newRow.querySelector('.payment-customer').style.display = '';
                newRow.querySelector('.payment-supplier').style.display = 'none';
                newRow.querySelector('.payment-treasury').style.display = '';
                newRow.querySelector('.payment-bank').style.display = 'none';
                newRow.querySelector('.payment-check').style.display = 'none';

                const removeBtn = newRow.querySelector('.remove-row');
                removeBtn.disabled = false;
                removeBtn.addEventListener('click', function () {
                    newRow.remove();
                    reindexRows();
                });

                newRow.querySelector('.payment-type').addEventListener('change', function () { toggleFields(newRow); });
                newRow.querySelector('.payment-method').addEventListener('change', function () { toggleFields(newRow); });

                document.getElementById('paymentsBody').appendChild(newRow);
                reindexRows();
            }

            document.getElementById('addRowBtn').addEventListener('click', createRow);

            document.querySelectorAll('#paymentsBody .payment-row').forEach(row => {
                row.querySelector('.payment-type').addEventListener('change', function () { toggleFields(row); });
                row.querySelector('.payment-method').addEventListener('change', function () { toggleFields(row); });
                toggleFields(row);
            });

            reindexRows();
        });
    </script>
</x-app-layout>
