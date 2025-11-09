<div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">Create Sales Return</h1>
    <flux:separator class="mb-4"></flux:separator>

    {{-- Search Input --}}
    @if (!$selectedSale)
        <div class="flex justify-between items-center mb-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search by sale code or customer..." class="border rounded p-2 w-1/3">
        </div>

        {{-- Table Sales --}}
        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Kode Penjualan</th>
                    <th class="p-2 text-left">Customer</th>
                    <th class="p-2 text-left">Tanggal</th>
                    <th class="p-2 text-left">Total</th>
                    <th class="p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $s)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-2">{{ $s->sale_code }}</td>
                        <td class="p-2">{{ $s->customer->customer_name ?? '-' }}</td>
                        <td class="p-2">{{ \Carbon\Carbon::parse($s->sale_date)->format('d M Y') }}</td>
                        <td class="p-2">{{ number_format($s->total_amount, 2) }}</td>
                        <td class="p-2 text-center">
                            <button wire:click="selectSale({{ $s->id }})"
                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Pilih
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4 text-gray-500">Tidak ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    @endif

    {{-- DETAIL RETURN --}}
    @if ($selectedSale)
        <hr class="my-4">

        <div>
            <h2 class="font-semibold mb-2">
                Detail Penjualan ({{ $selectedSale->sale_code }})
            </h2>

            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">Item</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Harga</th>
                        <th class="p-2">Subtotal</th>
                        <th class="p-2">Pilih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($selectedSale->saleDetails as $detail)
                        <tr class="border-t">
                            <td class="p-2 text-center">{{ $detail->item->item_name }}</td>
                            <td class="p-2 text-center">{{ $detail->quantity }}</td>
                            <td class="p-2 text-center">{{ number_format($detail->unit_price, 2) }}</td>
                            <td class="p-2 text-center">{{ number_format($detail->subtotal, 2) }}</td>
                            <td class="p-2 text-center">
                                <input type="checkbox" wire:click="toggleItem({{ $detail->id }})"
                                    {{ isset($selectedItems[$detail->id]) ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($selectedItems)
            <div class="mt-6">
                <h2 class="font-semibold mb-2">Barang yang Dikembalikan</h2>
                <table class="w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">Item</th>
                            <th class="p-2">Qty Return</th>
                            <th class="p-2">Kondisi</th>
                            <th class="p-2">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedItems as $detailId => $item)
                            @php $detail = $selectedSale->saleDetails->firstWhere('id', $detailId); @endphp
                            <tr class="border-t">
                                <td class="p-2 text-center">{{ $detail->item->item_name }}</td>
                                <td class="p-2 text-center">
                                    <input type="number" min="1"
                                        wire:model="selectedItems.{{ $detailId }}.quantity_returned"
                                        wire:change="recalculateSubTotal({{ $detailId }})"
                                        class="w-20 border rounded p-1">
                                </td>
                                <td class="p-2 text-center">
                                    <select wire:model="selectedItems.{{ $detailId }}.condition"
                                        class="border rounded p-1">
                                        <option value="good">Good</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="other">Other</option>
                                    </select>
                                </td>
                                <td class="p-2 text-center">
                                    <input type="text" wire:model="selectedItems.{{ $detailId }}.reason"
                                        placeholder="Reason..." class="w-full border rounded p-1">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex justify-end mt-6">
            <button wire:click="save" class="bg-blue-600 text-white px-6 py-2 rounded">
                Simpan Return
            </button>
        </div>
    @endif
</div>
