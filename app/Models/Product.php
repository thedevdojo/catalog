<?php

namespace App\Models;

use App\Enums\ProductCategory;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'creator',
        'tagline',
        'description',
        'details',
        'price',
        'compare_at_price',
        'image',
        'accent',
        'stock',
        'featured',
        'released_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ProductCategory::class,
            'details' => 'array',
            'featured' => 'boolean',
            'released_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Product>  $query
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('featured', true);
    }

    /**
     * @param  Builder<Product>  $query
     */
    public function scopeInCategory(Builder $query, ProductCategory $category): void
    {
        $query->where('category', $category->value);
    }

    public function isOnSale(): bool
    {
        return $this->compare_at_price !== null && $this->compare_at_price > $this->price;
    }

    public function isNew(): bool
    {
        return $this->released_at !== null && $this->released_at->gt(now()->subDays(30));
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= 5;
    }

    public function formattedPrice(): string
    {
        return self::formatCents($this->price);
    }

    public function formattedComparePrice(): ?string
    {
        return $this->compare_at_price ? self::formatCents($this->compare_at_price) : null;
    }

    public static function formatCents(int $cents): string
    {
        $dollars = $cents / 100;

        return $dollars == floor($dollars)
            ? '$'.number_format($dollars, 0)
            : '$'.number_format($dollars, 2);
    }
}
