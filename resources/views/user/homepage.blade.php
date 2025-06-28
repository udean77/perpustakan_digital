@extends('layouts.app')

@section('content')
<div class="my-4">
    @if(session('warning'))
    <!-- Modal -->
    <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
        <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title" id="warningModalLabel">Peringatan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            {{ session('warning') }}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Tutup</button>
        </div>
        </div>
    </div>
    </div>
    @endif

    <!-- Header Section -->
    <header class="py-5">
        <div class="container px-lg-5">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center shadow-sm">
                <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active text-center p-5">
                            <h1><strong>Selamat Datang di Pustaka Digital!</strong></h1>
                            <p>Temukan koleksi buku terbaru, diskon spesial, dan promo menarik setiap hari.<br>Belanja buku jadi lebih mudah dan menyenangkan!</p>
                            <a href="#" class="btn btn-primary mt-3">Lihat Promo Sekarang</a>
                        </div>
                        <div class="carousel-item text-center p-5">
                            <h1><strong>Diskon Hingga 70%!</strong></h1>
                            <p>Jangan lewatkan penawaran spesial minggu ini.</p>
                            <a href="#" class="btn btn-danger mt-3">Cek Promo</a>
                        </div>
                        <div class="carousel-item text-center p-5">
                            <h1><strong>Buku Terbaru!</strong></h1>
                            <p>Dapatkan buku-buku terbaru dengan harga terbaik</p>
                            <a href="#" class="btn btn-success mt-3">Lihat Koleksi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Bestseller Section -->
    <section class="my-5">
        <div class="container p-4 bg-light rounded-4 shadow-sm">
            <h2 class="text-center fw-bold mb-4">Book / E-book</h2>
            <hr style="border: 2px solid red; width: 50%; margin: 0 auto 30px auto;">

            <!-- Scroll Area -->
            <div class="position-relative">
                <div id="bookScroll" class="d-flex overflow-auto gap-3 py-2 px-1">
                    @foreach ($books as $book)
                        @if($book->status == 'active')
                       <a href="{{ route('books.show', $book->id) }}" class="text-decoration-none text-dark" style="min-width: 160px; flex: 0 0 auto;">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <img src="{{ asset('storage/' . $book->cover) }}" class="card-img-top rounded-top" alt="Book Cover" style="height: 160px; object-fit: cover;">
                                <div class="card-body px-2 py-3">
                                    @if($book->fast_delivery)
                                        <span class="badge bg-danger mb-1" style="font-size: 0.65rem;">Fast Delivery</span>
                                    @endif
                                    <h6 class="fw-semibold mb-1" style="font-size: 0.8rem;">{{ $book->title }}</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $book->author }}</p>
                                    <p class="text-muted mb-0" style="font-size: 0.7rem;">{{ $book->format }}</p>
                                    <p class="fw-bold mb-0" style="font-size: 0.8rem;">Rp {{ number_format($book->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>

                <!-- Scroll Buttons -->
                <button id="scrollLeft" class="btn btn-light position-absolute top-50 start-0 translate-middle-y shadow-sm">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button id="scrollRight" class="btn btn-light position-absolute top-50 end-0 translate-middle-y shadow-sm">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <!-- View More -->
            <div class="text-center mt-4">
                <a href="{{ route('books.index') }}" class="btn btn-danger btn-sm rounded-pill px-4">View More</a>
            </div>
        </div>
    </section>

    <!-- Recommendations Section for Authenticated Users -->
    @auth
    @if($recommendations->count() > 0)
    <section class="my-5">
        <div class="container p-4 bg-gradient rounded-4 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white fw-bold mb-0">
                    <i class="fas fa-star"></i> Rekomendasi untuk Anda
                </h2>
                <a href="{{ route('user.preferences.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-cog"></i> Atur Preferensi
                </a>
            </div>
            <hr style="border: 2px solid rgba(255,255,255,0.3); width: 100%; margin: 0 auto 30px auto;">

            <!-- Recommendations Grid -->
            <div class="row g-3">
                @foreach ($recommendations as $book)
                    <div class="col-md-4 col-lg-2">
                        <a href="{{ route('books.show', $book->id) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <img src="{{ asset('storage/' . $book->cover) }}" class="card-img-top rounded-top" alt="Book Cover" style="height: 160px; object-fit: cover;">
                                <div class="card-body px-2 py-3">
                                    <h6 class="fw-semibold mb-1" style="font-size: 0.8rem;">{{ $book->title }}</h6>
                                    <p class="text-muted mb-1" style="font-size: 0.75rem;">{{ $book->author }}</p>
                                    
                                    <!-- Rating -->
                                    @if($book->reviews_avg_rating)
                                        <div class="mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $book->reviews_avg_rating)
                                                    <i class="fas fa-star text-warning" style="font-size: 0.7rem;"></i>
                                                @else
                                                    <i class="far fa-star text-warning" style="font-size: 0.7rem;"></i>
                                                @endif
                                            @endfor
                                            <small class="text-muted ms-1" style="font-size: 0.65rem;">{{ number_format($book->reviews_avg_rating, 1) }}</small>
                                        </div>
                                    @endif
                                    
                                    <p class="fw-bold mb-0 text-primary" style="font-size: 0.8rem;">Rp {{ number_format($book->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- View More Recommendations -->
            <div class="text-center mt-4">
                <a href="{{ route('books.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
                    <i class="fas fa-search"></i> Cari Lebih Banyak
                </a>
            </div>
        </div>
    </section>
    @endif
    @endauth

    <!-- JS -->
    <script src="{{ asset('js/auto.js') }}"></script>
    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
            warningModal.show();
        });
    </script>
    @endif

</div>
@endsection
