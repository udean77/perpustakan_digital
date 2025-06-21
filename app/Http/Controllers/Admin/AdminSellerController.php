<?php

namespace App\Http\Controllers\Admin;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSellerController extends Controller
{
    public function index()
    {
        $sellers = Store::with('user')
                        ->withCount('books')
                        ->withAvg('reviews', 'rating')
                        ->get();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show($id)
    {
        $seller = Store::with('user', 'books')->findOrFail($id);
        return view('admin.sellers.detail', compact('seller'));
    }

    public function create()
    {
        // Kalau kamu perlu pilih user yg belum punya toko, bisa ambil user yg role penjual
        $users = User::where('role', 'penjual')->doesntHave('store')->get();
        return view('admin.sellers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:stores,user_id', // pastikan user belum punya toko
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|in:active,inactive,pending',
        ]);

        Store::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.sellers.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $seller = Store::findOrFail($id);
        $users = User::where('role', 'penjual')->get(); // opsional, kalau ingin ganti user toko
        return view('admin.sellers.edit', compact('seller', 'users'));
    }

    public function update(Request $request, $id)
    {
        $seller = Store::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:stores,user_id,' . $seller->id,
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $seller->update([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.sellers.index')->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $seller = Store::findOrFail($id);
        $seller->delete();

        return redirect()->route('admin.sellers.index')->with('success', 'Toko berhasil dihapus.');
    }

    public function activate($id)
    {
        $seller = Store::findOrFail($id);
        $seller->status = 'active';
        $seller->save();

        return redirect()->back()->with('success', 'Toko berhasil diaktifkan.');
    }

    public function deactivate($id)
    {
        $seller = Store::findOrFail($id);
        $seller->status = 'inactive';
        $seller->save();

        return redirect()->back()->with('success', 'Toko berhasil dinonaktifkan.');
    }

    public function verify($id)
    {
        $seller = Store::findOrFail($id);

        if ($seller->status === 'pending') {
            $seller->status = 'active';
            $seller->save();

            return redirect()->back()->with('success', 'Toko berhasil diverifikasi dan diaktifkan.');
        }

        return redirect()->back()->with('warning', 'Toko sudah diverifikasi sebelumnya atau tidak dalam status pending.');
    }
}
