<x-modal name="edit-merk" title="Edit Merk" subtitle="Make sure your input data is valid">  
    <flux:input wire:model="code" label="Code" placeholder="Merk code" />
    <flux:input wire:model="name" label="Name" placeholder="Merk name" />  
    <x-slot name="footer">
        <flux:button wire:click="update" type="submit" variant="primary" color="blue">Update</flux:button>
    </x-slot>
</x-modal>