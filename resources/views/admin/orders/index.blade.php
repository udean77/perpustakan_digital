@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Pesanan</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pemesan</th>
                    <th>Tanggal Pesan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->nama }}</td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $statusClass = match($order->status) {
                                'pending' => 'badge bg-warning',
                                'dikirim' => 'badge bg-info',
                                'selesai' => 'badge bg-success',
                                'dibatalkan' => 'badge bg-danger',
                                default => 'badge bg-secondary'
                            };
                        @endphp
                        <span class="{{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
