<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','description','short_description',
        'price','sale_price','cost_price','sku',
        'stock','min_order_qty','max_order_qty',
        'category_id','brand','weight','dimensions',
        'thumbnail','is_active','is_featured','is_digital',
        'meta_title','meta_description','tags',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active'  => 'boolean',
        'is_featured'=> 'boolean',
        'is_digital' => 'boolean',
        'tags'       => 'array',
    ];

    public function category()   { return $this->belongsTo(Category::class); }
    public function images()     { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function reviews()    { return $this->hasMany(Review::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function wishlist()   { return $this->hasMany(Wishlist::class); }
    public function variants()   { return $this->hasMany(ProductVariant::class); }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/'.$this->thumbnail);
        }
        return asset('images/placeholder-product.svg');
    }

    public function getCurrentPriceAttribute(): float
    {
        return ($this->sale_price && $this->sale_price < $this->price)
            ? (float)$this->sale_price
            : (float)$this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return (int)round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float)($this->reviews()->where('is_approved',true)->avg('rating') ?? 0);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->where('is_approved',true)->count();
    }

    public function isInStock(): bool { return $this->stock > 0; }

    public function scopeActive($q)   { return $q->where('is_active',true); }
    public function scopeFeatured($q) { return $q->where('is_featured',true); }
    public function scopeInStock($q)  { return $q->where('stock','>',0); }
    public function scopeOnSale($q)   { return $q->whereNotNull('sale_price')->whereColumn('sale_price','<','price'); }

    public function getRouteKeyName()
{
    return 'slug';
}
}
