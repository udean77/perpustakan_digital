@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row bg-white shadow rounded p-4">

        {{-- Gambar Buku --}}
        <div class="col-md-4 text-center">
            <img src="{{ asset('storage/' . $book->cover) }}" class="img-fluid rounded" alt="{{ $book->title }}" style="max-width: 250px; height: auto;">
            <small class="text-muted d-block mt-2">*Gambar sampul bisa berbeda.</small>
        </div>

        {{-- Informasi Buku --}}
        <div class="col-md-5">
            <h3 class="fw-bold">{{ $book->title }}</h3>
            <h5 class="text-muted mb-2">by {{ $book->author }}</h5>
            <p class="fw-semibold">
                <span class="badge {{ $book->book_type === 'ebook' ? 'bg-info' : 'bg-secondary' }}">
                    {{ $book->book_type === 'ebook' ? 'E-Book' : 'Buku Fisik' }}
                </span>
            </p>

            {{-- Rata-rata Rating --}}
            @if($book->reviews->count())
                @php
                    $averageRating = number_format($book->reviews->avg('rating'), 1);
                @endphp
                <span class="badge bg-warning text-dark mb-2">⭐ {{ $averageRating }} / 5 dari {{ $book->reviews->count() }} ulasan</span>
            @endif

            {{-- Harga --}}
            @if($book->discount_price)
                <h4 class="text-danger fw-bold">
                    Rp {{ number_format($book->discount_price, 0, ',', '.') }}
                    <del class="text-muted fs-6">Rp {{ number_format($book->price, 0, ',', '.') }}</del>
                </h4>
                <p class="text-success">
                    Hemat Rp {{ number_format($book->price - $book->discount_price, 0, ',', '.') }}
                    ({{ number_format(100 * ($book->price - $book->discount_price) / $book->price) }}%)
                </p>
            @else
                <h4 class="text-dark fw-bold">Rp {{ number_format($book->price, 0, ',', '.') }}</h4>
            @endif
            <p>{{ $book->description }}</p>
            <p class="fw-semibold text-success">Status: {{ $book->stock > 0 ? 'Tersedia' : 'Habis' }}</p>
        </div>

        {{-- Info Pengiriman & Aksi --}}
        <div class="col-md-3 border-start ps-4">
            <h5 class="fw-bold mb-2"><i class="bi bi-truck"></i> Pengiriman Gratis</h5>
            <p class="small text-muted">*Syarat & Ketentuan Berlaku</p>
            <p><i class="bi bi-clock-history me-1"></i> Estimasi dikirim dalam 1–2 hari</p>

            <p class="mt-4 mb-1 fw-semibold"><i class="bi bi-geo-alt-fill"></i> Alamat Toko</p>
            <p class="small text-muted">{{ $book->store->address ?? 'Alamat tidak tersedia' }}</p>

            {{-- Form Kuantitas dan Aksi --}}
            <form action="{{ route('cart.add', $book->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="mb-2">
                    <label for="quantity" class="form-label fw-semibold">Jumlah</label>
                    <div class="input-group quantity-selector">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuantity(-1)">−</button>
                        <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="{{ $book->stock }}">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="changeQuantity(1)">+</button>
                    </div>
                    <small class="text-muted d-block mt-1">Stok Tersedia: {{ $book->stock }}</small>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-bold" {{ $book->stock < 1 ? 'disabled' : '' }}>
                + Keranjang
            </button>
            </form>
            



            {{-- Wishlist & Aksi Tambahan --}}
            <div class="d-flex justify-content-between align-items-center text-muted small mt-3">
                <div role="button"
                    onclick="window.open('https://wa.me/{{ $book->store->phone }}?text=Halo%20saya%20tertarik%20dengan%20buku%20{{ urlencode($book->title) }}', '_blank')"
                    class="btn btn-success btn-sm"
                    style="cursor:pointer;">
                    <i class="bi bi-whatsapp me-1"></i> Chat via WhatsApp
                </div>

                <div class="vr mx-2"></div>

                 <form action="{{ route('user.wishlist.toggle', $book->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none p-0 text-muted fw-semibold">
                        <i class="bi {{ $isInWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-1"></i> Wishlist
                    </button>
                </form>

                <div class="vr mx-2"></div>

                <!-- Tombol Share -->
                <button type="button" class="btn btn-link text-decoration-none p-0 text-muted fw-semibold" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="bi bi-share me-1"></i> Share
                </button>

            </div>

        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between border rounded p-4 mt-4 bg-white">
        <div class="d-flex align-items-center">
            {{-- Logo Toko --}}
            <img src="{{ $book->store && $book->store->logo ? asset('storage/store_logo/' . $book->store->logo) : asset('images/store_default.png') }}" 
                class="rounded-circle me-3" width="80" height="80" alt="Logo Toko">

            <div>
                {{-- Nama Toko (klik untuk lihat detail) --}}
                <h5 class="mb-0 fw-bold">
                    <a href="{{ route('user.store.show', $book->store->id) }}" class="text-decoration-none text-dark">
                        {{ $book->store->name }}
                    </a>
                </h5>
                <small class="text-muted">{{ $book->store->address ?? 'Lokasi tidak tersedia' }}</small>
            </div>
        </div>

        {{-- Tombol Lihat --}}
        <a href="{{ route('user.store.show', $book->store->id) }}" class="btn btn-outline-success rounded-pill px-4 fw-semibold">
            Lihat
        </a>
    </div>



    {{-- Ulasan Pembeli --}}
   {{-- 📢 Daftar Ulasan Pembeli --}}
    <div class="row mt-5">
        <div class="col-md-12">
            <h4 class="mb-3">📢 Ulasan Pembeli</h4>

            @if($book->reviews->count())
                @php
                    $reviews = $book->reviews->chunk(3); // bagi 3 ulasan per slide
                @endphp

                <div id="ulasanCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($reviews as $index => $chunk)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="row">
                                    @foreach($chunk as $review)
                                        <div class="col-md-4">
                                            <div class="p-4 bg-light rounded-4 text-center shadow-sm h-100 mx-2">
                                                <div class="fw-bold">"<i>{{ $review->user->nama }}"</i></div>
                                                <div class="my-2">⭐ {{ $review->rating }} / 5</div>
                                                <div class="text-muted">{{ $review->comment }}</div>

                                                @auth
                                                    @if($review->user_id == auth()->id())
                                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#ulasanCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ulasanCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            @else
                <p class="text-muted">Belum ada ulasan untuk buku ini.</p>
            @endif
        </div>
    </div>


    {{-- 📝 Form Tambah Ulasan --}}
    <div class="row mt-4">
        <div class="col-md-12">
            @auth
                @php
                    $userReview = $book->reviews->firstWhere('user_id', auth()->id());
                @endphp

                @if($userReview)
                    <div class="alert alert-info">
                        ✅ Anda sudah memberikan ulasan untuk buku ini.
                    </div>
                @else
                    <h5>📝 Tulis Ulasan Anda</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $book->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select name="rating" id="rating" class="form-select" required>
                                <option value="">Pilih Rating</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} ⭐</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Komentar</label>
                            <textarea name="comment" id="comment" rows="3" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success" onclick="this.disabled=true; this.form.submit();">
                            Kirim Ulasan
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>


</div>




<!-- Modal Share -->
<!-- Modal Bagikan Buku -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="shareModalLabel">Bagikan Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <p>Bagikan buku ini ke teman-teman Anda melalui:</p>
        <div class="d-flex gap-2 mb-3">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-primary btn-sm">
            Facebook
          </a>
          <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-info btn-sm text-white">
            Twitter
          </a>
          <a href="mailto:?subject=Rekomendasi Buku&body=Lihat buku ini: {{ request()->fullUrl() }}" class="btn btn-secondary btn-sm">
            Email
          </a>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
      
    </div>
  </div>
</div>


@endsection


@push('scripts')
    <script src="{{ asset('js/book-detail.js') }}"></script>
    
@endpush
