<div class="p-6">
    <flux:heading size="xl" level="1">Item Returns</flux:heading>
    <flux:text size="" class="mt-2">item returns list</flux:text>
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
            <flux:button as="a" href="{{ route('create-purchase-returns') }}" variant="primary" color="blue">
                Create Purchase
            </flux:button>
        </div>
    </div>

    {{-- table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Return Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Purchase Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->returns as $return)
                    <tr">
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->return_number }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->purchase->purchase_code }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->return_date }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->status }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:button size="sm" color="secondary" wire:click="$dispatch('showReturnDetails', { returnId: {{ $return->id }} })"
                            >
                                View
                            </flux:button>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-600">No items found.</td>
                        </tr>
                @endforelse
            </tbody>
        </table>
        <livewire:superadmin.return.detail-purchase-return />
    </div>

    <div>
        {{ $this->returns->links() }}
    </div>
</div>
