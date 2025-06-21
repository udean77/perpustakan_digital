@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Item Pesanan</h2>

    <!-- Info Buku -->
    <p><strong>Judul Buku:</strong> {{ $orderItem->book->title }}</p>
    <p><strong>Harga:</strong> Rp {{ number_format($orderItem->book->price, 0, ',', '.') }}</p>
    <p><strong>Jumlah:</strong> {{ $orderItem->quantity }}</p>
    <p><strong>Total:</strong> Rp {{ number_format($orderItem->book->price * $orderItem->quantity, 0, ',', '.') }}</p>

    <hr>

    <!-- Info Pembeli -->
    <p><strong>Nama Pembeli:</strong> {{ $orderItem->order->user->nama }}</p>
    <p><strong>Alamat Pengiriman:</strong> {{ $orderItem->order->shipping_address }}</p>
    <p><strong>Tanggal Pesan:</strong> {{ $orderItem->order->ordered_at->format('d M Y') }}</p>
    <p><strong>Status Pesanan:</strong>
        <span class="badge bg-{{ $orderItem->order->status === 'cancelled' ? 'danger' : ($orderItem->order->status === 'completed' ? 'success' : 'secondary') }}">
            {{ ucfirst($orderItem->order->status) }}
        </span>
    </p>

    <hr>

    <!-- Flash Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Form Ubah Status -->
    @if(in_array($orderItem->order->status, ['cancelled', 'completed']))
        <div class="alert alert-warning">
            Pesanan ini telah <strong>{{ $orderItem->order->status === 'cancelled' ? 'dibatalkan oleh pembeli' : 'diselesaikan' }}</strong>. Anda tidak dapat mengubah statusnya.
        </div>
    @else
        <form action="{{ route('seller.orders.update', $orderItem->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="status">Ubah Status Pesanan:</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" {{ $orderItem->order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ $orderItem->order->status === 'processed' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ $orderItem->order->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ $orderItem->order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Update Status</button>
            <!-- Tombol kembali -->
        </form>
        @endif
        <a href="{{ route('seller.orders.index') }}" class="btn btn-secondary mt-3">← Kembali ke Daftar Pesanan</a>
</div>
@endsection
