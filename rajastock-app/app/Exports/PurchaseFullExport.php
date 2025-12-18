<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseFullExport implements FromCollection, WithHeadings
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function headings(): array
    {
        return [
            'Kode Pembelian',
            'Supplier',
            'Tanggal Pembelian',
            'Item',
            'Kode Item',
            'Qty',
            'Harga',
            'Subtotal',
        ];
    }

    public function collection()
    {
        return $this->purchases->flatMap(function ($purchase) {
            return $purchase->details->map(function ($d) use ($purchase) {
                return [
                    $purchase->purchase_code,
                    $purchase->supplier->supplier_name ?? '-',
                    $purchase->purchase_date,
                    $d->item_name,
                    $d->item_code,
                    $d->quantity,
                    $d->unit_price,
                    $d->subtotal,
                ];
            });
        });
    }
}
