<div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">Edit Sales Return</h1>
    <flux:separator class="mb-4"></flux:separator>

    {{-- DETAIL SALE --}}
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
                    <th class="p-2">Return?</th>
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
                            <input type="checkbox"
                                wire:click="toggleItem({{ $detail->id }})"
                                {{ isset($selectedItems[$detail->id]) ? 'checked' : '' }}>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- SELECTED RETURN ITEMS --}}
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
                        @php
                            $detail = $selectedSale->saleDetails->firstWhere('id', $detailId);
                        @endphp
                        <tr class="border-t">
                            <td class="p-2 text-center">
                                {{ $detail->item->item_name }}
                            </td>

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
                                <input type="text"
                                    wire:model="selectedItems.{{ $detailId }}.reason"
                                    placeholder="Reason..."
                                    class="w-full border rounded p-1">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="flex justify-end mt-6">
        <button wire:click="save" class="bg-blue-600 text-white px-6 py-2 rounded">
            Update Return
        </button>
    </div>
</div>
