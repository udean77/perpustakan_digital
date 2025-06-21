<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'price',
        'stock',
        'category',
        'description',
        'book_type',
        'cover',
        'ebook_file',
        'physical_book_file',
        'publisher',
        'store_id', 
        'user_id',
    ];

    public const CATEGORIES = [
        'fiksi' => 'Fiksi',
        'nonfiksi' => 'Non-Fiksi',
        'pendidikan' => 'Pendidikan',
        'novel' => 'Novel',
        'komik' => 'Komik',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'book_id', 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
    
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id'); 
        // Asumsi: kolom user_id di books adalah penjualnya
    }


}


