<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $chartData = [];
    public $year;

    public function mount()
    {
        $this->year = now()->year;
        $this->loadChartData();

        // trigger chart render ketika page pertama kali diload
        $this->dispatch('renderChartJS', $this->chartData);
    }

    /* ============================================================
     | SUMMARY STATS
     ============================================================ */
    protected function querySummaryStats()
    {
        // Penjualan bulan ini (qty bersih = qty terjual - qty retur)
        $salesQtyThisMonth = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('sales_return_details', 'sales_return_details.sales_detail_id', '=', 'sale_details.id')
            ->whereMonth('sales.sale_date', now()->month)
            ->selectRaw('SUM(sale_details.quantity - COALESCE(sales_return_details.quantity_returned, 0)) as qty')
            ->value('qty');

        // Pembelian bulan ini (qty beli)
        $purchasesQtyThisMonth = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchases_id', '=', 'purchases.id')
            ->whereMonth('purchases.purchase_date', now()->month)
            ->sum('purchase_details.quantity');

        return [
            'totalItems'         => Item::count(),
            'totalSuppliers'     => Supplier::count(),
            'totalCustomers'     => Customer::count(),
            'salesThisMonth'     => $salesQtyThisMonth ?? 0,
            'purchasesThisMonth' => $purchasesQtyThisMonth ?? 0,
        ];
    }


    public function getSummaryStats()
    {
        return Cache::remember('dashboard.stats', 60, fn() => $this->querySummaryStats());
    }


    /* ============================================================
     | CHART DATA (Penjualan = Qty Bersih)
     ============================================================ */

    // qty terjual – qty retur
    protected function queryMonthlySoldNet($year)
    {
        return DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('sales_return_details', 'sales_return_details.sales_detail_id', '=', 'sale_details.id')
            ->selectRaw("
            MONTH(sales.sale_date) as m,
            SUM(
                sale_details.quantity - COALESCE(sales_return_details.quantity_returned, 0)
            ) as total
        ")
            ->whereYear('sales.sale_date', $year)
            ->groupBy('m')
            ->pluck('total', 'm');
    }


    protected function queryMonthlyPurchaseQty($year)
    {
        return DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchases_id', '=', 'purchases.id')
            ->selectRaw("
            MONTH(purchases.purchase_date) as m,
            SUM(purchase_details.quantity) as total
        ")
            ->whereYear('purchases.purchase_date', $year)
            ->groupBy('m')
            ->pluck('total', 'm');
    }



    public function getChartData($year)
    {
        return Cache::remember("dashboard.chartData.$year", 60, function () use ($year) {

            $months = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'));

            $salesNetQty = $this->queryMonthlySoldNet($year);
            $purchasesQty = $this->queryMonthlyPurchaseQty($year);


            return $months->map(fn($label, $i) => [
                'month'     => $label,
                'sales'     => $salesNetQty[$i + 1] ?? 0,      // qty bersih
                'purchases' => $purchasesQty[$i + 1] ?? 0,
                // total amount
            ]);
        });
    }

    public function loadChartData()
    {
        $this->chartData = $this->getChartData($this->year);
    }


    /* ============================================================
     | LOW STOCK
     ============================================================ */
    public function getLowStockItems()
    {
        return Cache::remember('dashboard.lowStock', 60, function () {
            return Item::whereColumn('stock', '<', 'minimum_stock')
                ->orderBy('stock')
                ->limit(5)
                ->get();
        });
    }


    /* ============================================================
     | TOP SELLING (Qty)
     ============================================================ */
    public function getTopSellingItems()
    {
        return Cache::remember('dashboard.topSelling', 60, function () {
            return DB::table('sale_details')
                ->join('items', 'sale_details.item_id', '=', 'items.id')
                ->select('items.item_name', DB::raw('SUM(sale_details.quantity) as total_sold'))
                ->groupBy('items.item_name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        });
    }


    /* ============================================================
     | REFRESH BUTTON
     ============================================================ */
    public function refreshDashboard()
    {
        Cache::flush();

        $this->loadChartData();

        $this->dispatch('refreshChart', $this->chartData);
    }


    /* ============================================================
     | RENDER VIEW
     ============================================================ */
    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'stats'           => $this->getSummaryStats(),
            'lowStockItems'   => $this->getLowStockItems(),
            'topSellingItems' => $this->getTopSellingItems(),
            'chartData'       => $this->chartData,
        ]);
    }
}
