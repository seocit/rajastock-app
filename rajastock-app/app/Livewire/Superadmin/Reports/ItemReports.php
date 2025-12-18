<?php

namespace App\Livewire\Superadmin\Reports;

use App\Models\Item;
use App\Models\Merk;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ItemReports extends Component
{
    use WithPagination;

    public $search = '';
    public $merk = '';
    public $stockStatus = ''; // low / normal / all

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        return Item::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('item_name', 'like', "%{$this->search}%")
                        ->orWhere('item_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->merk, fn($q) => $q->where('merk_id', $this->merk))
            ->when($this->stockStatus === 'low', fn($q) => $q->whereColumn('stock', '<', 'minimum_stock'))
            ->when($this->stockStatus === 'normal', fn($q) => $q->whereColumn('stock', '>=', 'minimum_stock'));
    }

    public function getItemsProperty()
    {
        return $this->getFilteredQuery()
            ->orderBy('item_name')
            ->paginate(15);
    }



    /** --------------------------
     *  EXPORT PDF via MPDF
     * ------------------------- */
    public function exportPdf()
    {
        $data = [
            'items' => $this->getFilteredQuery()
                ->orderBy('item_name')
                ->get(), // <--- NO PAGINATION
        ];

        $html = view('reports.stock.pdf', $data)->render();

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => $mpdf->Output(),
            'laporan-stok.pdf'
        );
    }


    /** --------------------------
     *  EXPORT EXCEL
     * ------------------------- */
    public function exportExcel()
    {

        $items = $this->getFilteredQuery()
            ->orderBy('item_name')
            ->get();
        return Excel::download(
            new \App\Exports\StockExport($items),
            'laporan-stok.xlsx'
        );
    }

    /** --------------------------
     *  EXPORT CSV
     * ------------------------- */
    public function exportCsv()
    {

        $items = $this->getFilteredQuery()
            ->orderBy('item_name')
            ->get();

        return Excel::download(
            new \App\Exports\StockExport($items),
            'laporan-stok.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view(
            'livewire.superadmin.reports.item-reports',
            [
                'merks' => Merk::orderBy('merk_name')->get(),
            ]
        );
    }
}
