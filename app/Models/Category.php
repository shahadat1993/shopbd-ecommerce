<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'image', 'parent_id', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('is_active', true)->orderBy('sort_order');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }
    // এই মেথডটির ক্লোজিং ব্র্যাকেট (}) মিসিং ছিল, সেটি নিচে যুক্ত করা হলো
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTotalProductsCountAttribute()
    {
        if ($this->parent_id === null) {
            $childIds = $this->children()->pluck('id');
            return \App\Models\Product::active()->whereIn('category_id', $childIds->push($this->id))->count();
        }

        return \App\Models\Product::active()->where('category_id', $this->id)->count();
    }
}
