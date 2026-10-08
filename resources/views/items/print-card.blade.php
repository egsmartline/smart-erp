<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4; margin: 1cm 0.8cm; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif; direction: rtl; text-align: right; font-size: 13px; color: #1f2937; background: white; width: 100%; margin: 0 auto; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; overflow: hidden; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 4px; vertical-align: top; }
        .company-name { font-size: 18px; font-weight: bold; color: #2563eb; text-align: center; }
        .company-info { font-size: 11px; color: #6b7280; line-height: 1.5; text-align: center; }
        .doc-title { color: #2563eb; font-size: 16px; font-weight: bold; margin: 0 0 4px 0; text-align: center; }
        .doc-meta { font-size: 12px; margin: 2px 0; color: #374151; text-align: center; }
        .section-title { font-size: 14px; font-weight: bold; color: #2563eb; margin: 14px 0 6px 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 6px 0; }
        .info-table td { border: 1px solid #e5e7eb; padding: 7px 10px; font-size: 13px; width: 25%; }
        .info-table .lbl { color: #6b7280; font-size: 11px; display: block; margin-bottom: 2px; }
        .info-table .val { font-weight: bold; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 6px 0; }
        .summary-table td { border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 13px; width: 25%; text-align: center; }
        .summary-table .lbl { color: #6b7280; font-size: 11px; display: block; margin-bottom: 2px; }
        .summary-table .val { font-weight: bold; font-size: 16px; }
        .val.in { color: #059669; }
        .val.out { color: #dc2626; }
        table.data-table { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 8px 0; }
        table.data-table th { background: #2563eb; color: white; padding: 6px; text-align: center; font-size: 12px; }
        table.data-table td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 12px; text-align: center; overflow: hidden; }
        .font-mono { font-family: 'Courier New', monospace; }
        .footer { margin-top: 14px; border-top: 1px solid #d1d5db; padding-top: 7px; font-size: 11px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    @if($company && $company->logo)
                        <div><img src="{{ asset('storage/' . $company->logo) }}" style="height: 48px;"></div>
                    @endif
                    <div class="company-name">{{ $company->name ?? 'Smart ERP' }}</div>
                    <div class="company-info">
                        @if($company->address ?? null)<span>العنوان: {{ $company->address }}</span><br>@endif
                        @if($company->phone ?? null)<span>الهاتف: {{ $company->phone }}</span>@endif
                        @if($company->tax_number ?? null)<span class="font-mono"> | الرقم الضريبي: {{ $company->tax_number }}</span>@endif
                    </div>
                </td>
                <td style="width: 40%; text-align: center; vertical-align: top;">
                    <div class="doc-title">كارت الصنف</div>
                    <div class="doc-meta">الصنف: <strong>{{ $item->name }}</strong></div>
                    @if($item->name_ar)<div class="doc-meta">{{ $item->name_ar }}</div>@endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">بيانات الصنف</div>
    <table class="info-table">
        <tr>
            <td><span class="lbl">الكود (SKU)</span><span class="val font-mono">{{ $item->sku ?? '-' }}</span></td>
            <td><span class="lbl">سعر البيع</span><span class="val in">{{ number_format($item->selling_price, 2) }}</span></td>
            <td><span class="lbl">الوحدة</span><span class="val">{{ $item->unit->name ?? '-' }}</span></td>
            <td><span class="lbl">التصنيف</span><span class="val">{{ $item->category->name ?? '-' }}</span></td>
        </tr>
    </table>

    <div class="section-title">الأرصدة</div>
    <table class="summary-table">
        <tr>
            <td><span class="lbl">الرصيد الافتتاحي</span><span class="val">{{ number_format($opening, 2) }}</span></td>
            <td><span class="lbl">إجمالي الوارد</span><span class="val in">{{ number_format($totals['in'], 2) }}</span></td>
            <td><span class="lbl">إجمالي الصادر</span><span class="val out">{{ number_format($totals['out'], 2) }}</span></td>
            <td><span class="lbl">الرصيد الحالي</span><span class="val">{{ number_format($currentBalance, 2) }}</span></td>
        </tr>
    </table>

    <div class="section-title">حركة الصنف</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 24%;">التاريخ</th>
                <th style="width: 34%;">البيان / المرجع</th>
                <th style="width: 12%;">النوع</th>
                <th style="width: 9%;">وارد</th>
                <th style="width: 9%;">صادر</th>
                <th style="width: 7%;">الرصيد</th>
            </tr>
        </thead>
        <tbody>
            @forelse($all as $index => $m)
                <tr>
                    <td class="font-mono">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $m->created_at ? $m->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td style="text-align: right;">
                        @if($m->doc_number)
                            <span>{{ $m->doc_label }}</span>
                            <span class="font-mono">{{ $m->doc_number }}</span>
                            @if($m->description)<br><span style="font-size: 11px; color: #6b7280;">{{ $m->description }}</span>@endif
                        @else
                            {{ $m->description ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $typeLabels[$m->type] ?? $m->type }}</td>
                    <td class="font-mono" style="color: #059669;">{{ $m->in_qty ? number_format($m->in_qty, 2) : '' }}</td>
                    <td class="font-mono" style="color: #dc2626;">{{ $m->out_qty ? number_format($m->out_qty, 2) : '' }}</td>
                    <td class="font-mono">{{ number_format($m->balance_after, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center; color: #9ca3af; padding: 12px;">لا توجد حركات لهذا الصنف</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
