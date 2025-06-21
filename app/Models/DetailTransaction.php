<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'book_id',
        'quantity',
        'price'];

    // Relasi ke transaksi induk
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke buku
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
