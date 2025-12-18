<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleSummaryExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Customer',
            'Tanggal',
            'Jumlah Item',
            'Total Qty',
            
        ];
    }

    public function collection()
    {
        return $this->sales->map(function ($s) {
            return [
                $s->sale_code,
                $s->customer->customer_name ?? '-',
                $s->sale_date,
                $s->saleDetails->count(),
                $s->saleDetails->sum('quantity'),
                
            ];
        });
    }
}
