<flux:modal.trigger :name="$name">
    <flux:button variant="danger" size="sm">{{ $confirmText }}</flux:button>
</flux:modal.trigger>

<flux:modal :name="$name" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $title }}</flux:heading>

            <flux:text class="mt-2">
                <p>{{ $message }}</p>
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">{{ $cancelText }}</flux:button>
            </flux:modal.close>

            @if($wireEvent)
                <flux:button wire:click="{{ $wireEvent }}" variant="danger">{{ $confirmText }}</flux:button>
            @else
                <flux:button type="submit" variant="danger">{{ $confirmText }}</flux:button>
            @endif
        </div>
    </div>
</flux:modal>
