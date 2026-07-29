<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="utf-8">
    <title>طباعة باركود</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; }
        .print-area { padding: 8px; }
        .grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; }
        .card { border: 1px dashed #999; border-radius: 4px; padding: 4px; text-align: center; page-break-inside: avoid; }
        .name { font-size: 10px; font-weight: 500; color: #1f2937; margin-bottom: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .code-label { font-size: 9px; color: #9ca3af; margin-bottom: 1px; }
        .card img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
        .code { font-size: 9px; font-family: monospace; color: #6b7280; margin-top: 1px; }
        .sku { font-size: 9px; color: #9ca3af; }
        .no-print { margin: 10px; text-align: center; }
        .no-print button { padding: 10px 30px; font-size: 16px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .no-print button:hover { background: #1d4ed8; }
        @media print {
            @page { margin: 0.5cm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">طباعة</button>
    </div>
    <div class="print-area">
        <div class="grid">
            @forelse($items as $item)
                @for($i = 0; $i < $item['qty']; $i++)
                    <div class="card">
                        <div class="name">{{ $item['name'] }}</div>
                        <div class="code-label">كود: {{ $item['code'] }}</div>
                        <img src="data:image/png;base64,{{ $item['barcode_base64'] }}" alt="{{ $item['code'] }}">
                        <div class="code">{{ $item['code'] }}</div>
                        @if($item['sku'])
                            <div class="sku">SKU: {{ $item['sku'] }}</div>
                        @endif
                    </div>
                @endfor
            @empty
                <p>لا توجد أصناف للطباعة</p>
            @endforelse
        </div>
    </div>
</body>
</html>