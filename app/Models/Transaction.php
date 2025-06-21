<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailTransaction;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail transaksi (buku2 yang dibeli)
    public function details()
    {
        return $this->hasMany(DetailTransaction::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
