<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Tampilkan Daftar Buku
    public function index()
    {
        $books = Book::with('user')->get(); // Mengambil semua buku
        return view('admin.books.index', compact('books'));
    }


    public function toggleStatus($id)
    {
        $book = Book::findOrFail($id); // Ambil data buku berdasarkan ID

        // Toggle status buku
        $book->status = ($book->status === 'active') ? 'inactive' : 'active';
        $book->save();

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Status buku berhasil diperbarui.');
    }

    // Tampilkan Form Create Buku
    public function create()
    {
        return view('admin.books.create');
        
    }

    // Simpan Buku Baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        // Menangani upload gambar (optional)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('books');
        }

        // Simpan Buku
        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    // Tampilkan Form Edit Buku
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    // Update Buku
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        // Menangani upload gambar (optional)
        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::delete($book->image); // Hapus gambar lama jika ada
            }
            $data['image'] = $request->file('image')->store('books');
        }

        // Update Buku
        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    // Hapus Buku
    public function destroy(Book $book)
    {
        if ($book->image) {
            Storage::delete($book->image); // Hapus gambar buku jika ada
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
