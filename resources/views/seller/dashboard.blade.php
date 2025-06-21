@extends('layouts.app')

@section('title', 'Dashboard Penjual')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Logo Toko -->
                    <img src="{{ Auth::user()->store && Auth::user()->store->logo ? asset('storage/store_logo/' . Auth::user()->store->logo) : asset('images/store_default.png') }}" class="rounded mb-3" width="100" alt="Logo Toko">
                    
                    <!-- Nama Toko -->
                    <h5>{{ Auth::user()->store->name }}</h5>

                    <!-- Info Kontak -->
                    <p class="mb-1 text-muted">{{ Auth::user()->email }}</p>
                    <p class="mb-1">{{ Auth::user()->nomor_hp }}</p>

                    <!-- Status -->
                    <span class="badge bg-success">Terverifikasi</span>
                </div>
            </div>

            <!-- Menu Navigasi -->
            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action active">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9">
            <h4 class="mb-4">📊 Dashboard Penjual</h4>

            <div class="row g-4">
                <!-- Kartu Ringkasan -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Jumlah Buku</h6>
                            <h3>{{ $books_count }}</h3>
                            <small class="text-muted">Total produk yang ditambahkan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Pesanan</h6>
                            <h3>{{ $orders_count }}</h3>
                            <small class="text-muted">Semua pesanan dari pembeli</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Pendapatan</h6>
                            <h3>Rp {{ number_format($total_income, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total penjualan berhasil</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Pesanan Hari Ini</h6>
                            <h3>{{ $today_orders }}</h3>
                            <small class="text-muted">Update harian</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="card mt-5">
                <div class="card-header bg-light">
                    <strong>📈 Ringkasan Penjualan Mingguan</strong>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>

            <!-- Buku Terbaru -->
            <div class="card mt-5">
                <div class="card-header bg-light">
                    <strong>📚 Buku Terbaru</strong>
                </div>
                <div class="card-body">
                    @if($latest_books->count())
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latest_books as $book)
                                        <tr>
                                            <td>{{ $book->title }}</td>
                                            <td>{{ $book->category ?? '-' }}</td>
                                            <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                                            <td>{{ $book->stock }}</td>
                                            <td>{{ $book->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada buku ditambahkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($weekly_sales['labels']) !!},
            datasets: [{
                label: 'Penjualan (Rp)',
                data: {!! json_encode($weekly_sales['totals']) !!},
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
