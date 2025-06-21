<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

   public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240', // max 10MB
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('profile_photos', $filename, 'public');

            // Hapus foto lama jika ada
            if ($user->foto) {
                \Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $filePath;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diunggah.');
        }

        return back()->withErrors(['photo' => 'Tidak ada file foto yang diunggah.']);
    }


    public function editField($field)
    {
        $user = auth()->user();

        $allowedFields = ['nama', 'tanggal_lahir', 'jenis_kelamin', 'email', 'phone'];
        if (!in_array($field, $allowedFields)) {
            abort(404);
        }

        return view('profile.edit', compact('user', 'field'));
    }

    public function updateField(Request $request, $field)
    {
        $user = auth()->user();

        $allowedFields = ['nama', 'tanggal_lahir', 'jenis_kelamin', 'email', 'phone'];
        if (!in_array($field, $allowedFields)) {
            abort(404);
        }

        // Validasi sesuai field, contoh sederhana:
        $rules = [];
        switch ($field) {
            case 'nama':
                $rules[$field] = 'required|string|max:255';
                break;
            case 'tanggal_lahir':
                $rules[$field] = 'nullable|date';
                break;
            case 'jenis_kelamin':
                $rules[$field] = 'nullable|in:L,P'; // Contoh L=Pria, P=Wanita
                break;
            case 'email':
                $rules[$field] = 'required|email|unique:users,email,' . $user->id;
                break;
            case 'phone':
                $rules[$field] = 'nullable|string|max:13';
                break;
        }

        $request->validate($rules);

        $user->$field = $request->input($field);
        $user->save();

        return redirect()->route('user.profile')->with('success', ucfirst($field) . ' berhasil diperbarui.');
    }


    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'], // new_password_confirmation harus ada di form
        ]);

        $user = auth()->user();

        // Cek password lama cocok atau tidak
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok']);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user.profile')->with('success', 'Password berhasil diubah.');
    }

    public function showProfile()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->get();

        return view('user.profile', compact('user', 'addresses'));
    }

}

