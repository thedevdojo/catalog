<?php

use function Laravel\Folio\name;

name('shop');

?>

<x-layouts.marketing title="Shop Everything" description="Browse the full Catalog shelf — every book, record, and board game we currently stand behind.">
    <section class="mx-auto max-w-7xl px-4 pt-14 pb-20 sm:px-6 lg:px-8">
        <div class="max-w-2xl animate-enter-up">
            <p class="eyebrow">The full shelf</p>
            <h1 class="display mt-3 text-4xl sm:text-5xl">Shop everything</h1>
            <p class="mt-4 text-lg leading-relaxed text-muted">Every title in the catalog, in one place. If it's listed here, someone on the team owns it, loves it, and will defend it at length.</p>
        </div>

        <div class="mt-10">
            <livewire:shop.browse />
        </div>
    </section>
</x-layouts.marketing>
