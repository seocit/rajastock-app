<div class="p-4">

    <flux:heading size="xl" level="1">Laporan Stok Keluar</flux:heading>
    <flux:text class="mt-2">Preview laporan + export PDF / Excel / CSV</flux:text>
    <flux:separator class="mb-4" />

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">

        {{-- Search --}}
        <flux:input 
            wire:model.live.debounce.500ms="search"
            placeholder="Cari kode penjualan..." 
        />

        {{-- Customer --}}
        <flux:select wire:model.live="customer">
            <option value="">Semua Customer</option>
            @foreach ($customers as $c)
                <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
            @endforeach
        </flux:select>

        {{-- Dari Tanggal --}}
        <flux:input type="date" wire:model.live="dateStart" />

        {{-- Sampai Tanggal --}}
        <flux:input type="date" wire:model.live="dateEnd" />
    </div>


    <!-- EXPORT & MODE AREA -->
    <div class="flex flex-wrap items-end gap-3 mb-4">

        <!-- MODE CETAK -->
        <div class="flex flex-col">
            <label class="font-semibold text-sm mb-1">Mode Cetak</label>
            <select wire:model="printMode" class="border p-2 rounded w-48">
                <option value="summary">Ringkas (Tanpa Detail)</option>
                <option value="full">Lengkap (Dengan Detail)</option>
            </select>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-2">
            <flux:button wire:click="exportPdf" variant="primary" color="red">
                PDF
            </flux:button>

            <flux:button wire:click="exportExcel" variant="primary" color="green">
                Excel
            </flux:button>

            <flux:button wire:click="exportCsv" variant="primary">
                CSV
            </flux:button>
        </div>

    </div>


    <!-- TABLE -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Customer</th>
                    <th class="px-3 py-2">Tanggal</th>
                    <th class="px-3 py-2 text-right">Total Item</th>
                    <th class="px-3 py-2 text-right">Total Qty</th>
                    <th class="px-3 py-2 text-right">Total Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($this->sales as $s)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $s->sale_code }}</td>
                        <td class="px-3 py-2">{{ $s->customer->customer_name ?? '-' }}</td>
                        <td class="px-3 py-2">
                            {{ \Carbon\Carbon::parse($s->sale_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-2 text-right">{{ $s->saleDetails->count() }}</td>
                        <td class="px-3 py-2 text-right">{{ $s->saleDetails->sum('quantity') }}</td>
                        <td class="px-3 py-2 text-right">
                            Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            Tidak ada data penjualan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $this->sales->links() }}
        </div>

    </div>

</div>
