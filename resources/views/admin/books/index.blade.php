@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Buku</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Penjual</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                <td>{{ $book->stock }}</td>
                <td>{{ $book->user->nama}}</td>
                <td>
                    <span class="badge {{ $book->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($book->status) }}
                    </span>
                </td>
                 <td class="d-flex gap-1">
                    <!-- Nonaktifkan / Aktifkan Buku -->
                    <form method="POST" action="{{ route('admin.books.toggleStatus', $book->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $book->status == 'active' ? 'btn-warning' : 'btn-success' }}">
                            {{ $book->status == 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <!-- Hapus Buku -->
                    <form method="POST" action="{{ route('admin.books.destroy', $book->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                            Hapus
                        </button>
                    </form>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
