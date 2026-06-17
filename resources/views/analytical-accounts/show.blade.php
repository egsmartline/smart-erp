<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الحساب التحليلي</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('analytical-accounts.edit', $analyticalAccount) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('analytical-accounts.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">معلومات الحساب</h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">الكود</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ $analyticalAccount->code }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">الاسم</span>
                    <span class="text-sm font-medium text-gray-900">{{ $analyticalAccount->name }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">النوع</span>
                    @php
                        $typeLabels = [
                            'cost_center' => 'مركز تكلفة',
                            'profit_center' => 'مركز ربح',
                            'project' => 'مشروع',
                            'department' => 'قسم',
                        ];
                        $typeColors = [
                            'cost_center' => 'bg-blue-100 text-blue-800',
                            'profit_center' => 'bg-green-100 text-green-800',
                            'project' => 'bg-purple-100 text-purple-800',
                            'department' => 'bg-orange-100 text-orange-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$analyticalAccount->type] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $typeLabels[$analyticalAccount->type] ?? $analyticalAccount->type }}
                    </span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">الحساب الأب</span>
                    <span class="text-sm text-gray-900">{{ $analyticalAccount->parent->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">الحالة</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $analyticalAccount->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $analyticalAccount->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">-utilization الميزانية</h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">مبلغ الميزانية</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($analyticalAccount->budget_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">المبلغ الحالي</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($analyticalAccount->current_amount, 2) }}</span>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm text-gray-500">نسبة الاستهلاك</span>
                        <span class="text-sm font-bold {{ $utilization > 100 ? 'text-red-600' : ($utilization > 80 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ number_format($utilization, 1) }}%
                        </span>
                    </div>
                    <div class="h-3 w-full rounded-full bg-gray-200">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $utilization > 100 ? 'bg-red-500' : ($utilization > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ min($utilization, 100) }}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">المتبقي</span>
                    @php $remaining = $analyticalAccount->budget_amount - $analyticalAccount->current_amount; @endphp
                    <span class="font-mono text-sm font-bold {{ $remaining < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remaining, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
