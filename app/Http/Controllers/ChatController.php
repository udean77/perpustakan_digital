<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LLMService;
use App\Services\BookRecommendationService;
use App\Models\Book;
use App\Models\RedeemCode;
use App\Models\ChatHistory;
use App\Models\ChatAnalytics;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    public function chat(Request $request, LLMService $llm, BookRecommendationService $recommendationService)
    {
        $startTime = microtime(true);
        $userMessage = $request->input('message');
        $context = null;
        $detectedIntent = null;
        $queryType = null;

        // Track session
        $sessionId = Session::get('chat_session_id', uniqid());
        Session::put('chat_session_id', $sessionId);

        // Get or create analytics record
        $analytics = ChatAnalytics::where('session_id', $sessionId)->first();
        if (!$analytics) {
            $analytics = ChatAnalytics::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'started_at' => now(),
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'message_count' => 0
            ]);
        }

        // Deteksi pertanyaan tentang kode redeem/promo
        $redeemKeywords = ['kode redeem', 'kode promo', 'diskon', 'voucher', 'promo'];
        $isRedeemQuery = false;
        foreach ($redeemKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                $isRedeemQuery = true;
                $detectedIntent = 'redeem_code';
                $queryType = 'promo_inquiry';
                break;
            }
        }

        // Deteksi pertanyaan tentang best seller, ebook, fisik, dan kategori
        $isBestSeller = stripos($userMessage, 'best seller') !== false;
        $isEbook = stripos($userMessage, 'ebook') !== false;
        $isFisik = stripos($userMessage, 'fisik') !== false;
        $categoriesList = ['fiksi', 'non-fiksi', 'pendidikan', 'novel', 'komik'];
        $categoryDetected = null;
        foreach ($categoriesList as $cat) {
            if (stripos($userMessage, $cat) !== false) {
                $categoryDetected = $cat;
                $detectedIntent = 'book_search';
                $queryType = 'category_search';
                break;
            }
        }

        // Deteksi pertanyaan tentang buku
        $bookKeywords = ['buku', 'cari', 'judul', 'penulis', 'pengarang', 'novel', 'komik', 'tersedia', 'rekomendasi'];
        $isBookQuery = false;
        foreach ($bookKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                $isBookQuery = true;
                if (!$detectedIntent) {
                    $detectedIntent = 'book_search';
                    $queryType = 'general_search';
                }
                break;
            }
        }

        // Deteksi permintaan rekomendasi
        $recommendationKeywords = ['rekomendasi', 'saran', 'bagus', 'terbaik', 'populer'];
        $isRecommendationQuery = false;
        foreach ($recommendationKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                $isRecommendationQuery = true;
                $detectedIntent = 'book_recommendation';
                $queryType = 'recommendation_request';
                break;
            }
        }

        if ($isRedeemQuery) {
            $redeems = RedeemCode::where('status', 'active')->get(['code', 'description', 'valid_until']);
            if ($redeems->count() > 0) {
                $redeemList = $redeems->map(function($r) {
                    return "- Kode: {$r->code}, Keterangan: {$r->description}, Expired: {$r->valid_until}";
                })->join("\n");
                $context = "Kode redeem aktif di database:\n" . $redeemList;
            } else {
                $context = "Tidak ada kode redeem aktif saat ini.";
            }
        } else if ($isRecommendationQuery && auth()->check()) {
            // Berikan rekomendasi otomatis berdasarkan preferensi user
            $user = auth()->user();
            $recommendations = $recommendationService->getRecommendationsForUser($user, 3);
            
            if ($recommendations->count() > 0) {
                $recommendationList = $recommendations->map(function($book) {
                    $rating = $book->reviews_avg_rating ? number_format($book->reviews_avg_rating, 1) : 'Belum ada rating';
                    return "- {$book->title} oleh {$book->author} (⭐ {$rating}, Rp" . number_format($book->price) . ")";
                })->join("\n");
                $context = "Berdasarkan preferensi Anda, berikut rekomendasi buku:\n" . $recommendationList;
            } else {
                $context = "Berikut beberapa buku populer yang mungkin Anda sukai:\n";
                $popularBooks = $recommendationService->getPopularBooks(3);
                $popularList = $popularBooks->map(function($book) {
                    $rating = $book->reviews_avg_rating ? number_format($book->reviews_avg_rating, 1) : 'Belum ada rating';
                    return "- {$book->title} oleh {$book->author} (⭐ {$rating}, Rp" . number_format($book->price) . ")";
                })->join("\n");
                $context .= $popularList;
            }
        } else if ($isBookQuery || $isBestSeller || $isEbook || $isFisik || $categoryDetected) {
            $query = Book::query();
            if ($isBestSeller) {
                $query->orderBy('stock', 'desc');
                $queryType = 'best_seller_search';
            }
            if ($isEbook) {
                $query->where('book_type', 'ebook');
                $queryType = 'ebook_search';
            }
            if ($isFisik) {
                $query->where('book_type', 'physical');
                $queryType = 'physical_book_search';
            }
            if ($categoryDetected) {
                $query->where('category', $categoryDetected);
                $queryType = 'category_search';
            }
            $specific = false;
            foreach (['judul', 'penulis', 'pengarang', 'cari', 'karya'] as $spec) {
                if (stripos($userMessage, $spec) !== false) {
                    $specific = true;
                    $queryType = 'specific_search';
                    break;
                }
            }
            if ($specific) {
                $query->where(function($q) use ($userMessage) {
                    $q->where('title', 'like', "%{$userMessage}%")
                      ->orWhere('author', 'like', "%{$userMessage}%");
                });
            }
            $books = $query->limit(3)->get(['id', 'title', 'author', 'price', 'stock', 'book_type', 'category']);
            if ($books->count() > 0) {
                $bookList = $books->map(function($book) {
                    return "- Judul: {$book->title}, Penulis: {$book->author}, Harga: Rp" . number_format($book->price) . ", Stok: {$book->stock}, Jenis: {$book->book_type}, Kategori: {$book->category}";
                })->join("\n");
                $context = "Ditemukan data buku yang relevan di database (tampilkan maksimal 3 buku saja, jawab singkat):\n" . $bookList;
            } else {
                if ($categoryDetected) {
                    $context = "Buku dengan kategori tersebut belum tersedia di database.";
                } else {
                    $context = null; // Tidak ada buku yang cocok
                }
            }
        }
        
        // Hanya tambahkan histori pencarian jika context ada data buku
        if ($context && str_contains($context, 'Ditemukan data buku')) {
            $history = ChatHistory::where('user_id', auth()->id())->latest()->take(5)->pluck('message')->toArray();
            $context .= "\nHistori pencarian user: " . implode('; ', $history);
        }
        
        // Kirim pesan dan konteks (jika ada) ke LLMService
        $response = $llm->ask($userMessage, $context);
        
        // Calculate response time
        $responseTime = round((microtime(true) - $startTime) * 1000);
        
        // Update analytics
        $analytics->update([
            'intent_type' => $detectedIntent,
            'query_type' => $queryType,
            'response_time_ms' => $responseTime,
            'message_count' => $analytics->message_count + 1
        ]);
        
        // Save chat history
        ChatHistory::create([
            'user_id' => auth()->id(),
            'message' => $userMessage,
            'intent' => $detectedIntent,
        ]);
        
        return response()->json(['reply' => $response]);
    }

    /**
     * End chat session
     */
    public function endSession(Request $request)
    {
        $sessionId = $request->input('session_id');
        
        if ($sessionId) {
            ChatAnalytics::where('session_id', $sessionId)
                        ->update(['ended_at' => now()]);
        }
        
        Session::forget('chat_session_id');
        
        return response()->json(['success' => true]);
    }

    /**
     * Provide feedback for chat response
     */
    public function feedback(Request $request)
    {
        $sessionId = $request->input('session_id');
        $wasHelpful = $request->input('was_helpful');
        
        if ($sessionId) {
            ChatAnalytics::where('session_id', $sessionId)
                        ->update(['was_helpful' => $wasHelpful]);
        }
        
        return response()->json(['success' => true]);
    }
} 