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
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request, LLMService $llm, BookRecommendationService $recommendationService)
    {
        // Set PHP timeout to 120 seconds
        set_time_limit(120);
        
        try {
            $startTime = microtime(true);
            $userMessage = $request->input('message');
            
            // Validate input
            if (empty($userMessage)) {
                return response()->json(['error' => 'Message is required'], 400);
            }
            
            Log::info('Chat request received', [
                'message' => $userMessage,
                'user_id' => auth()->id(),
                'user_agent' => $request->header('User-Agent')
            ]);
            
            // Enhanced context detection for database queries
            $context = null;
            $detectedIntent = null;
            $queryType = null;

            // Track session
            $sessionId = Session::get('chat_session_id', uniqid());
            Session::put('chat_session_id', $sessionId);

            // Reset session for fresh conversation (optional - uncomment if you want each message to be independent)
            // Session::forget('chat_session_id');
            // $sessionId = uniqid();
            // Session::put('chat_session_id', $sessionId);

            // Get or create analytics record
            try {
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
            } catch (\Exception $e) {
                Log::warning('Failed to create analytics record: ' . $e->getMessage());
                // Continue without analytics
            }

            // Check for book-related queries
            if (stripos($userMessage, 'buku') !== false || stripos($userMessage, 'cari') !== false || stripos($userMessage, 'apa') !== false) {
                try {
                    // Get books with store information
                    $books = Book::with('store')
                                ->where('status', 'active')
                                ->where('stock', '>', 0)
                                ->limit(5)
                                ->get(['id', 'title', 'author', 'price', 'category', 'book_type', 'stock', 'store_id']);
                    
                    if ($books->count() > 0) {
                        $bookList = $books->map(function($book) {
                            $storeName = $book->store ? $book->store->name : 'Toko tidak tersedia';
                            return "{$book->title}\n   Penulis: {$book->author}\n   Harga: Rp" . number_format($book->price) . "\n   Kategori: {$book->category}\n   Toko: {$storeName}\n   Stok: {$book->stock}";
                        })->join("\n\n");
                        
                        $context = "BUKU YANG TERSEDIA DI DATABASE:\n\n" . $bookList;
                        $detectedIntent = 'book_search';
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to search books: ' . $e->getMessage());
                }
            }

            // Check for category queries
            if (stripos($userMessage, 'kategori') !== false || stripos($userMessage, 'jenis') !== false) {
                try {
                    $categories = Book::where('status', 'active')
                                     ->where('stock', '>', 0)
                                     ->distinct()
                                     ->pluck('category')
                                     ->filter()
                                     ->values();
                    
                    if ($categories->count() > 0) {
                        $categoryList = $categories->map(function($category) {
                            $count = Book::where('category', $category)
                                       ->where('status', 'active')
                                       ->where('stock', '>', 0)
                                       ->count();
                            return "{$category}\n   Jumlah buku: {$count}";
                        })->join("\n\n");
                        
                        $context = "KATEGORI BUKU YANG TERSEDIA:\n\n" . $categoryList;
                        $detectedIntent = 'category_search';
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to get categories: ' . $e->getMessage());
                }
            }

            // Check for store queries
            if (stripos($userMessage, 'toko') !== false || stripos($userMessage, 'penjual') !== false || stripos($userMessage, 'store') !== false) {
                try {
                    $stores = \App\Models\Store::where('status', 'active')
                                              ->withCount(['books' => function($query) {
                                                  $query->where('status', 'active')->where('stock', '>', 0);
                                              }])
                                              ->limit(5)
                                              ->get(['id', 'name', 'description']);
                    
                    if ($stores->count() > 0) {
                        $storeList = $stores->map(function($store) {
                            return "{$store->name}\n   Jumlah buku: {$store->books_count}\n   Deskripsi: {$store->description}";
                        })->join("\n\n");
                        
                        $context = "TOKO/PENJUAL YANG TERSEDIA:\n\n" . $storeList;
                        $detectedIntent = 'store_search';
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to get stores: ' . $e->getMessage());
                }
            }

            // Check for general database info queries
            if (stripos($userMessage, 'database') !== false || stripos($userMessage, 'tersedia') !== false || stripos($userMessage, 'apa saja') !== false) {
                try {
                    $totalBooks = Book::where('status', 'active')->where('stock', '>', 0)->count();
                    $totalStores = \App\Models\Store::where('status', 'active')->count();
                    $totalCategories = Book::where('status', 'active')->where('stock', '>', 0)->distinct()->count('category');
                    
                    $context = "INFORMASI DATABASE PUSTAKADIGITAL:\n\n";
                    $context .= "Total buku aktif: {$totalBooks}\n";
                    $context .= "Total toko aktif: {$totalStores}\n";
                    $context .= "Total kategori: {$totalCategories}\n\n";
                    
                    // Add sample books
                    $sampleBooks = Book::where('status', 'active')->where('stock', '>', 0)->limit(3)->get(['title', 'author', 'category']);
                    if ($sampleBooks->count() > 0) {
                        $context .= "CONTOH BUKU YANG TERSEDIA:\n\n";
                        $context .= $sampleBooks->map(function($book) {
                            return "{$book->title}\n   Penulis: {$book->author}\n   Kategori: {$book->category}";
                        })->join("\n\n");
                    }
                    
                    $detectedIntent = 'database_info';
                } catch (\Exception $e) {
                    Log::warning('Failed to get database info: ' . $e->getMessage());
                }
            }

            // Check for redeem code queries
            if (stripos($userMessage, 'redeem') !== false || stripos($userMessage, 'kode') !== false || stripos($userMessage, 'voucher') !== false || stripos($userMessage, 'diskon') !== false || stripos($userMessage, 'promo') !== false) {
                try {
                    $redeemCodes = RedeemCode::where('status', 'active')
                                            ->where('expired_at', '>', now())
                                            ->where('usage_limit', '>', 'used_count')
                                            ->limit(3)
                                            ->get(['code', 'discount_amount', 'discount_type', 'min_purchase', 'expired_at', 'usage_limit', 'used_count']);
                    
                    if ($redeemCodes->count() > 0) {
                        $codeList = $redeemCodes->map(function($code) {
                            $remainingUses = $code->usage_limit - $code->used_count;
                            $discountText = $code->discount_type === 'percentage' ? "{$code->discount_amount}%" : "Rp" . number_format($code->discount_amount);
                            $minPurchaseText = $code->min_purchase > 0 ? "Min. pembelian: Rp" . number_format($code->min_purchase) : "Tidak ada minimum pembelian";
                            $expiredDate = $code->expired_at->format('d/m/Y H:i');
                            
                            return "{$code->code}\n   Diskon: {$discountText}\n   {$minPurchaseText}\n   Berlaku sampai: {$expiredDate}\n   Sisa penggunaan: {$remainingUses}";
                        })->join("\n\n");
                        
                        $context = "KODE REDEEM/VOUCHER YANG TERSEDIA:\n\n" . $codeList;
                        $detectedIntent = 'redeem_code_search';
                    } else {
                        $context = "INFORMASI KODE REDEEM:\n\nSaat ini tidak ada kode redeem yang aktif. Kode redeem biasanya memberikan diskon atau cashback untuk pembelian buku. Silakan cek kembali nanti atau hubungi admin untuk informasi lebih lanjut.";
                        $detectedIntent = 'redeem_code_info';
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to get redeem codes: ' . $e->getMessage());
                }
            }
            
            // Send to LLMService
            Log::info('Sending to LLMService', ['context_length' => strlen($context ?? '')]);
            $response = $llm->ask($userMessage, $context);
            
            // Calculate response time
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            // Update analytics
            try {
                if (isset($analytics)) {
                    $analytics->update([
                        'intent_type' => $detectedIntent,
                        'query_type' => $queryType,
                        'response_time_ms' => $responseTime,
                        'message_count' => $analytics->message_count + 1
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update analytics: ' . $e->getMessage());
            }
            
            // Save chat history
            try {
                ChatHistory::create([
                    'user_id' => auth()->id(),
                    'message' => $userMessage,
                    'intent' => $detectedIntent,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to save chat history: ' . $e->getMessage());
            }
            
            Log::info('Chat response sent', ['response_length' => strlen($response)]);
            return response()->json([
                'success' => true,
                'response' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'response' => 'Maaf, terjadi kesalahan dalam memproses pesan Anda.'
            ], 500);
        }
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