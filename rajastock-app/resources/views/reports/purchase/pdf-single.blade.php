<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Stok Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align:center; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #333; padding:6px; font-size:11px; }
        thead { background:#f2f2f2; }
        .meta { margin-bottom:10px; }
    </style>
</head>

<body>

    <h2>Detail Pembelian: {{ $purchase->purchase_code }}</h2>

    <div class="meta">
        <strong>Supplier:</strong> {{ $purchase->supplier->supplier_name ?? '-' }} <br>
        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }} <br>
        <strong>Total Amount:</strong> Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Kode</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($purchase->details as $d)
                <tr>
                    <td>{{ $d->item_name }}</td>
                    <td>{{ $d->item_code }}</td>
                    <td>{{ $d->quantity }}</td>
                    <td>{{ number_format($d->unit_price, 0, ',', '.') }}</td>
                    <td>{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
