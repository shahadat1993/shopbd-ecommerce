<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'title', 'comment', 'is_approved'];
    protected $casts = ['is_approved' => 'boolean', 'rating' => 'integer'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function scopeApproved($query) { return $query->where('is_approved', true); }
}
