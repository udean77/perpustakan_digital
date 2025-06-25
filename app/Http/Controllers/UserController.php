<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Book;

class UserController extends Controller
{
    
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    
    public function homepage()
    {
        $books = Book::with('store')
            ->where('status', 'active')
            ->whereHas('store', function ($query) {
                $query->where('status', 'active');  // hanya dari toko aktif
            })
            ->latest()
            ->take(20)
            ->get();
        return view('user.homepage',compact('books')); // pastikan view ini ada di resources/views/user/homepage.blade.php
    }
    
    /**
     * Menampilkan profil pengguna (opsional).
     */
    /**
     * Mengubah role menjadi penjual.
     */
    public function becomeSeller()
    {
        $user = Auth::user();

        if ($user->role === 'penjual') {
            return back()->with('info', 'Kamu sudah menjadi penjual.');
        }

        $user->role = 'penjual';
        $user->save();

        // Re-login user agar session role diperbarui
        Auth::login($user);

        return redirect()->route('seller.dashboard')->with('success', 'Kamu sekarang menjadi penjual!');
    }


    /**
     * Update profil pengguna (opsional).
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->nama = $request->nama; // pakai 'nama', sesuai nama kolom di tabel
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function editField($field)
    {
        $user = Auth::user();

        // Daftar field yang boleh diedit
        $allowedFields = ['nama', 'email', 'hp', 'tanggal_lahir', 'jenis_kelamin'];

        if (!in_array($field, $allowedFields)) {
            abort(404);
        }

        return view('user.edit-field', compact('user', 'field'));
    }

    public function updateField(Request $request, $field)
    {
        $user = Auth::user();

        $allowedFields = ['nama', 'email', 'hp', 'tanggal_lahir', 'jenis_kelamin'];

        if (!in_array($field, $allowedFields)) {
            abort(404);
        }

        // Validasi berbeda-beda sesuai field
        $rules = [];

        switch ($field) {
            case 'nama':
                $rules['nama'] = 'required|string|max:255';
                break;
            case 'email':
                $rules['email'] = 'required|email|unique:users,email,' . $user->id;
                break;
            case 'hp':
                $rules['hp'] = 'required|string|max:20';
                break;
            case 'tanggal_lahir':
                $rules['tanggal_lahir'] = 'required|date';
                break;
            case 'jenis_kelamin':
                $rules['jenis_kelamin'] = 'required|in:L,P';
                break;
        }

        $validated = $request->validate($rules);

        $user->$field = $validated[$field];
        $user->save();

        return redirect()->route('user.profile')->with('success', 'Data ' . $field . ' berhasil diperbarui.');
    }

    public function changePasswordSubmit(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        // Logout user supaya session lama berakhir
        Auth::logout();

        // Redirect ke halaman login supaya user login ulang pakai password baru
        return redirect()->route('login')->with('success', 'Kata sandi berhasil diubah, silakan login ulang.');
    }
    


}
