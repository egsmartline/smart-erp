@extends('pdf.layout')
@section('document-info')
    <h2 style="color: #2563eb; margin: 0;">فاتورة مبيعات</h2>
    <p dir="rtl">رقم الفاتورة: <strong dir="ltr">{{ $invoice->invoice_number }}</strong></p>
    <p dir="rtl">التاريخ: <strong dir="ltr">{{ $invoice->date }}</strong></p>
    <p>الحالة: <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status === 'posted' ? 'مرحل' : ($invoice->status === 'paid' ? 'مدفوعة' : 'مسودة') }}</span></p>
@endsection

@section('content')
    <table>
        <tr>
            <td><strong>العميل:</strong> {{ $invoice->customer->name ?? '' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>#</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الخصم</th><th>الإجمالي</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $i => $line)
            <tr>
                <td dir="ltr">{{ $i + 1 }}</td>
                <td>{{ $line->item->name ?? '' }}</td>
                <td dir="ltr">{{ $line->quantity }}</td>
                <td dir="ltr">{{ number_format($line->unit_price, 2) }}</td>
                <td dir="ltr">{{ $line->discount ?? 0 }}</td>
                <td dir="ltr">{{ number_format($line->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row"><td colspan="5" style="text-align: left;">الإجمالي قبل الخصم</td><td dir="ltr">{{ number_format($invoice->subtotal, 2) }}</td></tr>
            <tr class="total-row"><td colspan="5" style="text-align: left;">الخصم</td><td dir="ltr">{{ number_format($invoice->discount_amount ?? 0, 2) }}</td></tr>
            <tr class="total-row"><td colspan="5" style="text-align: left;">الضريبة</td><td dir="ltr">{{ number_format($invoice->tax_amount ?? 0, 2) }}</td></tr>
            <tr class="total-row" style="background: #2563eb; color: white;"><td colspan="5" style="text-align: left;">الإجمالي</td><td dir="ltr">{{ number_format($invoice->total, 2) }}</td></tr>
        </tfoot>
    </table>
@endsection
