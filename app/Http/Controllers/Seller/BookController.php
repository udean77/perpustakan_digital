<?php

namespace App\Http\Controllers\Seller;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // Kalau ingin pakai kategori hardcoded, buat fungsi ini
    protected function getCategories()
    {
        return [
            'Fiksi',
            'Non-Fiksi',
            'Pendidikan',
            'Novel',
            'Komik',
            // Tambahkan kategori lainnya sesuai kebutuhan
        ];
    }

    public function index()
    {
        $books = Book::where('user_id', auth()->id())->get();
        $categories = $this->getCategories(); 
        return view('seller.books.index', compact('books','categories'));
    }

    public function create()
    {
        $categories = $this->getCategories();
        return view('seller.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $maxPrice = 99999999.99;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => ['required', 'numeric', 'max:' . $maxPrice],
            'stock' => 'required|integer',
            'category' => 'required|string|max:255',  // kategori sebagai string
            'description' => 'required|string',
            'book_type' => 'required|in:physical,ebook',
            'cover' => 'required|image|max:2048',
            'ebook_file' => 'nullable|file|mimes:pdf,epub,mobi|max:10240',
            'physical_book_file' => 'nullable|file|mimes:zip,rar|max:10240',
            'publisher' => 'nullable|string|max:255',
         ], [
            'price.max' => 'Harga maksimal adalah Rp 99.999.999,99. Mohon masukkan harga yang lebih kecil.',
        ]);

        $book = new Book();
        $book->user_id = auth()->id();

        // Assign kategori manual karena fillable tidak otomatis
        $book->category = $validated['category'];

        // Fill atribut lain
        $book->fill($validated);

        // Upload file cover
        $book->cover = $request->file('cover')->store('covers', 'public');

        if ($request->file('ebook_file')) {
            $book->ebook_file = $request->file('ebook_file')->store('ebooks', 'public');
        }
        if ($request->file('physical_book_file')) {
            $book->physical_book_file = $request->file('physical_book_file')->store('physical_books', 'public');
        }

        // Assign store_id dari user yang login
        $book->store_id = Auth::user()->store->id ?? null;

        $book->save();

        return redirect()->route('seller.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $book = Book::where('user_id', Auth::id())->findOrFail($id);
        return view('seller.books.show', compact('book'));
    }

    public function edit(string $id)
    {
        $book = Book::where('user_id', Auth::id())->findOrFail($id);
        $categories = $this->getCategories();
        return view('seller.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $book = Book::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $maxPrice = 99999999.99;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => ['required', 'numeric', 'max:' . $maxPrice],
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255', // perbaikan: kategori sebagai string
            'description' => 'nullable|string',
            'book_type' => 'required|in:physical,ebook',
            'cover' => 'nullable|image|max:2048',
            'ebook_file' => 'nullable|file|mimes:pdf,epub,mobi|max:10240',
            'physical_book_file' => 'nullable|file|mimes:zip,rar|max:10240',
            'publisher' => 'nullable|string|max:255',
        ], [
            'price.max' => 'Harga maksimal adalah Rp 99.999.999,99. Mohon masukkan harga yang lebih kecil.',
        ]);
        // Assign kategori manual
        $book->category = $validated['category'];

        // Fill atribut lain
        $book->fill($validated);

        if ($request->hasFile('cover')) {
            // Hapus file lama jika ada
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = $request->file('cover')->store('covers', 'public');
        }
        if ($request->hasFile('ebook_file')) {
            if ($book->ebook_file) {
                Storage::disk('public')->delete($book->ebook_file);
            }
            $book->ebook_file = $request->file('ebook_file')->store('ebooks', 'public');
        }
        if ($request->hasFile('physical_book_file')) {
            if ($book->physical_book_file) {
                Storage::disk('public')->delete($book->physical_book_file);
            }
            $book->physical_book_file = $request->file('physical_book_file')->store('physical_books', 'public');
        }

        // Assign store_id jika belum ada
        if (!$book->store_id) {
            $book->store_id = Auth::user()->store->id ?? null;
        }

        $book->save();

        return redirect()->route('seller.books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $book = Book::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        // Hapus file terkait jika ada
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        if ($book->ebook_file) {
            Storage::disk('public')->delete($book->ebook_file);
        }
        if ($book->physical_book_file) {
            Storage::disk('public')->delete($book->physical_book_file);
        }

        $book->delete();

        return redirect()->route('seller.books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
