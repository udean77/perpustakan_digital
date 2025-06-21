<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class TransactionController extends Controller
{
    // Tampilkan daftar transaksi user
    public function index()
    {
        $user = auth()->user();
        $transactions = Order::where('user_id', $user->id)
            ->with('items.book', 'redeemCode')  // tambahkan redeemCode
            ->orderByDesc('ordered_at')
            ->get();

        return view('user.transaction.index', compact('transactions'));
    }

    // Tampilkan detail transaksi tertentu
    public function show($id)
    {
        $user = auth()->user();
        $transaction = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with('items.book','address', 'redeemCode')  // tambahkan redeemCode
            ->firstOrFail();

        return view('user.transaction.show', ['order' => $transaction]);
    }

    public function cancel($id)
    {
        $user = auth()->user();
        $transaction = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update status order utama
        $transaction->status = 'cancelled';
        $transaction->save();

        // Update semua item pesanan dan kembalikan stok
        foreach ($transaction->items as $item) {
            $item->status = 'cancelled';
            $item->save();

            // Kembalikan stok buku (book)
            $book = $item->book;  // pakai relasi 'book' di model OrderItem
            if ($book) {
                $book->stock += $item->quantity; // jumlah item yang dibatalkan
                $book->save();
            }
        }

        return redirect()->route('user.transaction.index')->with('success', 'Transaksi berhasil dibatalkan dan stok kembali.');
    }



    // Konfirmasi pembayaran (contoh fitur sederhana, biasanya ada integrasi payment gateway)
    public function confirmPayment($id)
    {
        $user = auth()->user();
        $transaction = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $transaction->status = 'paid';
        $transaction->paid_at = now();
        $transaction->save();

        return redirect()->route('user.transactions.show', $id)->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }
}
