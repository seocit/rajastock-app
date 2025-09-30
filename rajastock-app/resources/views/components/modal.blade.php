<div>
    <flux:modal name="{{ $name ?? 'default-modal' }}" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $title ?? 'Modal Title' }}</flux:heading>
                <flux:text class="mt-2">{{ $subtitle ?? 'Subtitle here...' }}</flux:text>

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Slot untuk form / konten --}}
            {{ $slot }}

            <div class="flex">
                <flux:spacer />
                {{ $footer ?? '' }}
            </div>
        </div>
    </flux:modal>
</div>
