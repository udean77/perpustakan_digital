<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\BookRecommendationService;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        $categories = [
            (object)['id' => 'fiksi', 'name' => 'Fiksi'],
            (object)['id' => 'non-fiksi', 'name' => 'Non-Fiksi'],
            (object)['id' => 'pendidikan', 'name' => 'Pendidikan'],
            (object)['id' => 'novel', 'name' => 'Novel'],
            (object)['id' => 'komik', 'name' => 'Komik'],
        ];

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('author', 'like', '%' . $request->keyword . '%')
                   ->orWhere('category', 'like', '%' . $request->keyword . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $books = $query->paginate(12)->withQueryString();
        return view('user.books.index', compact('books', 'categories'));
    }

    // Endpoint API untuk JSON
    public function apiIndex(Request $request)
    {
        $query = Book::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
        }
        $books = $query->limit(10)->get(['id', 'title', 'author', 'description', 'price', 'stock']);
        return response()->json([
            'success' => true,
            'data' => $books,
            'count' => $books->count()
        ]);
    }

    // Menampilkan detail buku
    public function show($id, BookRecommendationService $recommendationService)
    {
        $book = Book::with('store')->findOrFail($id);

        $isInWishlist = false;
        $user = auth()->user();

        if ($user) {
            $isInWishlist = $user->wishlists()->where('book_id', $book->id)->exists();
        }

        // Get similar books
        $similarBooks = $recommendationService->getSimilarBooks($book, 4);

        return view('user.books.show', compact('book', 'isInWishlist', 'similarBooks'));
    }
}
