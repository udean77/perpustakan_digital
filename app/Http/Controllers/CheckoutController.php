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
   public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $cartItems = Cart::with('book')->where('user_id', $userId)->get();

        $total = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);
        $cartCount = $cartItems->count();
        $addresses = $user->addresses;

        // Validasi profil lengkap
        $fieldsToCheck = [
            'nama' => $user->nama,
            'email' => $user->email,
            'hp' => $user->hp,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
        ];

        $incompleteFields = [];
        foreach ($fieldsToCheck as $field => $value) {
            if (empty($value)) {
                $incompleteFields[] = $field;
            }
        }

        if (!empty($incompleteFields)) {
            return redirect()->route('user.profile')
                ->with('error', 'Lengkapi semua data profil Anda terlebih dahulu sebelum melanjutkan pembelian.');
        }

        // Validasi alamat pengiriman
        if ($user->addresses()->count() == 0) {
            return redirect()->route('user.profile')
                ->with('error', 'Tambahkan daftar alamat pengiriman terlebih dahulu sebelum melanjutkan pembelian.');
        }
        
        return view('user.orders.index', compact('cartItems', 'cartCount', 'total', 'addresses'));
    }



    // Proses checkout dari keranjang
   public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'redeem_code' => 'nullable|string'
        ]);

        $userId = auth()->id();
        $cartItems = Cart::with('book')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $subtotal = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);

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

            // Generate random shipping cost between 10,000 and 30,000
            $shippingCost = rand(10000, 30000);

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
                'shipping_cost' => $shippingCost,
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
                    'price' => $book->price,
                    'status' => 'pending',
                ]);
            }

            Cart::where('user_id', $userId)->delete();

            DB::commit();
            return redirect()->route('payment.create', $order->id)->with('success', 'Checkout berhasil diproses. Silakan lakukan pembayaran.');
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
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'redeem_code' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $book = Book::findOrFail($request->book_id);
            $quantity = $request->quantity;

            if ($book->stock < $quantity) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            $subtotal = $book->price * $quantity;
            
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
            
            // Generate random shipping cost between 10,000 and 30,000
            $shippingCost = rand(10000, 30000);

            $totalAmount = $subtotal - $discountAmount + $shippingCost;
            
            $selectedAddress = $user->addresses()->where('is_default', true)->first();
            if (!$selectedAddress) {
                DB::rollBack();
                return back()->with('error', 'Alamat default belum disetel.');
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $selectedAddress->id,
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'payment_method' => 'midtrans',
                'shipping_address' => $request->shipping_address,
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
                'price' => $book->price,
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
