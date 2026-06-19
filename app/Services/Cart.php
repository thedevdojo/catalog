<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class Cart
{
    public const FREE_SHIPPING_THRESHOLD = 7500;

    public const FLAT_SHIPPING_RATE = 800;

    private const SESSION_KEY = 'cart.items';

    /**
     * Add a product to the cart, clamped to available stock.
     */
    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->rawItems();
        $current = $items[$product->id] ?? 0;

        $items[$product->id] = min($current + max($quantity, 1), max($product->stock, 1));

        Session::put(self::SESSION_KEY, $items);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($productId);

            return;
        }

        $items = $this->rawItems();

        if (! isset($items[$productId])) {
            return;
        }

        $stock = Product::find($productId)?->stock ?? $quantity;
        $items[$productId] = min($quantity, max($stock, 1));

        Session::put(self::SESSION_KEY, $items);
    }

    public function remove(int $productId): void
    {
        $items = $this->rawItems();
        unset($items[$productId]);

        Session::put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Cart contents with hydrated products, preserving insertion order.
     *
     * @return Collection<int, array{product: Product, quantity: int}>
     */
    public function items(): Collection
    {
        $raw = $this->rawItems();

        if ($raw === []) {
            return collect();
        }

        $products = Product::whereIn('id', array_keys($raw))->get()->keyBy('id');

        return collect($raw)
            ->map(fn (int $quantity, int $productId) => $products->has($productId)
                ? ['product' => $products[$productId], 'quantity' => $quantity]
                : null)
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return array_sum($this->rawItems());
    }

    public function isEmpty(): bool
    {
        return $this->rawItems() === [];
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum(fn (array $item) => $item['product']->price * $item['quantity']);
    }

    public function shipping(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return $this->subtotal() >= self::FREE_SHIPPING_THRESHOLD ? 0 : self::FLAT_SHIPPING_RATE;
    }

    public function total(): int
    {
        return $this->subtotal() + $this->shipping();
    }

    public function amountUntilFreeShipping(): int
    {
        return max(0, self::FREE_SHIPPING_THRESHOLD - $this->subtotal());
    }

    public function hasFreeShipping(): bool
    {
        return ! $this->isEmpty() && $this->amountUntilFreeShipping() === 0;
    }

    /**
     * @return array<int, int>
     */
    private function rawItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
