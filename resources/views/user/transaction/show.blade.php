@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Detail Order #{{ $order->id }}</h2>

    {{-- Ringkasan Pesanan --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Ringkasan Pesanan</h5>
        </div>
        <div class="card-body">
            @foreach ($order->items as $item)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ $item->book->title }}</strong> <br>
                        Jumlah: {{ $item->quantity }} x Rp{{ number_format($item->book->price, 0, ',', '.') }}

                        {{-- Tampilkan tombol download jika eBook dan sudah bayar --}}
                       @if($item->book->book_type === 'ebook' && $item->book->ebook_file && $order->status === 'completed')
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $item->book->ebook_file) }}" class="btn btn-outline-success btn-sm" download>
                                    <i class="bi bi-file-earmark-arrow-down"></i> Unduh eBook (PDF)
                                </a>
                            </div>
                        @endif

                    </div>
                    <div>
                        Rp{{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>Total Harga</span>
                <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Alamat Pengiriman --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Alamat Pengiriman</h5>
        </div>
        <div class="card-body">
            @if ($order->address)
                <p>
                    <strong>{{ $order->address->label }}</strong> <br>
                    Penerima: {{ $order->address->nama_penerima }} <br>
                    {{ $order->address->alamat_lengkap }} <br>
                    Telepon: {{ $order->address->no_hp }}
                </p>
            @else
                <p>-</p>
            @endif
        </div>
    </div>



    {{-- Metode Pembayaran --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Metode Pembayaran</h5>
        </div>
        <div class="card-body">
            <p>{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
        </div>
    </div>

    {{-- Status Order --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Status Order</h5>
        </div>
        <div class="card-body">
            <p>{{ ucfirst($order->status) }}</p>
        </div>
    </div>

    <a href="{{ route('user.transaction.index') }}" class="btn btn-primary">Kembali ke Daftar Order</a>
</div>
@endsection
