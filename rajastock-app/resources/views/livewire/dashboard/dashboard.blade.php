<div class="p-6 space-y-6">

    {{-- HEADER --}}
    <h1 class="text-2xl font-bold text-gray-700">
        Dashboard <span class="text-gray-400 text-sm">Control Panel</span>
    </h1>

    {{-- STAT BOXES --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">

        <div class="rounded-lg p-3 sm:p-4 text-white shadow bg-blue-500 relative overflow-hidden">
            <div class="text-xl sm:text-3xl font-bold">{{ $stats['totalItems'] }}</div>
            <div class="text-xs sm:text-sm opacity-90">Total Items</div>
            <div class="absolute right-3 top-3 opacity-20 text-3xl sm:text-5xl">📦</div>
            <a href="{{ route('items') }}" class="text-[10px] sm:text-xs underline mt-1 inline-block">More info →</a>
        </div>

        <div class="rounded-lg p-3 sm:p-4 text-white shadow bg-green-500 relative overflow-hidden">
            <div class="text-xl sm:text-3xl font-bold">{{ $stats['totalSuppliers'] }}</div>
            <div class="text-xs sm:text-sm opacity-90">Suppliers</div>
            <div class="absolute right-3 top-3 opacity-20 text-3xl sm:text-5xl">🏭</div>
            <a href="{{ route('supplier') }}" class="text-[10px] sm:text-xs underline mt-1 inline-block">More info →</a>
        </div>

        <div class="rounded-lg p-3 sm:p-4 text-white shadow bg-yellow-500 relative overflow-hidden">
            <div class="text-xl sm:text-3xl font-bold">{{ $stats['totalCustomers'] }}</div>
            <div class="text-xs sm:text-sm opacity-90">Customers</div>
            <div class="absolute right-3 top-3 opacity-20 text-3xl sm:text-5xl">👥</div>
            <a href="{{ route('customer') }}" class="text-[10px] sm:text-xs underline mt-1 inline-block">More info →</a>
        </div>

        <div class="rounded-lg p-3 sm:p-4 text-white shadow bg-red-500 relative overflow-hidden">
            <div class="text-lg sm:text-2xl font-bold">
                {{ $stats['salesThisMonth'] }}
            </div>
            <div class="text-xs sm:text-sm opacity-90">Sold This Month (qty)</div>
            <div class="absolute right-3 top-3 opacity-20 text-3xl sm:text-5xl">📈</div>
            <a href="{{ route('sales') }}" class="text-[10px] sm:text-xs underline mt-1 inline-block">More info →</a>
        </div>

        <div class="rounded-lg p-3 sm:p-4 text-white shadow bg-indigo-500 relative overflow-hidden">
            <div class="text-lg sm:text-2xl font-bold">
                {{ $stats['purchasesThisMonth'] }}
            </div>
            <div class="text-xs sm:text-sm opacity-90">Purchased This Month (qty)</div>
            <div class="absolute right-3 top-3 opacity-20 text-3xl sm:text-5xl">📥</div>
            <a href="{{ route('purchases') }}" class="text-[10px] sm:text-xs underline mt-1 inline-block">More info
                →</a>
        </div>

    </div>



    {{-- SALES GRAPH --}}
    <div class="bg-white rounded-xl shadow p-5 border border-gray-200">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-semibold text-gray-700">Inventory Flow (Sold vs Purchased)</h2>
        </div>

        <div class="h-64">
            <canvas id="salesPurchaseChart"></canvas>
        </div>
    </div>

    {{-- BOTTOM SECTION --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- LOW STOCK --}}
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
            <h3 class="text-lg font-semibold mb-3 text-gray-700">⚠️ Low Stock Items</h3>

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-2 text-left">Item</th>
                        <th class="p-2 text-right">Stock</th>
                        <th class="p-2 text-right">Min</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockItems as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2">{{ $item->item_name }}</td>
                            <td class="p-2 text-right text-red-600 font-bold">{{ $item->stock }}</td>
                            <td class="p-2 text-right">{{ $item->minimum_stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-gray-400">All stock levels normal</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TOP SELLING --}}
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
            <h3 class="text-lg font-semibold mb-3 text-gray-700">🔥 Top Selling Items</h3>

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-2 text-left">Item</th>
                        <th class="p-2 text-right">Sold (qty)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSellingItems as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2">{{ $item->item_name }}</td>
                            <td class="p-2 text-right text-green-600 font-bold">{{ $item->total_sold }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-3 text-center text-gray-400">No sales yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>



{{-- Script Chart.js --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let salesPurchaseChart = null;

        function renderChart(chartData) {
            const canvas = document.getElementById('salesPurchaseChart');
            if (!canvas) return;

            const labels = chartData.map(i => i.month);
            const sales = chartData.map(i => i.sales);
            const purchases = chartData.map(i => i.purchases);

            // destroy chart lama kalau ada
            if (salesPurchaseChart) {
                salesPurchaseChart.destroy();
            }

            salesPurchaseChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Stok Terjual',
                            data: sales,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16,185,129,0.15)',
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Stok Dibeli',
                            data: purchases,
                            borderColor: '#FF9800',
                            backgroundColor: 'rgba(255,152,0,0.15)',
                            fill: true,
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                color: '#333'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#444'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#444'
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // chart pertama kali muncul saat halaman masuk
        document.addEventListener('livewire:load', () => {
            renderChart(@json($chartData));
        });

        // kalau user pindah page pakai wire:navigate → grafik tetap muncul
        document.addEventListener('livewire:navigated', () => {
            renderChart(@json($chartData));
        });

        // kalau komponen refresh (misal tombol refresh) → update grafik
        Livewire.on('renderChartJS', (data) => {
            renderChart(data);
        });
    </script>
@endpush
