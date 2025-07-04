@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h2 class="mb-4">Detail Pesanan</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nomor Pesanan:</strong> {{ $order->id }}</p>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : 'success' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
        </div>
    </div>

    <h4>Detail Buku</h4>
    @foreach($order->items as $item)
    <div class="card mb-3">
        <div class="row g-0 align-items-center">
            <div class="col-md-2 d-flex justify-content-center p-2">
                <img src="{{ asset('storage/' . $item->book->cover) }}" 
                     alt="Cover Buku" 
                     class="img-fluid rounded-start" style="max-height: 120px; object-fit: contain;">
            </div>
            <div class="col-md-10">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->book->title }}</h5>
                    <p class="card-text mb-1"><strong>Harga per Buku:</strong> Rp {{ number_format($item->book->price, 0, ',', '.') }}</p>
                    <p class="card-text mb-1"><strong>Jumlah:</strong> {{ $item->quantity }}</p>
                    <p class="card-text"><strong>Total Harga:</strong> Rp {{ number_format($item->quantity * $item->book->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Total Keseluruhan:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p><strong>Alamat Pengiriman:</strong> {{ $order->shipping_address }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($order->status === 'pending')
        <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin batalkan pesanan?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger">Batalkan Pesanan</button>
        </form>
    @endif

    <!-- Tombol kembali -->
    <div class="mt-4">
        <a href="{{ route('user.transaction.index') }}" class="btn btn-secondary">
            ← Ke Daftar Transaksi
        </a>
    </div>
</div>
@endsection
