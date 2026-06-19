<?php

use App\Enums\ProductCategory;

use function Laravel\Folio\name;

name('shop.category');

?>

@php
    $categoryEnum = ProductCategory::tryFrom($category) ?? abort(404);
@endphp

<x-layouts.marketing :title="'Shop '.$categoryEnum->label()" :description="$categoryEnum->description()">
    <section class="mx-auto max-w-7xl px-4 pt-14 pb-20 sm:px-6 lg:px-8">
        <div class="max-w-2xl animate-enter-up">
            <p class="eyebrow">{{ $categoryEnum->tagline() }}</p>
            <h1 class="display mt-3 text-4xl sm:text-5xl">{{ $categoryEnum->label() }}</h1>
            <p class="mt-4 text-lg leading-relaxed text-muted">{{ $categoryEnum->description() }}</p>
        </div>

        <div class="mt-10">
            <livewire:shop.browse :category="$categoryEnum" />
        </div>
    </section>
</x-layouts.marketing>
