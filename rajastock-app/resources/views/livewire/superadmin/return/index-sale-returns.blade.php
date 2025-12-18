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
        </div>

        <!-- Add Sales Return Button -->
        <div>
            @can('create sales returns')
                <flux:button as="a" href="{{ route('create-sale-returns') }}" variant="primary" color="blue">
                    Create Sales Return
                </flux:button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">

        {{-- Customer --}}
        <flux:select wire:model.live="customerId">
            <option value="">All Customers</option>
            @foreach (\App\Models\Customer::all() as $cust)
                <option value="{{ $cust->id }}">{{ $cust->customer_name }}</option>
            @endforeach
        </flux:select>

        {{-- Status --}}
        <flux:select wire:model.live="status">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </flux:select>

        {{-- Date Start --}}
        <flux:input type="date" wire:model.live="dateStart" />

        {{-- Date End --}}
        <flux:input type="date" wire:model.live="dateEnd" />

        {{-- Reset --}}
        <flux:button icon="arrow-path" variant="primary" wire:click="resetFilters">
            Reset
        </flux:button>

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
                        <td class="px-4 py-2 text-sm capitalize text-gray-600">
                            <flux:dropdown>
                                <flux:button icon:trailing="chevron-down" size="xs"
                                    class="px-2 py-1 rounded bg-gray-100">
                                    {{ ucfirst($return->status) }}
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'pending')">
                                        Pending
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'approved')">
                                        Approved
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'rejected')">
                                        Rejected
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:button size="sm" color="secondary"
                                wire:click="$dispatch('showSaleReturnDetails', { returnId: {{ $return->id }} })">
                                View
                            </flux:button>
                            @can('edit sales returns')
                                <flux:button as="a" href="{{ route('edit-sale-returns', $return->id) }}"
                                    size="sm" color="blue" variant="primary">
                                    Edit
                                </flux:button>
                                <flux:button variant="danger" size="sm" wire:click="delete({{ $return->id }})">
                                    Hapus
                                </flux:button>
                            @endcan
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


    {{-- MODAL KONFIRMASI DELETE --}}
    <flux:modal name="delete-sale-return" title="Hapus Sales Return" dismissible>
        <p>Anda yakin ingin menghapus sales return ini? </p>
        <p>Stok barang akan dikembalikan.</p>

        <div class="flex justify-end gap-2 mt-4">
            <flux:modal.close>
                <flux:button variant="ghost">Batal</flux:button>
            </flux:modal.close>
            <flux:button size="sm" variant="danger" wire:click="deleteSaleReturn">
                Ya, Hapus
            </flux:button>
        </div>
    </flux:modal>

</div>
