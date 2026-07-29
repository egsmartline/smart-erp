@extends('pdf.layout')
@section('document-info')
    <h2 style="color: #2563eb; margin: 0;">عرض سعر</h2>
    <p>رقم العرض: <strong>{{ $quotation->quote_number }}</strong></p>
    <p>التاريخ: {{ $quotation->date }}</p>
    @if($quotation->valid_until)
        <p>صالح حتى: {{ $quotation->valid_until }}</p>
    @endif
@endsection

@section('content')
    @php
        $printSubtotal = 0;
        $printDiscount = 0;
        $printTax = 0;
        foreach ($quotation->lines as $l) {
            $ls = $l->quantity * $l->unit_price;
            $ld = $ls * (($l->discount_percent ?? 0) / 100);
            $la = $ls - $ld;
            $lt = $la * (($l->tax_percent ?? 0) / 100);
            $printSubtotal += $ls;
            $printDiscount += $ld;
            $printTax += $lt;
        }
        $printTotal = $printSubtotal - $printDiscount + $printTax;
    @endphp
    @php $customer = $quotation->customer; @endphp
    <table>
        <tr><td><strong>العميل:</strong> {{ $customer->name ?? '' }}</td></tr>
        @if($customer && $customer->phone)
            <tr><td><strong>الهاتف:</strong> {{ $customer->phone }}</td></tr>
        @endif
    </table>
    <table class="data-table">
        <colgroup>
            <col style="width: 4%;">
            <col style="width: 28%;">
            <col style="width: 11%;">
            <col style="width: 16%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
            <col style="width: 15%;">
        </colgroup>
        <thead><tr><th>#</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الخصم</th><th>الضريبة</th><th>الإجمالي</th></tr></thead>
        <tbody>
            @foreach($quotation->lines as $i => $line)
            <tr>
                <td dir="ltr">{{ $i + 1 }}</td>
                <td>{{ $line->item->name ?? '' }}</td>
                <td dir="ltr">{{ number_format($line->quantity, 2) }}</td>
                <td dir="ltr">{{ number_format($line->unit_price, 2) }}</td>
                <td dir="ltr">{{ number_format($line->discount_amount ?? 0, 2) }}</td>
                <td dir="ltr">{{ number_format($line->tax_amount ?? 0, 2) }}</td>
                <td dir="ltr">{{ number_format($line->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="6" style="text-align: left;">المجموع الفرعي</td><td dir="ltr">{{ number_format($printSubtotal, 2) }}</td></tr>
            @if($printDiscount > 0)
            <tr><td colspan="6" style="text-align: left;">الخصم</td><td dir="ltr">{{ number_format($printDiscount, 2) }}</td></tr>
            @endif
            @if($printTax > 0)
            <tr><td colspan="6" style="text-align: left;">الضريبة</td><td dir="ltr">{{ number_format($printTax, 2) }}</td></tr>
            @endif
            <tr class="total-row"><td colspan="6" style="text-align: left;">الإجمالي</td><td dir="ltr">{{ number_format($printTotal, 2) }}</td></tr>
        </tfoot>
    </table>
    @if($quotation->terms)
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        <strong style="color: #2563eb;">الشروط والأحكام</strong>
        <p style="margin-top: 5px; white-space: pre-wrap;">{{ $quotation->terms }}</p>
    </div>
    @endif
@endsection
