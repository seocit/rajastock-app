<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .report-title { text-align: center; margin-top: 10px; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; font-size: 11px; }
        thead { background: #f2f2f2; }
        .numeric { text-align: right; }
    </style>
</head>

<body>

    <div class="report-title">Laporan Penjualan (Ringkas)</div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Jumlah Item</th>
                <th>Total Qty</th>
                <th>Total Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($sales as $s)
                <tr>
                    <td>{{ $s->sale_code }}</td>
                    <td>{{ $s->customer->customer_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->sale_date)->format('d/m/Y') }}</td>
                    <td class="numeric">{{ $s->saleDetails->count() }}</td>
                    <td class="numeric">{{ $s->saleDetails->sum('quantity') }}</td>
                    <td class="numeric">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
