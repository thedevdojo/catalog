<?php

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

name('orders.index');
middleware(['auth']);

?>

@php
    $orders = auth()->user()->orders()->with('items')->latest('placed_at')->get();
@endphp

<x-layouts.marketing title="My Purchases" description="Your order history at Catalog.">
    <section class="mx-auto max-w-4xl px-4 pt-14 pb-24 sm:px-6 lg:px-8">
        <div class="animate-enter-up">
            <p class="eyebrow">Order history</p>
            <h1 class="display mt-3 text-4xl sm:text-5xl">My purchases</h1>
        </div>

        @if ($orders->isEmpty())
            <div class="mt-16 flex flex-col items-center gap-5 rounded-xl border border-dashed border-line-strong py-20 text-center">
                <div class="flex size-16 items-center justify-center rounded-full bg-paper-deep">
                    <x-ui.icon name="package" class="size-7 text-faint" />
                </div>
                <div>
                    <p class="font-display text-xl font-medium">No orders yet</p>
                    <p class="mt-2 max-w-sm text-sm text-muted">When you place your first order, it'll show up here with live status updates.</p>
                </div>
                <a href="{{ route('shop') }}" wire:navigate class="btn btn-primary mt-2">Start Browsing</a>
            </div>
        @else
            <div class="stagger mt-12 space-y-5">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', ['order' => $order]) }}" wire:navigate class="card group block overflow-hidden transition-shadow duration-300 hover:shadow-lg">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-paper-deep/40 px-6 py-4">
                            <div class="flex items-center gap-4">
                                <p class="font-mono text-sm font-semibold tracking-wide">{{ $order->number }}</p>
                                <span class="badge {{ $order->status === 'delivered' ? 'badge-new' : ($order->status === 'shipped' ? 'badge-sale' : 'badge-neutral') }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </div>
                            <p class="text-[0.8125rem] text-muted">Placed {{ $order->placed_at->format('M j, Y') }}</p>
                        </div>
                        <div class="flex items-center justify-between gap-6 px-6 py-5">
                            <div class="flex items-center">
                                @foreach ($order->items->take(4) as $i => $item)
                                    <div class="art-frame w-14 shrink-0 border-2 border-paper {{ $i > 0 ? '-ml-4' : '' }}" style="z-index: {{ 10 - $i }}">
                                        <img src="{{ $item->image }}" alt="{{ $item->name }}" class="aspect-4/5 w-full object-cover">
                                    </div>
                                @endforeach
                                <p class="ml-4 text-sm text-muted">
                                    {{ $order->itemCount() }} {{ Str::plural('item', $order->itemCount()) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="font-display text-lg font-semibold">{{ $order->formattedTotal() }}</p>
                                <span class="flex size-9 items-center justify-center rounded-full border border-line-strong transition-all duration-300 group-hover:border-ink group-hover:bg-ink group-hover:text-paper">
                                    <x-ui.icon name="arrow-right" class="size-4" />
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.marketing>
