<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'address',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Di model Store.php
    public function books()
    {
        return $this->hasMany(Book::class, 'store_id', 'id');
    }
    
    public function reviews()
    {
        // Menggabungkan review dari semua buku toko ini
        return $this->hasManyThrough(Review::class, Book::class, 'store_id', 'book_id', 'id', 'id');
    }



}
