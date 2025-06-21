<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil transaksi dengan relasi user, items, book, seller, redeemCode
        $transactions = Order::with(['user', 'items.book.seller', 'redeemCode'])->latest()->paginate(10);

        // Hitung pendapatan bulan ini dari pesanan selesai
        $monthlyRevenue = Order::where('status', 'selesai')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return view('admin.transaction.index', compact('transactions', 'monthlyRevenue'));
    }
    public function show($id)
    {
        $order = Order::with('user', 'items.book.user', 'redeemCode')->findOrFail($id);
        return view('admin.transaction.show', compact('order'));
    }

}

