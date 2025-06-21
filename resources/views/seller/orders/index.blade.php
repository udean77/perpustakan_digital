@extends('layouts.app')

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

            <!-- Sidebar Menu -->
            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action active">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <h3 class="mb-4">Daftar Pesanan Buku</h3>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Judul Buku</th>
                        <th>Pembeli</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orderItems as $item)
                        <tr>
                            <td>#{{ $item->order->id }}</td>
                            <td>{{ $item->book->title }}</td>
                            <td>{{ $item->order->user->nama }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->status ?? 'pending' }}</td>
                           <td>
                                <a href="{{ route('seller.orders.show', $item->id) }}" class="btn btn-sm btn-primary">Detail</a>

                                @if ($item->order->status !== 'cancelled' && $item->order->status !== 'completed')
                                <form action="{{ route('seller.orders.cancel', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin batalkan pesanan ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Batalkan</button>
                                </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $orderItems->links() }}
        </div>
    </div>
</div>
@endsection
