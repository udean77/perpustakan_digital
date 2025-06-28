<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Store;
use App\Models\Order;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'bookCount' => Book::count(),
            'sellerCount' => User::where('role', 'penjual')->count(),
            'storeCount' => Store::count(),
        ]);
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalOrders = Order::count();
        // ...statistik lain

        return view('admin.dashboard', compact('totalUsers', 'totalBooks', 'totalOrders'));
    }
}
