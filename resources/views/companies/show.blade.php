<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الشركة</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('companies.edit', $company) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    العودة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <h3 class="mb-4 text-lg font-bold text-gray-800">معلومات الشركة</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">اسم الشركة</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">الاسم بالإنجليزية</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->name_en ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">البريد الإلكتروني</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">الهاتف</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">الرقم الضريبي</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->tax_number ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">الموقع الإلكتروني</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->website ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">العنوان</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">العملة الأساسية</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                {{ $company->currency_code }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">العملة الثانوية</label>
                        <p class="mt-1">
                            @if($company->secondaryCurrency)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                    {{ $company->secondary_currency_code }} - {{ $company->secondaryCurrency->name }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">الحالة</label>
                        <p class="mt-1">
                            @if($company->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">نشطة</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">غير نشطة</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <h3 class="mb-4 text-lg font-bold text-gray-800">شعار الشركة</h3>
                <div class="flex justify-center">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-32 w-32 rounded-xl object-cover shadow-md">
                    @else
                        <div class="flex h-32 w-32 items-center justify-center rounded-xl bg-blue-100 text-4xl font-bold text-blue-600">
                            {{ substr($company->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
