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
            $order = Order::with(['user', 'items.book'])->findOrFail($orderId);
            
            // Check if order belongs to authenticated user
            if ($order->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Unauthorized access');
            }

            // Create Snap Token
            $snapToken = $this->midtransService->createSnapToken($order);

            return view('user.payment.checkout', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Handle payment finish callback
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $orderIdParts = explode('-', $orderId);
        $orderId = $orderIdParts[1]; // Get the actual order ID

        $order = Order::find($orderId);
        
        if (!$order) {
            return redirect()->route('user.transaction.index')->with('error', 'Order not found');
        }

        // Check payment status
        try {
            $paymentStatus = $this->midtransService->getPaymentStatus($orderId);
            
            if ($paymentStatus->transaction_status === 'settlement' || 
                $paymentStatus->transaction_status === 'capture') {
                $order->update(['status' => 'selesai']);
                return redirect()->route('user.transaction.index')
                    ->with('success', 'Pembayaran berhasil! Pesanan Anda telah selesai.');
            } elseif ($paymentStatus->transaction_status === 'pending') {
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
        $orderId = $request->order_id;
        $orderIdParts = explode('-', $orderId);
        $orderId = $orderIdParts[1];

        $order = Order::find($orderId);
        
        if ($order) {
            $order->update(['status' => 'dibatalkan']);
        }

        return redirect()->route('user.transaction.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle payment pending callback
     */
    public function pending(Request $request)
    {
        $orderId = $request->order_id;
        $orderIdParts = explode('-', $orderId);
        $orderId = $orderIdParts[1];

        $order = Order::find($orderId);
        
        if ($order) {
            $order->update(['status' => 'pending']);
        }

        return redirect()->route('user.transaction.show', $order->id)
            ->with('info', 'Payment is pending. Please complete your payment.');
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