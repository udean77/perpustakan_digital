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

        $request->validate([
            'label' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'kode_pos' => 'nullable|string|max:5',
            'nama_penerima' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        $user->addresses()->create([
            'label' => $request->label,
            'province' => $request->province,
            'city' => $request->city,
            'alamat_lengkap' => $request->alamat_lengkap,
            'kode_pos' => $request->kode_pos,
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
            'province' => 'required|string',
            'city' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'kode_pos' => 'nullable|string|max:5',
            'nama_penerima' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        $address = Address::findOrFail($id);

        $address->update([
            'label' => $request->label,
            'province' => $request->province,
            'city' => $request->city,
            'alamat_lengkap' => $request->alamat_lengkap,
            'kode_pos' => $request->kode_pos,
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

    /**
     * Hapus alamat.
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        
        // Pastikan user hanya bisa menghapus alamatnya sendiri
        if ($address->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus alamat ini.');
        }
        
        $address->delete();
        
        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }

}
