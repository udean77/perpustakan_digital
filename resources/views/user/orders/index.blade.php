@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🛒 Checkout</h2>

    {{-- Informasi Pembeli --}}
    <div class="card mb-4">
        <div class="card-header">Informasi Pembeli</div>
        <div class="card-body">
            <p><strong>Nama:</strong> {{ auth()->user()->nama }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        </div>
    </div>

    {{-- Rincian Pesanan --}}
    <div class="card mb-4">
        <div class="card-header bg-light fw-semibold">Rincian Pesanan</div>
        <div class="card-body">
            @if ($cartItems->isNotEmpty())
                {{-- Debug info --}}
                <div class="alert alert-info">
                    <strong>Debug:</strong> Menampilkan {{ $cartItems->count() }} item(s)
                    @if(request()->has('selected_items'))
                        <br>Selected items: {{ implode(', ', request('selected_items')) }}
                    @endif
                </div>
                
                @foreach($cartItems as $item)
                    <div class="d-flex mb-3 p-2 border rounded shadow-sm">
                        <img src="{{ asset('storage/' . $item->book->cover) }}" 
                            alt="Cover Buku" 
                            class="me-3 rounded" 
                            style="width: 80px; height: 100px; object-fit: cover;">

                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->book->title }}</h6>
                            <div class="text-muted small">
                                <div>Harga: Rp{{ number_format($item->book->price, 0, ',', '.') }}</div>
                                <div>Jumlah: {{ $item->quantity }}</div>
                                <div class="fw-semibold mt-1">Subtotal: Rp{{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-medium">Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="text-center text-muted">Tidak ada item dalam pesanan.</div>
            @endif
        </div>
    </div>

    {{-- Form Checkout --}}
    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        {{-- Hidden inputs untuk selected cart items --}}
        @foreach($cartItems as $item)
            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
        @endforeach

        {{-- Alamat Pengiriman --}}
        <div class="card mb-4">
            <div class="card-header">Alamat Pengiriman</div>
            <div class="card-body">
               @forelse ($addresses as $address)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="shipping_address"
                            id="address{{ $address->id }}"
                            value="{{ $address->alamat_lengkap }}"
                            {{ old('shipping_address') == $address->alamat_lengkap ? 'checked' : '' }}>
                        <label class="form-check-label" for="address{{ $address->id }}">
                            {{ $address->label }} - {{ $address->alamat_lengkap }}
                        </label>
                    </div>
                @empty
                    <div class="text-danger">
                        Anda belum memiliki alamat pengiriman.
                        <a href="{{ route('user.address.index') }}" class="btn btn-sm btn-outline-primary mt-2">Tambah Alamat</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Biaya Pengiriman --}}
        <div class="card mb-4">
            <div class="card-header">Biaya Pengiriman</div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="shipping_cost" value="15000" id="shipping_15000" checked>
                    <label class="form-check-label" for="shipping_15000">
                        Regular (1-3 hari) - Rp 15.000
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="shipping_cost" value="25000" id="shipping_25000">
                    <label class="form-check-label" for="shipping_25000">
                        Express (1 hari) - Rp 25.000
                    </label>
                </div>
            </div>
        </div>

        {{-- Kode Redeem (Opsional) --}}
        <div class="card mb-4">
            <div class="card-header">Kode Redeem (Opsional)</div>
            <div class="card-body">
                <input type="text" name="redeem_code" class="form-control" placeholder="Masukkan kode redeem jika ada" value="{{ old('redeem_code') }}">
                <small class="text-muted">Kode redeem dapat memberikan diskon pada pembelian Anda</small>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100"
            onclick="return confirm('Apakah Anda yakin ingin melanjutkan pembayaran?') && confirm('Pastikan semua data sudah benar. Konfirmasi lagi untuk bayar!');">
            Bayar Sekarang
        </button>
    </form>
</div>
@endsection
