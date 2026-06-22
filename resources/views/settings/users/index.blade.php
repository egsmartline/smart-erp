<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إدارة المستخدمين</h2>
            <a href="{{ route('settings.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                مستخدم جديد
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-right text-sm font-semibold text-gray-600">
                        <th class="px-4 py-3">الاسم</th>
                        <th class="px-4 py-3">البريد الإلكتروني</th>
                        <th class="px-4 py-3">رقم الجوال</th>
                        <th class="px-4 py-3">الدور</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="text-sm text-gray-700 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-xs font-bold text-primary-700">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                    @if($user->is_system)
                                        <span class="rounded bg-primary-100 px-2 py-0.5 text-xs text-primary-700">نظام</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->phone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php $userRole = $roles->firstWhere('slug', $user->role); @endphp
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $userRole ? $userRole->name : $user->role }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
                                    <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">نشط</span>
                                @else
                                    <span class="rounded bg-red-100 px-2 py-0.5 text-xs text-red-700">معطل</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('settings.users.edit', $user) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">تعديل</a>
                                    @if(!$user->is_system)
                                        <form action="{{ route('settings.users.destroy', $user) }}" method="POST" onsubmit="return confirm('حذف المستخدم {{ $user->name }}؟')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition">حذف</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">لا يوجد مستخدمون</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
