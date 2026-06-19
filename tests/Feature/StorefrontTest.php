<?php

use App\Enums\ProductCategory;
use App\Models\Product;

it('renders the home page with featured products', function () {
    $featured = Product::factory()->featured()->create(['name' => 'The Lighthouse Variations']);
    Product::factory()->count(3)->featured()->create();

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Books, vinyl,')
        ->assertSee($featured->name);
});

it('renders the shop page with all products', function () {
    $products = Product::factory()->count(3)->create();

    $response = $this->get('/shop')->assertSuccessful();

    foreach ($products as $product) {
        $response->assertSee($product->name);
    }
});

it('renders category pages for every category', function (ProductCategory $category) {
    $product = Product::factory()->create(['category' => $category]);

    $this->get("/shop/{$category->value}")
        ->assertSuccessful()
        ->assertSee($category->label())
        ->assertSee($product->name);
})->with(ProductCategory::cases());

it('returns 404 for an unknown category', function () {
    $this->get('/shop/cassettes')->assertNotFound();
});

it('renders a product detail page', function () {
    $product = Product::factory()->create([
        'name' => 'Golden Hour Static',
        'tagline' => 'Warm analog synths for the last hour of daylight.',
    ]);

    $this->get("/products/{$product->slug}")
        ->assertSuccessful()
        ->assertSee('Golden Hour Static')
        ->assertSee($product->creator)
        ->assertSee($product->formattedPrice());
});

it('shows sold out state on the product page', function () {
    $product = Product::factory()->outOfStock()->create();

    $this->get("/products/{$product->slug}")
        ->assertSuccessful()
        ->assertSee('Sold Out');
});

it('renders the about page', function () {
    $this->get('/about')
        ->assertSuccessful()
        ->assertSee('Our story');
});
