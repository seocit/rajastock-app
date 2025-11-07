<flux:modal name="sale-details" wire:model="isOpen" dismissible
    class="!max-w-none !w-[95vw] md:!w-[80vw] lg:!w-[70vw] !h-[80vh] overflow-y-auto">
    @if ($sale)
        <div class="space-y-3">
            <flux:heading size="xl" class="mb-6 text-center md:text-left">Sale Details</flux:heading>

            {{-- Header info --}}
            <div class="flex flex-wrap text-sm gap-x-8 gap-y-3 sm:gap-x-6 md:gap-x-10">
                <div class="flex">
                    <div><strong>Code:</strong></div>
                    <div class="pl-2">{{ $sale->sale_code }}</div>
                </div>

                <div class="flex">
                    <div><strong>Customer:</strong></div>
                    <div class="pl-2">{{ $sale->customer->customer_name ?? '-' }}</div>
                </div>

                <div class="flex">
                    <div><strong>Date:</strong></div>
                    <div class="pl-2">{{ $sale->sale_date }}</div>
                </div>

                @if (isset($sale->status))
                    <div class="flex">
                        <div><strong>Status:</strong></div>
                        <div class="pl-2">{{ ucfirst($sale->status) }}</div>
                    </div>
                @endif
            </div>

            <flux:separator class="my-3" />

            {{-- Table section --}}
            <div class="overflow-x-auto rounded-lg border max-h-[55vh]">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="p-2 text-left">Item</th>
                            <th class="p-2 text-right">Qty</th>
                            <th class="p-2 text-right">Unit Price</th>
                            <th class="p-2 text-right">Discount</th>
                            <th class="p-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2">{{ $detail->item->item_name ?? '-' }}</td>
                                <td class="p-2 text-right">{{ $detail->quantity }}</td>
                                <td class="p-2 text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                <td class="p-2 text-right">{{ number_format($detail->discount ?? 0, 0, ',', '.') }}%</td>
                                <td class="p-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $details->links() }}
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-right mt-4">
                <strong>Total: Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong>
            </div>
        </div>
    @endif
</flux:modal>
