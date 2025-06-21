<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Store;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'bookCount' => Book::count(),
            'sellerCount' => User::where('role', 'penjual')->count(),
            'storeCount' => Store::count(), // Tambahkan ini
        ]);

    }
}
