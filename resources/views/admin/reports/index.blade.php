@extends('layouts.admin') {{-- Pastikan layout admin sudah ada --}}

@section('content')
<div class="container py-4">
    <h4 class="mb-4">📋 Daftar Laporan Pengguna (Admin)</h4>

    @if($reports->count())
        @php
            // Mapping jenis laporan untuk tampilan user-friendly
            $jenis = [
                'App\Models\Book' => 'Buku',
                'App\Models\Comment' => 'Komentar',
                // Tambahkan entitas lain jika ada
            ];
        @endphp

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Laporan</th>
                    <th>Pelapor</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Tanggal Laporan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $jenis[$report->reportable_type] ?? class_basename($report->reportable_type) }}</td>
                    <td>{{ $report->user->nama ?? '-' }}</td>
                    <td>{{ Str::limit($report->reason, 50) }}</td>
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
                        <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    @else
        <p class="text-muted">Tidak ada laporan saat ini.</p>
    @endif
</div>
@endsection
