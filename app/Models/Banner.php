<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'subtitle', 'image', 'link', 'position', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function scopeActive($query) { return $query->where('is_active', true)->orderBy('sort_order'); }
}
