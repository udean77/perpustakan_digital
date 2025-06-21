<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:admin,penjual,pembeli',
            'status' => 'required|in:active,inactive',
        ]);

        // Buat user baru dengan data yang sudah divalidasi dan hash password
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }


    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Toggle status antara 'active' dan 'inactive'
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'Status pengguna berhasil diperbarui.');
    }


    public function resetPassword(User $user) {
        $user->password = Hash::make('password123'); // Atur default baru
        $user->save();
        return back()->with('success', 'Password telah direset.');
    }

    public function changeRole(Request $request, User $user) {
        // Validate that the role is only 'admin'
        $request->validate(['role' => 'required|in:admin']);

        // Change the user's role to 'admin'
        $user->role = 'admin';
        $user->save();

        return back()->with('success', 'Peran pengguna berhasil diubah menjadi Admin.');
    }

}

