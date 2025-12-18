<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseReturnFullExport implements FromCollection, WithHeadings
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
            'Kode Pembelian',
            'Supplier',
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
                    $r->return_number,
                    $r->purchase->purchase_code ?? '-',
                    $r->purchase->supplier->supplier_name ?? '-',
                    $r->return_date->format('d/m/Y'),
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
