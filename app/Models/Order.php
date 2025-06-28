<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id', 
        'status',
        'total_amount',
        'shipping_cost',
        'payment_method',
        'shipping_address',
        'ordered_at',
        'redeem_code_id',
        'discount_amount'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);  // Relasi ke user pembeli
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');  // Relasi ke user penjual
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function address()
    {
        return $this->belongsTo(Address::class); // ⬅️ ini penting!
    }

    public function redeemCode()
    {
        return $this->belongsTo(RedeemCode::class);
    }
}
