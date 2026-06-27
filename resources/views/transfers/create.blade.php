<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تحويل جديد</h2>
            <a href="{{ route('transfers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('transfers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تحويل من</label>
                    <select id="from_type" name="from_type" onchange="toggleFrom()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="treasury" {{ old('from_type') === 'treasury' ? 'selected' : '' }}>خزينة</option>
                        <option value="bank" {{ old('from_type') === 'bank' ? 'selected' : '' }}>حساب بنكي</option>
                        <option value="account" {{ old('from_type') === 'account' ? 'selected' : '' }}>حساب مالي</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">&nbsp;</label>
                    <select id="from_id" name="from_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="">اختر</option>
                        <optgroup label="الخزائن" id="from-treasuries">
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}" {{ old('from_id') == $t->id ? 'selected' : '' }} data-group="treasury">{{ $t->name }} ({{ number_format($t->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="الحسابات البنكية" id="from-banks">
                            @foreach($bankAccounts as $b)
                                <option value="{{ $b->id }}" {{ old('from_id') == $b->id ? 'selected' : '' }} data-group="bank">{{ $b->account_name }} - {{ $b->bank_name }} ({{ number_format($b->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="الحسابات المالية" id="from-accounts">
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}" {{ old('from_id') == $a->id ? 'selected' : '' }} data-group="account">{{ $a->code }} - {{ $a->name }} ({{ number_format($a->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تحويل إلى</label>
                    <select id="to_type" name="to_type" onchange="toggleTo()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="treasury" {{ old('to_type') === 'treasury' ? 'selected' : '' }}>خزينة</option>
                        <option value="bank" {{ old('to_type') === 'bank' ? 'selected' : '' }}>حساب بنكي</option>
                        <option value="account" {{ old('to_type') === 'account' ? 'selected' : '' }}>حساب مالي</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">&nbsp;</label>
                    <select id="to_id" name="to_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="">اختر</option>
                        <optgroup label="الخزائن" id="to-treasuries">
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}" {{ old('to_id') == $t->id ? 'selected' : '' }} data-group="treasury">{{ $t->name }} ({{ number_format($t->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="الحسابات البنكية" id="to-banks">
                            @foreach($bankAccounts as $b)
                                <option value="{{ $b->id }}" {{ old('to_id') == $b->id ? 'selected' : '' }} data-group="bank">{{ $b->account_name }} - {{ $b->bank_name }} ({{ number_format($b->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="الحسابات المالية" id="to-accounts">
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}" {{ old('to_id') == $a->id ? 'selected' : '' }} data-group="account">{{ $a->code }} - {{ $a->name }} ({{ number_format($a->current_balance, 2) }})</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">المبلغ</label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">البيان</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    تنفيذ التحويل
                </button>
                <a href="{{ route('transfers.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">إلغاء</a>
            </div>
        </form>
    </div>

    <script>
    function toggleFrom() {
        var type = document.getElementById('from_type').value;
        document.getElementById('from-treasuries').style.display = type === 'treasury' ? '' : 'none';
        document.getElementById('from-banks').style.display = type === 'bank' ? '' : 'none';
        document.getElementById('from-accounts').style.display = type === 'account' ? '' : 'none';
        var sel = document.getElementById('from_id');
        for (var i = 0; i < sel.options.length; i++) {
            var opt = sel.options[i];
            if (opt.value === '') continue;
            var group = opt.getAttribute('data-group');
            if (group === 'treasury') opt.style.display = type === 'treasury' ? '' : 'none';
            if (group === 'bank') opt.style.display = type === 'bank' ? '' : 'none';
            if (group === 'account') opt.style.display = type === 'account' ? '' : 'none';
        }
        if (sel.selectedIndex > 0) {
            var selected = sel.options[sel.selectedIndex];
            if (selected.style.display === 'none') sel.value = '';
        }
    }
    function toggleTo() {
        var type = document.getElementById('to_type').value;
        document.getElementById('to-treasuries').style.display = type === 'treasury' ? '' : 'none';
        document.getElementById('to-banks').style.display = type === 'bank' ? '' : 'none';
        document.getElementById('to-accounts').style.display = type === 'account' ? '' : 'none';
        var sel = document.getElementById('to_id');
        for (var i = 0; i < sel.options.length; i++) {
            var opt = sel.options[i];
            if (opt.value === '') continue;
            var grp = opt.getAttribute('data-group');
            if (grp === 'treasury') opt.style.display = type === 'treasury' ? '' : 'none';
            if (grp === 'bank') opt.style.display = type === 'bank' ? '' : 'none';
            if (grp === 'account') opt.style.display = type === 'account' ? '' : 'none';
        }
        if (sel.selectedIndex > 0) {
            var selected = sel.options[sel.selectedIndex];
            if (selected.style.display === 'none') sel.value = '';
        }
    }
    toggleFrom();
    toggleTo();
    </script>
</x-app-layout>
