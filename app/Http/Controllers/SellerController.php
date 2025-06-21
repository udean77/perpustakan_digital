<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use App\Models\Book;
use App\Models\OrderItem;
use Carbon\Carbon;

class SellerController extends Controller
{
    public function create()
    {
        return view('seller.register');
    }
    public function dashboard()
    {
        $sellerId = Auth::id();
        $store = Store::where('user_id', $sellerId)->first();

        if (!$store || $store->status !== 'active') {
            return redirect()->route('user.homepage')->with('warning', 'Toko Anda sedang dinonaktifkan oleh admin');
        }

        $books_count = Book::where('user_id', $sellerId)->count();
        $orders_count = OrderItem::where('seller_id', $sellerId)
            ->distinct('order_id')
            ->count('order_id');
        $total_income = OrderItem::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->sum(DB::raw('price * quantity'));
        $today_orders = OrderItem::where('seller_id', $sellerId)
            ->whereDate('created_at', now()->toDateString())
            ->distinct('order_id')
            ->count('order_id');
        $latest_books = Book::where('user_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $weekly_sales = OrderItem::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price * quantity) as total_sales')
            )
            ->where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('d M'));
        }

        $salesByDate = $weekly_sales->keyBy(function ($item) {
            return Carbon::parse($item->date)->format('d M');
        });

        $labels = $dates->toArray();
        $totals = array_map(function ($date) use ($salesByDate) {
            return $salesByDate[$date]->total_sales ?? 0;
        }, $labels);

        $weekly_sales = [
            'labels' => $labels,
            'totals' => $totals,
        ];

        return view('seller.dashboard', compact(
            'books_count',
            'orders_count',
            'total_income',
            'today_orders',
            'latest_books',
            'weekly_sales'
        ));
    }




    public function homepage()
    {
        $user = Auth::user();
        $store = Store::where('user_id', Auth::id())->first();

        if (!$store || $store->status !== 'active') {
            return redirect()->route('user.homepage')->with('warning', 'Toko Anda Di nonaktif admin');
        }

        if ($user->role === 'penjual') {
            // Misalnya redirect ke seller dashboard atau tampilkan info penjual
            return redirect()->route('seller.dashboard');
        }


        return view('user.homepage'); // atau tampilkan tampilan umum
    }


    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'store_address' => 'required|string',
            'phone' => ['required', 'regex:/^628[0-9]{7,14}$/'],
            ], [
         'phone.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format: 6281234567890 (tanpa tanda + atau 0 di depan).',

        ]);

        $storeLogoPath = null;
        if ($request->hasFile('store_logo')) {
            $storeLogoPath = $request->file('store_logo')->store('store_logos', 'public');
        }

        // Simpan data toko ke database
        Store::create([
            'user_id' => auth()->id(),
            'name' => $request->store_name,
            'description' => $request->store_description,
            'logo' => $storeLogoPath,
            'address' => $request->store_address,
            'phone' => $request->phone,
        ]);

        $user = auth()->user();
        $user->role = 'penjual';
        $user->save();

        return redirect()->route('seller.dashboard')->with('success', 'Toko berhasil dibuat!');
    }

}
