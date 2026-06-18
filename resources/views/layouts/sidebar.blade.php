<!-- Sidebar -->
<aside class="fixed inset-y-0 right-0 z-50 w-64 bg-gray-900 text-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 translate-x-full lg:translate-x-0" dir="rtl">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b border-gray-800">
        @php $company = \App\Models\Company::where('tenant_id', session('current_tenant_id'))->first(); @endphp
        <div class="flex items-center gap-3">
            @if($company && $company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover">
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-lg font-bold">S</div>
            @endif
            <div>
                <div class="text-lg font-bold text-white">{{ $company->name ?? 'Smart ERP' }}</div>
                <div class="text-xs text-gray-400">نظام المحاسبة الذكي</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 180px)">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            الرئيسية
        </a>

        <!-- المحاسبة -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('accounts.*'), request()->routeIs('journals.*'), request()->routeIs('journal-entries.*'), request()->routeIs('payments.*'), request()->routeIs('payment-terms.*'), request()->routeIs('taxes.*'), request()->routeIs('bank-statements.*'), request()->routeIs('analytical-accounts.*'), request()->routeIs('budgets.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    المحاسبة
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('accounts.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('accounts.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    دليل الحسابات
                </a>
                <a href="{{ route('journals.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('journals.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    يوميات القيود
                </a>
                <a href="{{ route('journal-entries.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('journal-entries.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    القيود اليومية
                </a>
                <a href="{{ route('payments.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('payments.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    المدفوعات
                </a>
                <a href="{{ route('payment-terms.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('payment-terms.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    شروط الدفع
                </a>
                <a href="{{ route('taxes.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('taxes.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الضرائب
                </a>
                <a href="{{ route('bank-statements.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('bank-statements.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}>
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    كشف حساب البنك
                </a>
                <a href="{{ route('analytical-accounts.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('analytical-accounts.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الحسابات التحليلية
                </a>
                <a href="{{ route('budgets.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('budgets.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الميزانيات
                </a>
            </div>
        </div>

        <!-- المبيعات -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('sales-orders.*'), request()->routeIs('sales-invoices.*'), request()->routeIs('sales-returns.*'), request()->routeIs('quotations.*'), request()->routeIs('customers.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    المبيعات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('sales-orders.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('sales-orders.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    أوامر البيع
                </a>
                <a href="{{ route('sales-invoices.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('sales-invoices.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    فواتير البيع
                </a>
                <a href="{{ route('sales-returns.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('sales-returns.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    مرتجعات المبيعات
                </a>
                <a href="{{ route('quotations.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('quotations.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    عروض الأسعار
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('customers.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    العملاء
                </a>
            </div>
        </div>

        <!-- المشتريات -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('purchase-orders.*'), request()->routeIs('purchase-invoices.*'), request()->routeIs('purchase-returns.*'), request()->routeIs('suppliers.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    المشتريات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('purchase-orders.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    أوامر الشراء
                </a>
                <a href="{{ route('purchase-invoices.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('purchase-invoices.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    فواتير الشراء
                </a>
                <a href="{{ route('purchase-returns.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('purchase-returns.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    مرتجعات المشتريات
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الموردين
                </a>
            </div>
        </div>

        <!-- المخزون -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('items.*'), request()->routeIs('warehouses.*'), request()->routeIs('stock-movements.*'), request()->routeIs('inventory-count.*'), request()->routeIs('inventory-adjustments.*'), request()->routeIs('stock-transfers.*'), request()->routeIs('reordering-rules.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    المخزون
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('items.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('items.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الأصناف
                </a>
                <a href="{{ route('warehouses.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('warehouses.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    المخازن
                </a>
                <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('stock-movements.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    حركات المخزون
                </a>
                <a href="{{ route('inventory-count.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('inventory-count.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    جرد المخزون
                </a>
                <a href="{{ route('inventory-adjustments.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('inventory-adjustments.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تسوية المخزون
                </a>
                <a href="{{ route('stock-transfers.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('stock-transfers.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    التحويلات الداخلية
                </a>
                <a href="{{ route('reordering-rules.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reordering-rules.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    قواعد إعادة الطلب
                </a>
            </div>
        </div>

        <!-- التقارير -->
        <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    التقارير
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('reports.trial-balance') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.trial-balance') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    ميزان المراجعة
                </a>
                <a href="{{ route('reports.general-ledger') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.general-ledger') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    دفتر الأستاذ العام
                </a>
                <a href="{{ route('reports.income-statement') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.income-statement') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    قائمة الدخل
                </a>
                <a href="{{ route('reports.balance-sheet') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.balance-sheet') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الميزانية العمومية
                </a>
                <a href="{{ route('reports.cash-flow') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.cash-flow') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    التدفقات النقدية
                </a>
                <a href="{{ route('reports.sales') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.sales') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تقرير المبيعات
                </a>
                <a href="{{ route('reports.purchases') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.purchases') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تقرير المشتريات
                </a>
                <a href="{{ route('reports.customer-statement') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.customer-statement') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    كشف حساب العملاء
                </a>
                <a href="{{ route('reports.supplier-statement') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.supplier-statement') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    كشف حساب الموردين
                </a>
                <a href="{{ route('reports.vat') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.vat') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تقرير ضريبة القيمة المضافة
                </a>
                <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.inventory') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تقرير المخزون
                </a>
            </div>
        </div>

        <!-- شئون العاملين -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('employees.*'), request()->routeIs('departments.*'), request()->routeIs('job-positions.*'), request()->routeIs('attendance.*'), request()->routeIs('leaves.*'), request()->routeIs('expenses.*'), request()->routeIs('payroll.*'), request()->routeIs('loans.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    شئون العاملين
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('employees.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('employees.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الموظفون
                </a>
                <a href="{{ route('departments.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('departments.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الأقسام
                </a>
                <a href="{{ route('job-positions.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('job-positions.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الوظائف
                </a>
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('attendance.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الحضور والانصراف
                </a>
                <a href="{{ route('leaves.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('leaves.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الإجازات
                </a>
                <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('expenses.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    المصروفات
                </a>
                <a href="{{ route('payroll.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('payroll.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الرواتب
                </a>
                <a href="{{ route('loans.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('loans.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    السلف
                </a>
            </div>
        </div>

        <!-- الشركات -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('companies.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    الشركات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('companies.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('companies.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    إدارة الشركات
                </a>
            </div>
        </div>

        <!-- استيراد وتصدير البيانات -->
        <a href="{{ route('import.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('import.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            استيراد وتصدير البيانات
        </a>

        <!-- الإعدادات -->
        <div x-data="{ open: {{ in_array(true, [request()->routeIs('settings.*'), request()->routeIs('currencies.*'), request()->routeIs('fiscal-years.*')]) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    الإعدادات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('settings.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    إعدادات الشركة
                </a>
                <a href="{{ route('currencies.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('currencies.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    العملة
                </a>
                <a href="{{ route('fiscal-years.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('fiscal-years.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    السنة المالية
                </a>
            </div>
        </div>
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-gray-800 bg-gray-900 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold">
                {{ substr(Auth::user()->name ?? 'م', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'مستخدم' }}</div>
                <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded p-1.5 text-gray-400 hover:bg-gray-800 hover:text-white transition cursor-pointer" title="تسجيل الخروج">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
