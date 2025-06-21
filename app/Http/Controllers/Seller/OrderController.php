<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  // Pastikan pengguna sudah login
        $this->middleware('role:penjual'); 
    }

    public function index()
    {
        // Ambil semua OrderItem yang bukunya dimiliki seller yang login
        $orderItems = OrderItem::whereHas('book', function($query) {
            $query->where('user_id', auth()->id()); // Cek seller_id pada tabel books
        })
        ->with(['order.user', 'book']) // eager load relasi
        ->latest()
        ->paginate(10);

        return view('seller.orders.index', compact('orderItems'));
    }

    public function show($id)
    {
        $orderItem = OrderItem::with(['order.user', 'book'])->findOrFail($id);

       
    if ($orderItem->book->user_id != auth()->id()) {
        abort(403);
    }
    $orderItem->order->ordered_at = \Carbon\Carbon::parse($orderItem->order->ordered_at);

        return view('seller.orders.show', compact('orderItem'));
    }

    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::with('book', 'order')->findOrFail($id);

        // Pastikan buku milik seller yang sedang login
        if ($orderItem->book->user_id != auth()->id()) {
            abort(403);
        }

        // Cegah update jika status order sudah cancelled atau completed
        if (in_array($orderItem->order->status, ['cancelled', 'completed'])) {
            return back()->with('error', 'Pesanan ini tidak dapat diubah karena sudah dibatalkan atau diselesaikan.');
        }

        // Validasi status input
        $request->validate([
            'status' => 'required|in:pending,processed,shipped,completed'
        ]);

        // Update status item pesanan
        $orderItem->status = $request->status;
        $orderItem->save();

        $order = $orderItem->order;

        // Cek semua status order items
        $statuses = $order->items()->pluck('status')->unique();

        if ($statuses->count() === 1 && $statuses->first() === 'pending') {
            $order->status = 'pending';
        } elseif ($statuses->contains('pending')) {
            $order->status = 'pending';
        } elseif ($statuses->contains('processed') && !$statuses->contains('pending')) {
            $order->status = 'processed';
        } elseif ($statuses->contains('shipped') && !$statuses->contains('pending') && !$statuses->contains('processed')) {
            $order->status = 'shipped';
        } elseif ($statuses->count() === 1 && $statuses->first() === 'completed') {
            $order->status = 'completed';
        }

        $order->save();

        return redirect()->route('seller.orders.index')->with('success', 'Status pesanan dan order diperbarui');
    }


    public function cancel($id)
    {
        // Cari OrderItem berdasarkan ID
        $orderItem = OrderItem::with('book', 'order')->findOrFail($id);

        // Pastikan OrderItem milik seller yang login
        if ($orderItem->book->user_id != auth()->id()) {
            abort(403);
        }

        // Ubah status menjadi 'canceled'
        $orderItem->status = 'cancelled';
        $orderItem->save();

        // Update status keseluruhan order jika perlu
        $order = $orderItem->order;

        $statuses = $order->items()->pluck('status')->unique();

        if ($statuses->count() === 1 && $statuses->first() === 'cancelled') {
            $order->status = 'cancelled';
        } else {
            // Bisa disesuaikan, misal jika ada status lain selain canceled
            // Tetap status order sesuai logika bisnis kamu
        }

        $order->save();

        return redirect()->route('seller.orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }




}
