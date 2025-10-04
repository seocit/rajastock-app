<x-modal name="create-merk" title="Create Merk" subtitle="Make sure your input data is valid">  
    <flux:input wire:model="code" label="Code" placeholder="Merk code" />
    <flux:input wire:model="name" label="Name" placeholder="Merk name" />  
    <x-slot name="footer">
        <flux:button wire:click="resetForm" type="button" variant="outline" color="gray" class="mx-2">Reset</flux:button>
        <flux:button wire:click="save" type="submit" variant="primary" color="blue">Save</flux:button>
    </x-slot>
</x-modal>