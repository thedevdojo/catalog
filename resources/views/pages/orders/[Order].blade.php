<?php

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

name('orders.show');
middleware(['auth']);

?>

@php
    abort_unless($order->user_id === auth()->id(), 404);

    $order->load('items');
    $justPlaced = session('order-placed', false);

    $statuses = ['processing', 'shipped', 'delivered'];
    $currentStep = array_search($order->status, $statuses);
    $currentStep = $currentStep === false ? 0 : $currentStep;
@endphp

<x-layouts.marketing :title="'Order '.$order->number" description="Order details and status.">
    <section class="mx-auto max-w-4xl px-4 pt-14 pb-24 sm:px-6 lg:px-8">
        @if ($justPlaced)
            {{-- Confirmation hero --}}
            <div class="animate-enter-up mb-12 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-moss-soft">
                    <x-ui.icon name="check-circle" class="size-8 text-moss" />
                </div>
                <h1 class="display mt-6 text-4xl sm:text-5xl">Order placed. <em class="text-accent">Excellent taste.</em></h1>
                <p class="mx-auto mt-4 max-w-md text-lg text-muted">Thanks, {{ Str::before($order->name, ' ') }}. We're already wrapping it up — a confirmation is on its way to {{ $order->email }}.</p>
            </div>
        @else
            <div class="animate-enter-up">
                <a href="{{ route('orders.index') }}" wire:navigate class="flex w-fit items-center gap-1.5 text-sm font-medium text-muted transition hover:text-ink">
                    <x-ui.icon name="arrow-left" class="size-4" />
                    All purchases
                </a>
                <h1 class="display mt-4 text-4xl sm:text-5xl">Order {{ $order->number }}</h1>
            </div>
        @endif

        <div class="card mt-4 overflow-hidden">
            {{-- Meta bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-paper-deep/40 px-6 py-4">
                <div class="flex items-center gap-4">
                    <p class="font-mono text-sm font-semibold tracking-wide">{{ $order->number }}</p>
                    <span class="badge {{ $order->status === 'delivered' ? 'badge-new' : ($order->status === 'shipped' ? 'badge-sale' : 'badge-neutral') }}">
                        {{ $order->statusLabel() }}
                    </span>
                </div>
                <p class="text-[0.8125rem] text-muted">Placed {{ $order->placed_at->format('M j, Y \a\t g:ia') }}</p>
            </div>

            {{-- Status timeline --}}
            <div class="border-b border-line px-6 py-6">
                <div class="flex items-center">
                    @foreach (['Processing', 'Shipped', 'Delivered'] as $i => $label)
                        <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center gap-1.5 sm:flex-row sm:gap-2.5">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-full {{ $i <= $currentStep ? 'bg-moss text-paper' : 'border border-line-strong text-faint' }}">
                                    @if ($i < $currentStep || ($i === $currentStep && $order->status === 'delivered'))
                                        <x-ui.icon name="check" class="size-3.5" />
                                    @else
                                        <span class="text-[0.6875rem] font-bold">{{ $i + 1 }}</span>
                                    @endif
                                </span>
                                <span class="text-xs font-semibold {{ $i <= $currentStep ? 'text-ink' : 'text-faint' }}">{{ $label }}</span>
                            </div>
                            @if ($i < 2)
                                <div class="mx-3 h-px flex-1 {{ $i < $currentStep ? 'bg-moss' : 'bg-line-strong' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Items --}}
            <div class="divide-y divide-line px-6">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-5 py-5">
                        @if ($item->product)
                            <a href="{{ route('products.show', ['product' => $item->product]) }}" wire:navigate class="art-frame w-16 shrink-0">
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="aspect-4/5 w-full object-cover">
                            </a>
                        @else
                            <div class="art-frame w-16 shrink-0">
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="aspect-4/5 w-full object-cover">
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-display text-base font-medium">{{ $item->name }}</p>
                            <p class="mt-0.5 truncate text-sm text-muted">{{ $item->creator }}</p>
                            <p class="mt-1 text-xs text-faint">Qty {{ $item->quantity }} × {{ $item->formattedUnitPrice() }}</p>
                        </div>
                        <p class="font-medium tabular-nums">{{ $item->formattedLineTotal() }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Totals + address --}}
            <div class="grid gap-8 border-t border-line bg-paper-deep/30 px-6 py-6 sm:grid-cols-2">
                <div>
                    <h2 class="text-[0.6875rem] font-bold tracking-[0.16em] text-faint uppercase">Shipped to</h2>
                    <address class="mt-3 text-sm leading-relaxed text-ink-soft not-italic">
                        {{ $order->name }}<br>
                        {{ $order->address_line1 }}@if ($order->address_line2), {{ $order->address_line2 }}@endif<br>
                        {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                        {{ $order->country }}
                    </address>
                </div>
                <div class="space-y-1.5 text-sm sm:text-right">
                    <div class="flex justify-between sm:justify-end sm:gap-10">
                        <span class="text-muted">Subtotal</span>
                        <span class="font-medium tabular-nums">{{ $order->formattedSubtotal() }}</span>
                    </div>
                    <div class="flex justify-between sm:justify-end sm:gap-10">
                        <span class="text-muted">Shipping</span>
                        <span class="font-medium tabular-nums">{{ $order->formattedShipping() }}</span>
                    </div>
                    <div class="flex justify-between border-t border-line pt-2.5 text-base font-bold sm:justify-end sm:gap-10">
                        <span>Total</span>
                        <span class="tabular-nums">{{ $order->formattedTotal() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('shop') }}" wire:navigate class="btn btn-primary">Keep Browsing</a>
            <a href="{{ route('orders.index') }}" wire:navigate class="btn btn-ghost">View all purchases</a>
        </div>
    </section>
</x-layouts.marketing>
