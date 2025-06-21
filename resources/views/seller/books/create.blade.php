@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Logo Toko -->
                    <img src="{{ Auth::user()->store && Auth::user()->store->logo ? asset('storage/store_logo/' . Auth::user()->store->logo) : asset('images/store_default.png') }}" class="rounded mb-3" width="100" alt="Logo Toko">

                    <!-- Nama Toko -->
                    <h5>{{ Auth::user()->store->name }}</h5>

                    <!-- Info kontak -->
                    <p class="mb-1 text-muted">{{ Auth::user()->email }}</p>
                    <p class="mb-1">{{ Auth::user()->nomor_hp }}</p>

                    <!-- Status -->
                    <span class="badge bg-success">Terverifikasi</span>
                </div>
            </div>

            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action active">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Buku Baru</h5>
                </div>
                <div class="card-body">
                    <!-- Form for adding new book -->
                    <form action="{{ route('seller.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Book Title and Author -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control rounded-3" name="title" id="title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="author" class="form-label">Penulis</label>
                                <input type="text" class="form-control rounded-3" name="author" id="author" required>
                            </div>
                        </div>

                        <!-- Book Price, Stock, Category -->
                        <div class="row mb-3">
                           <div class="col-md-4">
                                <label for="price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control rounded-3 @error('price') is-invalid @enderror" name="price" id="price" step="0.01" min="0" max="99999999.99" required value="{{ old('price.maz') }}">
                                @error('price')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                            </div>


                            <div class="col-md-4">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control rounded-3" name="stock" id="stock" required>
                            </div>
                           <div class="col-md-4">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select rounded-3 @error('category') is-invalid @enderror" name="category" id="category" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Book Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control rounded-3" name="description" id="description" rows="4" required></textarea>
                        </div>

                        <!-- Publisher -->
                        <div class="mb-3">
                            <label for="publisher" class="form-label">Penerbit</label>
                            <input type="text" class="form-control rounded-3" name="publisher" id="publisher" required>
                        </div>

                        <!-- Book Type -->
                        <div class="mb-3">
                            <label for="book_type" class="form-label">Jenis Buku <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 @error('book_type') is-invalid @enderror" name="book_type" id="book_type" required>
                                <option value="">-- Pilih Jenis Buku --</option>
                                <option value="physical">Buku Fisik</option>
                                <option value="ebook">E-Book</option>
                            </select>
                            @error('book_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="bookTypeWarning" class="text-danger mt-1 d-none">* Silakan pilih jenis buku terlebih dahulu.</div>
                        </div>

                        <!-- Cover Photo -->
                        <div class="mb-3">
                            <label for="cover" class="form-label">Foto Sampul Buku</label>
                            <input type="file" class="form-control rounded-3" name="cover" id="cover" accept="image/*" required>
                            <small class="text-muted">Format .jpg, .jpeg, .png. Maks. 2MB</small>
                        </div>

                        <!-- E-book File (Optional) -->
                        <div class="mb-3" id="ebook_file_group" style="display: none;">
                            <label for="ebook_file" class="form-label">Upload File E-Book</label>
                            <input type="file" class="form-control rounded-3" name="ebook_file" id="ebook_file" accept=".pdf,.epub,.mobi">
                            <small class="text-muted">Format: PDF, EPUB, MOBI. Maks. 10MB</small>
                        </div>

                        <!-- Physical Book File (Optional) -->
                        <div class="mb-3" id="physical_file_group" style="display: none;">
                            <label for="physical_book_file" class="form-label">File Tambahan Buku Fisik (Opsional)</label>
                            <input type="file" class="form-control rounded-3" name="physical_book_file" id="physical_book_file" accept=".zip,.rar">
                            <small class="text-muted">Contoh: preview, materi bonus (ZIP/RAR)</small>
                        </div>

                        <!-- Buttons -->
                            <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Buku</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const bookTypeSelect = document.getElementById('book_type');
    const ebookGroup = document.getElementById('ebook_file_group');
    const physicalGroup = document.getElementById('physical_file_group');
    const warning = document.getElementById('bookTypeWarning');

    bookTypeSelect.addEventListener('change', function () {
        const type = this.value;

        if (type) {
            warning.classList.add('d-none');
        }

        if (type === 'ebook') {
            ebookGroup.style.display = 'block';
            physicalGroup.style.display = 'none';
        } else if (type === 'physical') {
            ebookGroup.style.display = 'none';
            physicalGroup.style.display = 'block';
        } else {
            ebookGroup.style.display = 'none';
            physicalGroup.style.display = 'none';
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function (e) {
        if (!bookTypeSelect.value) {
            e.preventDefault();
            warning.classList.remove('d-none');
            bookTypeSelect.focus();
        }
    });
</script>
@endsection
