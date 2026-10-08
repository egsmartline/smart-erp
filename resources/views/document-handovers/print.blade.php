<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>حافظة مستندات - {{ $documentHandover->handover_number }}</title>
    <style>
        @page { size: A4; margin: 0.8cm; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Traditional Arabic', 'Arial', sans-serif; direction: rtl; text-align: right; font-size: 12.65px; color: #1f2937; background: white; line-height: 1.5; }
        .logo { text-align: center; margin-bottom: 12px; }
        .logo img { width: 4cm; max-width: 35%; height: auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; margin-bottom: 14px; }
        .ref-box { text-align: right; font-size: 12.65px; }
        .ref-box .label { font-weight: bold; }
        .title { text-align: center; font-size: 17.25px; font-weight: bold; color: #1e3a8a; margin: 12px 0; border-bottom: 1px solid #d1d5db; padding-bottom: 6px; }
        .receipt-text { font-size: 18.4px; line-height: 1.7; margin-bottom: 14px; }
        .receipt-text .bold { font-weight: bold; }
        .receipt-text .underline { text-decoration: none; }
        .receipt-text .space { padding: 0 6px; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.data-table th { background: #2563eb; color: white; padding: 5px 4px; font-size: 14.55px; font-weight: bold; text-align: center; border: 1px solid #2563eb; }
        table.data-table td { padding: 4px 4px; border: 1px solid #d1d5db; font-size: 14.55px; text-align: center; font-weight: bold; }
        table.data-table td.name { text-align: right; }
        table.data-table td.check { color: #16a34a; font-size: 15.87px; font-weight: bold; }
        .ltr { direction: ltr; unicode-bidi: embed; }
        .declaration { margin-top: 18px; padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-size: 16.1px; line-height: 1.7; border-radius: 4px; }
        .declaration .bold { font-weight: bold; color: #1e3a8a; }
        .receive-date { margin-top: 14px; font-size: 13.8px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 25px; text-align: center; }
        .signature-item { width: 40%; }
        .signature-item .title { font-size: 12.65px; font-weight: bold; margin-top: 50px; border: none; padding: 0 0 40px 0; color: #000; }
        .footer { margin-top: 20px; border-top: 1px solid #d1d5db; padding-top: 6px; font-size: 11.5px; color: #9ca3af; text-align: center; }
        .notes-box { margin-top: 14px; font-size: 12.65px; }
        .notes-box .label { font-weight: bold; }
        .no-print { text-align: center; margin: 0 0 20px 0; }
        .no-print button { padding: 10px 32px; font-size: 14px; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 6px; }
        .no-print a { padding: 10px 32px; font-size: 14px; cursor: pointer; background: #6b7280; color: #fff; border: none; border-radius: 6px; text-decoration: none; display: inline-block; margin-right: 8px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">طباعة</button>
        <a href="{{ route('document-handovers.show', $documentHandover) }}">رجوع</a>
    </div>

    <div class="logo">
        <img src="{{ asset('storage/logos/uC1fArl6xIRgB06zIXVS3XXv2wVTups117rIvarX.png') }}" alt="Smart Line Logo">
    </div>

    <div class="header">
        <div class="ref-box">
            <div>رقم الحافظة: <span class="label ltr">{{ $documentHandover->handover_number }}</span>
            &nbsp;&nbsp;&nbsp; التاريخ: <span class="label">{{ $documentHandover->date->format('Y/m/d') }}</span>
            @if($documentHandover->shipment_ref)
                &nbsp;&nbsp;&nbsp; رقم الشحنة: <span class="label ltr">{{ $documentHandover->shipment_ref }}</span>
            @endif
            </div>
        </div>
    </div>

    <div class="title">حافظة مستندات</div>

    <div class="receipt-text">
        استلمت أنا الموقع أدناه: <span class="bold underline space">{{ $documentHandover->receiver_name ?? '............................................' }}</span>
        &nbsp;&nbsp;&nbsp; أحمل بطاقة رقم قومي: <span class="bold underline space">{{ $documentHandover->receiver_id_card ?? '............................................' }}</span>
        <br>
        صادرة من: <span class="bold underline space">{{ $documentHandover->receiver_id_issuer ?? '............................................' }}</span>
        &nbsp;&nbsp;&nbsp; العنوان: <span class="bold underline space">{{ $documentHandover->receiver_address }}</span>
        <br>
        تابع لشركة / جهة: <span class="bold underline space">{{ $documentHandover->receiver_entity ?? '............................................' }}</span>
        &nbsp;&nbsp;&nbsp; من شركة <span class="bold underline space">{{ $documentHandover->company_name ?? 'سمارت لاين للحلول الصناعية' }}</span>، <span class="bold underline space">حافظة مستندات الشحن</span>.
        <br>
        بصفتي <span class="bold underline space">{{ $documentHandover->receiver_role ?? '............................................' }}</span>
        مستلماً، كامل المستندات والوثائق المبينة في الجدول أدناه، والخاصة بشحنة
        <span class="bold underline space">{{ $documentHandover->shipment_ref ?? '......................' }}</span>
        ، وذلك دون أي تحفظ أو مسؤولية على الطرف المسلّم فور التوقيع.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:8%">تسليم</th>
                <th style="width:47%">اسم المستند</th>
                <th style="width:40%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="check">✓</td>
                    <td class="name">{{ $item->document_name }}</td>
                    <td>@if($item->copies && $item->copies > 0)عدد النسخ: {{ $item->copies }}@endif@if(($item->copies && $item->copies > 0) && $item->notes) - @endif{{ $item->notes ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد مستندات مسلمة</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($documentHandover->notes)
        <div class="notes-box">
            <span class="label">ملاحظات: </span>{{ $documentHandover->notes }}
        </div>
    @endif

    <div class="declaration">
        <span class="bold">إقرار وتوقيع المستلم:</span>
        أقر أنا المستلم بمعاينة المستندات المذكورة أعلاه وقبولها، وأصبحت تحت مسؤوليتي الكاملة لإنهاء الإجراءات.
    </div>

    <div class="signatures">
        <div class="signature-item">
            <div class="title">توقيع المستلم</div>
        </div>
        <div class="signature-item">
            <div class="title">توقيع المسلّم (سمارت لاين)</div>
        </div>
    </div>

    <div class="footer">
        تم إنشاء هذا المستند بواسطة نظام Smart ERP في {{ now()->format('Y/m/d H:i') }}
    </div>

    <script>window.onload = function() { window.print(); };</script>
</body>
</html>