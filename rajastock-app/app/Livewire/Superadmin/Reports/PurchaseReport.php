<?php

namespace App\Livewire\Superadmin\Reports;

use App\Exports\PurchaseFullExport;
use App\Exports\PurchaseSummaryExport;
use App\Models\Purchase;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseReport extends Component
{
    use WithPagination;

    public $search = '';
    public $supplier = '';
    public $dateStart = '';
    public $dateEnd = '';
    public $printMode = 'summary';

    protected $paginationTheme = 'tailwind';

    /** Reset pagination saat filter berubah */
    public function updated($key)
    {
        if (in_array($key, ['search', 'supplier', 'dateStart', 'dateEnd'])) {
            $this->resetPage();
        }
    }

    /** Query filter */
    private function getFilteredQuery()
    {
        return Purchase::with(['supplier', 'details.item'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('purchase_code', 'like', "%{$this->search}%")
            )
            ->when(
                $this->supplier,
                fn($q) =>
                $q->where('supplier_id', $this->supplier)
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('purchase_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('purchase_date', '<=', $this->dateEnd)
            );
    }

    public function getPurchasesProperty()
    {
        return $this->getFilteredQuery()
            ->orderByDesc('purchase_date')
            ->paginate(15);
    }

    /* ======================================================
        EXPORT PDF 
     ====================================================== */

    public function exportPdf()
    {
        $mode = in_array($this->printMode, ['summary', 'full'])
            ? $this->printMode
            : 'summary';

        $purchases = $this->getFilteredQuery()
            ->orderBy('purchase_date', 'desc')
            ->get();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        /* FULL MODE → Detail per transaksi */
        if ($mode === 'full') {

            $i = 0;
            foreach ($purchases as $purchase) {
                $html = view('reports.purchase.pdf-single', [
                    'purchase' => $purchase
                ])->render();

                $mpdf->WriteHTML($html);

                if ($i < count($purchases) - 1) {
                    $mpdf->AddPage();
                }
                $i++;
            }
        }

        /* SUMMARY MODE → tabel ringkas */ else {
            $html = view('reports.purchase.pdf-summary', [
                'purchases' => $purchases
            ])->render();

            $mpdf->WriteHTML($html);
        }

        return response()->streamDownload(
            fn() => $mpdf->Output(),
            'laporan-pembelian.pdf'
        );
    }

    /* ======================================================
        EXPORT EXCEL 
     ====================================================== */

    public function exportExcel()
    {
        $purchases = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new PurchaseFullExport($purchases)
                : new PurchaseSummaryExport($purchases),
            $this->printMode === 'full'
                ? 'laporan-pembelian-full.xlsx'
                : 'laporan-pembelian-summary.xlsx'
        );
    }

    /* ======================================================
        EXPORT CSV 
     ====================================================== */
    public function exportCsv()
    {
        $purchases = $this->getFilteredQuery()
            ->with(['supplier', 'details'])
            ->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new PurchaseFullExport($purchases)
                : new PurchaseSummaryExport($purchases),
            $this->printMode === 'full'
                ? 'laporan-pembelian-full.csv'
                : 'laporan-pembelian-summary.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view('livewire.superadmin.reports.purchase-report', [
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
        ]);
    }
}
