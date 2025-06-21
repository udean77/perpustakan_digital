<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Menampilkan daftar wishlist pengguna
    public function index()
    {
        $user = auth()->user();

        // Ambil semua buku yang di wishlist
        $books = $user->wishlistBooks()->paginate(12);

        return view('user.wishlist.index', compact('books'));
    }


    // Menambahkan buku ke wishlist
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
        ]);

        return back()->with('success', 'Buku ditambahkan ke wishlist');
    }

    // Menghapus buku dari wishlist
    public function destroy($id)
    {
        Wishlist::where('user_id', Auth::id())
                ->where('book_id', $id)
                ->delete();

        return back()->with('success', 'Buku dihapus dari wishlist');
    }

    public function toggleWishlist(Book $book)
    {
        $user = auth()->user();

        // Cek apakah buku sudah ada di wishlist user
        $exists = $user->wishlists()->where('book_id', $book->id)->exists();

        if ($exists) {
            // Hapus wishlist
            $user->wishlists()->where('book_id', $book->id)->delete();
            $message = 'Removed from wishlist';
        } else {
            // Tambah wishlist
            $user->wishlists()->create(['book_id' => $book->id]);
            $message = 'Added to wishlist';
        }

        return back()->with('status', $message);
    }



}
