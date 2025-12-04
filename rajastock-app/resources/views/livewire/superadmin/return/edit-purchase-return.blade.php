<div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">Edit Purchase Return</h1>
    <flux:separator class="mb-4"></flux:separator>

    {{-- INFO RETURN --}}
    <div class="bg-gray-50 border p-4 rounded">
        <p><strong>Return Number:</strong> {{ $purchaseReturn->return_number }}</p>
        <p><strong>Purchase Code:</strong> {{ $purchaseReturn->purchase->purchase_code }}</p>
        <p><strong>Supplier:</strong> {{ $purchaseReturn->purchase->supplier->supplier_name }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchaseReturn->return_date)->format('d M Y') }}</p>
    </div>

    <hr class="my-4">

    {{-- DETAIL PEMBELIAN --}}
    <h2 class="font-semibold mb-2">
        Detail Pembelian ({{ $purchaseReturn->purchase->purchase_code }})
    </h2>

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Item</th>
                <th class="p-2">Qty Beli</th>
                <th class="p-2">Harga</th>
                <th class="p-2">Subtotal</th>
                <th class="p-2 text-center">Pilih</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($purchaseReturn->purchase->details as $detail)
                <tr class="border-t">
                    <td class="p-2 text-center">{{ $detail->item->item_name }}</td>
                    <td class="p-2 text-center">{{ $detail->quantity }}</td>
                    <td class="p-2 text-center">{{ number_format($detail->unit_price, 2) }}</td>
                    <td class="p-2 text-center">{{ number_format($detail->subtotal, 2) }}</td>

                    <td class="p-2 text-center">
                        <input type="checkbox"
                            wire:click="toggleItem({{ $detail->id }})"
                            {{ isset($items[$detail->id]) ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FORM RETURN --}}
    @if ($items)
        <div class="mt-6">
            <h2 class="font-semibold mb-2">Barang yang Dikembalikan</h2>

            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">Item</th>
                        <th class="p-2">Qty Return</th>
                        <th class="p-2">Harga/Unit</th>
                        <th class="p-2">Subtotal</th>
                        <th class="p-2">Kondisi</th>
                        <th class="p-2">Alasan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $detailId => $item)
                        @php
                            $detail = $purchaseReturn->purchase->details->firstWhere('id', $detailId);
                        @endphp

                        <tr class="border-t">
                            <td class="p-2 text-center">{{ $detail->item->item_name }}</td>

                            {{-- Qty Return --}}
                            <td class="p-2 text-center">
                                <input type="number" min="1"
                                    wire:model.live="items.{{ $detailId }}.quantity_returned"
                                    class="w-20 border rounded p-1">
                            </td>

                            {{-- Harga Unit --}}
                            <td class="p-2 text-center">
                                <input type="number" step="0.01"
                                    wire:model.live="items.{{ $detailId }}.unit_price"
                                    class="w-24 border rounded p-1">
                            </td>

                            {{-- Subtotal --}}
                            <td class="p-2 text-center">
                                {{ number_format($item['sub_total'], 2) }}
                            </td>

                            {{-- Condition --}}
                            <td class="p-2 text-center">
                                <select wire:model="items.{{ $detailId }}.condition"
                                    class="border rounded p-1">
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="other">Other</option>
                                </select>
                            </td>

                            {{-- Reason --}}
                            <td class="p-2 text-center">
                                <input type="text" wire:model="items.{{ $detailId }}.reason"
                                    class="w-full border rounded p-1">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- BUTTON SAVE --}}
    <div class="flex justify-end mt-6">
        <button wire:click="update" class="bg-blue-600 text-white px-6 py-2 rounded">
            Update Return
        </button>
    </div>
</div>
