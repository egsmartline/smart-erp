<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">سجل التدقيق</h2>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">الجدول</label>
                <select name="table_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="customers" {{ request('table_name') === 'customers' ? 'selected' : '' }}>العملاء</option>
                    <option value="suppliers" {{ request('table_name') === 'suppliers' ? 'selected' : '' }}>الموردين</option>
                    <option value="items" {{ request('table_name') === 'items' ? 'selected' : '' }}>الأصناف</option>
                    <option value="sales_invoices" {{ request('table_name') === 'sales_invoices' ? 'selected' : '' }}>فواتير المبيعات</option>
                    <option value="purchase_invoices" {{ request('table_name') === 'purchase_invoices' ? 'selected' : '' }}>فواتير المشتريات</option>
                    <option value="journal_entries" {{ request('table_name') === 'journal_entries' ? 'selected' : '' }}>القيود اليومية</option>
                    <option value="payments" {{ request('table_name') === 'payments' ? 'selected' : '' }}>المدفوعات</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">العملية</label>
                <select name="action" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>إنشاء</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>تعديل</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>حذف</option>
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
                        <th class="px-4 py-3 font-semibold">رقم السجل</th>
                        <th class="px-4 py-3 font-semibold">البيانات القديمة</th>
                        <th class="px-4 py-3 font-semibold">البيانات الجديدة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-2">{{ $log->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : ($log->action === 'updated' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $log->action === 'created' ? 'إنشاء' : ($log->action === 'updated' ? 'تعديل' : 'حذف') }}
                                </span>
                            </td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $log->table_name }}</td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $log->model_id }}</td>
                            <td class="px-4 py-2 text-xs max-w-[200px] truncate">
                                @if($log->old_values)
                                    <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-blue-600 hover:underline">عرض</button>
                                    <pre class="hidden mt-1 p-2 bg-gray-50 rounded text-xs overflow-auto max-h-40">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs max-w-[200px] truncate">
                                @if($log->new_values)
                                    <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-blue-600 hover:underline">عرض</button>
                                    <pre class="hidden mt-1 p-2 bg-gray-50 rounded text-xs overflow-auto max-h-40">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
