<x-modal name="edit-merk" title="Edit Brand" subtitle="Make sure your input data is valid">  
    <flux:input wire:model="code" label="Code" placeholder="Brand code" />
    <flux:input wire:model="name" label="Name" placeholder="Brand name" />  
    <x-slot name="footer">
        <flux:button wire:click="update" type="submit" variant="primary" color="blue">Update</flux:button>
    </x-slot>
</x-modal>