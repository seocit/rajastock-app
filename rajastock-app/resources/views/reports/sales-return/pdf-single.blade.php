<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #eee; }
        .text-right { text-align: right; }
        h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<h3>SALES RETURN</h3>

{{-- HEADER --}}
<table>
    <tr>
        <td width="25%"><strong>Kode Retur</strong></td>
        <td width="25%">{{ $ret->return_code }}</td>
        <td width="20%"><strong>Tanggal</strong></td>
        <td width="30%">
            {{ \Carbon\Carbon::parse($ret->return_date)->format('d/m/Y') }}
        </td>
    </tr>
    <tr>
        <td><strong>Customer</strong></td>
        <td colspan="3">
            {{ $ret->sale->customer->customer_name ?? '-' }}
        </td>
    </tr>
</table>

{{-- DETAIL ITEM --}}
<table>
    <thead>
        <tr>
            <th>Kode Item</th>
            <th>Nama Item</th>
            <th>Qty Retur</th>
            <th>Kondisi</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ret->details as $d)
            <tr>
                <td>{{ $d->item_code }}</td>
                <td>{{ $d->item_name }}</td>
                <td class="text-right">{{ $d->quantity_returned }}</td>
                <td>{{ ucfirst($d->condition) }}</td>
                <td class="text-right">
                    {{ number_format($d->sub_total, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" class="text-right"><strong>Total Retur</strong></td>
            <td class="text-right">
                <strong>{{ number_format($ret->total_returned_amount, 0, ',', '.') }}</strong>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>
