<?php

namespace App\Livewire\Superadmin\Reports;

use App\Models\SalesReturn;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReturnFullExport;
use App\Exports\SalesReturnSummaryExport;

class SalesReturnReport extends Component
{
    use WithPagination;

    public $search = '';
    public $customer = '';
    public $dateStart = '';
    public $dateEnd = '';
    public $printMode = 'summary';

    protected $paginationTheme = 'tailwind';

    public function updated($key)
    {
        if (in_array($key, ['search', 'customer', 'dateStart', 'dateEnd'])) {
            $this->resetPage();
        }
    }

    private function getFilteredQuery()
    {
        return SalesReturn::with(['sale.customer', 'details'])
            ->when(
                $this->search,
                fn ($q) =>
                $q->where('return_code', 'like', "%{$this->search}%")
            )
            ->when(
                $this->customer,
                fn ($q) =>
                $q->whereHas('sale', fn ($s) =>
                    $s->where('customer_id', $this->customer)
                )
            )
            ->when(
                $this->dateStart,
                fn ($q) =>
                $q->whereDate('return_date', '>=', $this->dateStart)
            )
            ->when(
                $this->dateEnd,
                fn ($q) =>
                $q->whereDate('return_date', '<=', $this->dateEnd)
            );
    }

    public function getReturnsProperty()
    {
        return $this->getFilteredQuery()
            ->orderByDesc('return_date')
            ->paginate(15);
    }

    /* ================= PDF ================= */

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
                $html = view('reports.sales-return.pdf-single', [
                    'ret' => $ret
                ])->render();

                $mpdf->WriteHTML($html);

                if ($i < count($returns) - 1) {
                    $mpdf->AddPage();
                }
                $i++;
            }
        } else {
            $html = view('reports.sales-return.pdf-summary', [
                'returns' => $returns
            ])->render();

            $mpdf->WriteHTML($html);
        }

        return response()->streamDownload(
            fn () => $mpdf->Output(),
            'laporan-sales-return.pdf'
        );
    }

    /* ================= EXCEL ================= */

    public function exportExcel()
    {
        $returns = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new SalesReturnFullExport($returns)
                : new SalesReturnSummaryExport($returns),
            $this->printMode === 'full'
                ? 'sales-return-full.xlsx'
                : 'sales-return-summary.xlsx'
        );
    }

    /* ================= CSV ================= */

    public function exportCsv()
    {
        $returns = $this->getFilteredQuery()->get();

        return Excel::download(
            $this->printMode === 'full'
                ? new SalesReturnFullExport($returns)
                : new SalesReturnSummaryExport($returns),
            $this->printMode === 'full'
                ? 'sales-return-full.csv'
                : 'sales-return-summary.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view('livewire.superadmin.reports.sales-return-report', [
            'customers' => Customer::orderBy('customer_name')->get(),
        ]);
    }
}
