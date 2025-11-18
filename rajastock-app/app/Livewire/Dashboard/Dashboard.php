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
    public $year; // mudah kalau mau filter tahun

    public function mount()
    {
        $this->year = now()->year;
        $this->loadChartData();
    }

    /* ============================================================
     | 🔹 LOADING WRAPPER (agar bersih)
     ============================================================ */
    public function loadChartData()
    {
        $this->chartData = $this->getChartData($this->year);
    }

    /* ============================================================
     | 🔹 SUMMARY STATS
     ============================================================ */
    protected function querySummaryStats()
    {
        return [
            'totalItems'         => Item::count(),
            'totalSuppliers'     => Supplier::count(),
            'totalCustomers'     => Customer::count(),
            'salesThisMonth'     => Sale::whereMonth('sale_date', now()->month)->sum('total_amount'),
            'purchasesThisMonth' => Purchase::whereMonth('purchase_date', now()->month)->sum('total_amount'),
        ];
    }

    public function getSummaryStats()
    {
        return Cache::remember('dashboard.stats', 60, fn() => $this->querySummaryStats());
    }


    /* ============================================================
     | 🔹 CHART DATA
     ============================================================ */
    protected function queryMonthlyData($model, $dateField, $year)
    {
        return $model::selectRaw("MONTH($dateField) as m, SUM(total_amount) as total")
            ->whereYear($dateField, $year)
            ->groupBy('m')
            ->pluck('total', 'm');
    }

    public function getChartData($year)
    {
        return Cache::remember("dashboard.chartData.$year", 60, function () use ($year) {

            $months = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'));

            $sales = $this->queryMonthlyData(Sale::class, 'sale_date', $year);
            $purchases = $this->queryMonthlyData(Purchase::class, 'purchase_date', $year);

            return $months->map(fn($label, $i) => [
                'month'     => $label,
                'sales'     => $sales[$i + 1] ?? 0,
                'purchases' => $purchases[$i + 1] ?? 0,
            ]);
        });
    }


    /* ============================================================
     | 🔹 LOW STOCK
     ============================================================ */
    protected function queryLowStock()
    {
        return Item::whereColumn('stock', '<', 'minimum_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();
    }

    public function getLowStockItems()
    {
        return Cache::remember('dashboard.lowStock', 60, fn() => $this->queryLowStock());
    }


    /* ============================================================
     | 🔹 TOP SELLING ITEMS
     ============================================================ */
    protected function queryTopSelling()
    {
        return DB::table('sale_details')
            ->join('items', 'sale_details.item_id', '=', 'items.id')
            ->select('items.item_name', DB::raw('SUM(sale_details.quantity) as total_sold'))
            ->groupBy('items.item_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    public function getTopSellingItems()
    {
        return Cache::remember('dashboard.topSelling', 60, fn() => $this->queryTopSelling());
    }


    /* ============================================================
     | 🔹 REFRESH BUTTON
     ============================================================ */
    public function refreshDashboard()
    {
        Cache::flush();

        // reload everything
        $this->loadChartData();

        // trigger JS for chart update
        $this->dispatch('refreshChart', $this->chartData);
    }


    /* ============================================================
     | 🔹 RENDER VIEW
     ============================================================ */
    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'stats'          => $this->getSummaryStats(),
            'lowStockItems'  => $this->getLowStockItems(),
            'topSellingItems' => $this->getTopSellingItems(),
            'chartData'      => $this->chartData,
        ]);
    }
}
