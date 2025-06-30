@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Detail Pesanan #{{ $order->id }}</h2>

    <div class="mb-3">
        <strong>Nama Pemesan:</strong> {{ $order->user->nama }} <br>
        <strong>Email:</strong> {{ $order->user->email }} <br>
        <strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }} <br>
        <strong>Status:</strong> 
        <span class="badge bg-{{ $order->status === 'selesai' ? 'success' : ($order->status === 'dikirim' ? 'info' : ($order->status === 'dibatalkan' ? 'danger' : 'warning')) }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <h5>Daftar Buku:</h5>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->book->title }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-end">
        <h5>Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
</div>
@endsection
