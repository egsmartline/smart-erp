<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">{{ isset($user) ? 'تعديل مستخدم' : 'مستخدم جديد' }}</h2>
            <a href="{{ route('settings.users.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                العودة
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <form action="{{ isset($user) ? route('settings.users.update', $user) : route('settings.users.store') }}" method="POST" class="space-y-4">
                @csrf
                @if(isset($user)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">اسم المستخدم</label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">كلمة المرور</label>
                        <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="{{ isset($user) ? 'اتركه فارغًا دون تغيير' : '' }}">
                        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">رقم الجوال</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الدور</label>
                    <select name="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر دور</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ old('role', $user->role ?? '') == $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="text-sm text-gray-700">المستخدم نشط</label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="rounded-lg bg-primary-600 px-6 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                        {{ isset($user) ? 'حفظ التغييرات' : 'إنشاء المستخدم' }}
                    </button>
                    <a href="{{ route('settings.users.index') }}" class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
