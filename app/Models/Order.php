<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'status',
        'email',
        'name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'subtotal',
        'shipping',
        'total',
        'placed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'placed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'CAT-'.Str::upper(Str::random(2)).random_int(1000, 9999);
        } while (self::where('number', $number)->exists());

        return $number;
    }

    public function statusLabel(): string
    {
        return Str::title($this->status);
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function formattedSubtotal(): string
    {
        return Product::formatCents($this->subtotal);
    }

    public function formattedShipping(): string
    {
        return $this->shipping === 0 ? 'Free' : Product::formatCents($this->shipping);
    }

    public function formattedTotal(): string
    {
        return Product::formatCents($this->total);
    }
}
