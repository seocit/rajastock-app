<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .title { text-align:center; font-size:18px; font-weight:bold; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { border:1px solid #333; padding:6px; font-size:11px; }
        thead { background:#f2f2f2; }
    </style>
</head>

<body>

    <div class="title">Laporan Pembelian (Summary)</div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Supplier</th>
                <th>Tanggal</th>
                <th>Total Item</th>
                <th>Total Qty</th>
                <th>Total Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($purchases as $p)
                <tr>
                    <td>{{ $p->purchase_code }}</td>
                    <td>{{ $p->supplier->supplier_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d/m/Y') }}</td>
                    <td>{{ $p->details->count() }}</td>
                    <td>{{ $p->details->sum('quantity') }}</td>
                    <td>Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
