<?php
// ============ Cart ============
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'product_id', 'variant', 'qty', 'price'];
    protected $casts = ['price' => 'decimal:2', 'variant' => 'array'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function getSubtotalAttribute() { return $this->price * $this->qty; }
}
