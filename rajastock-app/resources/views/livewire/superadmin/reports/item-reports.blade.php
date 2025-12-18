<div class="p-4">

    <flux:heading size="xl" level="1">Laporan Stok Barang</flux:heading>
    <flux:text class="mt-2">Preview laporan + export PDF / Excel / CSV</flux:text>
    <flux:separator class="mb-4" />

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">

        <flux:input wire:model.live.debounce.500ms="search" placeholder="Cari item..." />

        <flux:select wire:model.live.debounce.200ms="merk">
            <option value="">Semua Merk</option>
            @foreach ($merks as $m)
                <option value="{{ $m->id }}">{{ $m->merk_name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live.debounce.200ms="stockStatus">
            <option value="">Semua Stok</option>
            <option value="low">Stok Rendah</option>
            <option value="normal">Stok Normal</option>
        </flux:select>

        <div class="flex gap-2">
            <flux:button wire:click="exportPdf" variant="primary" color="red">PDF</flux:button>
            <flux:button wire:click="exportExcel" variant="primary" color="green">Excel</flux:button>
            <flux:button wire:click="exportCsv" variant="primary">CSV</flux:button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Code</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Merk</th>
                    <th class="px-3 py-2">Stok</th>
                    <th class="px-3 py-2">Min Stok</th>
                    <th class="px-3 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->items as $item)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $item->item_code }}</td>
                        <td class="px-3 py-2">{{ $item->item_name }}</td>
                        <td class="px-3 py-2">{{ $item->merk->merk_name }}</td>
                        <td class="px-3 py-2">{{ $item->stock }}</td>
                        <td class="px-3 py-2">{{ $item->minimum_stock }}</td>
                        <td class="px-3 py-2">
                            @if ($item->stock < $item->minimum_stock)
                                <span class="text-red-600 font-semibold">Low Stock</span>
                            @else
                                <span class="text-green-600">Normal</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $this->items->links() }}
        </div>
    </div>

</div>
