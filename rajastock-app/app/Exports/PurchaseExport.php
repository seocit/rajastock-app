<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromCollection, WithHeadings
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function collection()
    {
        return $this->purchases->map(function ($p) {
            return [
                'Purchase Code' => $p->purchase_code,
                'Supplier' => $p->supplier->supplier_name ?? '-',
                'Date' => $p->purchase_date,
                'Total Items' => $p->details->count(),
                'Total Qty' => $p->details->sum('quantity'),
                'Total Amount' => $p->total_amount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Purchase Code',
            'Supplier',
            'Date',
            'Total Items',
            'Total Qty',
            'Total Amount',
        ];
    }
}
