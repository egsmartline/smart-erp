@extends('pdf.layout')
@section('document-info')
    <h2 style="color: #2563eb; margin: 0;">فاتورة شراء</h2>
    <p>رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong></p>
    <p>
        <span>التاريخ: <strong>{{ $invoice->date }}</strong></span>
        @if($invoice->due_date)
            <span style="margin-right: 20px;">المستحق: <strong>{{ $invoice->due_date }}</strong></span>
        @endif
    </p>
    <p>الحالة: <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status === 'posted' ? 'مرحل' : ($invoice->status === 'paid' ? 'مدفوعة' : 'مسودة') }}</span></p>
@endsection

@section('content')
    @php
        $printSubtotal = 0;
        $printDiscount = 0;
        $printTax = 0;
        foreach ($invoice->lines as $l) {
            $ls = $l->quantity * $l->unit_price;
            $ld = $ls * (($l->discount_percent ?? 0) / 100);
            $la = $ls - $ld;
            $lt = $la * (($l->tax_rate ?? 0) / 100);
            $printSubtotal += $ls;
            $printDiscount += $ld;
            $printTax += $lt;
        }
        $printTotal = $printSubtotal - $printDiscount + $printTax + ($invoice->shipping_amount ?? 0);
    @endphp
    <table>
        <tr><td><strong>المورد:</strong> {{ $invoice->supplier->name ?? '' }}</td></tr>
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
                <td dir="ltr">{{ number_format($line->quantity, 2) }}</td>
                <td dir="ltr">{{ number_format($line->unit_price, 2) }}</td>
                <td dir="ltr">{{ number_format($line->discount_amount ?? 0, 2) }}</td>
                <td dir="ltr">{{ number_format($line->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row"><td colspan="5" style="text-align: left;">الإجمالي</td><td dir="ltr">{{ number_format($printTotal, 2) }}</td></tr>
        </tfoot>
    </table>
@endsection
