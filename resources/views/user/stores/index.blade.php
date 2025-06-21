@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Temukan Toko Favoritmu</h2>

    {{-- Search Bar --}}
    <form action="{{ route('user.store.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari toko...">
            <button type="submit" class="btn btn-success">Cari</button>
        </div>
    </form>

    {{-- Grid Toko --}}
    <div class="row g-3">
        @forelse ($stores as $store)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="text-center p-3">
                         <img src="{{ $store && $store->logo ? asset('storage/store_logo/' . $book->store->logo) : asset('images/store_default.png') }}" class="rounded-circle me-3" width="80" height="80" alt="Logo Toko">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold text-truncate" title="{{ $store->name }}">{{ $store->name }}</h5>
                        <p class="text-muted small mb-1" title="{{ $store->address }}">
                            <i class="bi bi-geo-alt-fill"></i> {{ $store->address ?? 'Alamat tidak tersedia' }}
                        </p>
                        {{-- Optional: Rating toko --}}
                        @if($store->rating)
                            <p class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i> {{ number_format($store->rating, 1) }}
                            </p>
                        @endif
                        <a href="{{ route('user.store.show', $store->id) }}" class="btn btn-success btn-sm w-100">
                            Kunjungi Toko
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">Maaf, toko tidak ditemukan.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $stores->withQueryString()->links() }}
    </div>
</div>
@endsection
