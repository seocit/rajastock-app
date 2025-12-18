<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReturnSummaryExport implements FromCollection, WithHeadings
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
            'Total Item',
            'Total Qty Retur',
            
        ];
    }

    public function collection()
    {
        return $this->returns->map(function ($r) {
            return [
                $r->return_code,
                $r->sale->sale_code ?? '-',
                $r->sale->customer->customer_name ?? '-',
                // samakan gaya tanggal dengan full export
                \Carbon\Carbon::parse($r->return_date)->format('d/m/Y'),
                $r->details->count(),
                $r->details->sum('quantity_returned'),                
            ];
        });
    }
}
