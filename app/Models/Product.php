<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'stock',
        'sku',
        'images',
        'is_active',
        'is_featured',
        'tags',
        'weight',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'is_featured'   => 'boolean',
        'images'        => 'array',  // stored as JSON
        'tags'          => 'array',  // stored as JSON
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // ─── Mutators / Accessors ─────────────────────────────────────────────────

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Get first image or placeholder.
     */
    public function getThumbnailAttribute(): string
    {
        $images = $this->images ?? [];
        return count($images) > 0
            ? asset('storage/' . $images[0])
            : asset('images/product-placeholder.jpg');
    }

    /**
     * Get all image URLs.
     */
    public function getImageUrlsAttribute(): array
    {
        return collect($this->images ?? [])->map(fn ($img) => asset('storage/' . $img))->toArray();
    }

    /**
     * Formatted price string.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    /**
     * Is this product on sale?
     */
    public function getOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    /**
     * Discount percentage.
     */
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->on_sale) return 0;
        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
