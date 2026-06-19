<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'creator',
        'image',
        'unit_price',
        'quantity',
    ];

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lineTotal(): int
    {
        return $this->unit_price * $this->quantity;
    }

    public function formattedUnitPrice(): string
    {
        return Product::formatCents($this->unit_price);
    }

    public function formattedLineTotal(): string
    {
        return Product::formatCents($this->lineTotal());
    }
}
