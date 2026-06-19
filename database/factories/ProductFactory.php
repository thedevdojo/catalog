<?php

namespace Database\Factories;

use App\Enums\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucwords($this->faker->unique()->words(3, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => $this->faker->randomElement(ProductCategory::cases()),
            'creator' => $this->faker->name(),
            'tagline' => $this->faker->sentence(8),
            'description' => $this->faker->paragraphs(2, true),
            'details' => [
                ['label' => 'Format', 'value' => 'Hardcover'],
                ['label' => 'Pages', 'value' => (string) $this->faker->numberBetween(120, 480)],
            ],
            'price' => $this->faker->numberBetween(1200, 9500),
            'compare_at_price' => null,
            'image' => '/images/products/placeholder.svg',
            'accent' => '#1c1917',
            'stock' => $this->faker->numberBetween(3, 40),
            'featured' => false,
            'released_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['featured' => true]);
    }

    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'compare_at_price' => $attributes['price'] + $this->faker->numberBetween(500, 2000),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => ['stock' => 0]);
    }
}
