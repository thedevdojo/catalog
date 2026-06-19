<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = ++this.lastId;
            this.toasts.push({ id, message: detail.message ?? detail, type: detail.type ?? 'success' });
            setTimeout(() => this.dismiss(id), 3200);
        },
        dismiss(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
        lastId: 0,
    }"
    x-on:toast.window="add($event.detail)"
    class="pointer-events-none fixed inset-x-0 bottom-6 z-[90] flex flex-col items-center gap-2 px-4"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-y-3 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex items-center gap-2.5 rounded-full bg-ink py-2.5 pr-5 pl-4 text-sm font-medium text-paper shadow-xl"
        >
            <x-ui.icon name="check-circle" class="size-4.5 text-[#a8d8b9]" x-show="toast.type === 'success'" />
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
