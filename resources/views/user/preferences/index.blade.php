@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cog"></i> Preferensi Buku Saya
                    </h4>
                    <p class="mb-0 mt-2">Atur preferensi untuk mendapatkan rekomendasi buku yang lebih personal</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('user.preferences.update') }}" method="POST">
                        @csrf
                        
                        <!-- Kategori Favorit -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tags"></i> Kategori Favorit
                            </label>
                            <div class="row">
                                @php
                                    $categories = ['fiksi', 'non-fiksi', 'pendidikan', 'novel', 'komik'];
                                    $selectedCategories = $preferences->preferred_categories ?? [];
                                @endphp
                                @foreach($categories as $category)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="preferred_categories[]" 
                                                   value="{{ $category }}"
                                                   id="category_{{ $category }}"
                                                   {{ in_array($category, $selectedCategories) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_{{ $category }}">
                                                {{ ucfirst($category) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Pilih kategori buku yang Anda sukai</small>
                        </div>

                        <!-- Penulis Favorit -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-edit"></i> Penulis Favorit
                            </label>
                            <div id="authors-container">
                                @if(!empty($preferences->preferred_authors))
                                    @foreach($preferences->preferred_authors as $index => $author)
                                        <div class="input-group mb-2 author-input">
                                            <input type="text" class="form-control" 
                                                   name="preferred_authors[]" 
                                                   value="{{ $author }}" 
                                                   placeholder="Nama penulis">
                                            <button type="button" class="btn btn-outline-danger remove-author">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 author-input">
                                        <input type="text" class="form-control" 
                                               name="preferred_authors[]" 
                                               placeholder="Nama penulis">
                                        <button type="button" class="btn btn-outline-danger remove-author">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-author">
                                <i class="fas fa-plus"></i> Tambah Penulis
                            </button>
                            <small class="text-muted d-block mt-1">Tambahkan penulis favorit Anda</small>
                        </div>

                        <!-- Range Harga -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave"></i> Range Harga
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Harga Minimum</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" 
                                               name="min_price" 
                                               value="{{ $preferences->min_price ?? '' }}"
                                               placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga Maksimum</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" 
                                               name="max_price" 
                                               value="{{ $preferences->max_price ?? '' }}"
                                               placeholder="1000000">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Biarkan kosong jika tidak ada batasan harga</small>
                        </div>

                        <!-- Tipe Buku -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-book"></i> Tipe Buku
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="preferred_book_type" 
                                               value="physical" 
                                               id="type_physical"
                                               {{ ($preferences->preferred_book_type ?? 'both') == 'physical' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_physical">
                                            <i class="fas fa-book-open"></i> Buku Fisik
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="preferred_book_type" 
                                               value="ebook" 
                                               id="type_ebook"
                                               {{ ($preferences->preferred_book_type ?? 'both') == 'ebook' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_ebook">
                                            <i class="fas fa-tablet-alt"></i> E-Book
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="preferred_book_type" 
                                               value="both" 
                                               id="type_both"
                                               {{ ($preferences->preferred_book_type ?? 'both') == 'both' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_both">
                                            <i class="fas fa-th-large"></i> Keduanya
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Minimum -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star"></i> Rating Minimum
                            </label>
                            <select name="min_rating" class="form-select">
                                <option value="0" {{ ($preferences->min_rating ?? 0) == 0 ? 'selected' : '' }}>Tidak ada batasan</option>
                                <option value="1" {{ ($preferences->min_rating ?? 0) == 1 ? 'selected' : '' }}>⭐ 1+</option>
                                <option value="2" {{ ($preferences->min_rating ?? 0) == 2 ? 'selected' : '' }}>⭐⭐ 2+</option>
                                <option value="3" {{ ($preferences->min_rating ?? 0) == 3 ? 'selected' : '' }}>⭐⭐⭐ 3+</option>
                                <option value="4" {{ ($preferences->min_rating ?? 0) == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ 4+</option>
                                <option value="5" {{ ($preferences->min_rating ?? 0) == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5</option>
                            </select>
                            <small class="text-muted">Hanya tampilkan buku dengan rating minimal tertentu</small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Preferensi
                                </button>
                                <button type="button" class="btn btn-outline-info" id="auto-update">
                                    <i class="fas fa-magic"></i> Update Otomatis
                                </button>
                            </div>
                            <button type="button" class="btn btn-outline-danger" id="reset-preferences">
                                <i class="fas fa-trash"></i> Reset Preferensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success"></i> Preferensi akan digunakan untuk memberikan rekomendasi buku yang lebih personal</li>
                        <li><i class="fas fa-check text-success"></i> Update otomatis akan menganalisis aktivitas Anda (wishlist, review) untuk memperbarui preferensi</li>
                        <li><i class="fas fa-check text-success"></i> Anda dapat mengatur preferensi secara manual atau membiarkan sistem melakukannya secara otomatis</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add author functionality
    const addAuthorBtn = document.getElementById('add-author');
    const authorsContainer = document.getElementById('authors-container');

    addAuthorBtn.addEventListener('click', function() {
        const authorInput = document.createElement('div');
        authorInput.className = 'input-group mb-2 author-input';
        authorInput.innerHTML = `
            <input type="text" class="form-control" name="preferred_authors[]" placeholder="Nama penulis">
            <button type="button" class="btn btn-outline-danger remove-author">
                <i class="fas fa-times"></i>
            </button>
        `;
        authorsContainer.appendChild(authorInput);
    });

    // Remove author functionality
    authorsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-author') || e.target.closest('.remove-author')) {
            const authorInput = e.target.closest('.author-input');
            if (authorsContainer.children.length > 1) {
                authorInput.remove();
            }
        }
    });

    // Auto update functionality
    document.getElementById('auto-update').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin memperbarui preferensi berdasarkan aktivitas Anda?')) {
            fetch('{{ route("user.preferences.autoUpdate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Gagal memperbarui preferensi: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error.message);
            });
        }
    });

    // Reset preferences functionality
    document.getElementById('reset-preferences').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin mereset semua preferensi? Tindakan ini tidak dapat dibatalkan.')) {
            fetch('{{ route("user.preferences.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('Preferensi berhasil direset!');
                    location.reload();
                } else {
                    alert('Gagal mereset preferensi');
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error.message);
            });
        }
    });
});
</script>
@endsection 