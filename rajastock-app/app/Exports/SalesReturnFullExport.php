<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReturnFullExport implements FromCollection, WithHeadings
{
    protected $returns;

    public function __construct($returns)
    {
        $this->returns = $returns;
    }

    public function headings(): array
    {
        return [
            'Kode Retur',
            'Kode Penjualan',
            'Customer',
            'Tanggal Retur',
            'Item',
            'Kode Item',
            'Qty Retur',
            'Kondisi',
            'Subtotal Retur',
        ];
    }

    public function collection()
    {
        return $this->returns->flatMap(function ($r) {
            return $r->details->map(function ($d) use ($r) {
                return [
                    $r->return_code,
                    $r->sale->sale_code ?? '-',
                    $r->sale->customer->customer_name ?? '-',
                    // samakan gaya dengan purchase return
                    \Carbon\Carbon::parse($r->return_date)->format('d/m/Y'),
                    $d->item_name,
                    $d->item_code,
                    $d->quantity_returned,
                    ucfirst($d->condition),
                    $d->sub_total,
                ];
            });
        });
    }
}
