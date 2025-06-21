<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if ($user && $user->status !== 'active') {
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'penjual') {
                return redirect()->route('seller.dashboard');
            } elseif ($user->role === 'pembeli') {
                return redirect()->route('user.homepage');
            } else {
                return redirect('/'); // guest
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Tampilkan form registrasi
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses registrasi
    // Proses registrasi
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:pembeli,penjual', // validasi role
        ]);

        $user = new User();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role; // ambil dari form
        $user->save();

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
