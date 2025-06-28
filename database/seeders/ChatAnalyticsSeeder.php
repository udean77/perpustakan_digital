<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatAnalytics;
use App\Models\ChatHistory;
use App\Models\User;
use Carbon\Carbon;

class ChatAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for reference
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Creating dummy chat analytics without user references.');
        }

        // Create dummy chat analytics data
        $intentTypes = ['book_search', 'redeem_code', 'general_inquiry', 'technical_support', 'order_status'];
        $queryTypes = ['question', 'request', 'complaint', 'feedback', 'information'];
        
        for ($i = 0; $i < 50; $i++) {
            $startTime = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            $endTime = $startTime->copy()->addMinutes(rand(1, 30));
            
            ChatAnalytics::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'session_id' => 'session_' . uniqid(),
                'intent_type' => $intentTypes[array_rand($intentTypes)],
                'query_type' => $queryTypes[array_rand($queryTypes)],
                'was_helpful' => rand(0, 1),
                'response_time_ms' => rand(500, 5000),
                'message_count' => rand(1, 10),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'ip_address' => '192.168.1.' . rand(1, 255),
                'started_at' => $startTime,
                'ended_at' => $endTime,
                'created_at' => $startTime,
                'updated_at' => $endTime,
            ]);
        }

        // Create some chat history entries
        for ($i = 0; $i < 100; $i++) {
            ChatHistory::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'message' => 'Test message ' . ($i + 1),
                'intent' => $intentTypes[array_rand($intentTypes)],
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('Chat Analytics dummy data created successfully!');
    }
} 