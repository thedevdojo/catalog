<div class="sticky top-0 z-50">
    {{-- Announcement ribbon --}}
    <div
        class="relative overflow-hidden bg-ink text-paper"
        x-data="{
            messages: [
                'Free U.S. shipping on orders over $75',
                'Every order ships within 24 hours',
                '30-day returns, no questions asked',
            ],
            index: 0,
        }"
        x-init="setInterval(() => index = (index + 1) % messages.length, 4500)"
    >
        <div class="mx-auto flex h-8 max-w-[90rem] items-center justify-center px-4 text-center">
            <p class="text-[0.6875rem] font-medium tracking-[0.14em] uppercase opacity-80" x-text="messages[index]" x-transition.opacity></p>
        </div>
    </div>

    {{-- Main nav --}}
    <header
        class="border-b border-line bg-paper/95 backdrop-blur-md"
        x-data="{ mobileOpen: false }"
    >
        <div class="mx-auto flex h-[4.5rem] max-w-[90rem] items-center gap-8 px-4 sm:px-6 lg:px-10">
            {{-- Mobile menu button --}}
            <button class="-ml-2 p-2 xl:hidden" x-on:click="mobileOpen = true" aria-label="Open menu">
                <x-ui.icon name="menu" class="size-5.5" />
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" wire:navigate class="shrink-0 max-xl:absolute max-xl:left-1/2 max-xl:-translate-x-1/2" aria-label="Catalog home">
                <x-logo />
            </a>

            {{-- Primary links --}}
            <nav class="hidden items-center gap-7 xl:flex" aria-label="Primary">
                <a href="{{ route('shop.category', ['category' => 'books']) }}" wire:navigate class="nav-link">Books</a>
                <a href="{{ route('shop.category', ['category' => 'vinyl']) }}" wire:navigate class="nav-link">Vinyl</a>
                <a href="{{ route('shop.category', ['category' => 'board-games']) }}" wire:navigate class="nav-link">Board Games</a>
                <a href="{{ route('shop', ['sort' => 'newest']) }}" class="nav-link">New Arrivals</a>
                <a href="{{ route('shop', ['sale' => 1]) }}" class="nav-link">Sale</a>
                <a href="{{ route('about') }}" wire:navigate class="nav-link">About</a>
            </nav>

            {{-- Right: search, account, cart --}}
            <div class="ml-auto flex items-center gap-2">
                <form action="{{ route('shop') }}" method="GET" class="relative hidden lg:block" role="search">
                    <x-ui.icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-faint" />
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products, artists, authors…"
                        class="h-10 w-56 rounded-md border border-transparent bg-ink/[0.045] pr-3 pl-10 text-[0.8125rem] text-ink transition-all duration-300 placeholder:text-faint focus:w-72 focus:border-line-strong focus:bg-surface focus:outline-none xl:w-64"
                    >
                </form>

                @auth
                    <div class="relative" x-data="{ open: false }" x-on:keydown.escape.window="open = false">
                        <button
                            x-on:click="open = !open"
                            class="flex size-10 items-center justify-center rounded-md text-ink-soft transition hover:bg-ink/5 hover:text-ink"
                            aria-label="Account menu"
                        >
                            <x-ui.icon name="user" class="size-5" />
                        </button>
                        <div
                            x-cloak
                            x-show="open"
                            x-on:click.outside="open = false"
                            x-transition:enter="transition duration-150 ease-out"
                            x-transition:enter-start="-translate-y-1 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-100 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="card absolute right-0 z-50 mt-2 w-56 overflow-hidden p-1.5 shadow-xl"
                        >
                            <div class="border-b border-line px-3 pt-2 pb-2.5">
                                <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('orders.index') }}" wire:navigate class="mt-1 flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium text-ink-soft transition hover:bg-ink/5 hover:text-ink">
                                <x-ui.icon name="package" class="size-4.5" /> My Purchases
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 rounded-md px-3 py-2 text-left text-sm font-medium text-ink-soft transition hover:bg-ink/5 hover:text-ink">
                                    <x-ui.icon name="arrow-left" class="size-4.5" /> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="flex size-10 items-center justify-center rounded-md text-ink-soft transition hover:bg-ink/5 hover:text-ink"
                        aria-label="Sign in"
                    >
                        <x-ui.icon name="user" class="size-5" />
                    </a>
                @endauth

                <livewire:cart.counter />
            </div>
        </div>

        {{-- Mobile menu --}}
        <div
            x-cloak
            x-show="mobileOpen"
            class="fixed inset-0 z-[70] xl:hidden"
            x-on:keydown.escape.window="mobileOpen = false"
        >
            <div
                x-show="mobileOpen"
                x-transition.opacity.duration.200ms
                class="absolute inset-0 bg-ink/40 backdrop-blur-sm"
                x-on:click="mobileOpen = false"
            ></div>
            <div
                x-show="mobileOpen"
                x-transition:enter="transition duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="absolute inset-y-0 left-0 flex w-[20rem] max-w-[85vw] flex-col bg-paper shadow-2xl"
            >
                <div class="flex h-[4.5rem] items-center justify-between border-b border-line px-5">
                    <x-logo class="text-2xl" />
                    <button x-on:click="mobileOpen = false" class="-mr-1 p-1.5" aria-label="Close menu">
                        <x-ui.icon name="x" class="size-5.5" />
                    </button>
                </div>
                <div class="p-4">
                    <form action="{{ route('shop') }}" method="GET" class="relative" role="search">
                        <x-ui.icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-faint" />
                        <input
                            type="search"
                            name="search"
                            placeholder="Search the catalog…"
                            class="h-11 w-full rounded-md border border-line-strong bg-surface pr-3 pl-10 text-sm placeholder:text-faint focus:border-ink focus:outline-none"
                        >
                    </form>
                </div>
                <nav class="flex flex-col gap-0.5 px-4">
                    <a href="{{ route('shop') }}" wire:navigate class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">Shop All</a>
                    <a href="{{ route('shop.category', ['category' => 'books']) }}" wire:navigate class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">Books</a>
                    <a href="{{ route('shop.category', ['category' => 'vinyl']) }}" wire:navigate class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">Vinyl</a>
                    <a href="{{ route('shop.category', ['category' => 'board-games']) }}" wire:navigate class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">Board Games</a>
                    <a href="{{ route('shop', ['sort' => 'newest']) }}" class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">New Arrivals</a>
                    <a href="{{ route('shop', ['sale' => 1]) }}" class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">Sale</a>
                    <a href="{{ route('about') }}" wire:navigate class="rounded-md px-3 py-3 font-display text-xl transition hover:bg-ink/5">About</a>
                </nav>
                <div class="mt-auto border-t border-line p-5">
                    @auth
                        <a href="{{ route('orders.index') }}" wire:navigate class="btn btn-outline w-full">My Purchases</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-full">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
</div>
