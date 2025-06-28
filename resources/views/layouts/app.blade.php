<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Putaka Digital</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="{{ asset('backend/images/logo-pustaka.png') }}" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style-home.css') }}">

</head>
<body>

<!-- Responsive navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid px-lg-5 d-flex align-items-center">
        <!-- Logo -->
        <a class="navbar-brand me-3" href="{{ route('user.homepage') }}">
            <img src="{{ asset('backend/images/logo-pustaka.png') }}" alt="Logo" height="40">
        </a>

        <!-- Kategori + Search Bar -->
        <div class="d-flex align-items-center flex-grow-1">
            <!-- Dropdown Kategori -->
           <div class="dropdown me-2">
                <button class="btn btn-outline-light dropdown-toggle" type="button" id="kategoriDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Kategori
                </button>
                <ul class="dropdown-menu" aria-labelledby="kategoriDropdown">
                    <li><a class="dropdown-item" href="{{ route('books.index', ['category' => 'fiksi']) }}">Fiksi</a></li>
                    <li><a class="dropdown-item" href="{{ route('books.index', ['category' => 'non-fiksi']) }}">Non-Fiksi</a></li>
                    <li><a class="dropdown-item" href="{{ route('books.index', ['category' => 'pendidikan']) }}">Pendidikan</a></li>
                    <li><a class="dropdown-item" href="{{ route('books.index', ['category' => 'novel']) }}">Novel</a></li>
                    <li><a class="dropdown-item" href="{{ route('books.index', ['category' => 'komik']) }}">Komik</a></li>
                </ul>
            </div>


            <!-- Search Bar -->
          <form class="d-flex flex-grow-1" role="search" action="{{ route('books.index') }}" method="GET">
                <input 
                    class="form-control me-2" 
                    type="search" 
                    placeholder="Cari buku..." 
                    name="keyword" 
                    value="{{ request('keyword') }}" {{-- agar tetap tampil saat reload --}}
                    aria-label="Search">
                <button class="btn btn-outline-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

        </div>

        <!-- Kanan: Toko, Keranjang, Akun -->
        <ul class="navbar-nav ms-3 align-items-center">
            <!-- Toko -->
        <li class="nav-item dropdown-toko me-3">
          <!-- Blade Template -->
                @php
                    $hasStore = auth()->check() && auth()->user()->store !== null;
                @endphp

                <a class="nav-link" href="{{ $hasStore ? route('seller.dashboard') : '#' }}" title="Toko" onclick="{{ $hasStore ? '' : 'return confirmCreateStore(event)' }}">
                <i class="bi bi-shop"></i> Toko </a>
                @if (!$hasStore)
                <script>
                    function confirmCreateStore(event) {
                        event.preventDefault();
                        if (confirm('Anda belum memiliki toko. Apakah Anda ingin membuat toko sekarang?')) {
                            window.location.href = "{{ route('seller.store.create') }}";
                        }
                        return false;
                    }
                </script>
                @endif


            <div class="dropdown-content">
                @if (auth()->check() && Auth::user()->store)
                    <p class="mb-2">Toko Anda: <strong>{{ Auth::user()->store->name }}</strong></p>
                    <a href="{{ route('seller.dashboard') }}" class="btn btn-success mb-2 rounded-pill">Kelola Toko</a>
                @else
                    <p class="mb-2">Anda belum memiliki toko.</p>
                    <a href="{{ route('seller.register') }}" class="btn btn-success mb-2 rounded-pill">Buka Toko Gratis</a>
                    <p class="mb-0"> <a href="#" class="text-success fw-bold">Pelajari Selengkapnya</a></p>
                @endif
            </div>
        </li>


            <!-- Keranjang -->
            <li class="nav-item dropdown me-3 position-relative cart-hover-dropdown">
                <a class="nav-link position-relative" href="{{ route('user.cart.index') }}">
                    <i class="bi bi-cart"></i>
                    @if($cartCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $cartCount }}
                    </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm p-3 cart-dropdown" style="min-width: 300px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <strong>Keranjang</strong>
                        <a href="{{ route('user.cart.index') }}" class="text-success fw-bold small">Lihat</a>
                    </div>

                    @if($cartItems->count() > 0)
                        @foreach($cartItems as $item)
                            <div class="d-flex mb-3">
                                <a href="{{ route('user.cart.index') }}" class="d-flex align-items-center text-decoration-none">
                                    <img src="{{ asset('storage/' . $item->book->cover) }}" alt="Cover" width="50" class="me-2 rounded">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $item->book->title }}</div>
                                        <div class="text-muted small">Rp{{ number_format($item->book->price, 0, ',', '.') }} x {{ $item->quantity }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center">
                            <img src="{{ asset('images/cart.png') }}" alt="Keranjang Kosong" width="100">
                            <h6 class="mt-3 fw-bold">Wah, keranjang belanjamu kosong</h6>
                            <p class="text-muted small">Yuk, isi dengan barang-barang impianmu!</p>
                            <a href="{{ route('books.index') }}" class="btn btn-outline-success rounded-pill px-4 py-1">Mulai Belanja</a>
                        </div>
                    @endif
                </ul>
            </li>




            <!-- Akun -->
            <li class="nav-item dropdown">
                @if(auth()->check())
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="akunDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('images/img-default.jpg') }}"  alt="Avatar" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="akunDropdown" style="width: 300px;">
                    <li class="p-3">
                        <strong>{{ Auth::user()->nama }}</strong><br>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.preferences.index') }}">Preferensi Buku</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.wishlist.index') }}">Wishlist</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.reports.index') }}">Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.transaction.index') }}">Transaksi</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Keluar</button>
                        </form>
                    </li>
                </ul>
                @endif
            </li>
        </ul>
    </div>
</nav>


    <!-- Header-->
    @yield('content')

    

    
    <!-- Footer-->
    
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
    <!-- Custom Homepage Script -->
    <script src="{{ asset('js/autoscroll.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}"
            });
            </script>
    @endif
    
    <!-- Konfirmasi Delete -->
    <script>
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var konfdelete = $(this).data("konf-delete");
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus Data?',
                html: "Data yang dihapus <strong>" + konfdelete + "</strong> tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, dihapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success')
                        .then(() => {
                            form.submit();
                        });
                }
            });
        });
        </script>
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        </script>

<script src="{{ asset('js/chat.js') }}"></script>
<script src="{{ asset('js/create.js') }}"></script>
    
    @stack('scripts')
    
    <!-- Chat Widget -->
    @include('components.chat-widget')
    
</body>
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">PustakaDigital.co.id &copy; 2025</p>
    </div>
</footer>
</html>
