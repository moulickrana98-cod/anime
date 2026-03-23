<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Order status constants for consistent usage across the app.
     */
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUNDED   = 'refunded';

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'currency',
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'notes',
        // Shipping address snapshot
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax'      => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Generate a unique, readable order number.
     */
    public static function generateOrderNumber(): string
    {
        return 'ANI-' . strtoupper(uniqid());
    }

    /**
     * Human-readable status label with color class.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING    => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-800'],
            self::STATUS_PROCESSING => ['label' => 'Processing', 'class' => 'bg-blue-100 text-blue-800'],
            self::STATUS_SHIPPED    => ['label' => 'Shipped',    'class' => 'bg-purple-100 text-purple-800'],
            self::STATUS_DELIVERED  => ['label' => 'Delivered',  'class' => 'bg-green-100 text-green-800'],
            self::STATUS_CANCELLED  => ['label' => 'Cancelled',  'class' => 'bg-red-100 text-red-800'],
            self::STATUS_REFUNDED   => ['label' => 'Refunded',   'class' => 'bg-gray-100 text-gray-800'],
            default                 => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total, 2);
    }

    /**
     * All valid transitions from the current status.
     */
    public function getAllowedStatusTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING    => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
            self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
            self::STATUS_SHIPPED    => [self::STATUS_DELIVERED],
            default                 => [],
        };
    }
}
