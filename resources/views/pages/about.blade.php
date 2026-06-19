<?php

use function Laravel\Folio\name;

name('about');

?>

<x-layouts.marketing title="Our Story" description="Catalog is a small shop with strong opinions — here's how we choose what makes the shelf.">
    {{-- Hero --}}
    <section class="mx-auto max-w-4xl px-4 pt-20 pb-16 text-center sm:px-6 lg:px-8">
        <p class="eyebrow animate-enter-up">Our story</p>
        <h1 class="display mt-5 text-4xl sm:text-6xl animate-enter-up" style="animation-delay: 0.08s">
            A shop the size of <em class="text-accent">one good bookshelf</em>.
        </h1>
        <p class="mx-auto mt-7 max-w-2xl text-lg leading-relaxed text-pretty text-muted animate-enter-up" style="animation-delay: 0.16s">
            Catalog started as a spreadsheet three friends kept of things they kept recommending to each other — the book you press into someone's hands, the record you put on for guests, the game that rescues a rainy Sunday. Eventually the spreadsheet became a shop. The standard never changed: if we wouldn't replace it after losing it, we don't sell it.
        </p>
    </section>

    {{-- Stats strip --}}
    <section class="border-y border-line bg-surface">
        <div class="mx-auto grid max-w-5xl grid-cols-3 divide-x divide-line px-4 sm:px-6">
            <div class="py-10 text-center">
                <p class="display text-4xl sm:text-5xl">{{ App\Models\Product::count() }}</p>
                <p class="mt-2 text-[0.8125rem] font-medium text-muted">Titles on the shelf</p>
            </div>
            <div class="py-10 text-center">
                <p class="display text-4xl sm:text-5xl">3</p>
                <p class="mt-2 text-[0.8125rem] font-medium text-muted">Categories, by design</p>
            </div>
            <div class="py-10 text-center">
                <p class="display text-4xl sm:text-5xl">0</p>
                <p class="mt-2 text-[0.8125rem] font-medium text-muted">Things we haven't tried</p>
            </div>
        </div>
    </section>

    {{-- How we curate --}}
    <section id="values" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
        <div class="max-w-2xl">
            <p class="eyebrow">How we curate</p>
            <h2 class="display mt-3 text-3xl sm:text-4xl">Three rules, ruthlessly applied</h2>
        </div>
        <div class="mt-12 grid gap-5 md:grid-cols-3">
            <div class="card p-7">
                <span class="flex size-11 items-center justify-center rounded-full bg-accent-soft text-accent">
                    <x-ui.icon name="book" class="size-5" />
                </span>
                <h3 class="mt-5 font-display text-xl font-medium">We use everything first</h3>
                <p class="mt-2.5 text-sm leading-relaxed text-muted">Every book gets read, every record gets spun, every game hits at least three game nights. Months pass between "interesting" and "in stock". That's the point.</p>
            </div>
            <div class="card p-7">
                <span class="flex size-11 items-center justify-center rounded-full bg-moss-soft text-moss">
                    <x-ui.icon name="record" class="size-5" />
                </span>
                <h3 class="mt-5 font-display text-xl font-medium">Small beats endless</h3>
                <p class="mt-2.5 text-sm leading-relaxed text-muted">A shelf you can actually browse is a feature, not a limitation. When something new comes in, something else usually has to leave. The bar only rises.</p>
            </div>
            <div class="card p-7">
                <span class="flex size-11 items-center justify-center rounded-full bg-ink/8 text-ink">
                    <x-ui.icon name="dice" class="size-5" />
                </span>
                <h3 class="mt-5 font-display text-xl font-medium">Honest words only</h3>
                <p class="mt-2.5 text-sm leading-relaxed text-muted">Every description is written by the person who championed the item — including what it isn't. If a game runs long or a record is an acquired taste, we say so.</p>
            </div>
        </div>
    </section>

    {{-- Shipping & returns --}}
    <section id="shipping" class="scroll-mt-24 border-t border-line bg-paper-deep/50">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <p class="eyebrow">The practical bits</p>
                    <h2 class="display mt-3 text-3xl sm:text-4xl">Shipping & returns</h2>
                    <p class="mt-5 max-w-md leading-relaxed text-muted">Buying from a small shop shouldn't feel like a compromise. Here's exactly what to expect after you hit "place order".</p>
                </div>
                <dl class="space-y-7">
                    <div class="flex gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface text-ink shadow-sm">
                            <x-ui.icon name="truck" class="size-5" />
                        </span>
                        <div>
                            <dt class="font-display text-lg font-medium">Shipping</dt>
                            <dd class="mt-1 text-sm leading-relaxed text-muted">Orders ship within 24 hours, Monday through Saturday. U.S. shipping is a flat $8, and free once your order passes $75. Records ship in stiffened mailers; books get corner protection. We're not casual about this.</dd>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface text-ink shadow-sm">
                            <x-ui.icon name="returns" class="size-5" />
                        </span>
                        <div>
                            <dt class="font-display text-lg font-medium">Returns</dt>
                            <dd class="mt-1 text-sm leading-relaxed text-muted">30 days, no questions, no restocking fee. Email us, we send a label, you get refunded when it arrives. Returned items in good shape go to the local library or game café — never the landfill.</dd>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface text-ink shadow-sm">
                            <x-ui.icon name="mail" class="size-5" />
                        </span>
                        <div>
                            <dt class="font-display text-lg font-medium">Questions</dt>
                            <dd class="mt-1 text-sm leading-relaxed text-muted">A human answers <a href="mailto:hello@catalog.test" class="font-medium text-ink underline decoration-line-strong underline-offset-2 transition hover:decoration-ink">hello@catalog.test</a> within one business day — usually the same person who picked the thing you're asking about.</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-ink text-paper">
        <div class="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:py-24">
            <h2 class="display text-3xl text-paper sm:text-5xl">Come find your next favorite thing.</h2>
            <a href="{{ route('shop') }}" wire:navigate class="btn btn-accent btn-lg mt-8">
                Browse the Catalog
                <x-ui.icon name="arrow-right" class="size-4.5" />
            </a>
        </div>
    </section>
</x-layouts.marketing>
