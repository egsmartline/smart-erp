<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الحساب البنكي: {{ $bankAccount->bank_name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="text-center py-6">
            <div class="text-4xl font-bold {{ $bankAccount->current_balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($bankAccount->current_balance, 2) }} ج.م</div>
            <div class="text-sm text-gray-500 mt-2">الرصيد الحالي</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الحساب</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">البنك:</span><span class="font-medium">{{ $bankAccount->bank_name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">اسم الحساب:</span><span class="font-medium">{{ $bankAccount->account_name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">رقم الحساب:</span><span class="font-medium font-mono">{{ $bankAccount->account_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">IBAN:</span><span class="font-medium font-mono">{{ $bankAccount->iban ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Swift Code:</span><span class="font-medium font-mono">{{ $bankAccount->swift_code ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">العملة:</span><span class="font-medium">{{ $bankAccount->currency->name ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">الحساب المحاسبي:</span><span class="font-medium">{{ $bankAccount->account->name ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $bankAccount->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $bankAccount->is_active ? 'نشط' : 'غير نشط' }}</span>
            </div>
        </div>
    </div>
</x-app-layout>
