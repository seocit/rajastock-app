<x-modal name="create-supplier" title="Create Supplier" subtitle="Make sure your input data is valid">  
    <flux:input wire:model="supplier_code" label="Code" placeholder="Supplier code" />
    <flux:input wire:model="supplier_name" label="Name" placeholder="Supplier name" />  
    <flux:input wire:model="email" label="Email" placeholder="Email" />  
    <flux:input wire:model="no_contact" label="Contact" placeholder="Contact number" />  
    <flux:input wire:model="address" label="Address" placeholder="Supplier address" />  
    <x-slot name="footer">
        <flux:button wire:click="resetForm" type="button" variant="outline" color="gray" class="mx-2">Reset</flux:button>
        <flux:button wire:click="save" type="submit" variant="primary" color="blue">Save</flux:button>
    </x-slot>
</x-modal>
