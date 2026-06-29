<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">سجل التدقيق</h2>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">الجداول</label>
                <select name="model" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($models as $class => $label)
                        <option value="{{ $class }}" {{ request('model') === $class ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">العملية</label>
                <select name="action" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>إنشاء</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>تعديل</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>حذف</option>
                    <option value="restore" {{ request('action') === 'restore' ? 'selected' : '' }}>استعادة</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('audit-log.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ والوقت</th>
                        <th class="px-4 py-3 font-semibold">المستخدم</th>
                        <th class="px-4 py-3 font-semibold">العملية</th>
                        <th class="px-4 py-3 font-semibold">الجدول</th>
                        <th class="px-4 py-3 font-semibold">السجل</th>
                        <th class="px-4 py-3 font-semibold">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-2 text-xs">{{ $log->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : ($log->action === 'update' ? 'bg-yellow-100 text-yellow-800' : ($log->action === 'restore' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $log->action === 'create' ? 'إنشاء' : ($log->action === 'update' ? 'تعديل' : ($log->action === 'restore' ? 'استعادة' : 'حذف')) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ $models[$log->model] ?? class_basename($log->model) }}</td>
                            <td class="px-4 py-2 text-xs">
                                @if($log->url)
                                    <a href="{{ $log->url }}" class="text-blue-600 hover:underline font-medium" target="_blank">
                                        #{{ $log->model_id }}
                                    </a>
                                @else
                                    #{{ $log->model_id }}
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-600 max-w-[300px] truncate">
                                {{ $log->description }}
                                @if($log->old_values || $log->new_values)
                                    <button onclick="toggleJson({{ $log->id }})" class="mr-2 text-blue-500 hover:underline text-[11px]">عرض التفاصيل</button>
                                    <pre id="json-{{ $log->id }}" class="hidden mt-1 p-2 bg-gray-50 rounded text-[10px] overflow-auto max-h-40 whitespace-pre-wrap">
@if($log->old_values){{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@endif
@if($log->new_values && $log->old_values)
---
@endif
@if($log->new_values){{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@endif
                                    </pre>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
    </div>

    @push('scripts')
    <script>
        function toggleJson(id) {
            document.getElementById('json-' + id).classList.toggle('hidden');
        }
    </script>
    @endpush
</x-app-layout>