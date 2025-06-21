@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="p-4 bg-light rounded-4 shadow-sm">
        <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

        @if(count($cartItems) > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Buku</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td class="d-flex align-items-center">
                                <a href="{{ route('books.show', $item->book->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="{{ asset('storage/' . $item->book->cover) }}" alt="cover" width="50" class="me-3 rounded">
                                    <div>
                                        <div class="fw-semibold">{{ $item->book->title }}</div>
                                    </div>
                                </a>
                            </td>

                            <td>Rp {{ number_format($item->book->discount_price ?? $item->book->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format(($item->book->discount_price ?? $item->book->price) * $item->quantity, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <h5>Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></h5>
                <a href="{{ route('checkout.index') }}" class="btn btn-success">Lanjut ke Pembayaran</a>
            </div>
        @else
            <div class="text-center p-5">
                <img src="{{ asset('images/cart.png') }}" alt="Kosong" width="120">
                <h5 class="mt-3 fw-bold">Keranjangmu masih kosong</h5>
                <p class="text-muted">Yuk, cari buku favoritmu dan masukkan ke keranjang!</p>
                <a href="{{ route('user.homepage') }}" class="btn btn-outline-primary rounded-pill px-4">Mulai Belanja</a>
            </div>
        @endif
    </div>
</div>
@endsection
