@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">Manajemen Penjual</h2>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Nama Penjual</th>
                    <th>Nama Toko</th>
                    <th>Email</th>
                    <th>Status Toko</th>
                    <th>Buku Terdaftar</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sellers as $seller)
                    <tr>
                        <td>{{ $seller->user->nama }}</td>
                        <td>{{ $seller->name }}</td>
                        <td>{{ $seller->user->email }}</td>
                        <td>
                            @if($seller->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @elseif($seller->status == 'pending')
                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                            @else
                                <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td>{{ $seller->books_count ?? 0 }}</td>
                        <td>
                            {{ $seller->reviews_avg_rating !== null ? number_format($seller->reviews_avg_rating, 2) : 'N/A' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.seller.show', $seller->id) }}" class="btn btn-info btn-sm mb-1">Detail</a>

                            @if($seller->status == 'active')
                                <form action="{{ route('admin.seller.deactivate', $seller->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menonaktifkan toko ini?')">Non-Aktifkan</button>
                                </form>
                            @elseif($seller->status == 'pending')
                                <form action="{{ route('admin.seller.verify', $seller->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Verifikasi toko ini sekarang?')">Verifikasi</button>
                                </form>
                            @else
                                <form action="{{ route('admin.seller.activate', $seller->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Aktifkan toko ini kembali?')">Aktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(method_exists($sellers, 'links'))
        <div class="mt-3">
            {{ $sellers->links() }}
        </div>
    @endif
</div>

@endsection
