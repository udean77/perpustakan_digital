<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\User;

class DebugCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:cart {user_id? : User ID to debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug cart items for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            $userId = $this->ask('Enter user ID:');
        }
        
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }
        
        $cartItems = Cart::with('book')->where('user_id', $userId)->get();
        
        if ($cartItems->isEmpty()) {
            $this->info("User {$user->nama} has no cart items.");
            return 0;
        }
        
        $this->info("Cart items for user {$user->nama} (ID: {$userId}):");
        $this->table(
            ['Cart ID', 'Book ID', 'Book Title', 'Quantity', 'Price'],
            $cartItems->map(function ($item) {
                return [
                    $item->id,
                    $item->book->id,
                    $item->book->title,
                    $item->quantity,
                    $item->book->discount_price ?? $item->book->price
                ];
            })
        );
        
        return 0;
    }
} 