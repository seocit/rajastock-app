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

    public function mount()
    {
        $this->chartData = $this->getChartData();
    }

    // 🔹 Statistik utama
    public function getSummaryStats()
    {
        return Cache::remember('dashboard.stats', 60, function () {
            return [
                'totalItems'        => Item::count(),
                'totalSuppliers'    => Supplier::count(),
                'totalCustomers'    => Customer::count(),
                'salesThisMonth'    => Sale::whereMonth('sale_date', now()->month)->sum('total_amount'),
                'purchasesThisMonth' => Purchase::whereMonth('purchase_date', now()->month)->sum('total_amount'),
            ];
        });
    }

    // 🔹 Grafik penjualan & pembelian
    public function getChartData()
    {
        return Cache::remember('dashboard.chartData', 60, function () {
            $months = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->format('M'));

            $sales = Sale::selectRaw('MONTH(sale_date) as month, SUM(total_amount) as total')
                ->whereYear('sale_date', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');

            $purchases = Purchase::selectRaw('MONTH(purchase_date) as month, SUM(total_amount) as total')
                ->whereYear('purchase_date', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');

            return $months->map(fn($m, $i) => [
                'month'      => $m,
                'sales'      => $sales[$i + 1] ?? 0,
                'purchases'  => $purchases[$i + 1] ?? 0,
            ]);
        });
    }

    // 🔹 Barang stok rendah
    public function getLowStockItems()
    {
        return Cache::remember('dashboard.lowStock', 60, function () {
            return Item::whereColumn('stock', '<', 'minimum_stock')
                ->orderBy('stock', 'asc')
                ->limit(5)
                ->get();
        });
    }

    // 🔹 Barang paling banyak dijual
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

    public function refreshDashboard()
    {
        Cache::flush();
        $this->chartData = $this->getChartData();

        //  untuk update Chart tanpa reload 
        $this->dispatch('refreshChart', $this->chartData);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'stats' => $this->getSummaryStats(),
            'lowStockItems' => $this->getLowStockItems(),
            'topSellingItems' => $this->getTopSellingItems(),
            'chartData' => $this->chartData,
        ]);
    }
}
