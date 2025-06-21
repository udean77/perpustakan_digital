@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Detail Pesanan #{{ $order->id }}</h2>

    <div class="mb-3">
        <strong>Nama Pemesan:</strong> {{ $order->user->nama }} <br>
        <strong>Email:</strong> {{ $order->user->email }} <br>
        <strong>Telepon:</strong> {{ $order->user->hp ?? '-' }} <br>
        <strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }} <br>
        <strong>Status:</strong> 
        <span class="badge bg-{{ 
            $order->status === 'selesai' ? 'success' : 
            ($order->status === 'dikirim' ? 'info' : 
            ($order->status === 'dibatalkan' ? 'danger' : 'warning')) 
        }}">
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

    @php
        $subtotal = $order->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    @endphp

    <div class="text-end">
        <h5>Subtotal: Rp {{ number_format($subtotal, 0, ',', '.') }}</h5>
        <h5>Diskon: 
            @if($order->discount_amount > 0)
                <span class="text-success">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                @if($order->redeemCode)
                    <small class="text-muted">({{ $order->redeemCode->code }})</small>
                @endif
            @else
                Rp 0
            @endif
        </h5>
        <h5>Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
    </div>

    {{-- Informasi Kode Redeem --}}
    @if($order->redeemCode)
    <div class="card mt-3">
        <div class="card-header">
            <h5>Informasi Kode Redeem</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode:</strong> <code style="padding: 4px 8px; border: 1px solid #dee2e6; background-color: #f8f9fa; color: #495057; border-radius: 4px; font-weight: 600; font-size: 14px;">{{ $order->redeemCode->code }}</code></p>
                    <p><strong>Tipe:</strong> {{ ucfirst($order->redeemCode->type) }}</p>
                    <p><strong>Nilai:</strong> 
                        @if($order->redeemCode->value_type === 'percentage')
                            {{ $order->redeemCode->value }}%
                        @else
                            Rp{{ number_format($order->redeemCode->value, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Diskon Diterima:</strong> <span class="text-success">Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span></p>
                    <p><strong>Deskripsi:</strong> {{ $order->redeemCode->description ?? '-' }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $order->redeemCode->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($order->redeemCode->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.transaction.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Transaksi</a>
</div>
@endsection
