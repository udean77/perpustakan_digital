<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMService
{
    public function ask($userMessage, $context = null)
    {
        // System prompt untuk AI
        $systemPrompt = "Kamu adalah PustakawanAI, asisten chatbot untuk website PustakaDigital. Jawablah semua pertanyaan dalam Bahasa Indonesia. Jika ada data dari database, gunakan data tersebut untuk menjawab. Jika tidak ada data, jawab dengan sopan dan informatif.";

        $userPrompt = "Pertanyaan Pengguna: \"{$userMessage}\"";
        if ($context) {
            $userPrompt .= "\n\n[DATA DARI DATABASE UNTUK DIJAWAB OLEH AI, JANGAN ABAIKAN!]\n{$context}\n[AKHIR DATA]";
            $userPrompt .= "\nJawablah pertanyaan user HANYA berdasarkan data di atas. Jika tidak ada data yang cocok, katakan dengan sopan.";
        }

        $finalPrompt = $systemPrompt . "\n\n" . $userPrompt;

        $response = Http::timeout(60)->post('http://localhost:11434/api/generate', [
            'model' => 'mistral',
            'prompt' => $finalPrompt,
            'stream' => false
        ]);

        if ($response->failed()) {
            $errorBody = $response->body();
            Log::error('Ollama API request failed.', [
                'status' => $response->status(),
                'body' => $errorBody
            ]);
            return "DEBUG: Gagal terhubung ke Ollama. Status: " . $response->status() . " - Pesan: " . $errorBody;
        }

        $json = $response->json();
        if (isset($json['response'])) {
            return trim($json['response']);
        }

        Log::warning('Unexpected response format from Ollama.', ['response' => $json]);
        return 'Maaf, AI memberikan respons yang tidak terduga. Coba lagi ya.';
    }
} 