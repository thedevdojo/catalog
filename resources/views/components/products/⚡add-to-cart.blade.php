<?php

use App\Models\Product;
use App\Services\Cart;
use Livewire\Component;

new class extends Component
{
    public Product $product;

    public string $variant = 'full';

    public int $quantity = 1;

    public function add(Cart $cart): void
    {
        if (! $this->product->isInStock()) {
            return;
        }

        $cart->add($this->product, max(1, $this->quantity));
        $this->quantity = 1;

        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function incrementQuantity(): void
    {
        $this->quantity = min($this->quantity + 1, $this->product->stock);
    }

    public function decrementQuantity(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }
};
?>

<div>
    @if ($variant === 'icon')
        <button
            wire:click="add"
            class="flex size-10 items-center justify-center rounded-full bg-ink text-paper shadow-lg transition hover:bg-accent"
            aria-label="Add {{ $product->name }} to cart"
        >
            <span wire:loading.remove wire:target="add">
                <x-ui.icon name="plus" class="size-4.5" />
            </span>
            <span wire:loading wire:target="add">
                <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                    <path class="opacity-90" d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                </svg>
            </span>
        </button>
    @else
        @if ($product->isInStock())
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="flex h-[3.25rem] w-fit items-center rounded-full border border-line-strong px-1.5">
                    <button
                        wire:click="decrementQuantity"
                        class="flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink"
                        aria-label="Decrease quantity"
                    >
                        <x-ui.icon name="minus" class="size-4" />
                    </button>
                    <span class="w-9 text-center text-[0.9375rem] font-semibold tabular-nums">{{ $quantity }}</span>
                    <button
                        wire:click="incrementQuantity"
                        class="flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink {{ $quantity >= $product->stock ? 'pointer-events-none opacity-30' : '' }}"
                        aria-label="Increase quantity"
                    >
                        <x-ui.icon name="plus" class="size-4" />
                    </button>
                </div>
                <button wire:click="add" class="btn btn-primary btn-lg flex-1">
                    <span wire:loading.remove wire:target="add" class="flex items-center gap-2">
                        <x-ui.icon name="bag" class="size-4.5" />
                        Add to Cart — {{ Product::formatCents($product->price * $quantity) }}
                    </span>
                    <span wire:loading wire:target="add">Adding…</span>
                </button>
            </div>
            @if ($product->isLowStock())
                <p class="mt-3 flex items-center gap-1.5 text-[0.8125rem] font-medium text-accent">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-accent opacity-60"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-accent"></span>
                    </span>
                    Only {{ $product->stock }} left in stock
                </p>
            @endif
        @else
            <button class="btn btn-outline btn-lg w-full" disabled>Sold Out</button>
            <p class="mt-3 text-[0.8125rem] text-muted">This one went fast. Check back soon — restocks happen often.</p>
        @endif
    @endif
</div>
