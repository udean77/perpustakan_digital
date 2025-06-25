<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Store;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil toko pertama yang ada, atau buat jika tidak ada
        $store = Store::first();
        if (!$store) {
            // Asumsi User dengan ID 1 ada dan bisa menjadi penjual
            $store = Store::factory()->create(['user_id' => 1]); 
        }

        $books = [
            // Fiksi
            [
                'title' => 'Cincin Kabut Bromo',
                'author' => 'Ayu Utami',
                'description' => 'Sebuah petualangan magis di jantung Jawa Timur, di mana mitos dan realitas berpadu.',
                'price' => 120000,
                'stock' => 50,
                'category' => 'fiksi',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Mesin Waktu Kakek',
                'author' => 'Tere Liye',
                'description' => 'Kisah seorang anak yang menemukan mesin waktu tua di gudang kakeknya dan menjelajahi masa lalu.',
                'price' => 95000,
                'stock' => 75,
                'category' => 'fiksi',
                'book_type' => 'ebook',
            ],
            // Non-Fiksi
            [
                'title' => 'Filosofi Teras untuk Abad 21',
                'author' => 'Henry Manampiring',
                'description' => 'Mengupas bagaimana filsafat Stoa kuno bisa relevan untuk mengatasi kecemasan modern.',
                'price' => 88000,
                'stock' => 120,
                'category' => 'non-fiksi',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Seni Merajut Koneksi',
                'author' => 'Brene Brown',
                'description' => 'Panduan praktis untuk membangun hubungan yang lebih dalam dan otentik di era digital.',
                'price' => 110000,
                'stock' => 60,
                'category' => 'non-fiksi',
                'book_type' => 'ebook',
            ],
            // Novel
            [
                'title' => 'Aroma Karsa',
                'author' => 'Dee Lestari',
                'description' => 'Pencarian bunga misterius dengan aroma magis yang tersembunyi di pedalaman.',
                'price' => 150000,
                'stock' => 40,
                'category' => 'novel',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Gadis Kretek',
                'author' => 'Ratih Kumala',
                'description' => 'Mengungkap rahasia keluarga di balik industri kretek di Indonesia.',
                'price' => 105000,
                'stock' => 90,
                'category' => 'novel',
                'book_type' => 'physical',
            ],
            // Komik
            [
                'title' => 'Si Juki dan Petualangan Luar Angkasa',
                'author' => 'Faza Meonk',
                'description' => 'Komik strip kocak tentang petualangan Si Juki yang tak terduga ke planet lain.',
                'price' => 65000,
                'stock' => 200,
                'category' => 'komik',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Panji Tengkorak: Harta Karun Terlarang',
                'author' => 'Hans Jaladara',
                'description' => 'Kisah silat klasik Indonesia dalam format komik modern yang menegangkan.',
                'price' => 75000,
                'stock' => 150,
                'category' => 'komik',
                'book_type' => 'ebook',
            ],
            // Buku Tambahan
            [
                'title' => 'Ekspedisi Laut Biru',
                'author' => 'Eka Kurniawan',
                'description' => 'Petualangan nelayan dalam mencari legenda ikan raksasa di perairan timur Indonesia.',
                'price' => 98000,
                'stock' => 80,
                'category' => 'fiksi',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Quantum: Memahami Dunia Subatomik',
                'author' => 'Michio Kaku',
                'description' => 'Penjelasan fisika kuantum yang dapat diakses oleh pembaca awam.',
                'price' => 135000,
                'stock' => 55,
                'category' => 'non-fiksi',
                'book_type' => 'ebook',
            ],
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'description' => 'Kisah inspiratif 10 anak dari keluarga miskin yang bersekolah di Belitung.',
                'price' => 70000,
                'stock' => 300,
                'category' => 'novel',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Garuda di Dadaku',
                'author' => 'Salman Aristo',
                'description' => 'Perjuangan seorang anak untuk menjadi pemain sepak bola profesional.',
                'price' => 85000,
                'stock' => 110,
                'category' => 'novel',
                'book_type' => 'ebook',
            ],
             [
                'title' => 'Sejarah Rempah Nusantara',
                'author' => 'Prof. Dr. Sejarawan',
                'description' => 'Jelajahi bagaimana rempah-rempah membentuk sejarah dan peradaban di Nusantara.',
                'price' => 175000,
                'stock' => 30,
                'category' => 'non-fiksi',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'description' => 'Roman tetralogi yang menceritakan kisah Minke di era kolonial Belanda.',
                'price' => 140000,
                'stock' => 65,
                'category' => 'novel',
                'book_type' => 'physical',
            ],
            [
                'title' => 'Teriyaki Boy',
                'author' => 'Joko Anu',
                'description' => 'Komik slice-of-life tentang seorang pemuda yang membuka kedai teriyaki di Jakarta.',
                'price' => 55000,
                'stock' => 250,
                'category' => 'komik',
                'book_type' => 'ebook',
            ],
        ];

        foreach ($books as $bookData) {
            Book::create(array_merge($bookData, [
                'store_id' => $store->id,
                'user_id' => 1, // Pastikan user dengan id 1 ada
                'cover' => 'covers/default.jpg', // Asumsi ada cover default
                'status' => 'active',
            ]));
        }
    }
}
