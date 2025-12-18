<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #eee;
        }

        .text-right {
            text-align: right;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <h3>LAPORAN PURCHASE RETURN (RINGKAS)</h3>

    <table>
        <thead>
            <tr>
                <th>Kode Retur</th>
                <th>Supplier</th>
                <th>Tanggal</th>
                <th>Total Item</th>
                <th>Total Qty</th>                
            </tr>
        </thead>
        <tbody>
            @foreach ($returns as $r)
                <tr>
                    <td>{{ $r->return_number }}</td>
                    <td>{{ $r->purchase->supplier->supplier_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->return_date)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ $r->details->count() }}</td>
                    <td class="text-right">{{ $r->details->sum('quantity_returned') }}</td>                   
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
