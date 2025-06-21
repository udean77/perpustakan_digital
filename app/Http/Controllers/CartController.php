<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // View cart and calculate the total
    public function index()
    {
        $userId = Auth::id();
        $cartItems = Cart::with('book')->where('user_id', $userId)->get();

        $total = $cartItems->sum(function ($item) {
            $price = $item->book->discount_price ?? $item->book->price;
            return $price * $item->quantity;
        });

        return view('user.cart.index', compact('cartItems', 'total'));
    }


    // Remove an item from the cart
    public function remove($id)
    {
        $userId = Auth::id();

        // Cari cart item berdasarkan id dan user
        $cartItem = Cart::where('id', $id)
                        ->where('user_id', $userId)
                        ->first();

        if ($cartItem) {
            $cartItem->delete();

            return redirect()->route('user.cart.index')->with('success', 'Item dihapus dari keranjang.');
        }

        return redirect()->route('user.cart.index')->with('error', 'Item tidak ditemukan.');
    }


    // Add a book to the cart
    public function add(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $book->stock,
        ]);

        $quantity = $request->input('quantity', 1);

        $userId = Auth::id();

        // Cari cart item user untuk buku ini
        $cartItem = Cart::where('user_id', $userId)
                        ->where('book_id', $id)
                        ->first();

        if ($cartItem) {
            // Update quantity, pastikan tidak melebihi stok
            $newQty = $cartItem->quantity + $quantity;
            $cartItem->quantity = min($newQty, $book->stock);
            $cartItem->save();
        } else {
            // Tambah data baru
            Cart::create([
                'user_id' => $userId,
                'book_id' => $id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $cartItem = Cart::where('id', $id)
                        ->where('user_id', $userId)
                        ->firstOrFail();

        $book = $cartItem->book;

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $book->stock,
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('user.cart.index')->with('success', 'Jumlah item berhasil diperbarui.');
    }

    public function clear()
    {
        $userId = Auth::id();

        Cart::where('user_id', $userId)->delete();

        return redirect()->route('user.cart.index')->with('success', 'Semua item di keranjang berhasil dihapus.');
    }

}
