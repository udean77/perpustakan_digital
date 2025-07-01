<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Book;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\RedeemCode;

class CheckoutController extends Controller
{
    // Checkout dari keranjang
   public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        // Debug logging
        \Log::info('Checkout index called', [
            'user_id' => $userId,
            'request_data' => $request->all(),
            'has_selected_items' => $request->has('selected_items'),
            'selected_items' => $request->selected_items ?? 'none'
        ]);

        // Hanya proses jika ada selected_items
        if ($request->has('selected_items') && is_array($request->selected_items)) {
            $selectedItemIds = $request->selected_items;
            $cartItems = Cart::with('book')
                ->where('user_id', $userId)
                ->whereIn('id', $selectedItemIds)
                ->get();
                
            \Log::info('Cart items found', [
                'selected_ids' => $selectedItemIds,
                'found_items_count' => $cartItems->count(),
                'found_items' => $cartItems->pluck('id')->toArray()
            ]);
        } else {
            // Jika tidak ada selected_items, redirect ke cart dengan error
            \Log::warning('No selected items in checkout request');
            return redirect()->route('user.cart.index')->with('error', 'Pilih item yang ingin di-checkout.');
        }

        if ($cartItems->isEmpty()) {
            \Log::warning('Cart items empty after filtering');
            return redirect()->route('user.cart.index')->with('error', 'Tidak ada item yang dipilih untuk checkout.');
        }

        // Check if all items are ebooks
        $allEbooks = $cartItems->every(function($item) {
            return $item->book->book_type === 'ebook';
        });

        \Log::info('Order type check', [
            'all_ebooks' => $allEbooks,
            'items_types' => $cartItems->pluck('book.book_type')->toArray()
        ]);

        // --- PINDAH KE ORDER ---
        \DB::beginTransaction();
        try {
            $total = $cartItems->sum(fn($item) => ($item->book->discount_price ?? $item->book->price) * $item->quantity);
        $cartCount = $cartItems->count();
            
            // Handle address for ebooks vs physical books
            $addressId = null;
            $shippingCost = 0;
            
            if (!$allEbooks) {
                // For physical books, need address and shipping cost
                $addressId = $user->addresses()->where('is_default', true)->first()?->id ?? $user->addresses()->first()?->id;
                $shippingCost = rand(10000, 30000); // Random shipping cost for physical books
                
                if (!$addressId) {
                    \DB::rollBack();
                    \Log::error('No address found for physical book order');
                    return redirect()->route('user.cart.index')->with('error', 'Alamat pengiriman belum disetel. Silakan atur alamat terlebih dahulu.');
                }
            }

            \Log::info('Creating order', [
                'total' => $total,
                'shipping_cost' => $shippingCost,
                'address_id' => $addressId,
                'all_ebooks' => $allEbooks
            ]);

            // Buat order baru
            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'total_amount' => $total + $shippingCost,
                'shipping_cost' => $shippingCost,
                'payment_method' => 'midtrans',
                'shipping_address' => '',
                'status' => 'pending',
                'ordered_at' => now(),
            ]);

            \Log::info('Order created', ['order_id' => $order->id]);

            // Tambahkan order_items
            foreach ($cartItems as $item) {
                $orderItem = \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book->id,
                    'seller_id' => $item->book->user_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->discount_price ?? $item->book->price,
                    'status' => 'pending',
                ]);
                
                // Kurangi stok buku
                $book = $item->book;
                if ($book) {
                    $book->decrement('stock', $item->quantity);
                }

                \Log::info('Order item created', [
                    'order_item_id' => $orderItem->id,
                    'book_id' => $item->book->id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->discount_price ?? $item->book->price
                ]);
            }

            // Hapus item dari cart setelah pembayaran berhasil
            // Cart::whereIn('id', $selectedItemIds)->delete();
            // \Log::info('Cart items removed', ['removed_ids' => $selectedItemIds]);

            \DB::commit();
            \Log::info('Transaction committed successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('user.cart.index')->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }

        // Ambil ulang order dan tampilkan di checkout
        $order = \App\Models\Order::with('items.book')->find($order->id);
        
        \Log::info('Redirecting to payment', [
            'order_id' => $order->id,
            'order_items_count' => $order->items->count(),
            'order_total' => $order->total_amount,
            'user_id' => $order->user_id,
            'auth_user_id' => auth()->id()
        ]);
        
        // Validate that order was created successfully
        if (!$order || $order->items->count() === 0) {
            \Log::error('Order creation validation failed', [
                'order_exists' => $order ? 'yes' : 'no',
                'items_count' => $order ? $order->items->count() : 0
            ]);
            return redirect()->route('user.cart.index')->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }
        
        // Redirect ke halaman pembayaran
        return redirect()->route('payment.create', $order->id)->with('success', 'Checkout berhasil diproses. Silakan lakukan pembayaran.');
    }

    // Proses checkout dari keranjang
   public function process(Request $request)
    {
        // Debug: Log request data
        \Log::info('Checkout process request data:', [
            'all_data' => $request->all(),
            'selected_items' => $request->selected_items,
            'has_selected_items' => $request->has('selected_items'),
            'is_array' => is_array($request->selected_items)
        ]);
        
        $request->validate([
            'shipping_address' => 'required|string',
            'redeem_code' => 'nullable|string',
            'shipping_cost' => 'required|numeric|min:10000|max:30000',
            'selected_items' => 'required|array|min:1'
        ]);

        $userId = auth()->id();
        $selectedItemIds = $request->selected_items;
        
        \Log::info('Selected item IDs:', $selectedItemIds);
        
        // Ambil hanya item yang dipilih
        $cartItems = Cart::with('book')
            ->where('user_id', $userId)
            ->whereIn('id', $selectedItemIds)
            ->get();
            
        \Log::info('Found cart items:', $cartItems->pluck('id')->toArray());

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang kosong atau item tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $subtotal = $cartItems->sum(fn($item) => ($item->book->discount_price ?? $item->book->price) * $item->quantity);

            $redeemCode = null;
            $discountAmount = 0;
            $shippingCost = $request->shipping_cost;

            if ($request->filled('redeem_code')) {
                $code = RedeemCode::where('code', strtoupper($request->redeem_code))->first();

                if ($code && $code->canBeUsedFor($subtotal)) {
                    $redeemCode = $code;
                    
                    if ($redeemCode->type === 'free_shipping') {
                        $discountAmount = $shippingCost; // Diskon sebesar ongkir
                        $shippingCost = 0; // Ongkir jadi 0
                    } else {
                        $discountAmount = $redeemCode->calculateDiscount($subtotal);
                    }
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Kode redeem tidak valid atau tidak dapat digunakan.');
                }
            }

            $totalAmount = $subtotal - $discountAmount + $shippingCost;

            // ✅ Tambahkan ini untuk ambil alamat default
            $selectedAddress = $user->addresses()->where('is_default', true)->first();
            if (!$selectedAddress) {
                DB::rollBack();
                return back()->with('error', 'Alamat default belum disetel. Silakan atur alamat utama terlebih dahulu.');
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $selectedAddress->id,
                'total_amount' => $totalAmount,
                'shipping_cost' => $request->shipping_cost, // Simpan ongkir asli
                'payment_method' => 'midtrans',
                'shipping_address' => $request->shipping_address,
                'status' => 'pending',
                'ordered_at' => now(),
                'redeem_code_id' => $redeemCode ? $redeemCode->id : null,
                'discount_amount' => $discountAmount,
            ]);

            if ($redeemCode) {
                $redeemCode->incrementUsage();
            }

            foreach ($cartItems as $item) {
                $book = Book::find($item->book->id);

                if ($book->stock < $item->quantity) {
                    DB::rollBack();
                    return back()->with('error', "Stok tidak mencukupi untuk buku '{$book->title}'.");
                }

                $book->stock -= $item->quantity;
                $book->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'seller_id' => $book->user_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->discount_price ?? $book->price,
                    'status' => 'pending',
                ]);
            }

            // Jangan hapus dari keranjang dulu
            // Cart::whereIn('id', $selectedItemIds)->where('user_id', $userId)->delete();

            DB::commit();
            return redirect()->route('payment.create', $order->id)->with('success', 'Silakan lanjutkan ke pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }

    public function buyNowForm(Book $book)
    {
        $user = auth()->user();
        $addresses = $user->addresses;

        return view('user.orders.buy_now', compact('book', 'addresses'));
    }

    public function processBuyNow(Request $request)
    {
        $book = Book::findOrFail($request->book_id);
        
        // Validasi berbeda untuk ebook dan buku fisik
        if ($book->book_type === 'ebook') {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'quantity' => 'required|integer|min:1',
                'redeem_code' => 'nullable|string',
                'shipping_cost' => 'required|numeric|min:0|max:0' // Ebook tidak ada ongkir
            ]);
        } else {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'redeem_code' => 'nullable|string',
            'shipping_cost' => 'required|numeric|min:10000|max:30000'
        ]);
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $quantity = $request->quantity;

            if ($book->stock < $quantity) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            $subtotal = ($book->discount_price ?? $book->price) * $quantity;
            
            $redeemCode = null;
            $discountAmount = 0;

            if ($request->filled('redeem_code')) {
                $code = RedeemCode::where('code', strtoupper($request->redeem_code))->first();

                if ($code && $code->canBeUsedFor($subtotal)) {
                    $redeemCode = $code;
                    $discountAmount = $redeemCode->calculateDiscount($subtotal);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Kode redeem tidak valid atau tidak dapat digunakan.');
                }
            }
            
            // Gunakan shipping cost dari form
            $shippingCost = $request->shipping_cost;

            $totalAmount = $subtotal - $discountAmount + $shippingCost;
            
            // Untuk ebook, tidak perlu alamat pengiriman
            $selectedAddress = null;
            $shippingAddress = '';
            
            if ($book->book_type === 'physical') {
            $selectedAddress = $user->addresses()->where('is_default', true)->first();
            if (!$selectedAddress) {
                DB::rollBack();
                return back()->with('error', 'Alamat default belum disetel.');
                }
                $shippingAddress = $request->shipping_address;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $selectedAddress ? $selectedAddress->id : null,
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'payment_method' => 'midtrans',
                'shipping_address' => $shippingAddress,
                'status' => 'pending',
                'ordered_at' => now(),
                'redeem_code_id' => $redeemCode ? $redeemCode->id : null,
                'discount_amount' => $discountAmount,
            ]);

            $book->stock -= $quantity;
            $book->save();

            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $book->id,
                'seller_id' => $book->user_id,
                'quantity' => $quantity,
                'price' => $book->discount_price ?? $book->price,
                'status' => 'pending',
            ]);
            
            if ($redeemCode) {
                $redeemCode->incrementUsage();
            }

            DB::commit();
            return redirect()->route('payment.create', $order->id)->with('success', 'Checkout berhasil diproses. Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }
}
