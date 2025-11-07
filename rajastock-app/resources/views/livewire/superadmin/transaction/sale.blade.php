<div>
    <flux:heading size="xl" level="1">Transaction | Sales</flux:heading>
    <flux:text size="" class="mt-2">sale list</flux:text>
    <flux:separator class="mb-4"></flux:separator>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <!-- Searchbar -->

        <div class="flex w-full">
            <div wire:model.live.debounce.500ms="search" class="w-full md:w-1/3 mx-2">
                <flux:input icon="magnifying-glass" placeholder="Search items..." class="w-full" />
            </div>
            <flux:button icon="funnel">sort</flux:button>
        </div>

        <!-- Add Item Button -->
        <div>
            <flux:button as="a" href="{{ route('create-sale') }}" variant="primary" color="blue">
                Create Purchase
            </flux:button>
        </div>



    </div>
    {{-- Table --}}
    <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Sale Code</th>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Total</th>
                    {{-- <th class="px-4 py-2">Status</th> --}}
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->sales as $index => $sale)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 font-medium">{{ $sale->sale_code }}</td>
                        <td class="px-4 py-2">{{ $sale->customer->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $sale->sale_date }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        {{-- <td class="px-4 py-2">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                            {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td> --}}
                        <td class="px-4 py-2 text-center space-x-2">
                            <flux:button size="sm" color="secondary" wire:click="showDetails({{ $sale->id }})">
                                View
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">No sales found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $this->sales->links() }}
    </div>

    <livewire:superadmin.transaction.sales-detail />
</div>
