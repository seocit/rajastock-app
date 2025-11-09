<flux:modal name="return-details" wire:model="isOpen" dismissible
    class="!max-w-none !w-[95vw] md:!w-[80vw] lg:!w-[70vw] !h-[80vh] overflow-y-auto">

    @if ($returnData)
        <div class="space-y-3">
            <flux:heading size="xl" class="mb-6 text-center md:text-left">Sales Return Details</flux:heading>

            {{-- Header Info --}}
            <div class="flex flex-wrap text-sm gap-x-8 gap-y-3 sm:gap-x-6 md:gap-x-10">
                <div class="flex">
                    <div><strong>Return Code:</strong></div>
                    <div class="pl-2">{{ $returnData->return_number }}</div>
                </div>

                <div class="flex">
                    <div><strong>Sales Code:</strong></div>
                    <div class="pl-2">{{ $returnData->sale->sale_code ?? '-' }}</div>
                </div>

                <div class="flex">
                    <div><strong>Date:</strong></div>
                    <div class="pl-2">{{ $returnData->return_date }}</div>
                </div>

                <div class="flex">
                    <div><strong>Status:</strong></div>
                    <div class="pl-2">{{ ucfirst($returnData->status) }}</div>
                </div>
            </div>

            <flux:separator class="my-3" />

            {{-- Table Section --}}
            <div class="overflow-x-auto rounded-lg border max-h-[55vh]">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="p-2 text-left">Item</th>
                            <th class="p-2 text-right">Qty Returned</th>
                            <th class="p-2 text-left">Condition</th>
                            <th class="p-2 text-left">Reason</th>
                            <th class="p-2 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $d)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2">{{ $d->salesDetail->item->item_name ?? '-' }}</td>
                                <td class="p-2 text-right">{{ $d->quantity_returned }}</td>
                                <td class="p-2 text-left">{{ ucfirst($d->condition) }}</td>
                                <td class="p-2 text-left">{{ $d->reason ?: '-' }}</td>
                                <td class="p-2 text-left">
                                    Rp {{ number_format($d['sub_total'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $details->links() }}
                </div>
            </div>
        </div>
    @endif
</flux:modal>
