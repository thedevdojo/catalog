<?php

use App\Enums\ProductCategory;
use App\Models\Product;

use function Laravel\Folio\name;

name('home');

?>

@php
    $featured = Product::featured()->orderByDesc('released_at')->take(4)->get();
    $newArrivals = Product::orderByDesc('released_at')->take(8)->get();
    $categoryShowcase = collect(ProductCategory::cases())->map(fn ($category) => [
        'category' => $category,
        'cover' => Product::inCategory($category)->featured()->first() ?? Product::inCategory($category)->latest('released_at')->first(),
        'count' => Product::inCategory($category)->count(),
    ]);
    $heroTiles = collect(ProductCategory::cases())
        ->map(fn ($category) => Product::inCategory($category)->featured()->first() ?? Product::inCategory($category)->first())
        ->filter()
        ->values();
@endphp

<x-layouts.marketing>
    {{-- Hero --}}
    <section class="border-b border-line">
        <div class="mx-auto grid max-w-[90rem] items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[0.88fr_1.12fr] lg:gap-16 lg:px-10 lg:py-20">
            {{-- Copy --}}
            <div class="stagger max-w-xl">
                <p class="eyebrow">Curated Collection</p>
                <h1 class="display mt-6 text-[3.4rem] leading-[1.04] sm:text-7xl lg:text-[5.2rem]">
                    Books, vinyl,<br>and games.
                </h1>
                <div class="display-rule mt-8"></div>
                <p class="mt-8 max-w-md text-lg leading-relaxed text-pretty text-muted">
                    Thoughtfully chosen items for curious minds and meaningful moments. Every title on the shelf is read, played, and loved first.
                </p>
                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a href="{{ route('shop') }}" wire:navigate class="btn btn-primary btn-lg">Shop the collection</a>
                    <a href="{{ route('shop', ['sort' => 'newest']) }}" class="btn btn-outline btn-lg">New arrivals</a>
                </div>

                {{-- Category quick links --}}
                <div class="mt-14 flex items-center">
                    @foreach ([['books', 'book', 'Books'], ['vinyl', 'record', 'Vinyl'], ['board-games', 'dice', 'Board Games']] as $i => [$slug, $icon, $label])
                        <a
                            href="{{ route('shop.category', ['category' => $slug]) }}"
                            wire:navigate
                            class="group flex flex-col items-center gap-3 text-center {{ $i > 0 ? 'border-l border-line' : '' }} {{ $i === 0 ? 'pr-8 sm:pr-10' : ($i === 2 ? 'pl-8 sm:pl-10' : 'px-8 sm:px-10') }}"
                        >
                            <x-ui.icon name="{{ $icon }}" class="size-6 text-ink-soft transition-colors group-hover:text-accent" />
                            <span class="text-[0.6875rem] font-semibold tracking-[0.18em] text-ink-soft uppercase transition-colors group-hover:text-ink">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Editorial mosaic --}}
            @if ($heroTiles->isNotEmpty())
                <div class="grid grid-cols-2 grid-rows-2 gap-4 lg:gap-5">
                    @foreach ($heroTiles->take(3) as $i => $tile)
                        <a
                            href="{{ route('products.show', ['product' => $tile]) }}"
                            wire:navigate
                            class="art-frame block animate-enter-fade rounded-md {{ $i === 0 ? 'row-span-2' : '' }}"
                            @if ($i > 0) style="animation-delay: {{ 0.1 * $i }}s" @endif
                        >
                            <img
                                src="{{ $tile->image }}"
                                alt="{{ $tile->name }}"
                                class="{{ $i === 0 ? 'size-full' : 'aspect-[4/2.95] w-full object-[center_40%]' }} object-cover"
                                @if ($i === 0) fetchpriority="high" @endif
                            >
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Category tiles --}}
    <section class="mx-auto max-w-[90rem] px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
        <div class="flex items-end justify-between gap-6">
            <div>
                <p class="eyebrow">Three shelves, no filler</p>
                <h2 class="display mt-4 text-4xl sm:text-5xl">Pick your obsession</h2>
            </div>
            <a href="{{ route('shop') }}" wire:navigate class="btn btn-outline btn-sm hidden sm:inline-flex">
                Shop everything
                <x-ui.icon name="arrow-right" class="size-4" />
            </a>
        </div>

        <div class="mt-12 grid gap-5 md:grid-cols-3">
            @foreach ($categoryShowcase as $i => $entry)
                <a
                    href="{{ route('shop.category', ['category' => $entry['category']->value]) }}"
                    wire:navigate
                    class="group card relative overflow-hidden p-0 transition-shadow duration-300 hover:shadow-xl"
                >
                    <div class="art-frame aspect-4/5 rounded-none border-0">
                        @if ($entry['cover'])
                            <img src="{{ $entry['cover']->image }}" alt="{{ $entry['category']->label() }}" class="size-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="flex items-center justify-between px-6 py-5">
                        <div>
                            <h3 class="font-display text-2xl">{{ $entry['category']->label() }}</h3>
                            <p class="mt-1 text-sm text-muted">{{ $entry['count'] }} titles · {{ $entry['category']->tagline() }}</p>
                        </div>
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-line-strong transition-all duration-300 group-hover:border-ink group-hover:bg-ink group-hover:text-paper">
                            <x-ui.icon name="arrow-right" class="size-4.5" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured shelf --}}
    <section class="border-y border-line bg-surface">
        <div class="mx-auto max-w-[90rem] px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="eyebrow">Staff picks</p>
                    <h2 class="display mt-4 text-4xl sm:text-5xl">This month's shelf</h2>
                </div>
                <a href="{{ route('shop') }}" wire:navigate class="nav-link hidden text-sm sm:inline-block">View the full catalog</a>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-x-5 gap-y-12 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <x-product.card :$product />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Editorial statement --}}
    <section class="bg-paper-deep/60">
        <div class="mx-auto max-w-4xl px-4 py-24 text-center sm:px-6 lg:py-32">
            <p class="eyebrow">Why a catalog?</p>
            <p class="display mt-8 text-3xl leading-snug sm:text-[2.75rem]">
                "The internet has everything. That's the problem. <em class="text-accent">We sell the shortlist</em> — the {{ Product::count() }} things we'd grab if the shop caught fire."
            </p>
            <div class="display-rule mx-auto mt-10"></div>
            <p class="mt-6 text-xs font-semibold tracking-[0.22em] text-muted uppercase">The Catalog Team</p>
        </div>
    </section>

    {{-- New arrivals rail --}}
    <section class="mx-auto max-w-[90rem] px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
        <div class="flex items-end justify-between gap-6">
            <div>
                <p class="eyebrow">Just landed</p>
                <h2 class="display mt-4 text-4xl sm:text-5xl">New arrivals</h2>
            </div>
            <a href="{{ route('shop', ['sort' => 'newest']) }}" class="btn btn-outline btn-sm hidden sm:inline-flex">
                See all new
                <x-ui.icon name="arrow-right" class="size-4" />
            </a>
        </div>

        <div class="-mx-4 mt-12 flex snap-x snap-mandatory gap-5 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:px-6 lg:-mx-10 lg:px-10" style="scrollbar-width: thin;">
            @foreach ($newArrivals as $product)
                <div class="w-[16rem] shrink-0 snap-start sm:w-[17.5rem]">
                    <x-product.card :$product />
                </div>
            @endforeach
        </div>
    </section>

    {{-- The promise --}}
    <section class="border-t border-line bg-surface">
        <div class="mx-auto grid max-w-[90rem] gap-10 px-4 py-16 sm:grid-cols-3 sm:px-6 lg:px-10">
            <div class="flex gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-accent-soft text-accent">
                    <x-ui.icon name="truck" class="size-5" />
                </span>
                <div>
                    <h3 class="font-display text-xl">Fast, careful shipping</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-muted">Every order leaves within 24 hours, wrapped like it's a gift. Because it usually is.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-moss-soft text-moss">
                    <x-ui.icon name="returns" class="size-5" />
                </span>
                <div>
                    <h3 class="font-display text-xl">30-day returns</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-muted">Didn't love it? Send it back, no questions. We'll find it a better home.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-ink/8 text-ink">
                    <x-ui.icon name="shield" class="size-5" />
                </span>
                <div>
                    <h3 class="font-display text-xl">Secure checkout</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-muted">Your details stay encrypted, end to end. We only keep what shipping requires.</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.marketing>
