@extends('layouts.app')

@section('title', 'Ringkasan Penjualan')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Logo Toko -->
                    <img src="{{ Auth::user()->store && Auth::user()->store->logo ? asset('storage/store_logo/' . Auth::user()->store->logo) : asset('images/store_default.png') }}"
                        class="rounded mb-3" width="100" alt="Logo Toko">
                    
                    <!-- Nama Toko -->
                    <h5>{{ Auth::user()->store->name }}</h5>
                    
                    <!-- Info Kontak -->
                    <p class="mb-1 text-muted">{{ Auth::user()->email }}</p>
                    <p class="mb-1">{{ Auth::user()->nomor_hp }}</p>
                    
                    <!-- Status -->
                    <span class="badge bg-success">Terverifikasi</span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action active">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9">
            <h3 class="mb-4">📊 Ringkasan Penjualan</h3>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Transaksi</h5>
                            <p class="card-text fs-4">{{ $totalOrders }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Pendapatan (Completed)</h5>
                            <p class="card-text fs-4">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Status Pesanan</h5>
                            @foreach($orderStatusCounts as $status => $count)
                                <p class="mb-1">{{ ucfirst($status) }}: {{ $count }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <h5>🕒 Pesanan Terbaru</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Metode Pembayaran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>Rp {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
