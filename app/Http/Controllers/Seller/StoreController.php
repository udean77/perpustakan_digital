<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\Report;


class StoreController extends Controller
{
    
    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string',
            'store_address' => 'required|string',
            'phone' => ['required', 'regex:/^628[0-9]{7,14}$/'], 
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ], [
        'phone.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format: 6281234567890 (tanpa tanda + atau 0 di depan).',
        ]);
    
        $store = Auth::user()->store;
    
        if ($request->hasFile('store_logo')) {
            $file = $request->file('store_logo');
            if ($store->logo && Storage::exists('public/store_logo/' . $store->logo)) {
                Storage::delete('public/store_logo/' . $store->logo);
            }
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/store_logo', $filename);
            $store->logo = $filename;
        }
    
        // Simpan data lain
        $store->name = $request->store_name;
        $store->description = $request->store_description;
        $store->address = $request->store_address;
        $store->phone = $request->phone;
        $store->save();
    
        return redirect()->back()->with('success', 'Profil toko berhasil diperbarui!');
    }
    

    public function edit()
    {
        $store = Auth::user()->store;
        $books = Book::where('user_id', auth()->id())->get();
        return view('seller.store.index', compact('store','books'));
    }

    
    public function books(Request $request)
    {
        $books = Book::query();

        // Filter Stok
        if ($request->has('in_stock')) {
            $books->where('stock', '>', 0);
        }

        // Filter Harga
        if ($request->filled('min_price')) {
            $books->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $books->where('price', '<=', $request->max_price);
        }

        // Urutan
        if ($request->sort == 'low') {
            $books->orderBy('price', 'asc');
        } elseif ($request->sort == 'high') {
            $books->orderBy('price', 'desc');
        } else {
            $books->latest();
        }

        return view('user.homepage', [
            'books' => $books->get(),
        ]);
    }

    public function reportIssue(Request $request)
    {
        $request->validate([
            'reportable_id' => 'required|integer|exists:books,id',
            'reason' => 'required|string|max:1000',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'reportable_type' => \App\Models\Book::class,
            'reportable_id' => $request->reportable_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('report_success', 'Laporan Anda telah dikirim ke Admin.');
    }


    
}
