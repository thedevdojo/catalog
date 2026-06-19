<?php

use App\Models\Product;
use App\Services\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Listener exists solely to trigger a re-render with fresh cart contents.
    }

    public function increment(int $productId, Cart $cart): void
    {
        $quantity = $cart->items()->firstWhere(fn (array $item) => $item['product']->id === $productId)['quantity'] ?? 0;
        $cart->updateQuantity($productId, $quantity + 1);

        $this->dispatch('cart-updated');
    }

    public function decrement(int $productId, Cart $cart): void
    {
        $quantity = $cart->items()->firstWhere(fn (array $item) => $item['product']->id === $productId)['quantity'] ?? 0;
        $cart->updateQuantity($productId, $quantity - 1);

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $productId, Cart $cart): void
    {
        $cart->remove($productId);

        $this->dispatch('cart-updated');
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $cart = app(Cart::class);

        return [
            'items' => $cart->items(),
            'cart' => $cart,
        ];
    }
};
?>

<div
    x-data="{ open: false }"
    x-on:open-cart.window="open = true"
    x-on:keydown.escape.window="open = false"
>
    {{-- Overlay --}}
    <div
        x-cloak
        x-show="open"
        x-transition.opacity.duration.300ms
        class="fixed inset-0 z-[80] bg-ink/40 backdrop-blur-sm"
        x-on:click="open = false"
    ></div>

    {{-- Panel --}}
    <aside
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-400 ease-[cubic-bezier(0.16,1,0.3,1)]"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition duration-250 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[85] flex w-[26.5rem] max-w-[94vw] flex-col bg-paper shadow-2xl"
        role="dialog"
        aria-label="Shopping cart"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-line px-6 py-5">
            <h2 class="font-display text-xl font-medium italic">
                Your Cart
                @if ($cart->count() > 0)
                    <span class="font-sans text-sm font-semibold text-muted not-italic">({{ $cart->count() }})</span>
                @endif
            </h2>
            <button x-on:click="open = false" class="-mr-1.5 rounded-full p-1.5 text-muted transition hover:bg-ink/5 hover:text-ink" aria-label="Close cart">
                <x-ui.icon name="x" class="size-5" />
            </button>
        </div>

        @if ($items->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-1 flex-col items-center justify-center gap-5 px-8 text-center">
                <div class="flex size-16 items-center justify-center rounded-full bg-paper-deep">
                    <x-ui.icon name="bag" class="size-7 text-faint" />
                </div>
                <div>
                    <p class="font-display text-lg font-medium">Your cart is empty</p>
                    <p class="mt-1.5 text-sm text-pretty text-muted">Good things await. Start with a book, a record, or a game night.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('shop.category', ['category' => 'books']) }}" wire:navigate x-on:click="open = false" class="btn btn-outline btn-sm">Books</a>
                    <a href="{{ route('shop.category', ['category' => 'vinyl']) }}" wire:navigate x-on:click="open = false" class="btn btn-outline btn-sm">Vinyl</a>
                    <a href="{{ route('shop.category', ['category' => 'board-games']) }}" wire:navigate x-on:click="open = false" class="btn btn-outline btn-sm">Board Games</a>
                </div>
            </div>
        @else
            {{-- Free shipping meter --}}
            <div class="border-b border-line bg-surface px-6 py-4">
                @if ($cart->hasFreeShipping())
                    <p class="flex items-center gap-2 text-[0.8125rem] font-semibold text-moss">
                        <x-ui.icon name="check-circle" class="size-4.5" />
                        Your order qualifies for free shipping
                    </p>
                @else
                    <p class="text-[0.8125rem] font-medium text-ink-soft">
                        You're <span class="font-bold text-accent">{{ Product::formatCents($cart->amountUntilFreeShipping()) }}</span> away from free shipping
                    </p>
                @endif
                <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-paper-deep">
                    <div
                        class="h-full rounded-full {{ $cart->hasFreeShipping() ? 'bg-moss' : 'bg-accent' }} transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        style="width: {{ min(100, ($cart->subtotal() / Cart::FREE_SHIPPING_THRESHOLD) * 100) }}%"
                    ></div>
                </div>
            </div>

            {{-- Items --}}
            <div class="flex-1 divide-y divide-line overflow-y-auto px-6">
                @foreach ($items as $item)
                    <div class="flex gap-4 py-5" wire:key="cart-item-{{ $item['product']->id }}">
                        <a href="{{ route('products.show', ['product' => $item['product']]) }}" wire:navigate x-on:click="open = false" class="art-frame block w-[4.5rem] shrink-0">
                            <img src="{{ $item['product']->image }}" alt="{{ $item['product']->name }}" class="aspect-4/5 w-full object-cover">
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('products.show', ['product' => $item['product']]) }}" wire:navigate x-on:click="open = false" class="block truncate font-display text-[0.9375rem] font-medium">{{ $item['product']->name }}</a>
                                    <p class="mt-0.5 truncate text-xs text-muted">{{ $item['product']->creator }}</p>
                                </div>
                                <button
                                    wire:click="removeItem({{ $item['product']->id }})"
                                    class="shrink-0 rounded-full p-1 text-faint transition hover:bg-ink/5 hover:text-ink"
                                    aria-label="Remove {{ $item['product']->name }}"
                                >
                                    <x-ui.icon name="x" class="size-4" />
                                </button>
                            </div>
                            <div class="mt-auto flex items-center justify-between pt-2">
                                <div class="flex items-center rounded-full border border-line-strong">
                                    <button
                                        wire:click="decrement({{ $item['product']->id }})"
                                        class="flex size-7 items-center justify-center rounded-full text-muted transition hover:text-ink"
                                        aria-label="Decrease quantity"
                                    >
                                        <x-ui.icon name="minus" class="size-3.5" />
                                    </button>
                                    <span class="w-6 text-center text-[0.8125rem] font-semibold tabular-nums">{{ $item['quantity'] }}</span>
                                    <button
                                        wire:click="increment({{ $item['product']->id }})"
                                        class="flex size-7 items-center justify-center rounded-full text-muted transition hover:text-ink {{ $item['quantity'] >= $item['product']->stock ? 'pointer-events-none opacity-30' : '' }}"
                                        aria-label="Increase quantity"
                                    >
                                        <x-ui.icon name="plus" class="size-3.5" />
                                    </button>
                                </div>
                                <p class="text-sm font-semibold tabular-nums">{{ Product::formatCents($item['product']->price * $item['quantity']) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="border-t border-line bg-surface px-6 py-5">
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Subtotal</span>
                        <span class="font-medium text-ink tabular-nums">{{ Product::formatCents($cart->subtotal()) }}</span>
                    </div>
                    <div class="flex justify-between text-muted">
                        <span>Shipping</span>
                        <span class="font-medium text-ink tabular-nums">{{ $cart->shipping() === 0 ? 'Free' : Product::formatCents($cart->shipping()) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-line pt-2.5 text-base font-bold">
                        <span>Total</span>
                        <span class="tabular-nums">{{ Product::formatCents($cart->total()) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout') }}" x-on:click="open = false" class="btn btn-primary btn-lg mt-4 w-full" wire:navigate>
                    Continue to Checkout
                    <x-ui.icon name="arrow-right" class="size-4.5" />
                </a>
                <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-faint">
                    <x-ui.icon name="lock" class="size-3.5" />
                    Secure checkout · 30-day returns
                </p>
            </div>
        @endif
    </aside>
</div>
