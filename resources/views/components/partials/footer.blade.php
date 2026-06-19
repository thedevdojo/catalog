<footer class="border-t border-line bg-paper-deep/40">
    <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-10">
        {{-- Newsletter band --}}
        <div class="flex flex-col gap-8 border-b border-line py-16 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-md">
                <p class="eyebrow">The Shop Notes</p>
                <h2 class="display mt-4 text-3xl sm:text-4xl">One good email,<br>every other Friday.</h2>
                <p class="mt-4 text-sm leading-relaxed text-muted">What we're reading, spinning, and playing — plus first dibs on restocks. No noise, unsubscribe anytime.</p>
            </div>
            <form
                class="flex w-full max-w-md gap-2"
                x-data="{ email: '' }"
                x-on:submit.prevent="if (email) { $dispatch('toast', { message: 'You\'re on the list. Talk soon.' }); email = ''; }"
            >
                <input
                    type="email"
                    required
                    placeholder="you@example.com"
                    x-model="email"
                    class="input h-12"
                >
                <button type="submit" class="btn btn-primary h-12 shrink-0 px-6">Subscribe</button>
            </form>
        </div>

        {{-- Link columns --}}
        <div class="grid grid-cols-2 gap-10 py-14 sm:grid-cols-4">
            <div>
                <p class="text-[0.6875rem] font-semibold tracking-[0.2em] text-faint uppercase">Shop</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('shop') }}" wire:navigate class="text-ink-soft transition hover:text-ink">Everything</a></li>
                    <li><a href="{{ route('shop.category', ['category' => 'books']) }}" wire:navigate class="text-ink-soft transition hover:text-ink">Books</a></li>
                    <li><a href="{{ route('shop.category', ['category' => 'vinyl']) }}" wire:navigate class="text-ink-soft transition hover:text-ink">Vinyl</a></li>
                    <li><a href="{{ route('shop.category', ['category' => 'board-games']) }}" wire:navigate class="text-ink-soft transition hover:text-ink">Board Games</a></li>
                    <li><a href="{{ route('shop', ['sale' => 1]) }}" class="text-ink-soft transition hover:text-ink">Sale</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[0.6875rem] font-semibold tracking-[0.2em] text-faint uppercase">Company</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" wire:navigate class="text-ink-soft transition hover:text-ink">Our Story</a></li>
                    <li><a href="{{ route('about') }}#values" wire:navigate class="text-ink-soft transition hover:text-ink">How We Curate</a></li>
                    <li><a href="https://devdojo.com" target="_blank" rel="noopener" class="text-ink-soft transition hover:text-ink">Built on DevDojo</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[0.6875rem] font-semibold tracking-[0.2em] text-faint uppercase">Support</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}#shipping" wire:navigate class="text-ink-soft transition hover:text-ink">Shipping & Returns</a></li>
                    <li><a href="mailto:hello@catalog.test" class="text-ink-soft transition hover:text-ink">Contact Us</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}" wire:navigate class="text-ink-soft transition hover:text-ink">My Purchases</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-ink-soft transition hover:text-ink">Sign In</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <p class="text-[0.6875rem] font-semibold tracking-[0.2em] text-faint uppercase">The Promise</p>
                <ul class="mt-4 space-y-3 text-sm text-ink-soft">
                    <li class="flex items-center gap-2.5"><x-ui.icon name="truck" class="size-4.5 text-faint" /> Free shipping over $75</li>
                    <li class="flex items-center gap-2.5"><x-ui.icon name="returns" class="size-4.5 text-faint" /> 30-day easy returns</li>
                    <li class="flex items-center gap-2.5"><x-ui.icon name="shield" class="size-4.5 text-faint" /> Secure checkout</li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col items-center justify-between gap-4 border-t border-line py-7 sm:flex-row">
            <x-logo class="text-xl" />
            <p class="text-xs text-faint">© {{ date('Y') }} Catalog. A demo storefront template — no real orders are fulfilled.</p>
        </div>
    </div>
</footer>
