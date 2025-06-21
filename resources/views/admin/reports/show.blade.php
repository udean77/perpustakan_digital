@extends('layouts.admin') {{-- Gunakan layout admin kamu --}}

@section('title', 'Detail Laporan')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Detail Laporan</h1>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Informasi Pelapor --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Informasi Pelapor</h5>
            <p><strong>Nama:</strong> {{ $report->user->nama }}</p>
            <p><strong>Email:</strong> {{ $report->user->email }}</p>
        </div>
    </div>

    {{-- Detail Laporan --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Detail Laporan</h5>

            @php
                $jenis = [
                    'App\Models\Book' => 'Buku',
                    'App\Models\Order' => 'Transaksi',
                    'App\Models\User' => 'Pengguna',
                ];
                $jenisLaporan = $jenis[$report->reportable_type] ?? class_basename($report->reportable_type);
            @endphp

            <p><strong>Jenis Laporan:</strong> {{ $jenisLaporan }}</p>

            @if ($report->reportable_type === 'App\Models\Book')
                <p><strong>Judul Buku:</strong> {{ $report->reportable->title ?? '-' }}</p>
                <p><strong>Penulis:</strong> {{ $report->reportable->author ?? '-' }}</p>
            @elseif ($report->reportable_type === 'App\Models\Order')
                <p><strong>ID Transaksi:</strong> #{{ $report->reportable->id ?? '-' }}</p>
            @elseif ($report->reportable_type === 'App\Models\User')
                <p><strong>Nama Pengguna:</strong> {{ $report->reportable->nama ?? '-' }}</p>
                <p><strong>Email Pengguna:</strong> {{ $report->reportable->email ?? '-' }}</p>
            @else
                <p><strong>Detail Objek:</strong> Tidak tersedia</p>
            @endif

            <p><strong>Alasan Laporan:</strong></p>
            <div class="border p-2 mb-2">{{ $report->reason ?? '-' }}</div>

            <p><strong>Tanggal Lapor:</strong> {{ $report->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Ubah Status --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Status Laporan</h5>
            <form action="{{ route('admin.reports.updateStatus', $report->id) }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select w-auto">
                    <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="process" {{ $report->status === 'process' ? 'selected' : '' }}>Diproses</option>
                    <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                </select>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    {{-- Hapus Laporan --}}
    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus Laporan</button>
    </form>

    {{-- Kembali --}}
    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary mt-3">← Kembali ke Daftar Laporan</a>
</div>
@endsection
