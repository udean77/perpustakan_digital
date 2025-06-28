<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatAnalytics;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatAnalyticsController extends Controller
{
    public function index()
    {
        try {
            // Statistik umum
            $stats = [
                'total_sessions' => ChatAnalytics::count(),
                'total_messages' => ChatHistory::count(),
                'active_sessions_today' => ChatAnalytics::today()->whereNull('ended_at')->count(),
                'avg_response_time' => ChatAnalytics::getAverageResponseTime(),
                'satisfaction_rate' => ChatAnalytics::getSatisfactionRate(),
            ];

            // Intent yang paling sering digunakan
            $topIntents = ChatAnalytics::whereNotNull('intent_type')
                                      ->selectRaw('intent_type, COUNT(*) as count')
                                      ->groupBy('intent_type')
                                      ->orderBy('count', 'desc')
                                      ->limit(5)
                                      ->get();

            // Query type yang paling sering digunakan
            $topQueryTypes = ChatAnalytics::whereNotNull('query_type')
                                         ->selectRaw('query_type, COUNT(*) as count')
                                         ->groupBy('query_type')
                                         ->orderBy('count', 'desc')
                                         ->limit(5)
                                         ->get();

            // Aktivitas per jam (24 jam terakhir)
            $hourlyActivity = ChatAnalytics::where('created_at', '>=', now()->subDay())
                                          ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                                          ->groupBy('hour')
                                          ->orderBy('hour')
                                          ->get();

            // Aktivitas per hari (7 hari terakhir)
            $dailyActivity = ChatAnalytics::where('created_at', '>=', now()->subWeek())
                                         ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                         ->groupBy('date')
                                         ->orderBy('date')
                                         ->get();

            // User yang paling aktif
            $topUsers = ChatAnalytics::with('user')
                                    ->whereNotNull('user_id')
                                    ->selectRaw('user_id, COUNT(*) as session_count, SUM(message_count) as total_messages')
                                    ->groupBy('user_id')
                                    ->orderBy('session_count', 'desc')
                                    ->limit(10)
                                    ->get();

            // Session terbaru
            $recentSessions = ChatAnalytics::with('user')
                                          ->latest()
                                          ->limit(10)
                                          ->get();

        } catch (\Exception $e) {
            // Fallback data jika database tidak tersedia
            $stats = [
                'total_sessions' => 0,
                'total_messages' => 0,
                'active_sessions_today' => 0,
                'avg_response_time' => 0,
                'satisfaction_rate' => 0,
            ];

            $topIntents = collect([]);
            $topQueryTypes = collect([]);
            $hourlyActivity = collect([]);
            $dailyActivity = collect([]);
            $topUsers = collect([]);
            $recentSessions = collect([]);
        }

        return view('admin.chat_analytics', compact(
            'stats',
            'topIntents',
            'topQueryTypes',
            'hourlyActivity',
            'dailyActivity',
            'topUsers',
            'recentSessions'
        ));
    }

    public function sessions()
    {
        $sessions = ChatAnalytics::with('user')
                                ->latest()
                                ->paginate(20);

        return view('admin.chat_sessions', compact('sessions'));
    }

    public function sessionDetail($sessionId)
    {
        $session = ChatAnalytics::with('user')
                               ->where('session_id', $sessionId)
                               ->firstOrFail();

        $messages = ChatHistory::where('user_id', $session->user_id)
                              ->where('created_at', '>=', $session->started_at)
                              ->where('created_at', '<=', $session->ended_at ?? now())
                              ->orderBy('created_at')
                              ->get();

        return view('admin.chat_session_detail', compact('session', 'messages'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = ChatAnalytics::with('user');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $data = $query->get();

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json($data);
    }

    private function exportToCsv($data)
    {
        $filename = 'chat_analytics_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'User ID', 'User Name', 'Session ID', 'Intent Type', 'Query Type',
                'Was Helpful', 'Response Time (ms)', 'Message Count', 'Started At', 'Ended At',
                'User Agent', 'IP Address', 'Created At'
            ]);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user_id,
                    $row->user ? $row->user->nama : 'Guest',
                    $row->session_id,
                    $row->intent_type,
                    $row->query_type,
                    $row->was_helpful ? 'Yes' : 'No',
                    $row->response_time_ms,
                    $row->message_count,
                    $row->started_at,
                    $row->ended_at,
                    $row->user_agent,
                    $row->ip_address,
                    $row->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 