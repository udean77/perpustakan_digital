<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'intent_type',
        'query_type',
        'was_helpful',
        'response_time_ms',
        'user_agent',
        'ip_address',
        'started_at',
        'ended_at',
        'message_count'
    ];

    protected $casts = [
        'was_helpful' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'message_count' => 'integer',
        'response_time_ms' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk mendapatkan analytics hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Scope untuk mendapatkan analytics minggu ini
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // Scope untuk mendapatkan analytics bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Method untuk menghitung rata-rata waktu respons
    public static function getAverageResponseTime()
    {
        return self::whereNotNull('response_time_ms')->avg('response_time_ms');
    }

    // Method untuk mendapatkan intent yang paling sering digunakan
    public static function getMostCommonIntent()
    {
        return self::whereNotNull('intent_type')
                  ->selectRaw('intent_type, COUNT(*) as count')
                  ->groupBy('intent_type')
                  ->orderBy('count', 'desc')
                  ->first();
    }

    // Method untuk mendapatkan tingkat kepuasan user
    public static function getSatisfactionRate()
    {
        $total = self::whereNotNull('was_helpful')->count();
        $helpful = self::where('was_helpful', true)->count();
        
        return $total > 0 ? round(($helpful / $total) * 100, 2) : 0;
    }
} 