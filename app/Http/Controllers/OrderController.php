<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;


class OrderController extends Controller
{
    public function index()
    {
        // Ambil daftar pesanan user yang login, terbaru dulu, pagination 10 per halaman
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show($id)
    {
        // Ambil order dulu
        $order = Order::with('items.book', 'address')->findOrFail($id);

        // Cek authorization setelah order didapat
        $this->authorize('view', $order);

        return view('user.orders.show', compact('order'));
    }



    public function cancel($id)
    {
        $user = auth()->user();

        // Ambil transaksi milik user dengan status pending
        $order = Order::with('items.book') // eager load untuk efisiensi
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Ubah status order utama
        $order->status = 'cancelled';
        $order->save();

        // Batalkan semua item dan kembalikan stok buku
        foreach ($order->items as $item) {
            $item->status = 'cancelled';
            $item->save();

            if ($item->book) {
                $item->book->increment('stock', $item->quantity);
            }
        }

        return redirect()->route('user.transaction.index')->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
    }

}

