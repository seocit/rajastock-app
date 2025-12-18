<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseSummaryExport implements FromCollection, WithHeadings
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Supplier',
            'Tanggal',
            'Total Item',
            'Total Qty',
            'Total Amount',
        ];
    }

    public function collection()
    {
        return $this->purchases->map(function ($p) {
            return [
                $p->purchase_code,
                $p->supplier->supplier_name ?? '-',
                $p->purchase_date,
                $p->details->count(),
                $p->details->sum('quantity'),
                $p->total_amount,
            ];
        });
    }
}
