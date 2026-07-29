<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">طباعة الباركود</h2>
            <button onclick="printBarcodes()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition no-print">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 no-print">
        <form method="GET" class="mb-4 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="بحث بالاسم أو الكود أو الباركود..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">التصنيف</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('barcodes.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        @if($barcodes->isEmpty())
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <p class="text-lg font-medium">لا توجد أصناف بها باركود</p>
                <p class="text-sm mt-1">أضف باركود للأصناف من صفحة الأصناف أولاً</p>
            </div>
        @else
            <div id="barcode-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($barcodes as $item)
                    <div class="barcode-card border border-gray-200 rounded-lg p-3 text-center">
                        <div class="flex items-start justify-between">
                            <div class="text-xs font-medium text-gray-800 truncate flex-1 text-right">{{ $item->name }}</div>
                            <input type="number" min="0" max="99" value="0"
                                class="barcode-qty w-12 text-center border border-gray-300 rounded text-xs py-0.5 mr-1">
                        </div>
                        <div class="text-[10px] text-gray-400 mb-0.5">كود: {{ $item->display_barcode }}</div>
                        <img src="data:image/png;base64,{{ $item->barcode_base64 }}" alt="{{ $item->display_barcode }}" class="mx-auto" style="max-width:100%;height:auto;">
                        <div class="text-[10px] font-mono text-gray-500 mt-0.5">{{ $item->display_barcode }}</div>
                        @if($item->sku && $item->sku != $item->display_barcode)
                            <div class="text-[10px] text-gray-400">SKU: {{ $item->sku }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        .barcode-qty { -moz-appearance: textfield; }
        .barcode-qty::-webkit-inner-spin-button,
        .barcode-qty::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
    @endpush

    @push('scripts')
    <script>
        function printBarcodes() {
            var cards = document.querySelectorAll('#barcode-grid .barcode-card');

            var win = window.open('about:blank', '_blank');
            if (!win) { alert('من فضلك اسمح للنوافذ المنبثقة'); return; }

            // Build CSS
            var css = '*{box-sizing:border-box;margin:0;padding:0}';
            css += 'body{font-family:sans-serif;direction:rtl}';
            css += '.tb{text-align:center;margin:3px}';
            css += '.tb button{padding:6px 16px;font-size:12px;background:#2563eb;color:#fff;border:none;border-radius:4px;cursor:pointer}';
            css += '.g{display:grid;grid-template-columns:repeat(4,1fr);gap:2px;padding:2px}';
            css += '.c{border:1px dashed #ccc;border-radius:2px;padding:2px;text-align:center;page-break-inside:avoid}';
            css += '.n{font-size:9px;font-weight:500;color:#1f2937;margin-bottom:0;line-height:1.3;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;word-break:break-word}';
            css += '.l{font-size:8px;color:#9ca3af;margin-bottom:0;line-height:1.2}';
            css += '.c img{max-width:100%;height:auto;display:block;margin:0 auto;max-height:28px}';
            css += '.o{font-size:8px;font-family:monospace;color:#6b7280;margin-top:0;line-height:1.2}';
            css += '.s{font-size:8px;color:#9ca3af;line-height:1.2}';
            css += '@media print{@page{margin:0.2cm}.tb{display:none!important}}';

            // Build card HTML
            var cardsHtml = '';
            cards.forEach(function(card) {
                var qty = parseInt(card.querySelector('.barcode-qty').value) || 0;
                if (qty < 1) return;
                var imgSrc = card.querySelector('img').src;
                var nameEl = card.querySelector('.text-xs.font-medium');
                var name = nameEl ? nameEl.textContent.trim() : '';
                var codeEl = card.querySelector('.font-mono');
                var code = codeEl ? codeEl.textContent.trim() : '';
                var codeLabel = code;
                var skuText = '';
                var divs = card.querySelectorAll('div');
                for (var d = 0; d < divs.length; d++) {
                    var t = divs[d].textContent.trim();
                    if (t.indexOf('كود:') === 0) codeLabel = t.replace('كود:', '').trim();
                    if (t.indexOf('SKU:') === 0) skuText = t;
                }
                for (var i = 0; i < qty; i++) {
                    cardsHtml += '<div class="c">';
                    cardsHtml += '<div class="n">' + esc(name) + '</div>';
                    cardsHtml += '<div class="l">كود: ' + esc(codeLabel) + '</div>';
                    cardsHtml += '<img src="' + imgSrc + '" alt="' + esc(code) + '">';
                    cardsHtml += '<div class="o">' + esc(code) + '</div>';
                    if (skuText) cardsHtml += '<div class="s">' + esc(skuText) + '</div>';
                    cardsHtml += '</div>';
                }
            });

            // Write skeleton
            win.document.open();
            win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>طباعة باركود</title><style>' + css + '</style></head><body>');
            win.document.write('<div class="tb"><button onclick="window.print()">طباعة</button></div>');
            win.document.write('<div id="pg"></div>');
            win.document.write('</body></html>');
            win.document.close();

            // Inject cards via innerHTML
            var pg = win.document.getElementById('pg');
            if (pg) {
                pg.className = 'g';
                pg.innerHTML = cardsHtml;
            }

            win.focus();
        }

        function esc(s) {
            if (!s) return '';
            return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
    @endpush
</x-app-layout>