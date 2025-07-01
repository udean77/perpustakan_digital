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
        $systemPrompt = "Anda adalah asisten AI di toko buku online. Berikan jawaban yang sangat singkat dan langsung pada intinya, maksimal 3 kalimat. Tugas utama Anda adalah memberikan rekomendasi buku, menjawab pertanyaan tentang buku, dan membantu pengguna menavigasi toko. Selalu jawab dalam Bahasa Indonesia.";

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