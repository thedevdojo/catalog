<?php

use App\Models\Product;
use App\Services\Cart;
use Livewire\Livewire;

it('adds a product to the cart via the add-to-cart component', function () {
    $product = Product::factory()->create(['stock' => 10]);

    Livewire::test('products.add-to-cart', ['product' => $product])
        ->call('add')
        ->assertDispatched('cart-updated')
        ->assertDispatched('open-cart');

    expect(app(Cart::class)->count())->toBe(1);
});

it('adds the selected quantity to the cart', function () {
    $product = Product::factory()->create(['stock' => 10]);

    Livewire::test('products.add-to-cart', ['product' => $product])
        ->call('incrementQuantity')
        ->call('incrementQuantity')
        ->call('add');

    expect(app(Cart::class)->count())->toBe(3);
});

it('clamps cart quantity to available stock', function () {
    $product = Product::factory()->create(['stock' => 2]);
    $cart = app(Cart::class);

    $cart->add($product, 5);

    expect($cart->count())->toBe(2);
});

it('shows cart contents in the drawer', function () {
    $product = Product::factory()->create(['stock' => 10]);
    app(Cart::class)->add($product);

    Livewire::test('cart.drawer')
        ->assertSee($product->name)
        ->assertSee($product->creator);
});

it('updates quantities from the drawer', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $cart = app(Cart::class);
    $cart->add($product);

    Livewire::test('cart.drawer')
        ->call('increment', $product->id)
        ->assertDispatched('cart-updated');

    expect($cart->count())->toBe(2);
});

it('removes an item when quantity is decremented to zero', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $cart = app(Cart::class);
    $cart->add($product);

    Livewire::test('cart.drawer')->call('decrement', $product->id);

    expect($cart->isEmpty())->toBeTrue();
});

it('removes items from the drawer', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $cart = app(Cart::class);
    $cart->add($product);

    Livewire::test('cart.drawer')
        ->call('removeItem', $product->id)
        ->assertSee('Your cart is empty');

    expect($cart->isEmpty())->toBeTrue();
});

it('shows the cart count in the nav counter', function () {
    $product = Product::factory()->create(['stock' => 10]);
    app(Cart::class)->add($product, 2);

    Livewire::test('cart.counter')->assertSet('count', 2);
});

it('calculates free shipping over the threshold', function () {
    $product = Product::factory()->create(['price' => 8000, 'stock' => 10]);
    $cart = app(Cart::class);
    $cart->add($product);

    expect($cart->shipping())->toBe(0)
        ->and($cart->hasFreeShipping())->toBeTrue()
        ->and($cart->total())->toBe(8000);
});

it('charges flat shipping under the threshold', function () {
    $product = Product::factory()->create(['price' => 2000, 'stock' => 10]);
    $cart = app(Cart::class);
    $cart->add($product);

    expect($cart->shipping())->toBe(Cart::FLAT_SHIPPING_RATE)
        ->and($cart->amountUntilFreeShipping())->toBe(5500);
});
