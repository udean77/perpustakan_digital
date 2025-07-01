<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $itemDetails = $this->getItemDetails($order);
        
        // Calculate total from item details for validation and for gross_amount
        $calculatedTotal = 0;
        foreach ($itemDetails as $item) {
            $calculatedTotal += $item['price'] * $item['quantity'];
        }
        
        // Log for debugging
        Log::info('Midtrans payment details', [
            'order_id' => $order->id,
            'order_total_amount' => $order->total_amount,
            'calculated_total' => $calculatedTotal,
            'shipping_cost' => $order->shipping_cost,
            'discount_amount' => $order->discount_amount,
            'item_details' => $itemDetails
        ]);
        
        // Validate that calculated total matches order total
        if (abs($calculatedTotal - $order->total_amount) > 0.01) {
            Log::warning('Total mismatch in Midtrans payment', [
                'order_id' => $order->id,
                'order_total' => $order->total_amount,
                'calculated_total' => $calculatedTotal,
                'difference' => $calculatedTotal - $order->total_amount
            ]);
            // Tidak perlu throw error, cukup log warning
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => $calculatedTotal, // HARUS SAMA DENGAN JUMLAH ITEM
            ],
            'customer_details' => [
                'first_name' => $order->user->nama,
                'email' => $order->user->email,
                'phone' => $order->user->hp ?? '',
            ],
            'item_details' => $itemDetails,
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

        // Add shipping cost as separate item
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
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

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            if ($order->status !== 'selesai') {
                DB::transaction(function () use ($order, $orderId, $paymentType) {
                    $order->update(['status' => 'selesai', 'payment_method' => $paymentType]);
                    Log::info("Order ID {$orderId} status updated to 'selesai' with payment method '{$paymentType}'");

                    // Ambil ID buku dari item pesanan
                    $bookIds = $order->items->pluck('book_id')->toArray();

                    // Hapus item dari keranjang pengguna
                    if (!empty($bookIds)) {
                        \App\Models\Cart::where('user_id', $order->user_id)
                            ->whereIn('book_id', $bookIds)
                            ->delete();
                        Log::info("Cart items removed for user ID {$order->user_id}", [
                            'book_ids' => $bookIds
                        ]);
                    }

                    // Stok sudah dikurangi saat checkout, jadi tidak perlu lagi di sini
                });

            } else {
                Log::info("Order ID {$orderId} already marked as 'selesai'. No action taken.");
            }
        } elseif ($transactionStatus == 'pending') {
            $order->update(['status' => 'pending', 'payment_method' => $paymentType]);
            Log::info("Order ID {$orderId} status updated to 'pending' with payment method '{$paymentType}'");
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire' || $transactionStatus == 'deny') {
            if ($order->status !== 'dibatalkan' && $order->status !== 'selesai') {
                $order->update(['status' => 'dibatalkan', 'payment_method' => $paymentType]);
                Log::info("Order ID {$orderId} status updated to 'dibatalkan'");

                // Kembalikan stok karena pesanan dibatalkan/gagal
                foreach ($order->items as $item) {
                    if ($item->book) {
                        $item->book->increment('stock', $item->quantity);
                        Log::info("Stock restored for book ID {$item->book->id}", [
                            'order_id' => $orderId,
                            'quantity' => $item->quantity
                        ]);
                    }
                }
            }
        } else {
            // Log unknown status for debugging
            Log::warning("Unknown transaction status for Order ID {$orderId}: {$transactionStatus}");
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