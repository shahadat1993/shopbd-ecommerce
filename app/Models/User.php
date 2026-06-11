<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'is_active', 'email_verified_at',
    ];

    protected $hidden  = ['password', 'remember_token'];
    protected $casts   = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];
    protected $appends = ['avatar_url'];

    public function orders()    { return $this->hasMany(Order::class); }
    public function reviews()   { return $this->hasMany(Review::class); }
    public function wishlist()  { return $this->hasMany(Wishlist::class); }
    public function addresses() { return $this->hasMany(Address::class); }
    public function cart()      { return $this->hasMany(Cart::class); }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        // Gravatar fallback
        $hash = md5(strtolower(trim($this->email ?? '')));
        return "https://www.gravatar.com/avatar/{$hash}?s=80&d=identicon";
    }
}
