<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LLMService;
use App\Models\Book;
use App\Models\RedeemCode;
use App\Models\ChatHistory;

class ChatController extends Controller
{
    public function chat(Request $request, LLMService $llm)
    {
        $userMessage = $request->input('message');
        $context = null;

        // Deteksi pertanyaan tentang kode redeem/promo
        $redeemKeywords = ['kode redeem', 'kode promo', 'diskon', 'voucher', 'promo'];
        $isRedeemQuery = false;
        foreach ($redeemKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                $isRedeemQuery = true;
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
                break;
            }
        }
        // Deteksi pertanyaan tentang buku
        $bookKeywords = ['buku', 'cari', 'judul', 'penulis', 'pengarang', 'novel', 'komik', 'tersedia', 'rekomendasi'];
        $isBookQuery = false;
        foreach ($bookKeywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                $isBookQuery = true;
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
        } else if ($isBookQuery || $isBestSeller || $isEbook || $isFisik || $categoryDetected) {
            $query = Book::query();
            if ($isBestSeller) {
                $query->orderBy('stock', 'desc');
            }
            if ($isEbook) {
                $query->where('book_type', 'ebook');
            }
            if ($isFisik) {
                $query->where('book_type', 'physical');
            }
            if ($categoryDetected) {
                $query->where('category', $categoryDetected);
            }
            $specific = false;
            foreach (['judul', 'penulis', 'pengarang', 'cari', 'karya'] as $spec) {
                if (stripos($userMessage, $spec) !== false) {
                    $specific = true;
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
                    $context = "Tidak ada buku yang cocok ditemukan di database untuk pencarian user.";
                }
            }
        }
        
        $history = ChatHistory::where('user_id', auth()->id())->latest()->take(5)->pluck('message')->toArray();
        $context .= "\nHistori pencarian user: " . implode('; ', $history);
        
        // Kirim pesan dan konteks (jika ada) ke LLMService
        $response = $llm->ask($userMessage, $context);
        
        ChatHistory::create([
            'user_id' => auth()->id(),
            'message' => $userMessage,
            'intent' => $detectedIntent ?? null, // bisa diisi kategori/kata kunci
        ]);
        
        return response()->json(['reply' => $response]);
    }
} 