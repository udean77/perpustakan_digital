@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->store && Auth::user()->store->logo 
                        ? asset('storage/store_logo/' . Auth::user()->store->logo) 
                        : asset('images/store_default.png') }}" 
                        class="rounded mb-3" width="100" alt="Logo Toko">
                    <h5>{{ Auth::user()->store->name }}</h5>
                    <p class="mb-1 text-muted">{{ Auth::user()->email }}</p>
                    <p class="mb-1">{{ Auth::user()->nomor_hp }}</p>
                    <span class="badge bg-success">Terverifikasi</span>
                </div>
            </div>

            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action active">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9">
            <div class="container py-4">
                <h4 class="mb-4">📋 Laporan Buku di Toko Anda</h4>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($reports->isEmpty())
                    <p class="text-muted">Tidak ada laporan terhadap buku Anda.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Pelapor</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    @if($report->reportable && class_basename($report->reportable_type) === 'Book')
                                        <tr>
                                            <td>
                                                <strong>{{ $report->reportable->title }}</strong>
                                            </td>
                                            <td>{{ $report->user->nama ?? '-' }}</td>
                                            <td>{{ Str::limit($report->reason, 80) }}</td>
                                            <td>
                                                @if($report->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @elseif($report->status === 'process')
                                                    <span class="badge bg-primary">Diproses</span>
                                                @else
                                                    <span class="badge bg-success">Selesai</span>
                                                @endif
                                            </td>
                                            <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('seller.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
