@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-l from-primary-600 to-primary-800 px-8 py-6">
            <h2 class="text-2xl font-bold text-white">إعداد النظام</h2>
            <p class="text-primary-100 mt-1">يرجى إكمال البيانات التالية لبدء استخدام النظام</p>
        </div>

        <form method="POST" action="{{ route('setup.store') }}" class="p-8">
            @csrf

            <div class="space-y-8">
                <!-- Company Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        بيانات الشركة
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">اسم الشركة <span class="text-red-500">*</span></label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="اسم الشركة">
                            @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Number -->
                        <div>
                            <label for="tax_number" class="block text-sm font-semibold text-gray-700 mb-2">الرقم الضريبي <span class="text-red-500">*</span></label>
                            <input type="text" id="tax_number" name="tax_number" value="{{ old('tax_number') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="الرقم الضريبي">
                            @error('tax_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="company_address" class="block text-sm font-semibold text-gray-700 mb-2">العنوان <span class="text-red-500">*</span></label>
                            <input type="text" id="company_address" name="company_address" value="{{ old('company_address') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="عنوان الشركة">
                            @error('company_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="company_phone" class="block text-sm font-semibold text-gray-700 mb-2">الهاتف <span class="text-red-500">*</span></label>
                            <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="رقم الهاتف">
                            @error('company_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="currency_id" class="block text-sm font-semibold text-gray-700 mb-2">العملة <span class="text-red-500">*</span></label>
                            <select id="currency_id" name="currency_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white">
                                <option value="">اختر العملة</option>
                                @foreach($currencies as $id => $name)
                                <option value="{{ $id }}" {{ old('currency_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('currency_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        بيانات مدير النظام
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Admin Name -->
                        <div>
                            <label for="admin_name" class="block text-sm font-semibold text-gray-700 mb-2">اسم المدير <span class="text-red-500">*</span></label>
                            <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="اسم المدير">
                            @error('admin_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Admin Email -->
                        <div>
                            <label for="admin_email" class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="admin@company.com">
                            @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Admin Password -->
                        <div>
                            <label for="admin_password" class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                            <input type="password" id="admin_password" name="admin_password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="كلمة المرور">
                            @error('admin_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="admin_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                            <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                placeholder="تأكيد كلمة المرور">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    سيتم إنشاء الحسابات الأساسية تلقائياً
                </p>
                <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        بدء الإعداد
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
