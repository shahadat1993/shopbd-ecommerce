<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUNDED   = 'refunded';

    const PAYMENT_PENDING   = 'pending';
    const PAYMENT_PAID      = 'paid';
    const PAYMENT_FAILED    = 'failed';
    const PAYMENT_REFUNDED  = 'refunded';

    const PAYMENT_METHOD_COD        = 'cod';
    const PAYMENT_METHOD_STRIPE     = 'stripe';
    const PAYMENT_METHOD_SSLCOMMERZ = 'sslcommerz';

    protected $fillable = [
        'order_number', 'user_id',
        'subtotal', 'discount', 'shipping_charge', 'tax', 'total',
        'coupon_id', 'coupon_code', 'coupon_discount',
        'status', 'payment_status', 'payment_method',
        'stripe_session_id', 'sslcommerz_tran_id', 'transaction_id',
        'shipping_name', 'shipping_phone', 'shipping_email',
        'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_zip', 'shipping_country',
        'notes', 'admin_notes',
        'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount'        => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'tax'             => 'decimal:2',
        'total'           => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'shipped_at'      => 'datetime',
        'delivered_at'    => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function items()   { return $this->hasMany(OrderItem::class); }
    public function coupon()  { return $this->belongsTo(Coupon::class); }

    public static function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending'    => 'warning',
            'confirmed'  => 'info',
            'processing' => 'primary',
            'shipped'    => 'secondary',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            'refunded'   => 'dark',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending'  => 'warning',
            'paid'     => 'success',
            'failed'   => 'danger',
            'refunded' => 'info',
        ];
        return $badges[$this->payment_status] ?? 'secondary';
    }

    public function scopeStatus($query, $status) { return $query->where('status', $status); }
}
