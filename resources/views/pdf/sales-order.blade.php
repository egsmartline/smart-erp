@extends('pdf.layout')
@section('document-info')
    <h2 style="color: #2563eb; margin: 0;">أمر بيع</h2>
    <p>رقم الأمر: <strong>{{ $order->order_number }}</strong></p>
    <p>التاريخ: {{ $order->date }}</p>
@endsection

@section('content')
    <table>
        <tr><td><strong>العميل:</strong> {{ $order->customer->name ?? '' }}</td></tr>
    </table>
    <table>
        <thead><tr><th>#</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الخصم</th><th>الإجمالي</th></tr></thead>
        <tbody>
            @foreach($order->lines as $i => $line)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $line->item->name ?? '' }}</td>
                <td>{{ $line->quantity }}</td>
                <td>{{ number_format($line->unit_price, 2) }}</td>
                <td>{{ $line->discount ?? 0 }}</td>
                <td>{{ number_format($line->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row"><td colspan="5" style="text-align: left;">الإجمالي</td><td>{{ number_format($order->total, 2) }}</td></tr>
        </tfoot>
    </table>
@endsection
