<div class="p-6">
    <flux:heading size="xl" level="1">Sales Returns</flux:heading>
    <flux:text class="mt-2">List of returned sales items</flux:text>
    <flux:separator class="mb-4"></flux:separator>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <!-- Searchbar -->
        <div class="flex w-full">
            <div class="w-full md:w-1/3 mx-2">
                <flux:input icon="magnifying-glass" placeholder="Search sales returns..."
                    wire:model.live.debounce.500ms="search" class="w-full" />
            </div>
            <flux:button icon="funnel">Sort</flux:button>
        </div>

        <!-- Add Sales Return Button -->
        <div>
            <flux:button as="a" href="{{ route('create-sale-returns') }}" variant="primary" color="blue">
                Create Sales Return
            </flux:button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Return Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Sale Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->returns as $return)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->return_code }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->sale->sale_code ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-sm capitalize text-gray-600">{{ $return->status }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:button size="sm" color="secondary"
                                wire:click="$dispatch('showSaleReturnDetails', { returnId: {{ $return->id }} })">

                                View
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-600">
                            No sales returns found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <livewire:superadmin.return.detail-sale-return />
    </div>

    <div class="mt-4">
        {{ $this->returns->links() }}
    </div>
</div>
