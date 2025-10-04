
<div>
    <flux:heading size="xl" level="1">Supplier</flux:heading>
    <flux:text size="" class="mt-2">List Supplier </flux:text>
    <flux:separator class="mb-4"></flux:separator>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <!-- Searchbar -->

        <div class="flex w-full">
            <div  wire:model.live.debounce.500ms="search" class="w-full md:w-1/3 mx-2">
                <flux:input  icon="magnifying-glass" placeholder="Search items..."
                    class="w-full" />
            </div>
            <flux:button icon="funnel">sort</flux:button>
        </div>
        <!-- Add Item Button -->
        <div>
            <flux:modal.trigger name="create-supplier">
                <flux:button variant="primary" color="blue">Add Supplier</flux:button>
            </flux:modal.trigger>
            <livewire:superadmin.supplier.create-supplier />
            <livewire:superadmin.supplier.edit-supplier />
        </div>

    </div>

    {{-- add item button --}}
    {{-- table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>                  
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Email </th>                  
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Contact Number</th>                  
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Address</th>                  
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->suppliers as $item)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->supplier_code }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->supplier_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->email }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->no_contact }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->address }}</td>
                        <td class="px-4 py-2 text-sm">
                            <flux:button wire:click="edit({{ $item->id }})" :loading="true" variant="primary"
                                size="sm" color="blue">Edit</flux:button>
                            <flux:button wire:click="delete({{ $item->id }})" :loading="false"
                                variant="danger" size="sm">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-600">No items found.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        <div class="py-2 px-4">
            {{ $this->suppliers->links() }}
        </div>        
    </div>
    
    {{-- modal confirm --}}
    <flux:modal name="delete-supplier" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Supplier?</flux:heading>

                <flux:text class="mt-2">
                    <p>You're about to delete this item.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" wire:click="deleteSupplier()">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
