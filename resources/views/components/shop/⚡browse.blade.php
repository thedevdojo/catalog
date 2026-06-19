<?php

use App\Enums\ProductCategory;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    public ?ProductCategory $category = null;

    #[Url(except: 'featured')]
    public string $sort = 'featured';

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: false)]
    public bool $sale = false;

    public function mount(?ProductCategory $category = null): void
    {
        $this->category = $category;
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function clearSale(): void
    {
        $this->sale = false;
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $query = Product::query();

        if ($this->category !== null) {
            $query->inCategory($this->category);
        }

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(fn ($q) => $q
                ->where('name', 'like', $term)
                ->orWhere('creator', 'like', $term)
                ->orWhere('tagline', 'like', $term));
        }

        if ($this->sale) {
            $query->whereNotNull('compare_at_price')->whereColumn('compare_at_price', '>', 'price');
        }

        match ($this->sort) {
            'newest' => $query->orderByDesc('released_at'),
            'price-asc' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('featured')->orderByDesc('released_at'),
        };

        return [
            'products' => $query->get(),
        ];
    }
};
?>

<div>
    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 border-y border-line py-4 sm:flex-row sm:items-center sm:justify-between">
        <nav class="flex flex-wrap items-center gap-2" aria-label="Categories">
            <a
                href="{{ route('shop') }}"
                wire:navigate
                class="btn btn-pill btn-sm {{ $category === null && ! $sale ? 'btn-primary' : 'btn-outline' }}"
            >Everything</a>
            @foreach (ProductCategory::cases() as $case)
                <a
                    href="{{ route('shop.category', ['category' => $case->value]) }}"
                    wire:navigate
                    class="btn btn-pill btn-sm {{ $category === $case ? 'btn-primary' : 'btn-outline' }}"
                >{{ $case->label() }}</a>
            @endforeach
            @if ($sale)
                <button wire:click="clearSale" class="btn btn-pill btn-sm btn-primary">
                    Sale
                    <x-ui.icon name="x" class="size-3.5" />
                </button>
            @endif
            @if (trim($search) !== '')
                <button wire:click="clearSearch" class="btn btn-pill btn-sm btn-outline">
                    "{{ Str::limit($search, 24) }}"
                    <x-ui.icon name="x" class="size-3.5" />
                </button>
            @endif
        </nav>

        <div class="flex items-center gap-3">
            <p class="text-[0.8125rem] text-muted tabular-nums">{{ $products->count() }} {{ Str::plural('item', $products->count()) }}</p>
            <div class="relative">
                <select
                    wire:model.live="sort"
                    class="h-9 cursor-pointer appearance-none rounded-md border border-line-strong bg-surface pr-9 pl-4 text-[0.8125rem] font-medium focus:border-ink focus:outline-none"
                    aria-label="Sort products"
                >
                    <option value="featured">Featured</option>
                    <option value="newest">Newest</option>
                    <option value="price-asc">Price: Low to High</option>
                    <option value="price-desc">Price: High to Low</option>
                    <option value="name">Alphabetical</option>
                </select>
                <x-ui.icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-3.5 size-3.5 -translate-y-1/2 text-muted" />
            </div>
        </div>
    </div>

    {{-- Grid --}}
    @if ($products->isEmpty())
        <div class="flex flex-col items-center gap-4 py-24 text-center">
            <div class="flex size-16 items-center justify-center rounded-full bg-paper-deep">
                <x-ui.icon name="search" class="size-7 text-faint" />
            </div>
            <p class="font-display text-2xl">Nothing on this shelf</p>
            <p class="max-w-sm text-sm text-muted">
                @if (trim($search) !== '')
                    No matches for "{{ $search }}". Try a different title, author, or artist.
                @else
                    We're restocking this shelf. Browse everything else while you wait.
                @endif
            </p>
            <div class="mt-2 flex gap-2">
                @if (trim($search) !== '')
                    <button wire:click="clearSearch" class="btn btn-outline">Clear search</button>
                @endif
                <a href="{{ route('shop') }}" wire:navigate class="btn btn-primary">Shop Everything</a>
            </div>
        </div>
    @else
        <div
            class="mt-10 grid grid-cols-2 gap-x-5 gap-y-12 md:grid-cols-3 xl:grid-cols-4"
            wire:loading.class="opacity-50"
            wire:target="sort, search, sale"
        >
            @foreach ($products as $product)
                <x-product.card :$product :wire:key="'product-'.$product->id" />
            @endforeach
        </div>
    @endif
</div>
