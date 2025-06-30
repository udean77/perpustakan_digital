<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\LLMService;

class CheckOllamaStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ollama:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Ollama status and available models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Ollama status...');
        
        // Check if Ollama is running
        try {
            $response = Http::timeout(5)->get('http://localhost:11434/api/tags');
            
            if ($response->successful()) {
                $this->info('✅ Ollama is running on port 11434');
                
                $data = $response->json();
                $models = $data['models'] ?? [];
                
                if (empty($models)) {
                    $this->warn('⚠️  No models found in Ollama');
                    $this->info('💡 You can install a model using: ollama pull mistral');
                } else {
                    $this->info('📚 Available models:');
                    foreach ($models as $model) {
                        $this->line("  - {$model['name']} (Size: " . $this->formatBytes($model['size'] ?? 0) . ")");
                    }
                }
                
                // Test LLMService
                $this->info('🧪 Testing LLMService...');
                $llm = new LLMService();
                $this->info("📋 Using model: " . $llm->getModel());
                
                // Test a simple query
                $testResponse = $llm->ask('Hello, test message');
                if (str_contains($testResponse, 'DEBUG:') || str_contains($testResponse, 'Maaf, terjadi kesalahan')) {
                    $this->error('❌ LLMService test failed: ' . $testResponse);
                } else {
                    $this->info('✅ LLMService test successful');
                    $this->line('Response: ' . substr($testResponse, 0, 100) . '...');
                }
                
            } else {
                $this->error('❌ Ollama responded with error: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Cannot connect to Ollama: ' . $e->getMessage());
            $this->info('💡 Make sure Ollama is running: ollama serve');
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 