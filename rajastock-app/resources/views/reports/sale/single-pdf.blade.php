<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; font-size: 11px; }
        thead { background: #f2f2f2; }
        .meta { margin-bottom: 10px; line-height: 1.4; }
        .numeric { text-align: right; }
    </style>
</head>

<body>

    <h2>Detail Penjualan: {{ $sale->sale_code }}</h2>

    <div class="meta">
        <strong>Customer:</strong> {{ $sale->customer->customer_name ?? '-' }} <br>
        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }} <br>
        @if($sale->description)
            <strong>Keterangan:</strong> {{ $sale->description }} <br>
        @endif
        <strong>Total Amount:</strong> Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Discount</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($sale->saleDetails as $d)
                <tr>
                    <td>{{ $d->item_code }}</td>
                    <td>{{ $d->item_name }}</td>
                    <td class="numeric">{{ $d->quantity }}</td>
                    <td class="numeric">{{ $d->discount }}</td>
                    <td class="numeric">{{ number_format($d->unit_price, 0, ',', '.') }}</td>
                    <td class="numeric">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
