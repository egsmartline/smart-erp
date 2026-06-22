<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إدارة الصلاحيات</h2>
            <a href="{{ route('settings.roles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                دور جديد
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800">الأدوار</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-800">{{ $role->name }}</span>
                                        @if($role->is_system)
                                            <span class="rounded bg-primary-100 px-2 py-0.5 text-xs text-primary-700">نظام</span>
                                        @endif
                                        @if(!$role->is_active)
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-500">معطل</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $role->description }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $role->permissions->count() }} صلاحية</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('settings.roles.edit', $role) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">تعديل</a>
                                    @if(!$role->is_system)
                                        <form action="{{ route('settings.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('حذف الدور {{ $role->name }}؟')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition">حذف</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">لا توجد أدوار مضافة</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800">تعيين دور لمستخدم</h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('settings.roles.assign') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">المستخدم</label>
                            <select name="user_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">اختر مستخدم</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">الدور</label>
                            <select name="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">اختر دور</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">تعيين الدور</button>
                    </form>
                </div>
            </div>

            <div class="mt-4 rounded-xl bg-white shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 p-4">
                    <h3 class="font-bold text-gray-800">المستخدمين والأدوار</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <div class="p-3 text-sm">
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            <span class="block text-xs text-gray-500">
                                @php $userRole = $roles->firstWhere('slug', $user->role); @endphp
                                {{ $userRole ? $userRole->name : $user->role }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
