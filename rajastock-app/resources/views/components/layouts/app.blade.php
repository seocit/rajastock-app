<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        @include('components.toast')
        {{ $slot }}
        @stack('scripts')
    </flux:main>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('success', (data) => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: data.message
                    }
                }));
            });

            Livewire.on('error', (data) => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: data.message
                    }
                }));
            });
        });
    </script>

</x-layouts.app.sidebar>
