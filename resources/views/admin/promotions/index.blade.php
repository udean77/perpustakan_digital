@extends('layouts.admin')

@section('title', 'Manajemen Promosi')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Daftar Promosi</h5>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm float-right">Tambah Promosi</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Kode Redeem</th>
                        <th>Status</th>
                        <th>Berakhir Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $promotion->image_path) }}" alt="{{ $promotion->title }}" width="100">
                            </td>
                            <td>{{ $promotion->title }}</td>
                            <td>{{ $promotion->redeemCode->code ?? 'N/A' }}</td>
                            <td>
                                @if($promotion->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>{{ $promotion->expires_at ? $promotion->expires_at->format('d M Y H:i') : 'Tidak ada batas' }}</td>
                            <td>
                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-info btn-sm">Edit</a>
                                <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus promosi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada promosi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $promotions->links() }}
        </div>
    </div>
</div>
@endsection 