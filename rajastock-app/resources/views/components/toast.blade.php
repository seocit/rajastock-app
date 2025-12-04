<div x-data="toast" class="fixed top-5 right-5 z-[9999] space-y-2" x-cloak>
    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.show" x-transition
            :class="{
                'bg-green-500 text-white': t.type === 'success',
                'bg-yellow-500 text-black': t.type === 'error',
            }"
            class="px-4 py-3 rounded shadow-md min-w-[260px] font-semibold shadow-lg">
            <span x-text="t.message"></span>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toast', () => ({
            toasts: [],

            init() {
                window.addEventListener('toast', (e) => {
                    const id = Date.now();

                    this.toasts.push({
                        id,
                        type: e.detail.type,
                        message: e.detail.message,
                        show: true
                    });


                    setTimeout(() => {
                        let toast = this.toasts.find(t => t.id === id);
                        if (toast) toast.show = false;
                    }, 3000);


                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 3500);
                });
            }
        }))
    });
</script>
