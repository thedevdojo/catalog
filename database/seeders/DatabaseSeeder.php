<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProductSeeder::class);

        $demo = User::factory()->create([
            'name' => 'Demo Customer',
            'email' => 'demo@catalog.test',
        ]);

        $this->seedOrderFor($demo, ['the-lighthouse-variations' => 1, 'kyoto-rain' => 1], 'delivered', 18);
        $this->seedOrderFor($demo, ['tidewater' => 1, 'parlor-birds' => 2], 'shipped', 3);
    }

    /**
     * @param  array<string, int>  $slugsWithQuantities
     */
    private function seedOrderFor(User $user, array $slugsWithQuantities, string $status, int $daysAgo): void
    {
        $products = Product::whereIn('slug', array_keys($slugsWithQuantities))->get()->keyBy('slug');

        $subtotal = 0;

        foreach ($slugsWithQuantities as $slug => $quantity) {
            $subtotal += $products[$slug]->price * $quantity;
        }

        $shipping = $subtotal >= Cart::FREE_SHIPPING_THRESHOLD ? 0 : Cart::FLAT_SHIPPING_RATE;

        $order = Order::create([
            'user_id' => $user->id,
            'number' => Order::generateNumber(),
            'status' => $status,
            'email' => $user->email,
            'name' => $user->name,
            'address_line1' => '418 Juniper Lane',
            'address_line2' => 'Apt 2B',
            'city' => 'Portland',
            'state' => 'OR',
            'postal_code' => '97209',
            'country' => 'United States',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'placed_at' => now()->subDays($daysAgo),
        ]);

        foreach ($slugsWithQuantities as $slug => $quantity) {
            $product = $products[$slug];

            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'creator' => $product->creator,
                'image' => $product->image,
                'unit_price' => $product->price,
                'quantity' => $quantity,
            ]);
        }
    }
}
