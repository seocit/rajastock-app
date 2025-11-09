<div>
    <flux:heading size="xl" level="1">New Purchases</flux:heading>
    <flux:separator class="mb-10"></flux:separator>
    {{-- Supplier & Date --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <flux:select wire:model="supplier_id" label="Supplier" placeholder="Select supplier" searchable>
            <flux:select.option value="">-- Select Suppplier --</flux:select.option>
            @foreach ($suppliers as $s)
                <flux:select.option value="{{ $s->id }}">{{ $s->supplier_name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input type="date" wire:model="purchase_date" label="Purchase Date" />
    </div>


    {{-- Item List --}}
    <div class="p-4 space-y-4 border border-gray-200 rounded-2xl shadow-sm bg-white">
        <flux:heading size="lg">Items</flux:heading>
        {{-- 🔁 Refresh Total Button --}}
        <flux:button variant="primary" icon="arrow-path" wire:click="refreshTotal">
            refresh
        </flux:button>

        @foreach ($rows as $index => $row)
            <div class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
                <flux:select wire:model.live="rows.{{ $index }}.item_id" label="Item" placeholder="Select item"
                    searchable>
                    @foreach ($items as $item)
                        <flux:select.option value="{{ $item->id }}">{{ $item->item_name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="number" wire:model.live="rows.{{ $index }}.quantity"
                    wire:change="refreshTotal" label="Qty" min="1" />

                <flux:input type="number" wire:model.live="rows.{{ $index }}.unit_price"
                    wire:change="refreshTotal" label="Unit Price" min="0" />

                <flux:input type="number" kbd="%" wire:model.live="rows.{{ $index }}.discount"
                    wire:change="refreshTotal" label="Discount" min="0" />

                <flux:input readonly wire:model="rows.{{ $index }}.subtotal" label="Subtotal" />

                <flux:button color="destructive" icon="trash" wire:click="removeRow({{ $index }})" />
            </div>
        @endforeach


        <div class="pt-2">
            <flux:button variant="outline" wire:click="addRow">

                Add Item
            </flux:button>
        </div>
    </div>

    {{-- Total & Save --}}
    <div class="flex justify-between items-center mt-4">
        <flux:heading size="lg">Total: Rp {{ number_format($total, 0, ',', '.') }}</flux:heading>
        <flux:button color="primary" wire:click="save">

            Save Purchase
        </flux:button>
    </div>
</div>
