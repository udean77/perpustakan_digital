<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
        'hp',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     // Mengambil relasi seller (penjual)
    public function ordersAsSeller()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // Mengambil relasi orders (pembeli)
    public function ordersAsBuyer()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
    
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function isSeller()
    {
        return $this->role === 'seller';  // Misalnya, menggunakan role untuk menentukan apakah user adalah seller
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

   public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistBooks()
    {
        // Mendapatkan buku yang di wishlist user
        return $this->belongsToMany(Book::class, 'wishlists', 'user_id', 'book_id');
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function chatAnalytics()
    {
        return $this->hasMany(ChatAnalytics::class);
    }

    public function chatHistories()
    {
        return $this->hasMany(ChatHistory::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default-avatar.png');
    }

}
