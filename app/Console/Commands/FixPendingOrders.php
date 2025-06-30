<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class FixPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fix-pending {--order-id= : Specific order ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix pending orders that have been paid by checking Midtrans status';

    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        parent::__construct();
        $this->midtransService = $midtransService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->option('order-id');
        
        if ($orderId) {
            // Fix specific order
            $order = Order::find($orderId);
            if (!$order) {
                $this->error("Order with ID {$orderId} not found.");
                return 1;
            }
            
            $this->fixOrder($order);
        } else {
            // Fix all pending orders
            $pendingOrders = Order::where('status', 'pending')
                ->where('payment_method', 'midtrans')
                ->get();
            
            if ($pendingOrders->isEmpty()) {
                $this->info('No pending orders found.');
                return 0;
            }
            
            $this->info("Found {$pendingOrders->count()} pending orders. Checking payment status...");
            
            $bar = $this->output->createProgressBar($pendingOrders->count());
            $bar->start();
            
            foreach ($pendingOrders as $order) {
                $this->fixOrder($order);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
        }
        
        $this->info('Order status check completed.');
        return 0;
    }
    
    private function fixOrder(Order $order)
    {
        try {
            // Get payment status from Midtrans
            $paymentStatus = $this->midtransService->getPaymentStatus($order->id);
            
            if (isset($paymentStatus['transaction_status'])) {
                $transactionStatus = $paymentStatus['transaction_status'];
                
                if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                    $order->update(['status' => 'selesai']);
                    $this->info("Order {$order->id} status updated to 'selesai' (was pending)");
                    Log::info("Order {$order->id} status fixed from pending to selesai via command");
                } elseif ($transactionStatus === 'cancel' || $transactionStatus === 'expire' || $transactionStatus === 'deny') {
                    $order->update(['status' => 'dibatalkan']);
                    $this->info("Order {$order->id} status updated to 'dibatalkan' (was pending)");
                    Log::info("Order {$order->id} status fixed from pending to dibatalkan via command");
                } else {
                    $this->line("Order {$order->id} still pending with status: {$transactionStatus}");
                }
            } else {
                $this->warn("Order {$order->id}: Could not get payment status from Midtrans");
            }
        } catch (\Exception $e) {
            $this->error("Error checking order {$order->id}: " . $e->getMessage());
            Log::error("Error fixing order {$order->id}: " . $e->getMessage());
        }
    }
} 