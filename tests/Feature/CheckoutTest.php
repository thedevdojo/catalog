<?php

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('redirects guests to login', function () {
    $this->get('/checkout')->assertRedirect();
});

it('renders the checkout page for authenticated users', function () {
    $product = Product::factory()->create(['stock' => 5]);
    app(Cart::class)->add($product);

    $this->actingAs(User::factory()->create())
        ->get('/checkout')
        ->assertSuccessful()
        ->assertSee('Checkout')
        ->assertSee($product->name);
});

it('places an order and clears the cart', function () {
    Mail::fake();

    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 2000, 'stock' => 5]);
    $cart = app(Cart::class);
    $cart->add($product, 2);

    Livewire::actingAs($user)
        ->test('checkout.form')
        ->set('name', 'Demo Customer')
        ->set('email', 'demo@example.com')
        ->set('address_line1', '418 Juniper Lane')
        ->set('city', 'Portland')
        ->set('state', 'OR')
        ->set('postal_code', '97209')
        ->call('placeOrder')
        ->assertHasNoErrors()
        ->assertDispatched('cart-updated');

    $order = Order::sole();

    expect($order->user_id)->toBe($user->id)
        ->and($order->subtotal)->toBe(4000)
        ->and($order->shipping)->toBe(Cart::FLAT_SHIPPING_RATE)
        ->and($order->total)->toBe(4000 + Cart::FLAT_SHIPPING_RATE)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->quantity)->toBe(2)
        ->and($product->fresh()->stock)->toBe(3)
        ->and($cart->isEmpty())->toBeTrue();

    Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail) => $mail->hasTo('demo@example.com'));
});

it('validates required shipping fields', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);
    app(Cart::class)->add($product);

    Livewire::actingAs($user)
        ->test('checkout.form')
        ->set('name', '')
        ->set('address_line1', '')
        ->call('placeOrder')
        ->assertHasErrors(['name', 'address_line1', 'city', 'state', 'postal_code']);

    expect(Order::count())->toBe(0);
});

it('rejects orders that exceed available stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $cart = app(Cart::class);
    $cart->add($product, 3);

    // Stock drops after the items were carted.
    $product->update(['stock' => 1]);

    Livewire::actingAs($user)
        ->test('checkout.form')
        ->set('name', 'Demo Customer')
        ->set('email', 'demo@example.com')
        ->set('address_line1', '418 Juniper Lane')
        ->set('city', 'Portland')
        ->set('state', 'OR')
        ->set('postal_code', '97209')
        ->call('placeOrder')
        ->assertHasErrors(['cart']);

    expect(Order::count())->toBe(0);
});

it('shows an empty state when checking out with no items', function () {
    Livewire::actingAs(User::factory()->create())
        ->test('checkout.form')
        ->assertSee('nothing to check out');
});
