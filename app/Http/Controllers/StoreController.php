<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    // Menampilkan profil toko berdasarkan ID
    public function show($id)
    {
        $store = Store::with('user')->findOrFail($id);
        $store = Store::with(['books.reviews.user'])->findOrFail($id);
        return view('user.stores.show', compact('store'));
    }

    // Menampilkan profil toko milik user yang sedang login (untuk seller)
    public function myStore()
    {
        $store = Store::where('user_id', auth()->id())->firstOrFail();
        return view('seller.store.profile', compact('store'));
    }

    // (opsional) Menampilkan semua toko (misalnya untuk admin)
     public function index()
    {
        // Ambil semua toko, bisa juga pake pagination
        $stores = Store::paginate(10);

        return view('user.stores.index', compact('stores'));
    }
    
    

}
