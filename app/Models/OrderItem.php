<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',   // snapshot at time of purchase
        'product_image',  // snapshot at time of purchase
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): string
    {
        return $this->product_image
            ? asset('storage/' . $this->product_image)
            : asset('images/product-placeholder.jpg');
    }
}
