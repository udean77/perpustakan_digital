<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'province',
        'city',
        'alamat_lengkap',
        'kode_pos',
        'nama_penerima',
        'no_hp',
        'is_default',
    ];

    // Relasi: Alamat milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
