<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'value', 'price_modifier', 'stock', 'sku'];
    protected $casts = ['price_modifier' => 'decimal:2'];

    public function product() { return $this->belongsTo(Product::class); }
}
