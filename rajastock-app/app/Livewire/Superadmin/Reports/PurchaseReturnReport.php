<?php

namespace App\Livewire\Superadmin\Reports;

use App\Exports\PurchaseReturnFullExport;
use App\Exports\PurchaseReturnSummaryExport;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseReturnReport extends Component
{
    use WithPagination;

    public $search = '';
    public $supplier = '';
    public $dateStart = '';
    public $dateEnd = '';
    public $printMode = 'summary';

    protected $paginationTheme = 'tailwind';


    public function updated($key)
    {
        if (in_array($key, ['search', 'supplier', 'dateStart', 'dateEnd'])) {
            $this->resetPage();
        }
    }


    private function getFilteredQuery()
    {
        return PurchaseReturn::with([
            'purchase.supplier',
            'details'
        ])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('return_number', 'like', "%{$this->search}%")
            )
            ->when(
                $this->supplier,
                fn($q) =>
                $q->whereHas(
                    'purchase',
                    fn($p) =>
                    $p->where('supplier_id', $this->supplier)
                )
            )
            ->when(
                $this->dateStart,
                fn($q) =>
                $q->whereDate('return_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn($q) =>
                $q->whereDate('return_date', '<=', $this->dateEnd)
            );
    }

    public function getReturnsProperty()
    {
        return $this->getFilteredQuery()
            ->orderByDesc('return_date')
            ->paginate(15);
    }


    public function exportPdf()
    {
        $mode = in_array($this->printMode, ['summary', 'full'])
            ? $this->printMode
            : 'summary';

        $returns = $this->getFilteredQuery()
            ->orderBy('return_date', 'desc')
            ->get();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        if ($mode === 'full') {
            $i = 0;
            foreach ($returns as $ret) {
                $html = view('reports.purchase-return.pdf-single', [
                    'ret' => $ret
                ])->render();

                $mpdf->WriteHTML($html);

                if ($i < count($returns) - 1) {
                    $mpdf->AddPage();
                }
                $i++;
            }
        } else {
            $html = view('reports.purchase-return.pdf-summary', [
                'returns' => $returns
            ])->render();

            $mpdf->WriteHTML($html);
        }

        return response()->streamDownload(
            fn() => $mpdf->Output(),
            'laporan-purchase-return.pdf'
        );
    }


    public function exportExcel()
    {
        $returns = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new PurchaseReturnFullExport($returns)
                : new PurchaseReturnSummaryExport($returns),
            $this->printMode === 'full'
                ? 'laporan-purchase-return-full.xlsx'
                : 'laporan-purchase-return-summary.xlsx'
        );
    }


    public function exportCsv()
    {
        $returns = $this->getFilteredQuery()
            ->with(['purchase.supplier', 'details'])
            ->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new PurchaseReturnFullExport($returns)
                : new PurchaseReturnSummaryExport($returns),
            $this->printMode === 'full'
                ? 'laporan-purchase-return-full.csv'
                : 'laporan-purchase-return-summary.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view('livewire.superadmin.reports.purchase-return-report', [
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
        ]);
    }
}
