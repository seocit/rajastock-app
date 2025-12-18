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
        </div>

        <!-- Add Item Button -->
        @can('create purchase returns')
            <div>
                <flux:button as="a" href="{{ route('create-purchase-returns') }}" variant="primary" color="blue">
                    Create Purchase Return
                </flux:button>
            </div>
        @endcan
    </div>

    {{-- FILTER ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 mb-4 items-end">

        {{-- Supplier --}}
        <div>
            <flux:select wire:model.live="supplierId">
                <option value="">All Suppliers</option>
                @foreach (\App\Models\Supplier::all() as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->supplier_name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Status --}}
        <div>
            <flux:select wire:model.live="status">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </flux:select>
        </div>

        {{-- Date Start --}}
        <div>
            <flux:input type="date" wire:model.live="dateStart" />
        </div>

        {{-- Date End --}}
        <div>
            <flux:input type="date" wire:model.live="dateEnd" />
        </div>

        {{-- Reset --}}
        <div class="flex">
            <flux:button icon="arrow-path" variant="primary" wire:click="resetFilters" class="w-full">
                Reset
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
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->return_number }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->purchase->purchase_code }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $return->return_date }}</td>
                        {{-- Status dropdown --}}
                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:dropdown>
                                <flux:button icon:trailing="chevron-down" size="xs"
                                    class="px-2 py-1 rounded border bg-white text-gray-600">
                                    {{ ucfirst($return->status) }}
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'pending')">
                                        Pending
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'completed')">
                                        Completed
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="updateStatus({{ $return->id }}, 'cancelled')">
                                        Cancelled
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                        {{-- Status dropdown --}}
                        <td class="px-4 py-2 text-sm text-gray-600">
                            <flux:button size="sm" color="secondary"
                                wire:click="$dispatch('showReturnDetails', { returnId: {{ $return->id }} })">
                                View
                            </flux:button>
                            @can('edit purchase returns')
                                <flux:button size="sm" color="blue" variant="primary"
                                    href="{{ route('edit-purchase', $return->id) }}">
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

    {{-- MODAL KONFIRMASI DELETE --}}
    <x-modal name="delete-purchase-return" title="Hapus Return Pembelian?" subtitle="Aksi ini akan mengembalikan stok"
        dismissible>
        <div class="space-y-4">

            <p class="text-sm text-gray-600">
                Apakah Anda yakin ingin menghapus retur pembelian ini?
                Stok barang akan dikembalikan ke jumlah sebelum retur.
            </p>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button size="sm" variant="danger" wire:click="deletePurchaseReturn">
                    Ya, Hapus
                </flux:button>
            </div>

        </div>
    </x-modal>

</div>
