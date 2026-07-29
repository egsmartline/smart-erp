@extends('pdf.layout')
@section('document-info')
    <h2 style="color: #2563eb; margin: 0;">أمر شراء</h2>
    <p>رقم الأمر: <strong>{{ $order->order_number }}</strong></p>
    <p>التاريخ: {{ $order->date }}</p>
@endsection

@section('content')
    @php
        $printSubtotal = 0;
        $printDiscount = 0;
        $printTax = 0;
        foreach ($order->lines as $l) {
            $ls = $l->quantity * $l->unit_price;
            $ld = $ls * (($l->discount_percent ?? 0) / 100);
            $la = $ls - $ld;
            $lt = $la * (($l->tax_rate ?? 0) / 100);
            $printSubtotal += $ls;
            $printDiscount += $ld;
            $printTax += $lt;
        }
        $printTotal = $printSubtotal - $printDiscount + $printTax + ($order->shipping_amount ?? 0);
    @endphp
    <table>
        <tr><td><strong>المورد:</strong> {{ $order->supplier->name ?? '' }}</td></tr>
    </table>
    <table class="data-table">
        <colgroup>
            <col style="width: 5%;">
            <col style="width: 30%;">
            <col style="width: 13%;">
            <col style="width: 18%;">
            <col style="width: 15%;">
            <col style="width: 19%;">
        </colgroup>
        <thead><tr><th>#</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الخصم</th><th>الإجمالي</th></tr></thead>
        <tbody>
            @foreach($order->lines as $i => $line)
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
