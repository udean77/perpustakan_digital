<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class BookRecommendationService
{
    /**
     * Mendapatkan rekomendasi buku berdasarkan preferensi user
     */
    public function getRecommendationsForUser(User $user, $limit = 6)
    {
        $preferences = $user->preferences;
        
        if (!$preferences) {
            // Jika user belum punya preferensi, berikan rekomendasi berdasarkan popularitas
            return $this->getPopularBooks($limit);
        }

        $query = Book::where('status', 'active')
                    ->where('stock', '>', 0);

        // Filter berdasarkan kategori favorit
        if (!empty($preferences->preferred_categories)) {
            $query->whereIn('category', $preferences->preferred_categories);
        }

        // Filter berdasarkan penulis favorit
        if (!empty($preferences->preferred_authors)) {
            $query->whereIn('author', $preferences->preferred_authors);
        }

        // Filter berdasarkan harga
        if ($preferences->min_price) {
            $query->where('price', '>=', $preferences->min_price);
        }
        if ($preferences->max_price) {
            $query->where('price', '<=', $preferences->max_price);
        }

        // Filter berdasarkan tipe buku
        if ($preferences->preferred_book_type !== 'both') {
            $query->where('book_type', $preferences->preferred_book_type);
        }

        // Filter berdasarkan rating minimum
        if ($preferences->min_rating > 0) {
            $query->whereHas('reviews', function($q) use ($preferences) {
                $q->havingRaw('AVG(rating) >= ?', [$preferences->min_rating]);
            });
        }

        // Exclude buku yang sudah di wishlist atau cart
        $excludeBookIds = $this->getExcludedBookIds($user);
        if (!empty($excludeBookIds)) {
            $query->whereNotIn('id', $excludeBookIds);
        }

        // Order by rating dan stok
        $books = $query->with(['reviews', 'store'])
                      ->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc')
                      ->orderBy('stock', 'desc')
                      ->limit($limit)
                      ->get();

        // Jika tidak cukup buku berdasarkan preferensi, tambahkan buku populer
        if ($books->count() < $limit) {
            $remainingLimit = $limit - $books->count();
            $popularBooks = $this->getPopularBooks($remainingLimit, $books->pluck('id')->toArray());
            $books = $books->merge($popularBooks);
        }

        return $books;
    }

    /**
     * Mendapatkan buku populer berdasarkan rating dan jumlah review
     */
    public function getPopularBooks($limit = 6, $excludeIds = [])
    {
        $query = Book::where('status', 'active')
                    ->where('stock', '>', 0);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->with(['reviews', 'store'])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->orderBy('reviews_avg_rating', 'desc')
                    ->orderBy('reviews_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Mendapatkan rekomendasi berdasarkan buku yang sedang dilihat
     */
    public function getSimilarBooks(Book $book, $limit = 4)
    {
        return Book::where('status', 'active')
                  ->where('stock', '>', 0)
                  ->where('id', '!=', $book->id)
                  ->where(function($query) use ($book) {
                      $query->where('category', $book->category)
                            ->orWhere('author', $book->author)
                            ->orWhere('book_type', $book->book_type);
                  })
                  ->with(['reviews', 'store'])
                  ->withAvg('reviews', 'rating')
                  ->orderBy('reviews_avg_rating', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Mendapatkan rekomendasi berdasarkan riwayat chat
     */
    public function getRecommendationsFromChatHistory(User $user, $limit = 4)
    {
        $chatHistory = $user->chatHistories()
                           ->whereNotNull('intent')
                           ->latest()
                           ->take(10)
                           ->get();

        $keywords = [];
        foreach ($chatHistory as $chat) {
            // Extract keywords dari pesan chat
            $words = explode(' ', strtolower($chat->message));
            $keywords = array_merge($keywords, $words);
        }

        // Filter kata kunci yang relevan
        $relevantKeywords = array_filter($keywords, function($word) {
            return strlen($word) > 3 && !in_array($word, ['yang', 'dari', 'untuk', 'dengan', 'dalam', 'pada', 'oleh', 'saya', 'anda', 'mereka', 'kami', 'kamu']);
        });

        if (empty($relevantKeywords)) {
            return collect();
        }

        $query = Book::where('status', 'active')
                    ->where('stock', '>', 0);

        $query->where(function($q) use ($relevantKeywords) {
            foreach ($relevantKeywords as $keyword) {
                $q->orWhere('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('category', 'like', "%{$keyword}%");
            }
        });

        return $query->with(['reviews', 'store'])
                    ->withAvg('reviews', 'rating')
                    ->orderBy('reviews_avg_rating', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Update preferensi user berdasarkan aktivitas
     */
    public function updateUserPreferences(User $user)
    {
        $preferences = $user->preferences ?? new UserPreference(['user_id' => $user->id]);

        // Analisis kategori dari wishlist dan review
        $categories = $this->analyzeUserCategories($user);
        if (!empty($categories)) {
            $preferences->preferred_categories = $categories;
        }

        // Analisis penulis dari wishlist dan review
        $authors = $this->analyzeUserAuthors($user);
        if (!empty($authors)) {
            $preferences->preferred_authors = $authors;
        }

        // Analisis range harga
        $priceRange = $this->analyzeUserPriceRange($user);
        if ($priceRange) {
            $preferences->min_price = $priceRange['min'];
            $preferences->max_price = $priceRange['max'];
        }

        // Analisis tipe buku
        $bookType = $this->analyzeUserBookType($user);
        if ($bookType) {
            $preferences->preferred_book_type = $bookType;
        }

        $preferences->save();
        return $preferences;
    }

    /**
     * Analisis kategori favorit user
     */
    private function analyzeUserCategories(User $user)
    {
        $categories = DB::table('books')
            ->join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->where('wishlists.user_id', $user->id)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->pluck('category')
            ->toArray();

        return $categories;
    }

    /**
     * Analisis penulis favorit user
     */
    private function analyzeUserAuthors(User $user)
    {
        $authors = DB::table('books')
            ->join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->where('wishlists.user_id', $user->id)
            ->selectRaw('author, COUNT(*) as count')
            ->groupBy('author')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->pluck('author')
            ->toArray();

        return $authors;
    }

    /**
     * Analisis range harga user
     */
    private function analyzeUserPriceRange(User $user)
    {
        $prices = DB::table('books')
            ->join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->where('wishlists.user_id', $user->id)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price, AVG(price) as avg_price')
            ->first();

        if ($prices && $prices->min_price) {
            return [
                'min' => $prices->min_price * 0.8, // 20% di bawah minimum
                'max' => $prices->max_price * 1.2  // 20% di atas maksimum
            ];
        }

        return null;
    }

    /**
     * Analisis tipe buku favorit user
     */
    private function analyzeUserBookType(User $user)
    {
        $bookTypes = DB::table('books')
            ->join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->where('wishlists.user_id', $user->id)
            ->selectRaw('book_type, COUNT(*) as count')
            ->groupBy('book_type')
            ->orderBy('count', 'desc')
            ->first();

        return $bookTypes ? $bookTypes->book_type : null;
    }

    /**
     * Mendapatkan ID buku yang harus di-exclude dari rekomendasi
     */
    private function getExcludedBookIds(User $user)
    {
        $wishlistIds = $user->wishlists()->pluck('book_id')->toArray();
        $cartIds = $user->carts()->pluck('book_id')->toArray();
        
        return array_unique(array_merge($wishlistIds, $cartIds));
    }
} 