<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Services\BookRecommendationService;

class UserPreferenceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $preferences = $user->preferences;
        
        // Jika belum ada preferensi, buat berdasarkan aktivitas user
        if (!$preferences) {
            $recommendationService = app(BookRecommendationService::class);
            $preferences = $recommendationService->updateUserPreferences($user);
        }

        return view('user.preferences.index', compact('preferences'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'string|in:fiksi,non-fiksi,pendidikan,novel,komik',
            'preferred_authors' => 'nullable|array',
            'preferred_authors.*' => 'string|max:100',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'preferred_book_type' => 'required|in:physical,ebook,both',
            'min_rating' => 'nullable|integer|min:1|max:5'
        ]);

        $preferences = $user->preferences ?? new UserPreference(['user_id' => $user->id]);
        
        $preferences->fill([
            'preferred_categories' => $request->preferred_categories,
            'preferred_authors' => $request->preferred_authors,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'preferred_book_type' => $request->preferred_book_type,
            'min_rating' => $request->min_rating ?? 0
        ]);

        $preferences->save();

        return redirect()->back()->with('success', 'Preferensi berhasil diperbarui!');
    }

    public function autoUpdate()
    {
        $user = auth()->user();
        $recommendationService = app(BookRecommendationService::class);
        
        try {
            $preferences = $recommendationService->updateUserPreferences($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Preferensi berhasil diperbarui otomatis berdasarkan aktivitas Anda',
                'preferences' => $preferences
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui preferensi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reset()
    {
        $user = auth()->user();
        
        if ($user->preferences) {
            $user->preferences->delete();
        }

        return redirect()->back()->with('success', 'Preferensi berhasil direset!');
    }
} 