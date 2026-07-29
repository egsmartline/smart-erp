<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ $payment->type === 'receipt' ? 'سند استلام نقدية' : 'سند صرف' }} - {{ $payment->payment_number }}</title>
    <style>
        @page { margin: 5mm; }
        body { font-family: 'Traditional Arabic', 'Arial', sans-serif; font-size: 14px; color: #000; margin: 0; padding: 0; }
        .voucher { max-width: 210mm; margin: 0 auto; padding: 4mm; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 3mm; }
        .header .company { font-size: 16px; font-weight: bold; }
        .header .ref { text-align: left; font-size: 16px; font-weight: bold; }
        .title { text-align: center; font-size: 20px; font-weight: bold; margin: 3mm 0; }
        .body-text { font-size: 16px; font-weight: bold; line-height: 1.6; margin-bottom: 3mm; }
        .body-text .field { display: inline; padding: 0 4px; }
        .body-text .field-lg { display: inline; padding: 0 4px; }
        .method-box { margin: 3mm 0; padding: 3mm; border: 1px solid #000; font-size: 16px; font-weight: bold; }
        .signatures { display: flex; justify-content: space-between; margin-top: 6mm; text-align: center; }
        .signature-item { width: 30%; }
        .signature-item .line { border-top: 1px solid #000; margin: 8mm 0 2px 0; }
        .signature-item .title { font-size: 16px; font-weight: bold; }
        .signature-item .name { font-size: 11px; margin-top: 1px; }
        .footer { text-align: center; margin-top: 4mm; font-size: 10px; color: #888; border-top: 1px solid #ccc; padding-top: 2mm; }
        .no-print { display: none; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="voucher">
        <div class="header">
            <div class="company">
                @if($company && $company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" style="height: 35px; object-fit: contain;" alt="Logo">
                @else
                    {{ config('app.name') }}
                @endif
            </div>
            <div class="ref">
                الرقم المرجعي: <span style="display: inline-block;">{{ $payment->payment_number }}</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                التاريخ: <span style="display: inline-block;">{{ $payment->date->format('Y/m/d') }}</span>
            </div>
        </div>

        <div class="title">{{ $payment->type === 'receipt' ? 'سند استلام نقدية' : 'سند صرف' }}</div>

        @if($payment->type === 'payment')
            <div class="body-text">
                قام السيد/
                <span style="padding: 0 4px;">{{ $payment->supplier?->name ?? $payment->customer?->name ?? $payment->account?->name ?? '______' }}</span>
                <br>باستلام مبلغ نقدي قدره/
                <span class="field-lg" style="font-size:90%">{{ number_format($payment->amount, 2) }}</span>
                جنيهاً مصرياً (فقط: <strong>{{ $amountInWords }}</strong> جنيهاً مصرياً لا غير)
                <br><br>
                بصفته:
                <span class="field">
                    {{ $payment->supplier ? 'مورد' : ($payment->customer ? 'عميل' : '______________________') }}
                </span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                بغرض:
                <span class="field-lg">{{ $payment->notes ?? '______________________' }}</span>
            </div>
        @else
            <div class="body-text">
                قام السيد/
                <span style="padding: 0 4px;">{{ $payment->customer?->name ?? $payment->supplier?->name ?? $payment->account?->name ?? '______' }}</span>
                <br>بدفع مبلغ نقدي قدره/
                <span class="field-lg" style="font-size:90%">{{ number_format($payment->amount, 2) }}</span>
                جنيهاً مصرياً (فقط: <strong>{{ $amountInWords }}</strong> جنيهاً مصرياً لا غير)
                <br><br>
                بصفته:
                <span class="field">
                    {{ $payment->customer ? 'عميل' : ($payment->supplier ? 'مورد' : '______________________') }}
                </span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                بغرض:
                <span class="field-lg">{{ $payment->notes ?? '______________________' }}</span>
            </div>
        @endif

        <div class="method-box">
            طريقة الصرف:
            @if($payment->payment_method === 'cash') نقداً
            @elseif($payment->payment_method === 'check')
                شيك رقم: {{ $payment->check_number ?? '______' }}
                تاريخ: {{ $payment->check_date?->format('Y/m/d') ?? '__/__/____' }}
                بنك: {{ $payment->bankAccount?->bank_name ?? '______' }}
            @elseif($payment->payment_method === 'bank_transfer')
                تحويل بنكي - {{ $payment->bankAccount?->bank_name ?? '______' }}
            @else
                {{ $payment->payment_method }}
            @endif
        </div>

        <div class="signatures">
            <div class="signature-item">
                <div class="line"></div>
                <div class="title">توقيع قسم الحسابات</div>
            </div>
            <div class="signature-item">
                <div class="line"></div>
                <div class="title">توقيع المستلم</div>
            </div>
            <div class="signature-item">
                <div class="line"></div>
                <div class="title">ختم الشركة</div>
            </div>
        </div>

        <div class="footer">
            نظام Business ERP | {{ now()->format('Y-m-d H:i') }}
        </div>

        <div style="text-align: center; margin-top: 8mm;" class="no-print">
            <button onclick="window.print()" style="padding: 10px 32px; font-size: 16px; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 6px;">طباعة</button>
            <button onclick="window.close()" style="padding: 10px 32px; font-size: 16px; cursor: pointer; background: #6b7280; color: #fff; border: none; border-radius: 6px;">إغلاق</button>
        </div>
    </div>

    <script>window.onload = function() { window.print(); };</script>
</body>
</html>
