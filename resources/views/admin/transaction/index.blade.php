@extends('layouts.admin')

@section('title', 'Manajemen Keuangan')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Laporan Keuangan</h2>

    {{-- Ringkasan Pendapatan Bulanan --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5>Total Pendapatan Bulan Ini</h5>
            <h3 class="text-success">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama Pembeli</th>
                    <th>Nama Penjual</th>
                    <th>Jumlah</th>
                    <th>Diskon</th>
                    <th>Kode Redeem</th>
                    <th>Status</th>
                    <th>Aksi</th> {{-- Kolom aksi --}}
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    @php
                        $firstItem = $trx->items->first();
                        $seller = $firstItem?->book?->user;
                        $sellerName = ($seller && $seller->role === 'penjual') ? $seller->nama : 'Tidak diketahui';
                    @endphp
                    <tr>
                        <td>#{{ $trx->id }}</td>
                        <td>{{ $trx->created_at->format('d M Y') }}</td>
                        <td>{{ $trx->user->nama ?? '-' }}</td>
                        <td>{{ $sellerName }}</td>
                        <td style="white-space: nowrap;">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->discount_amount > 0)
                                <span class="text-success" style="white-space: nowrap;">-Rp{{ number_format($trx->discount_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">Rp0</span>
                            @endif
                        </td>
                        <td>
                            @if($trx->redeemCode)
                                <span class="badge" style="background-color: #1A592D; color: white;">{{ $trx->redeemCode->code }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ 
                                $trx->status === 'selesai' ? 'success' : 
                                ($trx->status === 'dikirim' ? 'info' : 
                                ($trx->status === 'dibatalkan' ? 'danger' : 'warning')) 
                            }}">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.transaction.show', $trx->id) }}" class="btn btn-primary btn-sm">
                                Show
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
