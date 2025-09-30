<x-modal name="edit-item" title="Edit Item" subtitle="Make sure your input data is valid">
    <flux:input wire:model="code" label="Code" placeholder="Item code" />
    <flux:input wire:model="name" label="Name" placeholder="Item name" />
    <flux:input wire:model="price" label="Price" placeholder="price" />
    <flux:input wire:model="sellingPrice" label="Selling Price" placeholder="Selling price" />
    <flux:input wire:model="stock" label="Stock" placeholder="stock" />
    <flux:input wire:model="minimumStock" label="Minimum Stock" placeholder="Minimum Stock" />
    <flux:textarea wire:model="description" label="Description" placeholder="Description item ..." />

    <x-slot name="footer">
        <flux:button wire:click="update" type="submit" variant="primary" color="blue">Update</flux:button>
    </x-slot>
</x-modal>
