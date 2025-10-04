<x-modal name="create-item" title="Create Item" subtitle="Make sure your input data is valid">
    <flux:select wire:model="merk_id" label="Merk" placeholder="Select merk" searchable>
        <flux:select.option value="">-- Select Merk --</flux:select.option>
        @foreach ($merks as $merk)
            <flux:select.option value="{{ $merk->id }}">{{ $merk->code }} -> {{ $merk->merk_name }}
            </flux:select.option>
        @endforeach
    </flux:select>
    <flux:input wire:model="code" label="Code" placeholder="Item code" />
    <flux:input wire:model="name" label="Name" placeholder="Item name" />
    <flux:input wire:model="price" label="Price" placeholder="price" />
    <flux:input wire:model="sellingPrice" label="Selling Price" placeholder="Selling price" />
    <flux:input wire:model="stock" label="Stock" placeholder="stock" />
    <flux:input wire:model="minimumStock" label="Minimum Stock" placeholder="Minimum Stock" />
    <flux:textarea wire:model="description" label="Description" placeholder="Description item ..." />

    <x-slot name="footer">
        <flux:button wire:click="resetForm" type="button" variant="outline" color="gray" class="mx-2">Reset</flux:button>
        <flux:button wire:click="save" type="submit" variant="primary" color="blue">Save</flux:button>
    </x-slot>
</x-modal>
