<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMService
{
    private $model = 'mistral';
    
    public function __construct()
    {
        // Try to get available models and set a fallback
        $this->initializeModel();
    }
    
    private function initializeModel()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:11434/api/tags');
            if ($response->successful()) {
                $data = $response->json();
                $models = $data['models'] ?? [];
                
                // Check if mistral is available
                $mistralAvailable = false;
                foreach ($models as $model) {
                    if (str_contains(strtolower($model['name']), 'mistral')) {
                        $this->model = $model['name'];
                        $mistralAvailable = true;
                        break;
                    }
                }
                
                // If mistral not available, use first available model
                if (!$mistralAvailable && !empty($models)) {
                    $this->model = $models[0]['name'];
                    Log::info('Mistral not available, using model: ' . $this->model);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not check available models: ' . $e->getMessage());
        }
    }

    public function ask($userMessage, $context = null)
    {
        // Set PHP timeout to 120 seconds
        set_time_limit(120);
        
        // System prompt untuk AI
        $systemPrompt = "Kamu adalah Customer Service PustakaDigital yang ramah dan profesional. 

PERATURAN PENTING:
1. Berperan sebagai customer service yang ramah, sopan, dan membantu
2. Jawablah SEMUA pertanyaan dalam Bahasa Indonesia dengan bahasa yang santun
3. JIKA ADA DATA DARI DATABASE, GUNAKAN DATA TERSEBUT SECARA LENGKAP
4. JANGAN PERNAH mengarang judul atau isi buku yang tidak ada di database
5. JANGAN PERNAH memberikan jawaban generik jika ada data spesifik dari database
6. GUNAKAN FORMAT DATA YANG SUDAH DISEDIAKAN dengan struktur yang rapi
7. Setiap pertanyaan harus dijawab secara INDEPENDEN, jangan merujuk ke percakapan sebelumnya
 8. Mulai setiap jawaban dengan sapaan yang ramah dan sopan
9. JAWABLAH SECARA LANGSUNG - jangan tambahkan informasi yang tidak diminta
10. Jika ditanya kode redeem, tampilkan kode redeem saja
11. Jika ditanya buku, tampilkan buku saja
12. Jika ditanya kategori, tampilkan kategori saja
13. Berikan saran yang membantu jika diperlukan
14. Tunjukkan empati dan kesediaan untuk membantu

GAYA BAHASA CUSTOMER SERVICE:
- Gunakan bahasa yang sopan dan ramah
- Mulai dengan sapaan seperti 'Halo!', 'Selamat datang!', atau 'Terima kasih sudah menghubungi kami!'
- Akhiri dengan penawaran bantuan seperti 'Ada yang bisa saya bantu lagi?' atau 'Silakan tanyakan jika ada yang ingin diketahui lebih lanjut'
- Gunakan kata-kata seperti 'kami', 'saya', 'Anda' dengan sopan
- Berikan informasi yang jelas dan mudah dipahami

KEMAMPUAN YANG TERSEDIA:
- Mencari buku berdasarkan judul, penulis, kategori
- Menampilkan informasi toko/penjual
- Menampilkan kategori buku yang tersedia
- Memberikan informasi umum tentang database
- Menjawab pertanyaan tentang stok dan harga buku
- Menampilkan kode redeem/voucher yang tersedia
- Memberikan informasi diskon dan promo

KODE REDEEM:
- Kode redeem memberikan diskon atau cashback untuk pembelian buku
- Ada 2 jenis diskon: persentase (%) atau nominal (Rp)
- Setiap kode memiliki batas minimum pembelian
- Kode memiliki batas waktu berlaku dan batas penggunaan
- Jika tidak ada kode aktif, berikan informasi umum tentang sistem redeem

INSTRUKSI KHUSUS:
- Jika ada data database, GUNAKAN DATA TERSEBUT SECARA LENGKAP
- JANGAN ABAIKAN data yang diberikan dalam format yang sudah disediakan
- Tampilkan data dengan format yang sama seperti yang diberikan
- JAWABLAH SECARA LANGSUNG tanpa informasi tambahan yang tidak diminta
- Berperan sebagai customer service yang ramah dan membantu
- Jika user bertanya 'apa saja yang tersedia' atau 'apa yang ada di database', berikan informasi lengkap tentang buku, toko, kategori, dan kode redeem yang tersedia.";

        $userPrompt = "Pertanyaan Pengguna: \"{$userMessage}\"";
        if ($context) {
            $userPrompt .= "\n\n[DATA DARI DATABASE - GUNAKAN SECARA LENGKAP!]\n{$context}\n[AKHIR DATA]";
            $userPrompt .= "\n\nINSTRUKSI PENTING: Jawablah pertanyaan user DENGAN MENGGUNAKAN DATA DI ATAS SECARA LENGKAP. Tampilkan data dengan format yang sama seperti yang diberikan. JANGAN ABAIKAN data ini dan jangan berikan jawaban generik. JAWABLAH SECARA LANGSUNG tanpa informasi tambahan yang tidak diminta. Berperan sebagai customer service yang ramah dan sopan. Jika tidak ada data yang cocok, jawab: 'Maaf, kami tidak menemukan buku yang sesuai pencarian Anda.'";
        }

        $finalPrompt = $systemPrompt . "\n\n" . $userPrompt;

        try {
            $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
                'model' => $this->model,
                'prompt' => $finalPrompt,
                'stream' => false
            ]);

            if ($response->failed()) {
                $errorBody = $response->body();
                Log::error('Ollama API request failed.', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'model' => $this->model
                ]);
                return "DEBUG: Gagal terhubung ke Ollama. Status: " . $response->status() . " - Pesan: " . $errorBody . " - Model: " . $this->model;
            }

            $json = $response->json();
            if (isset($json['response'])) {
                return trim($json['response']);
            }

            Log::warning('Unexpected response format from Ollama.', ['response' => $json]);
            return 'Maaf, AI memberikan respons yang tidak terduga. Coba lagi ya.';
        } catch (\Exception $e) {
            Log::error('Exception in LLMService: ' . $e->getMessage());
            return 'Maaf, terjadi kesalahan dalam menghubungi AI. Pastikan Ollama berjalan dan model tersedia.';
        }
    }
    
    public function getModel()
    {
        return $this->model;
    }
} 