<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال استلام نقدية - {{ $payment->payment_number }}</title>
    <style>
        body {
            font-family: 'Traditional Arabic', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f7f6;
        }
        .receipt {
            background: #fff;
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 22px; font-weight: bold; }
        .header .info { text-align: left; font-size: 14px; }
        .header .info div { margin-bottom: 5px; }
        .header .info span { font-weight: bold; }
        .field { margin-bottom: 5px; font-size: 16px; }
        .field-label { font-weight: bold; display: inline-block; width: 100px; }
        .field-value { display: inline-block; width: calc(100% - 110px); padding: 5px 0; }
        .field-value.highlight { background: #e6f2ff; font-weight: bold; text-align: center; }
        .footer { display: flex; justify-content: space-between; margin-top: 20px; font-size: 16px; }
        .signature { text-align: center; width: 45%; }
        .signature-line { border-bottom: 1px solid #333; margin-top: 25px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button, .no-print a {
            padding: 10px 32px; font-size: 16px; cursor: pointer; border: none; border-radius: 6px; text-decoration: none; display: inline-block;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; border-radius: 0; margin: 0; width: 100%; min-height: auto; }
            @page { margin: 10mm; size: A5; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="background:#059669;color:#fff;">طباعة</button>
        <a href="{{ route('payments.show', $payment) }}" style="background:#6b7280;color:#fff;">رجوع</a>
    </div>

    <div class="receipt">
        <div class="header">
            <h2>إيصال استلام نقدية</h2>
            <div class="info">
                <div>رقم الإيصال: <span>{{ $payment->payment_number }}</span></div>
                <div>التاريخ: <span>{{ $payment->date->format('Y/m/d') }}</span></div>
            </div>
        </div>

        <div>
            <div class="field">
                <span class="field-label">استلمنا من السيد:</span>
                <span class="field-value">{{ $payment->customer?->name ?? $payment->supplier?->name ?? $payment->account?->name ?? '______________________' }}</span>
            </div>

            <div class="field">
                <span class="field-label">مبلغ وقدره:</span>
                <span class="field-value highlight">{{ number_format($payment->amount, 2) }} جنيهاً مصرياً (فقط: {{ $amountInWords }} جنيهاً مصرياً لا غير)</span>
            </div>

            <div class="field">
                <span class="field-label">وذلك عن قيمة:</span>
                <span class="field-value">{{ $payment->notes ?? '______________________' }}</span>
            </div>

            <div class="field">
                <span class="field-label">طريقة الدفع:</span>
                <span class="field-value">
                    @if($payment->payment_method === 'cash') نقداً
                    @elseif($payment->payment_method === 'check') شيك
                    @elseif($payment->payment_method === 'bank_transfer') تحويل بنكي
                    @else {{ $payment->payment_method }}
                    @endif
                </span>
            </div>
        </div>

        <div class="footer">
            <div class="signature">
                <span>توقيع المستلم</span>
                <div class="signature-line"></div>
            </div>
            <div class="signature">
                <span>توقيع العميل</span>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <script>window.onload = function() { window.print(); };</script>
</body>
</html>
