@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📚 Wishlist Saya</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($books->isEmpty())
        <div class="alert alert-info">
            Wishlist kamu masih kosong. Yuk, tambahkan buku favoritmu!
        </div>
    @else
        <div class="row">
            @foreach ($books as $book)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}"
                                 class="card-img-top"
                                 alt="{{ $book->title }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-book.png') }}"
                                 class="card-img-top"
                                 alt="Default Cover"
                                 style="height: 200px; object-fit: cover;">
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $book->title }}</h5>
                            <p class="card-text text-muted">
                                Penulis: {{ $book->author ?? 'Tidak diketahui' }}
                            </p>
                            <p class="card-text">
                                {{ Str::limit($book->description, 100) }}
                            </p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-primary">
                                    Lihat Buku
                                </a>
                                <form action="{{ route('user.wishlist.destroy', $book->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">🗑 Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
