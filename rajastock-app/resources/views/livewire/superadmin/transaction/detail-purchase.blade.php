<flux:modal name="purchase-details" wire:model="isOpen" dismissible
    class="!max-w-none !w-[95vw] md:!w-[80vw] lg:!w-[70vw] !h-[80vh] overflow-y-auto">
    @if ($purchase)
        <div class="space-y-3">
            <flux:heading size="xl" class="mb-6 text-center md:text-left">Purchase Details</flux:heading>

            {{-- Header info --}}
            <div class="flex flex-wrap text-sm gap-x-8 gap-y-3 sm:gap-x-6 md:gap-x-10">
                <div class="flex">
                    <div><strong>Code:</strong></div>
                    <div class="pl-2">{{ $purchase->purchase_code }}</div>
                </div>

                <div class="flex">
                    <div><strong>Supplier:</strong></div>
                    <div class="pl-2">{{ $purchase->supplier->supplier_name ?? '-' }}</div>
                </div>

                <div class="flex">
                    <div><strong>Date:</strong></div>
                    <div class="pl-2">{{ $purchase->purchase_date }}</div>
                </div>

                <div class="flex">
                    <div><strong>Status:</strong></div>
                    <div class="pl-2">{{ ucfirst($purchase->status) }}</div>
                </div>
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
                        @foreach ($purchase->details as $d)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2">{{ $d->item->item_name ?? '-' }}</td>
                                <td class="p-2 text-right">{{ $d->quantity }}</td>
                                <td class="p-2 text-right">Rp {{ number_format($d->unit_price, 0, ',', '.') }}</td>
                                <td class="p-2 text-right">{{ number_format($d->discount ?? 0, 0, ',', '.') }}%</td>
                                <td class="p-2 text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
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
                <strong>Total: Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</strong>
            </div>
           
        </div>
    @endif
</flux:modal>
