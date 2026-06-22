<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.roles.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ isset($role) ? 'تعديل دور' : 'دور جديد' }}</h2>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ isset($role) ? route('settings.roles.update', $role) : route('settings.roles.store') }}" method="POST">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1">
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">اسم الدور <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">الاسم (إنجليزي)</label>
                        <input type="text" name="name_en" value="{{ old('name_en', $role->name_en ?? '') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    @if(!isset($role))
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">المعرف (slug) <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="admin, accountant, ...">
                    </div>
                    @endif
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $role->description ?? '') }}</textarea>
                    </div>
                    @if(isset($role) && !$role->is_system)
                    <div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span>الدور نشط</span>
                        </label>
                    </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                            {{ isset($role) ? 'حفظ التغييرات' : 'إنشاء الدور' }}
                        </button>
                        <a href="{{ route('settings.roles.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">إلغاء</a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800">الصلاحيات</h3>
                        <div class="flex items-center gap-2 text-sm">
                            <button type="button" onclick="document.querySelectorAll('.perm-checkbox').forEach(c=>c.checked=true)" class="text-primary-600 hover:text-primary-800">تحديد الكل</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="document.querySelectorAll('.perm-checkbox').forEach(c=>c.checked=false)" class="text-gray-500 hover:text-gray-700">إلغاء الكل</button>
                        </div>
                    </div>

                    @php
                        $groupNames = [
                            'accounts' => 'دليل الحسابات',
                            'customers' => 'العملاء',
                            'suppliers' => 'الموردين',
                            'items' => 'الأصناف',
                            'sales_invoices' => 'فواتير البيع',
                            'purchase_invoices' => 'فواتير الشراء',
                            'sales_orders' => 'أوامر البيع',
                            'purchase_orders' => 'أوامر الشراء',
                            'sales_returns' => 'مرتجعات البيع',
                            'purchase_returns' => 'مرتجعات الشراء',
                            'quotations' => 'عروض الأسعار',
                            'delivery_notes' => 'إذنات التسليم',
                            'receipt_notes' => 'إذنات الاستلام',
                            'journal_entries' => 'قيود اليومية',
                            'payments' => 'المدفوعات',
                            'stock' => 'المخزون',
                            'expenses' => 'المصروفات',
                            'budgets' => 'الميزانيات',
                            'bank' => 'البنوك',
                            'treasury' => 'الخزينة',
                            'employees' => 'الموظفين',
                            'payroll' => 'الرواتب',
                            'custodies' => 'العهد',
                            'reports' => 'التقارير',
                            'settings' => 'الإعدادات',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($permissions as $group => $perms)
                            <div class="rounded-lg border border-gray-200 p-3">
                                <h4 class="font-bold text-sm text-gray-700 mb-2 pb-2 border-b border-gray-100">
                                    {{ $groupNames[$group] ?? $group }}
                                </h4>
                                <div class="space-y-1.5">
                                    @foreach($perms as $perm)
                                        <label class="flex items-start gap-2 text-sm cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                class="perm-checkbox mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                {{ isset($role) && $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                            <span>{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
