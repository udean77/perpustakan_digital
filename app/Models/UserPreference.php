<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_authors',
        'min_price',
        'max_price',
        'preferred_book_type',
        'min_rating'
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'min_rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method untuk mendapatkan kategori favorit
    public function getPreferredCategoriesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Helper method untuk mendapatkan penulis favorit
    public function getPreferredAuthorsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
} 