<div>
    <flux:heading size="xl" level="1">Transaction | Sales</flux:heading>
    <flux:text class="mt-2">sales list</flux:text>
    <flux:separator class="mb-4"></flux:separator>

    {{-- TOP BAR --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

        {{-- SEARCH --}}
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.500ms="search" icon="magnifying-glass" placeholder="Search sales..." />
        </div>

        {{-- CREATE BUTTON --}}
        @can('create sales')
            <div class="flex justify-start md:justify-end">
                <flux:button as="a" href="{{ route('create-sale') }}" variant="primary" color="blue">
                    Create Sale
                </flux:button>
            </div>
        @endcan

    </div>

    {{-- FILTER GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">

        {{-- Customer Filter --}}
        <div>
            <flux:select wire:model.live="customerId">
                <option value="">All Customers</option>
                @foreach (\App\Models\Customer::all() as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->customer_name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Date Start --}}
        <div>
            <flux:input type="date" wire:model.live="dateStart" placeholder="Start Date" />
        </div>

        {{-- Date End --}}
        <div>
            <flux:input type="date" wire:model.live="dateEnd" placeholder="End Date" />
        </div>

        {{-- Reset Button --}}
        <div class="flex items-center justify-start md:justify-end">
            <flux:button icon="arrow-path" variant="primary" wire:click="resetFilters">
                Reset
            </flux:button>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Sale Code</th>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($this->sales as $sale)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $sale->sale_code }}</td>
                        <td class="px-4 py-2">{{ $sale->customer->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $sale->sale_date }}</td>
                        <td class="px-4 py-2">
                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            @if ($sale->is_posted)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700 font-semibold">
                                    POSTED
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700 font-semibold">
                                    DRAFT
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-2 text-center space-x-2">

                            {{-- VIEW (selalu boleh) --}}
                            <flux:button size="sm" color="secondary" wire:click="showDetails({{ $sale->id }})">
                                View
                            </flux:button>
                            @if (!$sale->is_posted)
                                {{-- POST --}}
                                <flux:button size="sm" variant="primary" color="green"
                                    wire:click="confirmPostSale({{ $sale->id }})">
                                    Post
                                </flux:button>
                                {{-- EDIT --}}
                                <flux:button size="sm" variant="primary" color="blue"
                                    href="{{ route('edit-sale', $sale->id) }}">
                                    Edit
                                </flux:button>
                                {{-- DELETE --}}
                                <flux:button size="sm" variant="danger" wire:click="delete({{ $sale->id }})">
                                    Delete
                                </flux:button>
                            @endif
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            No sales found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $this->sales->links() }}
    </div>

    {{-- Detail Modal --}}
    <livewire:superadmin.transaction.sales-detail />

    {{-- Delete Modal --}}
    <flux:modal name="delete-sale" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Sale?</flux:heading>
                <flux:text class="mt-2">
                    <p>Apakah anda yakin akan menghapus record Penjualan ini?</p>
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deleteSale">
                    Ya, Hapus!
                </flux:button>
            </div>
        </div>
    </flux:modal>


    {{-- Post Sale Confirmation Modal --}}
    <flux:modal name="post-sale" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Post Sale?</flux:heading>
                <flux:text class="mt-2">
                    <p>Setelah penjualan diposting:</p>

                    <ul class="list-disc list-inside mt-2 text-sm text-gray-600">
                        <li>Stok barang akan dikurangi</li>
                        <li>Data penjualan tidak bisa diedit atau dihapus</li>
                    </ul>

                    <p class="mt-3 font-medium text-red-600">
                        Yakin ingin melanjutkan?
                    </p>
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" color="green" wire:click="postSaleConfirmed">
                    Ya, Post Sekarang
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
