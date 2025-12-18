<?php

namespace App\Livewire\Superadmin\Reports;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaleSummaryExport;
use App\Exports\SaleFullExport;
use App\Models\Customer;

class SaleReport extends Component
{
    use WithPagination;

    public $search = '';
    public $customer = '';
    public $dateStart = '';
    public $dateEnd = '';
    public $printMode = 'summary'; // summary | full

    protected $paginationTheme = 'tailwind';

    public function updated($key)
    {
        if (in_array($key, ['search', 'customer', 'dateStart', 'dateEnd'])) {
            $this->resetPage();
        }
    }

    private function getFilteredQuery()
    {
        return Sale::with(['customer', 'saleDetails.item'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('sale_code', 'like', "%{$this->search}%")
            )
            ->when(
                $this->customer,
                fn($q) =>
                $q->where('customer_id', $this->customer)
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('sale_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('sale_date', '<=', $this->dateEnd)
            );
    }

    public function getSalesProperty()
    {
        return $this->getFilteredQuery()
            ->orderByDesc('sale_date')
            ->paginate(15);
    }

    /* ============== PDF EXPORT ============== */
    public function exportPdf()
    {
        $mode = in_array($this->printMode, ['summary', 'full'])
            ? $this->printMode
            : 'summary';

        $sales = $this->getFilteredQuery()
            ->orderBy('sale_date', 'desc')
            ->get();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        if ($mode === 'full') {
            $count = $sales->count();
            $index = 0;

            foreach ($sales as $sale) {
                $html = view('reports.sale.single-pdf', [
                    'sale' => $sale,
                ])->render();

                $mpdf->WriteHTML($html);

                if (++$index < $count) {
                    $mpdf->AddPage();
                }
            }
        } else {
            $html = view('reports.sale.pdf-summary', [
                'sales' => $sales,
            ])->render();

            $mpdf->WriteHTML($html);
        }

        return response()->streamDownload(
            fn() => $mpdf->Output(),
            'laporan-penjualan.pdf'
        );
    }

    /* ============== EXCEL EXPORT ============== */
    public function exportExcel()
    {
        $sales = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new SaleFullExport($sales)
                : new SaleSummaryExport($sales),
            $this->printMode === 'full'
                ? 'laporan-penjualan-full.xlsx'
                : 'laporan-penjualan-summary.xlsx'
        );
    }

    /* ============== CSV EXPORT ============== */
    public function exportCsv()
    {
        $sales = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new SaleFullExport($sales)
                : new SaleSummaryExport($sales),
            $this->printMode === 'full'
                ? 'laporan-penjualan-full.csv'
                : 'laporan-penjualan-summary.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view('livewire.superadmin.reports.sale-report',[
            'customers' => Customer::orderBy('customer_name')->get(),
        ]);
    }
}
