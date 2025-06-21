@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3 mb-4">
            <form method="GET" action="{{ route('books.index') }}">
                <div class="bg-white p-3 shadow-sm rounded">
                    <h5 class="fw-bold">Filter</h5>
                    <hr>

                    <!-- Jenis Buku -->
                    <p class="mb-1 fw-semibold">Jenis Buku</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenis[]" value="physical" id="jenisPhysical"
                            @if(is_array(request('jenis')) && in_array('physical', request('jenis'))) checked @endif>
                        <label class="form-check-label" for="jenisPhysical">Buku Fisik</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenis[]" value="ebook" id="jenisEbook"
                            @if(is_array(request('jenis')) && in_array('ebook', request('jenis'))) checked @endif>
                        <label class="form-check-label" for="jenisEbook">E-Book</label>
                    </div>

                    <!-- Kategori -->
                    <p class="mt-3 mb-1 fw-semibold">Kategori</p>
                    @foreach($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="kategori[]" value="{{ $category->id }}" id="kategori{{ $category->id }}"
                            @if(is_array(request('kategori')) && in_array($category->id, request('kategori'))) checked @endif>
                        <label class="form-check-label" for="kategori{{ $category->id }}">{{ $category->name }}</label>
                    </div>
                    @endforeach

                    <!-- Rating -->
                    <p class="mt-3 mb-1 fw-semibold">Rating</p>
                    @for($i = 5; $i >= 1; $i--)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}"
                            @if(request('rating') == $i) checked @endif>
                        <label class="form-check-label" for="rating{{ $i }}">
                            {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }}
                        </label>
                    </div>
                    @endfor

                    <!-- Harga -->
                    <p class="mt-3 mb-1 fw-semibold">Harga</p>
                    <input type="number" class="form-control mb-2" name="harga_min" placeholder="Min Rp" value="{{ request('harga_min') }}">
                    <input type="number" class="form-control mb-2" name="harga_max" placeholder="Max Rp" value="{{ request('harga_max') }}">

                    <!-- Stok -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="stok_tersedia" id="stokTersedia" {{ request()->has('stok_tersedia') ? 'checked' : '' }}>
                        <label class="form-check-label" for="stokTersedia">Stok Tersedia</label>
                    </div>

                    <!-- Tombol Filter -->
                    <button type="submit" class="btn btn-primary mt-3 w-100">Terapkan Filter</button>
                </div>
            </form>
        </div>

        <!-- Konten Buku -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Menampilkan {{ $books->count() }} buku dari total {{ $books->total() }}</span>
                <form method="GET" action="{{ route('books.index') }}">
                    {{-- Simpan filter yang sedang aktif sebagai hidden input --}}
                    @foreach(request()->except('sort', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <select class="form-select w-auto" name="sort" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                        <option value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </form>
            </div>

            <div class="row g-4">
                @forelse($books as $book)
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('books.show', $book->id) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ asset('storage/' . $book->cover) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 220px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-primary">{{ $book->title }}</h6>
                                    <p class="mb-1 text-muted">by {{ $book->author }}</p>
                                    <p class="mb-1 small">
                                        <span class="badge {{ $book->book_type == 'ebook' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ $book->book_type == 'ebook' ? 'E-Book' : 'Buku Fisik' }}
                                        </span>
                                    </p>
                                    <p class="fw-semibold">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                                    <p class="mb-0 text-secondary small">{{ $book->store->name ?? 'Toko Tidak Diketahui' }}</p>
                                    <div class="text-warning small">
                                        @php
                                            $avgRating = round($book->reviews_avg_rating ?? 0);
                                        @endphp
                                        {{ str_repeat('★', $avgRating) }}{{ str_repeat('☆', 5 - $avgRating) }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-center">Tidak ada buku yang sesuai filter.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
