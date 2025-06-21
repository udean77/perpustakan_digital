@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Header Toko --}}
    <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white">
        <div class="d-flex align-items-center">
             <img src="{{ $store && $store->logo ? asset('storage/store_logo/' . $book->store->logo) : asset('images/store_default.png') }}" 
                class="rounded-circle me-3" width="80" height="80" alt="Logo Toko">
            <div>
                <h4 class="mb-0">{{ $store->name }}</h4>
                <small class="text-muted">{{ $store->address }}</small>
            </div>
        </div>
        @php
            $whatsappNumber = preg_replace('/^0/', '62', $store->phone);
        @endphp

        <div>
            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="btn btn-outline-success rounded-pill px-4">
                Chat Penjual via WhatsApp
            </a>
        </div>

    </div>

    {{-- Tab Navigasi --}}
    <ul class="nav nav-tabs mt-4 " id="storeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="produk-tab" data-bs-toggle="tab" data-bs-target="#produk" type="button">Produk</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ulasan-tab" data-bs-toggle="tab" data-bs-target="#ulasan" type="button">Ulasan</button>
        </li>
    </ul>

    <div class="tab-content" id="storeTabContent">
        {{-- Tab Produk --}}
        <div class="tab-pane fade show active py-4" id="produk">
            <h5 class="mb-3">Etalase Toko</h5>
            <div class="row">
                @foreach ($store->books as $book)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('storage/' . $book->cover) }}" class="card-img-top" alt="{{ $book->title }}">
                            <div class="card-body">
                                <h6 class="card-title">{{ $book->title }}</h6>
                                <p class="text-muted">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tab Ulasan --}}
        <div class="tab-pane fade py-4" id="ulasan">
            @php
                $allReviews = $store->books->flatMap->reviews;
            @endphp

            @if($allReviews->isEmpty())
                <p>Belum ada ulasan.</p>
            @else
                @foreach($allReviews as $review)
                    <div class="border-bottom pb-3 mb-3">
                        <strong>{{ $review->user->nama ?? 'Pengguna' }}</strong>
                        <p class="mb-1">{{ $review->comment }}</p>
                        <small class="text-muted">Rating: {{ $review->rating }} / 5</small>
                    </div>
                @endforeach
            @endif
        </div>

    </div>
</div>
@endsection
