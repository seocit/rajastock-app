<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleFullExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function headings(): array
    {
        return [
            'Kode Penjualan',
            'Customer',
            'Tanggal',
            'Item Code',
            'Item Name',
            'Qty',
            'Discount',
            'Unit Price',
            'Subtotal',
            'Total Amount',
        ];
    }

    public function collection()
    {
        return $this->sales->flatMap(function ($sale) {
            return $sale->saleDetails->map(function ($d) use ($sale) {
                return [
                    $sale->sale_code,
                    $sale->customer->customer_name ?? '-',
                    $sale->sale_date,
                    $d->item_code,
                    $d->item_name,
                    $d->quantity,
                    $d->discount,
                    $d->unit_price,
                    $d->subtotal,
                    $sale->total_amount,
                ];
            });
        });
    }
}
