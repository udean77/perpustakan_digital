@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>{{ $seller->name }}</h3>
    
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Informasi Toko</h5>
            <p class="card-text"><strong>Deskripsi Toko:</strong> {{ $seller->description }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $seller->user->email }}</p>
            <p class="card-text"><strong>Alamat:</strong> {{ $seller->address }}</p>
            <p class="card-text"><strong>Status Toko:</strong> 
                @if($seller->status == 'active')
                    <span class="badge bg-success">Aktif</span>
                @elseif($seller->status == 'pending')
                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                @else
                    <span class="badge bg-danger">Non-Aktif</span>
                @endif
            </p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.seller.index') }}" class="btn btn-secondary">Kembali</a>
            
            <div>
                @if($seller->status == 'pending')
                    <a href="{{ route('admin.seller.verify', $seller->id) }}" class="btn btn-primary">Verifikasi Toko</a>
                @endif

                @if($seller->status == 'active')
                    <a href="{{ route('admin.seller.deactivate', $seller->id) }}" class="btn btn-warning">Nonaktifkan Toko</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
