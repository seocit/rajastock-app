<div>
    <flux:heading size="xl" level="1">Item List</flux:heading>
    <flux:text size="" class="mt-2">List Item for Accu product</flux:text>
    <flux:separator class="mb-4"></flux:separator>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <!-- Search -->

        <div class="flex w-full">
            <div class="w-full md:w-1/3 mx-2">
                <flux:input wire:model.live.debounce.500ms="search" icon="magnifying-glass" placeholder="Search items..."
                    class="w-full" />
            </div>
            <flux:button icon="funnel">sort</flux:button>
        </div>
        <!-- Add Item Button -->
        <div>
            <flux:modal.trigger name="create-item">
                <flux:button variant="primary" color="blue">Add Item</flux:button>
            </flux:modal.trigger>
            <livewire:superadmin.items.create-item />
            <livewire:superadmin.items.edit-item />
        </div>

    </div>

    {{-- add item button --}}
    {{-- table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-max w-full border border-gray-200 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Item Code</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Item Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Merk</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Price</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Selling Price</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Minimum Stock</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->items as $item)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $loop->index + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->item_code }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->item_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->merk->merk_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">Rp. {{ $item->price }},-</td>
                        <td class="px-4 py-2 text-sm text-gray-600">Rp. {{ $item->selling_price }},-</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->minimum_stock }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->stock }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->description }}</td>
                        <td class="px-4 py-2 text-sm">
                            <flux:button wire:click="edit({{ $item->id }})" :loading="true" variant="primary"
                                size="sm" color="blue">Edit</flux:button>
                            <flux:button wire:click="delete({{ $item->id }})" :loading="false"
                                variant="danger" size="sm">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-2 text-center text-sm text-gray-600">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-2">
            {{ $this->items->links() }}
        </div>
    </div>


    {{-- modal confirm --}}
    <flux:modal name="delete-item" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Item?</flux:heading>

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

                <flux:button type="submit" variant="danger" wire:click="deleteItem()">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
