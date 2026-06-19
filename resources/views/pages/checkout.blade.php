<?php

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

name('checkout');
middleware(['auth']);

?>

<x-layouts.marketing title="Checkout" description="Secure checkout — review your order and shipping details.">
    <section class="mx-auto max-w-7xl px-4 pt-14 pb-24 sm:px-6 lg:px-8">
        <div class="animate-enter-up">
            <p class="eyebrow">Almost there</p>
            <h1 class="display mt-3 text-4xl sm:text-5xl">Checkout</h1>
        </div>

        <div class="mt-12">
            <livewire:checkout.form />
        </div>
    </section>
</x-layouts.marketing>
