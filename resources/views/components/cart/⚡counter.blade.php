<?php

use App\Services\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public int $count = 0;

    public function mount(Cart $cart): void
    {
        $this->count = $cart->count();
    }

    #[On('cart-updated')]
    public function refreshCount(Cart $cart): void
    {
        $this->count = $cart->count();
    }
};
?>

<button
    x-on:click="$dispatch('open-cart')"
    class="relative flex size-9 items-center justify-center rounded-full text-ink-soft transition hover:bg-ink/5 hover:text-ink"
    aria-label="Open cart ({{ $count }} {{ Str::plural('item', $count) }})"
>
    <x-ui.icon name="bag" class="size-5" />
    @if ($count > 0)
        <span class="absolute -top-0.5 -right-0.5 flex size-[1.125rem] items-center justify-center rounded-full bg-accent text-[0.625rem] font-bold text-accent-fg">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</button>
