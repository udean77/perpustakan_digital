<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Snap Token for payment
     */
    public function createSnapToken(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->nama,
                'email' => $order->user->email,
                'phone' => $order->user->hp ?? '',
            ],
            'item_details' => $this->getItemDetails($order),
            'enabled_payments' => [
                'credit_card', 'bca_va', 'bni_va', 'bri_va', 'mandiri_clickpay',
                'gopay', 'indomaret', 'danamon_online', 'akulaku'
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
                'error' => route('payment.error'),
                'pending' => route('payment.pending'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Snap Token: ' . $e->getMessage());
        }
    }

    /**
     * Get item details for Midtrans
     */
    private function getItemDetails(Order $order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->book->id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->book->title,
            ];
        }

        // Add discount as negative item if exists
        if ($order->discount_amount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -$order->discount_amount,
                'quantity' => 1,
                'name' => 'Diskon' . ($order->redeemCode ? ' (' . $order->redeemCode->code . ')' : ''),
            ];
        }

        return $items;
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function handleNotification($notification)
    {
        Log::info('Handling Midtrans notification', $notification);

        $orderIdFromMidtrans = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $signatureKey = $notification['signature_key'];
        
        $orderIdParts = explode('-', $orderIdFromMidtrans);
        $orderId = $orderIdParts[1]; // Get the actual order ID

        $order = Order::find($orderId);
        
        if (!$order) {
            Log::error('Order not found for ID: ' . $orderId);
            throw new \Exception('Order not found');
        }

        // Verify signature key
        $expectedSignatureKey = hash('sha512', 
            $orderIdFromMidtrans . 
            $statusCode . 
            $grossAmount . 
            config('midtrans.server_key')
        );

        if ($signatureKey !== $expectedSignatureKey) {
            Log::error('Invalid signature key for Order ID: ' . $orderId, [
                'expected' => $expectedSignatureKey,
                'received' => $signatureKey
            ]);
            throw new \Exception('Invalid signature key');
        }

        // Update order status based on payment status
        $transactionStatus = $notification['transaction_status'];
        $paymentType = $notification['payment_type'];
        Log::info("Transaction status for Order ID {$orderId}: {$transactionStatus}");

        if ($transactionStatus == 'settlement') {
            $order->update(['status' => 'selesai', 'payment_method' => $paymentType]);
            Log::info("Order ID {$orderId} status updated to 'selesai' with payment method '{$paymentType}'");
        } elseif ($transactionStatus == 'pending') {
            $order->update(['status' => 'pending', 'payment_method' => $paymentType]);
            Log::info("Order ID {$orderId} status updated to 'pending' with payment method '{$paymentType}'");
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire' || $transactionStatus == 'deny') {
            $order->update(['status' => 'dibatalkan', 'payment_method' => $paymentType]);
            Log::info("Order ID {$orderId} status updated to 'dibatalkan'");
        }

        return $order;
    }

    /**
     * Get payment status from Midtrans
     */
    public function getPaymentStatus($orderId)
    {
        try {
            $response = \Midtrans\Transaction::status($orderId);
            return $response;
        } catch (\Exception $e) {
            throw new \Exception('Failed to get payment status: ' . $e->getMessage());
        }
    }
} 