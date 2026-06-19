<?php

use function Laravel\Folio\name;

name('products.show');

?>

@php
    $related = App\Models\Product::inCategory($product->category)
        ->where('id', '!=', $product->id)
        ->inRandomOrder()
        ->take(4)
        ->get();
@endphp

<x-layouts.marketing :title="$product->name" :description="$product->tagline">
    <section class="mx-auto max-w-7xl px-4 pt-10 pb-20 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-[0.8125rem] text-muted" aria-label="Breadcrumb">
            <a href="{{ route('shop') }}" wire:navigate class="transition hover:text-ink">Shop</a>
            <span class="text-faint">/</span>
            <a href="{{ route('shop.category', ['category' => $product->category->value]) }}" wire:navigate class="transition hover:text-ink">{{ $product->category->label() }}</a>
            <span class="text-faint">/</span>
            <span class="truncate font-medium text-ink">{{ $product->name }}</span>
        </nav>

        <div class="mt-8 grid gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
            {{-- Artwork --}}
            <div class="lg:sticky lg:top-32 lg:self-start">
                <div class="art-frame relative animate-enter-fade">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="aspect-4/5 w-full object-cover" fetchpriority="high">
                    <div class="absolute top-4 left-4 flex flex-col items-start gap-1.5">
                        @if (! $product->isInStock())
                            <span class="badge badge-neutral bg-paper/90">Sold Out</span>
                        @elseif ($product->isOnSale())
                            <span class="badge badge-sale bg-paper/90">Sale</span>
                        @elseif ($product->isNew())
                            <span class="badge badge-new bg-paper/90">New</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="stagger">
                <p class="eyebrow">{{ $product->category->singularLabel() }}</p>
                <h1 class="display mt-3 text-4xl sm:text-[3.25rem]">{{ $product->name }}</h1>
                <p class="mt-3 text-lg text-muted">
                    {{ $product->category->creatorLabel() }} · <span class="font-medium text-ink-soft">{{ $product->creator }}</span>
                </p>

                <div class="mt-6 flex items-baseline gap-3">
                    <p class="font-display text-3xl font-semibold">{{ $product->formattedPrice() }}</p>
                    @if ($product->isOnSale())
                        <p class="text-lg text-faint line-through">{{ $product->formattedComparePrice() }}</p>
                        <span class="badge badge-sale">Save {{ App\Models\Product::formatCents($product->compare_at_price - $product->price) }}</span>
                    @endif
                </div>

                <p class="mt-6 border-l-2 border-accent pl-4 font-display text-lg leading-relaxed font-medium italic text-ink-soft">
                    {{ $product->tagline }}
                </p>

                <div class="mt-8">
                    <livewire:products.add-to-cart :$product />
                </div>

                <div class="mt-10 space-y-5 border-t border-line pt-8">
                    <h2 class="text-[0.6875rem] font-bold tracking-[0.16em] text-faint uppercase">Why we stock it</h2>
                    <p class="text-[0.9375rem] leading-relaxed text-pretty text-ink-soft">{{ $product->description }}</p>
                </div>

                @if ($product->details)
                    <div class="mt-8 border-t border-line pt-8">
                        <h2 class="text-[0.6875rem] font-bold tracking-[0.16em] text-faint uppercase">The details</h2>
                        <dl class="mt-4 divide-y divide-line">
                            @foreach ($product->details as $detail)
                                <div class="flex items-center justify-between py-2.5 text-sm">
                                    <dt class="text-muted">{{ $detail['label'] }}</dt>
                                    <dd class="font-medium">{{ $detail['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                <div class="mt-8 grid grid-cols-3 gap-3 border-t border-line pt-8 text-center">
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.icon name="truck" class="size-5 text-muted" />
                        <p class="text-xs font-medium text-muted">Free over $75</p>
                    </div>
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.icon name="returns" class="size-5 text-muted" />
                        <p class="text-xs font-medium text-muted">30-day returns</p>
                    </div>
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.icon name="package" class="size-5 text-muted" />
                        <p class="text-xs font-medium text-muted">Ships in 24h</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <div class="mt-24 border-t border-line pt-16">
                <div class="flex items-end justify-between gap-6">
                    <div>
                        <p class="eyebrow">Keep browsing</p>
                        <h2 class="display mt-3 text-3xl">More {{ strtolower($product->category->label()) }} we love</h2>
                    </div>
                    <a href="{{ route('shop.category', ['category' => $product->category->value]) }}" wire:navigate class="nav-link hidden text-sm sm:inline-block">View all {{ strtolower($product->category->label()) }}</a>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-x-5 gap-y-10 lg:grid-cols-4">
                    @foreach ($related as $relatedProduct)
                        <x-product.card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.marketing>
