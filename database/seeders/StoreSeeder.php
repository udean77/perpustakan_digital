<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data stores yang ada
        \App\Models\Store::query()->delete();

        // Buat user penjual baru untuk menghindari konflik
        $sellers = User::factory(5)->seller()->create();

        $stores = [
            [
                'user_id' => $sellers[0]->id,
                'name' => 'Toko Buku Gramedia Digital',
                'description' => 'Toko buku online terpercaya dengan koleksi buku terlengkap dari berbagai genre dan penerbit.',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone' => '021-5550123',
                'logo' => null,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(2),
            ],
            [
                'user_id' => $sellers[1]->id,
                'name' => 'Pustaka Digital Indonesia',
                'description' => 'Spesialis buku digital dan e-book dengan koleksi buku pendidikan dan akademik.',
                'address' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'phone' => '021-5550456',
                'logo' => null,
                'status' => 'active',
                'created_at' => Carbon::now()->subMonths(1),
            ],
            [
                'user_id' => $sellers[2]->id,
                'name' => 'Buku Murah Online',
                'description' => 'Toko buku dengan harga terjangkau, fokus pada buku-buku populer dan bestseller.',
                'address' => 'Jl. Gatot Subroto No. 67, Jakarta Selatan',
                'phone' => '021-5550789',
                'logo' => null,
                'status' => 'active',
                'created_at' => Carbon::now()->subWeeks(2),
            ],
            [
                'user_id' => $sellers[3]->id,
                'name' => 'Toko Buku Anak Ceria',
                'description' => 'Spesialis buku anak-anak, komik, dan buku cerita dengan ilustrasi menarik.',
                'address' => 'Jl. Kebayoran Lama No. 89, Jakarta Selatan',
                'phone' => '021-5550111',
                'logo' => null,
                'status' => 'active',
                'created_at' => Carbon::now()->subWeeks(1),
            ],
            [
                'user_id' => $sellers[4]->id,
                'name' => 'Buku Teknologi & IT',
                'description' => 'Toko buku khusus teknologi, programming, dan buku IT dengan koleksi terbaru.',
                'address' => 'Jl. Kuningan No. 12, Jakarta Selatan',
                'phone' => '021-5550222',
                'logo' => null,
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }

        $this->command->info('Store data seeded successfully!');
    }
} 