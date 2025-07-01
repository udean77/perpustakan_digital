<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RedeemCode;
use Illuminate\Support\Facades\DB;


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

    public function applyVoucher(Request $request, Order $order)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if ($order->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Voucher hanya bisa diterapkan pada pesanan yang belum dibayar.'], 400);
        }

        $code = RedeemCode::where('code', strtoupper($request->code))->first();

        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak ditemukan.'], 404);
        }
        
        $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);

        if (!$code->canBeUsedFor($subtotal)) {
             return response()->json(['success' => false, 'message' => 'Voucher tidak dapat digunakan untuk pesanan ini.'], 400);
        }

        DB::beginTransaction();
        try {
            $shippingCost = $order->shipping_cost;
            $discountAmount = 0;

            if ($code->type === 'free_shipping') {
                $discountAmount = $shippingCost;
            } else {
                $discountAmount = $code->calculateDiscount($subtotal);
            }

            $order->discount_amount = $discountAmount;
            $order->redeem_code_id = $code->id;
            $order->total_amount = round(($subtotal + $shippingCost) - $discountAmount, 2);
            $order->save();

            $code->incrementUsage();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Voucher berhasil diterapkan!',
                'data' => [
                    'discount_amount' => $order->discount_amount,
                    'total_amount' => $order->total_amount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menerapkan voucher: ' . $e->getMessage()], 500);
        }
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

