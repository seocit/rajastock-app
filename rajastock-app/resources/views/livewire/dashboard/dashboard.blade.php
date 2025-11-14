<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">📊 Dashboard Stok Barang</h1>
        <button wire:click="refreshDashboard"
            class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5" />
            </svg>
            Refresh
        </button>
    </div>

    {{-- Statistik ringkas --}}
    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-5">
        <div class="bg-white p-4 rounded-xl shadow text-center">
            <h3 class="text-sm text-gray-500">Total Barang</h3>
            <p class="text-xl font-bold text-blue-600">{{ $stats['totalItems'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
            <h3 class="text-sm text-gray-500">Supplier</h3>
            <p class="text-xl font-bold text-green-600">{{ $stats['totalSuppliers'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
            <h3 class="text-sm text-gray-500">Customer</h3>
            <p class="text-xl font-bold text-indigo-600">{{ $stats['totalCustomers'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
            <h3 class="text-sm text-gray-500">Penjualan Bulan Ini</h3>
            <p class="text-lg font-bold text-emerald-600">
                Rp {{ number_format($stats['salesThisMonth'], 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
            <h3 class="text-sm text-gray-500">Pembelian Bulan Ini</h3>
            <p class="text-lg font-bold text-orange-600">
                Rp {{ number_format($stats['purchasesThisMonth'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Grafik Penjualan dan Pembelian --}}
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-3 text-gray-800">
            Grafik Penjualan vs Pembelian ({{ now()->year }})
        </h2>
        <div class="h-72">
            <canvas id="salesPurchaseChart"></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let salesPurchaseChart = null;

            function renderChart(chartData) {
                const ctx = document.getElementById('salesPurchaseChart');
                if (!ctx) return;

                const labels = chartData.map(item => item.month);
                const sales = chartData.map(item => item.sales);
                const purchases = chartData.map(item => item.purchases);

                if (salesPurchaseChart) salesPurchaseChart.destroy();

                salesPurchaseChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'Penjualan',
                                data: sales,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34,197,94,0.2)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Pembelian',
                                data: purchases,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.2)',
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            document.addEventListener('livewire:init', () => {
                const chartData = @json($chartData);
                renderChart(chartData);

                // render ulang jika Livewire update
                Livewire.hook('morph.updated', () => {
                    const updatedData = @json($chartData);
                    renderChart(updatedData);
                });
            });
        </script>
    @endpush



    {{-- Barang stok rendah & terlaris --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- Stok Menipis --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">⚠️ Stok Menipis</h3>
            <table class="min-w-full text-sm border border-gray-100">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left p-2">Barang</th>
                        <th class="text-right p-2">Stok</th>
                        <th class="text-right p-2">Min</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockItems as $item)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">{{ $item->item_name }}</td>
                            <td class="text-right p-2 text-red-500 font-semibold">{{ $item->stock }}</td>
                            <td class="text-right p-2">{{ $item->minimum_stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-gray-500">Semua stok aman</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Barang Terlaris --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">🔥 Barang Terlaris</h3>
            <table class="min-w-full text-sm border border-gray-100">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left p-2">Barang</th>
                        <th class="text-right p-2">Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSellingItems as $item)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2">{{ $item->item_name }}</td>
                            <td class="text-right p-2 text-emerald-600 font-semibold">{{ $item->total_sold }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-3 text-center text-gray-500">Belum ada penjualan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
