<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة مبيعات - {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A4; margin: 1.5cm 1cm; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif; direction: rtl; text-align: right; font-size: 12px; color: #1f2937; background: white; padding: 20px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; overflow: hidden; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 5px; }
        .company-name { font-size: 18px; font-weight: bold; color: #2563eb; }
        .company-info { font-size: 10px; color: #6b7280; line-height: 1.6; }
        .document-info h2 { color: #2563eb; font-size: 16px; margin: 0 0 5px 0; }
        .document-info p { font-size: 11px; margin: 2px 0; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #2563eb; color: white; padding: 8px; text-align: center; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; text-align: center; }
        .total-row td { font-weight: bold; background: #f3f4f6; }
        .total-row.final td { background: #2563eb; color: white; }
        .ltr { direction: ltr; text-align: left; unicode-bidi: embed; }
        .rtl { direction: rtl; text-align: right; unicode-bidi: embed; }
        .footer { margin-top: 20px; border-top: 1px solid #d1d5db; padding-top: 10px; font-size: 10px; color: #9ca3af; text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-mono { font-family: 'Courier New', monospace; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    @if($company && $company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" style="height: 50px;">
                    @endif
                    <div class="company-name">{{ $company->name ?? 'Smart ERP' }}</div>
                    <div class="company-info">
                        @if($company->address)<span>العنوان: {{ $company->address }}</span><br>@endif
                        @if($company->phone)<span>الهاتف: {{ $company->phone }}</span>@endif
                        @if($company->tax_number)<span class="ltr"> | الرقم الضريبي: {{ $company->tax_number }}</span>@endif
                    </div>
                </td>
                <td style="width: 40%; text-align: left; vertical-align: top;">
                    <div class="document-info">
                        <h2>فاتورة مبيعات</h2>
                        <p>رقم الفاتورة: <strong class="ltr">{{ $invoice->invoice_number }}</strong></p>
                        <p>التاريخ: <strong class="ltr">{{ $invoice->date }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <p><strong>العميل:</strong> {{ $invoice->customer->name ?? '' }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 40%;">الصنف</th>
                <th style="width: 12%;">الكمية</th>
                <th style="width: 15%;">السعر</th>
                <th style="width: 13%;">الخصم</th>
                <th style="width: 15%;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $i => $line)
            <tr>
                <td class="ltr">{{ $i + 1 }}</td>
                <td class="text-right">{{ $line->item->name ?? '' }}</td>
                <td class="ltr font-mono">{{ number_format($line->quantity, 2) }}</td>
                <td class="ltr font-mono">{{ number_format($line->unit_price, 2) }}</td>
                <td class="ltr font-mono">{{ number_format($line->discount_amount ?? 0, 2) }}</td>
                <td class="ltr font-mono">{{ number_format($line->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-left">الإجمالي قبل الخصم</td>
                <td class="ltr font-mono">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr class="total-row">
                <td colspan="5" class="text-left">الخصم</td>
                <td class="ltr font-mono">{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->tax_amount > 0)
            <tr class="total-row">
                <td colspan="5" class="text-left">الضريبة</td>
                <td class="ltr font-mono">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row final">
                <td colspan="5" class="text-left">الإجمالي</td>
                <td class="ltr font-mono">{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا المستند بواسطة نظام Smart ERP</p>
    </div>
</body>
</html>
