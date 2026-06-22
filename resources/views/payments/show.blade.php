<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الدفعة: {{ $payment->payment_number }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="text-center py-6">
            <div class="text-4xl font-bold {{ $payment->type === 'receipt' ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $payment->type === 'receipt' ? '+' : '-' }} {{ number_format($payment->amount, 2) }}
            </div>
            <div class="mt-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $payment->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    {{ $payment->type === 'receipt' ? 'قبض' : 'دفع' }}
                </span>
            </div>
            <div class="text-sm text-gray-500 mt-2">
                @if($payment->status === 'completed')
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">مكتمل</span>
                @elseif($payment->status === 'pending')
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">قيد الانتظار</span>
                @else
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">{{ $payment->status }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الدفعة</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">رقم الدفعة:</span><span class="font-medium font-mono">{{ $payment->payment_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التاريخ:</span><span class="font-medium">{{ $payment->date->format('Y-m-d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">النوع:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $payment->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $payment->type === 'receipt' ? 'قبض' : 'دفع' }}
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">المبلغ:</span><span class="font-medium font-mono text-lg">{{ number_format($payment->amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">العملة:</span><span class="font-medium">{{ $payment->currency->name ?? '-' }} ({{ $payment->currency->code ?? '-' }})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">سعر الصرف:</span><span class="font-medium font-mono">{{ $payment->exchange_rate }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المبلغ بالعملة المحلية:</span><span class="font-medium font-mono">{{ number_format($payment->amount_in_currency, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">طريقة الدفع:</span>
                    <span class="font-medium">
                        @if($payment->payment_method === 'cash') نقداً
                        @elseif($payment->payment_method === 'bank_transfer') تحويل بنكي
                        @elseif($payment->payment_method === 'check') شيك
                        @else {{ $payment->payment_method }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($payment->status === 'completed')
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">مكتمل</span>
                    @elseif($payment->status === 'pending')
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">قيد الانتظار</span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">{{ $payment->status }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الطرف الآخر</h3>
            <div class="space-y-3 text-sm">
                @if($payment->customer)
                    <div class="flex justify-between"><span class="text-gray-500">العميل:</span><span class="font-medium">{{ $payment->customer->name }}</span></div>
                @endif
                @if($payment->account)
                    <div class="flex justify-between"><span class="text-gray-500">حساب المصروف:</span><span class="font-medium">{{ $payment->account->code }} - {{ $payment->account->name }}</span></div>
                @endif
                @if($payment->supplier)
                    <div class="flex justify-between"><span class="text-gray-500">المورد:</span><span class="font-medium">{{ $payment->supplier->name }}</span></div>
                @endif
                @if($payment->treasury)
                    <div class="flex justify-between"><span class="text-gray-500">جهة الدفع:</span><span class="font-medium">{{ $payment->treasury->name }} ({{ $payment->treasury->code }})</span></div>
                @endif
                @if($payment->bankAccount)
                    <div class="flex justify-between"><span class="text-gray-500">الحساب البنكي:</span><span class="font-medium">{{ $payment->bankAccount->bank_name }} - {{ $payment->bankAccount->account_name }}</span></div>
                @endif
                @if($payment->check_number)
                    <div class="flex justify-between"><span class="text-gray-500">رقم الشيك:</span><span class="font-medium font-mono">{{ $payment->check_number }}</span></div>
                @endif
                @if($payment->reference)
                    <div class="flex justify-between"><span class="text-gray-500">رقم المرجع:</span><span class="font-medium font-mono">{{ $payment->reference }}</span></div>
                @endif
                @if($payment->notes)
                    <div class="flex justify-between"><span class="text-gray-500">البيان:</span><span class="font-medium">{{ $payment->notes }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">أنشئ بواسطة:</span><span class="font-medium">{{ $payment->user->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">تاريخ الإنشاء:</span><span class="font-medium">{{ $payment->created_at->format('Y-m-d H:i') }}</span></div>
            </div>
        </div>
    </div>
</x-app-layout>
