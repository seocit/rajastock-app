<x-modal name="edit-customer" title="Edit Customer" subtitle="Make sure your input data is valid">
    <flux:input wire:model="customer_code" label="Code" placeholder="Customer code" />
    <flux:input wire:model="customer_name" label="Name" placeholder="Customer name" />
    <flux:input wire:model="email" label="Email" placeholder="Email" />
    <flux:input wire:model="no_contact" label="Contact" placeholder="No contact" />
    <flux:input wire:model="address" label="Address" placeholder="Address" />
    <x-slot name="footer">
        <flux:button wire:click="update" type="submit" variant="primary" color="blue">Update</flux:button>
    </x-slot>
</x-modal>
