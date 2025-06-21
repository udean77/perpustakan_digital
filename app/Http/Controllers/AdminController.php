<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Transaction;

class AdminController extends Controller
{
    // 1. Dashboard Admin
    public function dashboard()
    {
        $userCount = User::count();
        $bookCount = Book::count();
        $transactionCount = Transaction::count();

        return view('admin.dashboard', compact('userCount', 'bookCount', 'transactionCount'));
    }

    // 2. Daftar Pengguna
    public function users()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.users.index', compact('users'));
    }

    // 3. Toggle Status Pengguna
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Status pengguna diperbarui.');
    }

    // 4. Daftar Buku
    public function books()
    {
        $books = Book::with('seller')->get(); // pastikan relasi `seller` ada
        return view('admin.books.index', compact('books'));
    }

    // 5. Daftar Transaksi
    public function transactions()
    {
        $transactions = Transaction::with('user')->latest()->get(); // pastikan relasi `user` ada
        return view('admin.transactions.index', compact('transactions'));
    }

    // 6. Ubah Role Pengguna
    public function editRole($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit-role', compact('user'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:guest,pembeli,penjual',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Role pengguna diperbarui.');
    }
}
