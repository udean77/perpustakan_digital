<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $user = auth()->user();

        // Cek apakah user sudah pernah mengulas buku ini
        $existingReview = Review::where('user_id', $user->id)
                                ->where('book_id', $bookId)
                                ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk buku ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $review = Review::findOrFail($id);

        // Cek apakah review milik user yang sedang login
        if ($review->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus ulasan ini.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus.');
    }

}
