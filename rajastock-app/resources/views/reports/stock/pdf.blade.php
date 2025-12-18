<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .report-title {
            text-align: center;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-info {
            margin: 20px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table thead tr {
            background: #f2f2f2;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>


    <div class="report-title">Laporan Stok Barang</div>

    {{-- META INFORMATION --}}
    <div class="meta-info">
        <strong>Total Barang:</strong> {{ $items->count() }} Barang <br>
        <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }} <br>
        <strong>Dicetak Oleh:</strong> {{ auth()->user()->name }}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th width="8%">Code</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Merek</th>
                <th width="10%">Stok</th>
                <th width="10%">Minimum</th>
                <th width="12%">Harga Beli</th>
                <th width="12%">Harga Jual</th>
                <th width="13%">Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i)
                <tr>
                    <td>{{ $i->item_code }}</td>
                    <td>{{ $i->item_name }}</td>
                    <td>{{ $i->merk->merk_name }}</td>
                    <td>{{ $i->stock }}</td>
                    <td>{{ $i->minimum_stock }}</td>
                    <td>Rp {{ number_format($i->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($i->selling_price, 0, ',', '.') }}</td>
                    <td>{{ $i->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh sistem pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
