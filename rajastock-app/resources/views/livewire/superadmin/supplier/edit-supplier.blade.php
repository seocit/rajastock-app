<x-modal name="edit-supplier" title="Edit Supplier" subtitle="Make sure your input data is valid">
    <flux:input wire:model="supplier_code" label="Code" placeholder="Supplier code" />
    <flux:input wire:model="supplier_name" label="Name" placeholder="Supplier name" />
    <flux:input wire:model="email" label="Email" placeholder="Email" />
    <flux:input wire:model="no_contact" label="Contact" placeholder="No contact" />
    <flux:input wire:model="address" label="Address" placeholder="Address" />
    <x-slot name="footer">
        <flux:button wire:click="update" type="submit" variant="primary" color="blue">Update</flux:button>
    </x-slot>
</x-modal>
