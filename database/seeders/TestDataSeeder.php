<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Address;
use App\Models\Cart;
use App\Models\RedeemCode;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembeli',
            'status' => 'active',
            'hp' => '08123456789',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L'
        ]);

        // Create test seller
        $seller = User::create([
            'nama' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'role' => 'penjual',
            'status' => 'active',
            'hp' => '08123456788',
            'tanggal_lahir' => '1985-01-01',
            'jenis_kelamin' => 'P'
        ]);

        // Create test book
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'This is a test book',
            'price' => 100000,
            'stock' => 10,
            'user_id' => $seller->id,
            'book_type' => 'physical',
            'cover' => 'books/default-cover.jpg'
        ]);

        // Create test address
        $address = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'nama_penerima' => 'Test User',
            'no_hp' => '08123456789',
            'alamat_lengkap' => 'Jl. Test No. 123, Jakarta',
            'is_default' => true
        ]);

        // Create test cart item
        Cart::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'quantity' => 1
        ]);

        // Create test redeem code
        RedeemCode::create([
            'code' => 'TEST50',
            'type' => 'discount',
            'value' => 50,
            'value_type' => 'percentage',
            'description' => 'Diskon 50% untuk testing',
            'min_purchase' => 50000,
            'max_usage' => 100,
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(1),
            'status' => 'active'
        ]);

        $this->command->info('Test data created successfully!');
        $this->command->info('Test user: test@example.com / password');
        $this->command->info('Test seller: seller@example.com / password');
        $this->command->info('Test redeem code: TEST50');
    }
}
