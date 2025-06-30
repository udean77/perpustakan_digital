<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Create payment with Midtrans
     */
    public function createPayment(Request $request, $orderId)
    {
        try {
            \Log::info('Payment creation started', ['order_id' => $orderId]);
            
            // Load order with all necessary relationships
            $order = Order::with([
                'user', 
                'items.book', 
                'items.book.store',
                'address',
                'redeemCode'
            ])->findOrFail($orderId);
            
            \Log::info('Order loaded', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'auth_user_id' => auth()->id(),
                'items_count' => $order->items->count(),
                'total_amount' => $order->total_amount,
                'has_address' => $order->address ? 'yes' : 'no',
                'has_redeem_code' => $order->redeemCode ? 'yes' : 'no'
            ]);
            
            // Check if order belongs to authenticated user
            if ($order->user_id !== auth()->id()) {
                \Log::warning('Unauthorized access attempt', [
                    'order_user_id' => $order->user_id,
                    'auth_user_id' => auth()->id()
                ]);
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Log order items details
            foreach ($order->items as $item) {
                \Log::info('Order item', [
                    'item_id' => $item->id,
                    'book_id' => $item->book_id,
                    'book_title' => $item->book->title ?? 'No title',
                    'book_type' => $item->book->book_type ?? 'No type',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'has_book_relation' => $item->book ? 'yes' : 'no'
                ]);
            }

            // Create Snap Token
            $snapToken = $this->midtransService->createSnapToken($order);
            
            \Log::info('Snap token created', ['snap_token' => $snapToken ? 'generated' : 'null']);

            return view('user.payment.checkout', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Handle payment finish callback
     */
    public function finish(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        if (!$midtransOrderId) {
            return redirect()->route('user.transaction.index')->with('error', 'ID Pesanan tidak ditemukan.');
        }

        $orderIdParts = explode('-', $midtransOrderId);
        if (count($orderIdParts) < 2) {
            return redirect()->route('user.transaction.index')->with('error', 'Format ID Pesanan tidak valid.');
        }
        
        $orderId = $orderIdParts[1]; // Get the actual order ID
        $order = Order::find($orderId);
        
        if (!$order) {
            return redirect()->route('user.transaction.index')->with('error', 'Order not found');
        }

        // Check payment status
        try {
            $paymentStatus = $this->midtransService->getPaymentStatus($orderId);
            
            if (isset($paymentStatus['transaction_status']) && 
                ($paymentStatus['transaction_status'] === 'settlement' || 
                 $paymentStatus['transaction_status'] === 'capture')) {
                $order->update(['status' => 'selesai']);
                return redirect()->route('user.transaction.index')
                    ->with('success', 'Pembayaran berhasil! Pesanan Anda telah selesai.');
            } elseif (isset($paymentStatus['transaction_status']) && $paymentStatus['transaction_status'] === 'pending') {
                return redirect()->route('user.transaction.show', $order->id)
                    ->with('info', 'Pembayaran tertunda. Mohon selesaikan pembayaran Anda.');
            } else {
                return redirect()->route('user.transaction.show', $order->id)
                    ->with('error', 'Pembayaran gagal atau dibatalkan.');
            }
        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage());
            return redirect()->route('user.transaction.show', $order->id)
                ->with('error', 'Unable to verify payment status. Please contact support.');
        }
    }

    /**
     * Handle payment error callback
     */
    public function error(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        if ($midtransOrderId) {
            $orderIdParts = explode('-', $midtransOrderId);
            if (count($orderIdParts) >= 2) {
                $orderId = $orderIdParts[1];
                $order = Order::find($orderId);
                
                if ($order) {
                    $order->update(['status' => 'dibatalkan']);
                }
            }
        }

        return redirect()->route('user.transaction.index')
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }

    /**
     * Handle payment pending callback
     */
    public function pending(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        if (!$midtransOrderId) {
            return redirect()->route('user.transaction.index')->with('info', 'Pesanan menunggu pembayaran.');
        }

        $orderIdParts = explode('-', $midtransOrderId);
        if (count($orderIdParts) < 2) {
            return redirect()->route('user.transaction.index')->with('error', 'Format ID Pesanan tidak valid.');
        }

        $orderId = $orderIdParts[1];
        $order = Order::find($orderId);
        
        if ($order) {
            $order->update(['status' => 'pending']);
            return redirect()->route('user.transaction.show', $order->id)
                ->with('info', 'Pembayaran tertunda. Mohon selesaikan pembayaran Anda.');
        }

        return redirect()->route('user.transaction.index')->with('info', 'Pesanan Anda menunggu pembayaran.');
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function notification(Request $request)
    {
        Log::info('Midtrans notification received.', $request->all());

        try {
            $notification = $request->all();
            $order = $this->midtransService->handleNotification($notification);
            
            Log::info('Midtrans notification processed successfully for Order ID: ' . $order->id);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment notification failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
} 