<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class UserOrderSummaryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Total jumlah order
        $totalOrders = Order::where('user_id', $user->id)->count();

        // Total pendapatan (jumlah total_amount dari pesanan selesai)
        $totalRevenue = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Ringkasan status
        $orderStatusCounts = Order::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // 5 pesanan terakhir
        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('ordered_at', 'desc')
            ->take(5)
            ->get();

        return view('seller.details.index', compact(
            'totalOrders',
            'totalRevenue',
            'orderStatusCounts',
            'recentOrders'
        ));
    }
}
