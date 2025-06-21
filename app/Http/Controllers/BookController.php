<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // Daftar kategori sesuai data string di kolom 'category'
        $categories = [
            (object)['id' => 'fiksi', 'name' => 'Fiksi'],
            (object)['id' => 'non-fiksi', 'name' => 'Non-Fiksi'],
            (object)['id' => 'pendidikan', 'name' => 'Pendidikan'],
            (object)['id' => 'novel', 'name' => 'Novel'],
            (object)['id' => 'komik', 'name' => 'Komik'],
        ];

        $query = Book::with('store')
            ->withAvg('reviews', 'rating')
            ->where('status', 'active')
            ->whereHas('store', function ($q) {
                $q->where('status', 'active'); // Filter toko aktif
            });

        // Filter keyword di title atau author
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('author', 'like', '%' . $request->keyword . '%')
                   ->orWhere('category', 'like', '%' . $request->keyword . '%');
            });
        }
        // Jika filter 'category' tunggal (dari dropdown), masukkan ke request->kategori
        if ($request->filled('category')) {
            $request->merge([
                'kategori' => array_merge((array)$request->kategori, [$request->category])
            ]);
        }


        // Filter kategori (bisa lebih dari satu)
        if ($request->filled('kategori')) {
            $kategori = is_array($request->kategori) ? $request->kategori : [$request->kategori];
            $query->whereIn('category', $kategori);
        }

        // Filter jenis buku (physical/ebook)
        if ($request->filled('jenis')) {
            $jenis = is_array($request->jenis) ? $request->jenis : [$request->jenis];
            $query->whereIn('book_type', $jenis);
        }

        // Filter rating minimum (berdasarkan avg rating)
        if ($request->filled('rating')) {
            $query->having('reviews_avg_rating', '>=', $request->rating);
        }

        // Filter harga_min dan harga_max
        if ($request->filled('harga_min')) {
            $query->where('price', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('price', '<=', $request->harga_max);
        }

        // Filter stok tersedia (stock > 0)
        if ($request->has('stok_tersedia')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        switch ($request->input('sort')) {
            case 'harga_terendah':
                $query->orderBy('price', 'asc');
                break;
            case 'harga_tertinggi':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ambil data dengan pagination 12 per halaman
        $books = $query->paginate(12)->withQueryString();

        return view('user.books.index', compact('books', 'categories'));
    }

    // Menampilkan detail buku
    public function show($id)
    {
        $book = Book::with('store')->findOrFail($id);

        $isInWishlist = false;
        $user = auth()->user();

        if ($user) {
            $isInWishlist = $user->wishlists()->where('book_id', $book->id)->exists();
        }

        return view('user.books.show', compact('book', 'isInWishlist'));
    }
}
