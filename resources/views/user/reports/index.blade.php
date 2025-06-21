@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Riwayat Laporan Saya</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($reports->isEmpty())
        <p>Belum ada laporan.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Detail</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    @php
                        $item = $report->reportable;
                        $type = class_basename($report->reportable_type); // Misal: Book, Seller, Order
                    @endphp
                    <tr>
                        <td>{{ $type }}</td>
                        <td>
                            @if($item instanceof \App\Models\Book)
                                Judul Buku: {{ $item->title }}<br>
                                Penjual: {{ $item->seller->store->name ?? $item->seller->name ?? '-' }}
                            @elseif($item instanceof \App\Models\User)
                                Penjual: {{ $item->store->name ?? $item->nama }}
                            @elseif($item instanceof \App\Models\Order)
                                Transaksi #{{ $item->id }} - {{ $item->status }}
                            @else
                                <em>Data tidak ditemukan</em>
                            @endif
                        </td>
                        <td>{{ $report->reason }}</td>
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('user.reports.create') }}" class="btn btn-primary mt-3">Buat Laporan Baru</a>
</div>
@endsection
