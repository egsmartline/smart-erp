<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; direction: rtl; text-align: right; font-size: 12px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #2563eb; }
        .company-info { font-size: 10px; color: #666; }
        .company-info span { display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #2563eb; color: white; padding: 8px; text-align: right; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #eee; font-size: 11px; text-align: right; }
        .total-row { font-weight: bold; background: #f3f4f6; }
        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #666; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-posted { background: #d1fae5; color: #065f46; }
        .badge-paid { background: #dbeafe; color: #1e40af; }
        .ltr { direction: ltr; text-align: left; unicode-bidi: embed; }
        .rtl { direction: rtl; text-align: right; unicode-bidi: embed; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" style="height: 50px;">
                    @endif
                    <div class="company-name" dir="rtl">{{ $company->name ?? 'Smart ERP' }}</div>
                    <div class="company-info" dir="rtl">
                        @if($company->address)<span>العنوان: {{ $company->address }}</span><br>@endif
                        @if($company->phone)<span>الهاتف: {{ $company->phone }}</span>@endif
                        @if($company->tax_number)<span dir="ltr"> | الرقم الضريبي: {{ $company->tax_number }}</span>@endif
                    </div>
                </td>
                <td style="border: none; text-align: left; width: 40%; vertical-align: top;">
                    @yield('document-info')
                </td>
            </tr>
        </table>
    </div>
    @yield('content')
    <div class="footer" dir="rtl">
        <p>تم إنشاء هذا المستند بواسطة نظام Smart ERP في {{ now()->format('Y/m/d H:i') }}</p>
    </div>
</body>
</html>
