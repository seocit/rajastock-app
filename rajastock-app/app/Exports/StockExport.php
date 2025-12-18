<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Nama Item',
            'Merk',
            'Stok',
            'Minimum Stok',
        ];
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                $item->item_code,
                $item->item_name,
                $item->merk->merk_name,
                $item->stock,
                $item->minimum_stock,
            ];
        });
    }
}
