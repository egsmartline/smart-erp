<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ $tradeOperation->operation_number }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 6px 8px; text-align: right; font-size: 11px; }
        table th { background: #f5f5f5; font-weight: 600; width: 25%; }
        .section-title { font-size: 13px; font-weight: 700; background: #eee; padding: 6px 10px; margin: 15px 0 8px; border-radius: 3px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; }
        .badge-import { background: #dbeafe; color: #1e40af; }
        .badge-export { background: #d1fae5; color: #065f46; }
        .badge-draft { background: #f3f4f6; color: #374151; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-shipped { background: #fef3c7; color: #92400e; }
        .badge-cleared { background: #f3e8ff; color: #6b21a8; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tradeOperation->operation_number }}</h1>
        <p>
            <span class="badge {{ $tradeOperation->type === 'import' ? 'badge-import' : 'badge-export' }}">{{ $tradeOperation->type === 'import' ? 'استيراد' : 'تصدير' }}</span>
            @php
                $statusMap = ['draft' => 'badge-draft', 'confirmed' => 'badge-confirmed', 'shipped' => 'badge-shipped', 'cleared' => 'badge-cleared', 'completed' => 'badge-completed', 'cancelled' => 'badge-cancelled'];
            @endphp
            <span class="badge {{ $statusMap[$tradeOperation->status] ?? 'badge-draft' }}">{{ ['draft' => 'مسودة', 'confirmed' => 'مؤكدة', 'shipped' => 'تم الشحن', 'cleared' => 'تم التخليص', 'completed' => 'مكتملة', 'cancelled' => 'ملغية'][$tradeOperation->status] ?? $tradeOperation->status }}</span>
        </p>
    </div>

    <div class="section-title">معلومات أساسية</div>
    <table>
        <tr><th>التاريخ</th><td>{{ $tradeOperation->date->format('Y-m-d') }}</td><th>الجهة</th><td>{{ $tradeOperation->party_name ?? '—' }}</td></tr>
        <tr><th>الدولة</th><td>{{ $tradeOperation->country ?? '—' }}</td><th>Incoterm</th><td>{{ $tradeOperation->incoterm ?? '—' }}</td></tr>
        <tr><th>ميناء التحميل</th><td>{{ $tradeOperation->port_of_loading ?? '—' }}</td><th>ميناء التفريغ</th><td>{{ $tradeOperation->port_of_discharge ?? '—' }}</td></tr>
    </table>

    <div class="section-title">القيمة والعملة</div>
    <table>
        <tr><th>العملة</th><td>{{ $tradeOperation->currency?->code ?? '—' }}</td><th>سعر الصرف</th><td>{{ number_format($tradeOperation->exchange_rate, 4) }}</td></tr>
        <tr><th>إجمالي القيمة</th><td>{{ number_format($tradeOperation->total_value, 2) }}</td><th>إجمالي المصروفات</th><td>{{ number_format($tradeOperation->totalCosts(), 2) }}</td></tr>
    </table>

    <div class="section-title">معلومات الشحن</div>
    <table>
        <tr><th>طريقة الشحن</th><td>{{ ['sea' => 'بحري', 'air' => 'جوي', 'land' => 'بري'][$tradeOperation->shipping_method] ?? $tradeOperation->shipping_method ?? '—' }}</td><th>رقم الحاوية</th><td>{{ $tradeOperation->container_number ?? '—' }}</td></tr>
        <tr><th>اسم السفينة</th><td>{{ $tradeOperation->vessel_name ?? '—' }}</td><th>بوليصة الشحن</th><td>{{ $tradeOperation->bill_of_lading_number ?? '—' }}</td></tr>
        <tr><th>ETD</th><td>{{ $tradeOperation->etd_date?->format('Y-m-d') ?? '—' }}</td><th>ETA</th><td>{{ $tradeOperation->eta_date?->format('Y-m-d') ?? '—' }}</td></tr>
    </table>

    @if($tradeOperation->lc_number)
    <div class="section-title">خطاب الاعتماد (LC)</div>
    <table>
        <tr><th>رقم LC</th><td>{{ $tradeOperation->lc_number }}</td><th>البنك المصدر</th><td>{{ $tradeOperation->lc_issuing_bank ?? '—' }}</td></tr>
        <tr><th>البنك المستفيد</th><td>{{ $tradeOperation->lc_beneficiary_bank ?? '—' }}</td><th>النوع</th><td>{{ ['sight' => 'At Sight', 'deferred' => 'Deferred', 'standby' => 'Standby'][$tradeOperation->lc_type] ?? $tradeOperation->lc_type ?? '—' }}</td></tr>
        <tr><th>القيمة</th><td>{{ number_format($tradeOperation->lc_amount, 2) }}</td><th>تاريخ الإصدار / الانتهاء</th><td>{{ $tradeOperation->lc_issue_date?->format('Y-m-d') ?? '—' }} / {{ $tradeOperation->lc_expiry_date?->format('Y-m-d') ?? '—' }}</td></tr>
    </table>
    @endif

    <div class="section-title">المصروفات</div>
    <table>
        <tr><th>القيمة الجمركية</th><td>{{ $tradeOperation->customs_value ? number_format($tradeOperation->customs_value, 2) : '—' }}</td><th>مبلغ الجمارك</th><td>{{ $tradeOperation->customs_duty_amount ? number_format($tradeOperation->customs_duty_amount, 2) : '—' }}</td></tr>
        <tr><th>تكلفة الشحن</th><td>{{ $tradeOperation->shipping_cost ? number_format($tradeOperation->shipping_cost, 2) : '—' }}</td><th>التأمين</th><td>{{ $tradeOperation->insurance_cost ? number_format($tradeOperation->insurance_cost, 2) : '—' }}</td></tr>
        <tr><th>الفحص</th><td>{{ $tradeOperation->inspection_cost ? number_format($tradeOperation->inspection_cost, 2) : '—' }}</td><th>أخرى</th><td>{{ $tradeOperation->other_costs ? number_format($tradeOperation->other_costs, 2) : '—' }}</td></tr>
    </table>

    @if($tradeOperation->notes)
    <div class="section-title">ملاحظات</div>
    <p>{{ $tradeOperation->notes }}</p>
    @endif

    <div class="footer">تمت الطباعة في {{ now()->format('Y-m-d H:i') }}</div>
</body>
</html>