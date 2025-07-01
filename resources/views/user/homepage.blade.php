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
                        <div class="carousel-item active text-center p-5" style="background: url('{{ asset('images/bg-img1.jpg') }}') center center/cover no-repeat;">
                            <h1><strong>Selamat Datang di Pustaka Digital!</strong></h1>
                            <p>Temukan koleksi buku terbaru, diskon spesial, dan promo menarik setiap hari.<br>Belanja buku jadi lebih mudah dan menyenangkan!</p>
                        </div>
                        <div class="carousel-item text-center p-5" style="background: url('{{ asset('images/bg-img2.jpg') }}') center center/cover no-repeat;">
                            <h1><strong>Diskon Hingga 70%!</strong></h1>
                            <p>Jangan lewatkan penawaran spesial minggu ini.</p>
                        </div>
                        <div class="carousel-item text-center p-5" style="background: url('{{ asset('images/bg-img3.jpg') }}') center center/cover no-repeat;">
                            <h1><strong>Tersedia voucher sampai 50%</strong></h1>
                            <p>Silahkan tanya ai assistant untuk mendapatkan voucher</p>
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

    <!-- Recommendations Section (Only for authenticated users) -->
    @auth
        @if($recommendations->count() > 0)
        <section class="my-5">
            <div class="container p-4 bg-light rounded-4 shadow-sm">
                <h2 class="text-center fw-bold mb-4">Rekomendasi untuk Anda</h2>
                <hr style="border: 2px solid red; width: 50%; margin: 0 auto 30px auto;">

                <!-- Scroll Area -->
                <div class="position-relative">
                    <div id="recommendationScroll" class="d-flex overflow-auto gap-3 py-2 px-1">
                        @foreach ($recommendations as $book)
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
                    <button id="recommendationScrollLeft" class="btn btn-light position-absolute top-50 start-0 translate-middle-y shadow-sm">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button id="recommendationScrollRight" class="btn btn-light position-absolute top-50 end-0 translate-middle-y shadow-sm">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>
        @endif
    @else
        <!-- Call to Action for Guest Users -->
        <section class="my-5">
            <div class="container p-4 bg-light rounded-4 shadow-sm text-center">
                <h2 class="fw-bold mb-3">Bergabunglah dengan Kami!</h2>
                <p class="text-muted mb-4">Dapatkan rekomendasi buku personal dan akses ke fitur-fitur eksklusif</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Login</a>
                </div>
            </div>
        </section>
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
