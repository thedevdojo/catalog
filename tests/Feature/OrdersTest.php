<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

function createOrderFor(User $user): Order
{
    $product = Product::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'number' => Order::generateNumber(),
        'status' => 'processing',
        'email' => $user->email,
        'name' => $user->name,
        'address_line1' => '418 Juniper Lane',
        'city' => 'Portland',
        'state' => 'OR',
        'postal_code' => '97209',
        'country' => 'United States',
        'subtotal' => $product->price,
        'shipping' => 800,
        'total' => $product->price + 800,
        'placed_at' => now(),
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'name' => $product->name,
        'creator' => $product->creator,
        'image' => $product->image,
        'unit_price' => $product->price,
        'quantity' => 1,
    ]);

    return $order;
}

it('redirects guests away from purchases', function () {
    $this->get('/orders')->assertRedirect();
});

it('lists the user\'s purchases', function () {
    $user = User::factory()->create();
    $order = createOrderFor($user);

    $this->actingAs($user)
        ->get('/orders')
        ->assertSuccessful()
        ->assertSee($order->number)
        ->assertSee('My purchases');
});

it('shows an empty state with no purchases', function () {
    $this->actingAs(User::factory()->create())
        ->get('/orders')
        ->assertSuccessful()
        ->assertSee('No orders yet');
});

it('shows an order detail page', function () {
    $user = User::factory()->create();
    $order = createOrderFor($user);

    $this->actingAs($user)
        ->get("/orders/{$order->number}")
        ->assertSuccessful()
        ->assertSee($order->number)
        ->assertSee($order->items->first()->name);
});

it('blocks access to another user\'s order', function () {
    $owner = User::factory()->create();
    $order = createOrderFor($owner);

    $this->actingAs(User::factory()->create())
        ->get("/orders/{$order->number}")
        ->assertNotFound();
});
