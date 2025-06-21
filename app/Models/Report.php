<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'reportable_type', 'reportable_id', 'reason', 'status'
    ];

    // Relasi polymorphic ke produk, seller, atau transaksi
    public function reportable()
    {
        return $this->morphTo();
    }

    // Relasi user pelapor
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
