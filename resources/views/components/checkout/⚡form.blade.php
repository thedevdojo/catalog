<?php

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $address_line1 = '';

    public string $address_line2 = '';

    public string $city = '';

    public string $state = '';

    public string $postal_code = '';

    public string $country = 'United States';

    public function mount(): void
    {
        $this->name = auth()->user()->name ?? '';
        $this->email = auth()->user()->email ?? '';
    }

    public function placeOrder(Cart $cart): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'address_line1' => ['required', 'string', 'max:160'],
            'address_line2' => ['nullable', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:80'],
        ]);

        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->addError('cart', 'Your cart is empty.');

            return;
        }

        foreach ($items as $item) {
            if ($item['quantity'] > $item['product']->stock) {
                $this->addError('cart', "Sorry — \"{$item['product']->name}\" only has {$item['product']->stock} left in stock. Adjust your cart and try again.");

                return;
            }
        }

        $order = DB::transaction(function () use ($cart, $items) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'number' => Order::generateNumber(),
                'status' => 'processing',
                'email' => $this->email,
                'name' => $this->name,
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2 ?: null,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'subtotal' => $cart->subtotal(),
                'shipping' => $cart->shipping(),
                'total' => $cart->total(),
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'creator' => $item['product']->creator,
                    'image' => $item['product']->image,
                    'unit_price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        $cart->clear();
        Mail::to($order->email)->send(new OrderPlaced($order));

        $this->dispatch('cart-updated');

        session()->flash('order-placed', true);

        $this->redirectRoute('orders.show', ['order' => $order], navigate: true);
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

<div>
    @if ($items->isEmpty())
        <div class="flex flex-col items-center gap-5 py-24 text-center">
            <div class="flex size-16 items-center justify-center rounded-full bg-paper-deep">
                <x-ui.icon name="bag" class="size-7 text-faint" />
            </div>
            <div>
                <p class="font-display text-2xl font-medium">There's nothing to check out</p>
                <p class="mt-2 text-sm text-muted">Your cart is empty. Let's fix that.</p>
            </div>
            <a href="{{ route('shop') }}" wire:navigate class="btn btn-primary">Browse the Shop</a>
        </div>
    @else
        <form wire:submit="placeOrder" class="grid gap-12 lg:grid-cols-[1fr_26rem]">
            {{-- Left: contact, shipping, payment --}}
            <div class="space-y-10">
                @error('cart')
                    <div class="card border-accent/40 bg-accent-soft px-5 py-4 text-sm font-medium text-accent">{{ $message }}</div>
                @enderror

                <section>
                    <div class="flex items-center gap-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-ink text-xs font-bold text-paper">1</span>
                        <h2 class="font-display text-xl font-medium">Contact</h2>
                    </div>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="checkout-name" class="input-label">Full name</label>
                            <input id="checkout-name" type="text" wire:model="name" class="input" autocomplete="name">
                            @error('name') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="checkout-email" class="input-label">Email</label>
                            <input id="checkout-email" type="email" wire:model="email" class="input" autocomplete="email">
                            @error('email') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center gap-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-ink text-xs font-bold text-paper">2</span>
                        <h2 class="font-display text-xl font-medium">Shipping Address</h2>
                    </div>
                    <div class="mt-5 grid gap-4">
                        <div>
                            <label for="checkout-address1" class="input-label">Street address</label>
                            <input id="checkout-address1" type="text" wire:model="address_line1" class="input" placeholder="123 Mercer Street" autocomplete="address-line1">
                            @error('address_line1') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="checkout-address2" class="input-label">Apartment, suite, etc. <span class="font-normal text-faint">(optional)</span></label>
                            <input id="checkout-address2" type="text" wire:model="address_line2" class="input" autocomplete="address-line2">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="checkout-city" class="input-label">City</label>
                                <input id="checkout-city" type="text" wire:model="city" class="input" autocomplete="address-level2">
                                @error('city') <p class="input-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="checkout-state" class="input-label">State</label>
                                <input id="checkout-state" type="text" wire:model="state" class="input" autocomplete="address-level1">
                                @error('state') <p class="input-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="checkout-postal" class="input-label">ZIP code</label>
                                <input id="checkout-postal" type="text" wire:model="postal_code" class="input" autocomplete="postal-code">
                                @error('postal_code') <p class="input-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="checkout-country" class="input-label">Country</label>
                            <input id="checkout-country" type="text" wire:model="country" class="input" autocomplete="country-name">
                            @error('country') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center gap-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-ink text-xs font-bold text-paper">3</span>
                        <h2 class="font-display text-xl font-medium">Payment</h2>
                    </div>
                    <div class="card mt-5 overflow-hidden">
                        <div class="flex items-center justify-between border-b border-line bg-paper-deep/50 px-5 py-3">
                            <p class="flex items-center gap-2 text-[0.8125rem] font-semibold text-ink-soft">
                                <x-ui.icon name="card" class="size-4.5" />
                                Card
                            </p>
                            <span class="badge badge-neutral">Demo Mode</span>
                        </div>
                        <div class="grid gap-4 p-5">
                            <div>
                                <label class="input-label" for="demo-card">Card number</label>
                                <input id="demo-card" type="text" value="4242 4242 4242 4242" class="input bg-paper-deep/40 text-muted" readonly>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label" for="demo-exp">Expiry</label>
                                    <input id="demo-exp" type="text" value="12 / 29" class="input bg-paper-deep/40 text-muted" readonly>
                                </div>
                                <div>
                                    <label class="input-label" for="demo-cvc">CVC</label>
                                    <input id="demo-cvc" type="text" value="424" class="input bg-paper-deep/40 text-muted" readonly>
                                </div>
                            </div>
                            <p class="text-xs leading-relaxed text-faint">
                                This template ships in demo mode — no payment is collected. Swap this section for Stripe Checkout or Paddle when you're ready to take real orders.
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Right: order summary --}}
            <aside class="lg:sticky lg:top-32 lg:self-start">
                <div class="card overflow-hidden">
                    <div class="border-b border-line px-6 py-4">
                        <h2 class="font-display text-lg font-medium">Order Summary</h2>
                    </div>
                    <div class="max-h-[22rem] divide-y divide-line overflow-y-auto px-6">
                        @foreach ($items as $item)
                            <div class="flex items-center gap-4 py-4" wire:key="summary-{{ $item['product']->id }}">
                                <div class="art-frame relative w-14 shrink-0">
                                    <img src="{{ $item['product']->image }}" alt="{{ $item['product']->name }}" class="aspect-4/5 w-full object-cover">
                                    <span class="absolute -top-1.5 -right-1.5 flex size-5 items-center justify-center rounded-full bg-ink text-[0.625rem] font-bold text-paper">{{ $item['quantity'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold">{{ $item['product']->name }}</p>
                                    <p class="truncate text-xs text-muted">{{ $item['product']->creator }}</p>
                                </div>
                                <p class="text-sm font-medium tabular-nums">{{ Product::formatCents($item['product']->price * $item['quantity']) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 border-t border-line bg-paper-deep/30 px-6 py-5 text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="font-medium text-ink tabular-nums">{{ Product::formatCents($cart->subtotal()) }}</span>
                        </div>
                        <div class="flex justify-between text-muted">
                            <span>Shipping</span>
                            <span class="font-medium {{ $cart->shipping() === 0 ? 'text-moss' : 'text-ink' }} tabular-nums">{{ $cart->shipping() === 0 ? 'Free' : Product::formatCents($cart->shipping()) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-line pt-3 text-base font-bold">
                            <span>Total</span>
                            <span class="tabular-nums">{{ Product::formatCents($cart->total()) }}</span>
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg mt-4 w-full">
                            <span wire:loading.remove wire:target="placeOrder" class="flex items-center gap-2">
                                <x-ui.icon name="lock" class="size-4" />
                                Place Order — {{ Product::formatCents($cart->total()) }}
                            </span>
                            <span wire:loading wire:target="placeOrder">Placing your order…</span>
                        </button>
                        <p class="pt-2 text-center text-xs text-faint">30-day returns · Ships within 24 hours</p>
                    </div>
                </div>
            </aside>
        </form>
    @endif
</div>
