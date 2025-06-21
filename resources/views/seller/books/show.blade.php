@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h5>{{ $book->title }}</h5>
        </div>
        <div class="card-body">
            <img src="{{ asset('storage/' . $book->cover_path) }}" class="mb-3" width="150" alt="Cover">
            <p><strong>Penulis:</strong> {{ $book->author }}</p>
            <p><strong>Harga:</strong> Rp{{ number_format($book->price, 0, ',', '.') }}</p>
            <p><strong>Stok:</strong> {{ $book->stock }}</p>
            <p><strong>Kategori:</strong> {{ ucfirst($book->category) }}</p>
            <p><strong>Deskripsi:</strong><br>{{ $book->description }}</p>
        </div>
    </div>
</div>
@endsection
