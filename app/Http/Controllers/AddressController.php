<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * Simpan alamat baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek apakah user sudah memiliki alamat utama
        if ($user->addresses()->exists()) {
            return redirect()->back()->with('error', 'Kamu sudah memiliki alamat utama.');
        }

        $request->validate([
            'label' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'nama_penerima' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        $user->addresses()->create([
            'label' => $request->label,
            'alamat_lengkap' => $request->alamat_lengkap,
            'nama_penerima' => $request->nama_penerima,
            'no_hp' => $request->no_hp,
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Perbarui data alamat.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'nama_penerima' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        $address = Address::findOrFail($id);

        $address->update([
            'label' => $request->label,
            'alamat_lengkap' => $request->alamat_lengkap,
            'nama_penerima' => $request->nama_penerima,
            'no_hp' => $request->no_hp,
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
    }
    public function edit($id)
    {
        $address = Address::findOrFail($id);
        return view('address.edit', compact('address'));
    }

}
