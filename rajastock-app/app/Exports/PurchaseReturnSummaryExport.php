<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseReturnSummaryExport implements FromCollection, WithHeadings
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
            'Total Item',
            'Total Qty Retur',
            'Total Nilai Retur',
        ];
    }

    public function collection()
    {
        return $this->returns->map(function ($r) {
            return [
                $r->return_number,
                $r->purchase->purchase_code ?? '-',
                $r->purchase->supplier->supplier_name ?? '-',
                $r->return_date,
                $r->details->count(),
                $r->details->sum('quantity_returned'),
                $r->total_returned_amount,
            ];
        });
    }
}
