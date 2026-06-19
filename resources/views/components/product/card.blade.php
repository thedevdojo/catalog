@props(['product', 'eager' => false])

<div class="group relative flex flex-col">
    <a href="{{ route('products.show', ['product' => $product]) }}" wire:navigate class="art-frame relative block aspect-4/5">
        <img
            src="{{ $product->image }}"
            alt="{{ $product->name }}"
            class="size-full object-cover"
            @if (! $eager) loading="lazy" @endif
        >

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col items-start gap-1.5">
            @if (! $product->isInStock())
                <span class="badge badge-neutral bg-paper/90">Sold Out</span>
            @elseif ($product->isOnSale())
                <span class="badge badge-sale bg-paper/90">Sale</span>
            @elseif ($product->isNew())
                <span class="badge badge-new bg-paper/90">New</span>
            @endif
        </div>
    </a>

    {{-- Quick add --}}
    @if ($product->isInStock())
        <div class="absolute right-3 bottom-[5.5rem] translate-y-1 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-y-0 group-hover:opacity-100 max-lg:hidden">
            <livewire:products.add-to-cart :$product variant="icon" :wire:key="'quick-add-'.$product->id" />
        </div>
    @endif

    <div class="mt-3.5 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[0.6875rem] font-bold tracking-[0.14em] text-faint uppercase">{{ $product->category->singularLabel() }}</p>
            <h3 class="mt-1 truncate font-display text-[1.0625rem] leading-snug font-medium">
                <a href="{{ route('products.show', ['product' => $product]) }}" wire:navigate>{{ $product->name }}</a>
            </h3>
            <p class="mt-0.5 truncate text-sm text-muted">{{ $product->creator }}</p>
        </div>
        <div class="shrink-0 pt-4 text-right">
            <p class="text-sm font-semibold">{{ $product->formattedPrice() }}</p>
            @if ($product->isOnSale())
                <p class="text-xs text-faint line-through">{{ $product->formattedComparePrice() }}</p>
            @endif
        </div>
    </div>
</div>
