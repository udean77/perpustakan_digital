@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">🚩 Detail Laporan Buku</h4>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">📚 {{ $report->reportable->title ?? 'Judul tidak tersedia' }}</h5>
            <p><strong>Pelapor:</strong> {{ $report->user->nama ?? '-' }}</p>
            <p><strong>Alasan:</strong><br>{{ $report->reason }}</p>
            <p><strong>Status:</strong>
                @if($report->status === 'pending')
                    <span class="badge bg-warning text-dark">Menunggu</span>
                @elseif($report->status === 'process')
                    <span class="badge bg-primary">Diproses</span>
                @else
                    <span class="badge bg-success">Selesai</span>
                @endif
            </p>
            <p><strong>Tanggal Laporan:</strong> {{ $report->created_at->format('d M Y H:i') }}</p>

            <hr>

            <h6>📦 Informasi Buku:</h6>
            <ul>
                <li><strong>Judul:</strong> {{ $report->reportable->title ?? '-' }}</li>
                <li><strong>Penulis:</strong> {{ $report->reportable->author ?? '-' }}</li>
                <li><strong>Harga:</strong> 
                    @if($report->reportable && isset($report->reportable->price))
                        Rp{{ number_format($report->reportable->price, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </li>
                <li><strong>Stok:</strong> {{ $report->reportable->stock ?? '-' }}</li>
            </ul>

            <!-- Aksi untuk update status dan hapus laporan -->
            <div class="mt-4">
                @if($report->status === 'pending')
                    <form action="{{ route('seller.reports.updateStatus', $report->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="process">
                        <button type="submit" class="btn btn-primary">Tandai Diproses</button>
                    </form>
                @elseif($report->status === 'process')
                    <form action="{{ route('seller.reports.updateStatus', $report->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit" class="btn btn-success">Tandai Selesai</button>
                    </form>
                @endif

                <form action="{{ route('seller.reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Laporan</button>
                </form>
            </div>

            <a href="{{ route('seller.reports.index') }}" class="btn btn-secondary mt-3">⬅️ Kembali ke Daftar Laporan</a>
        </div>
    </div>
</div>
@endsection
