@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
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
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action active">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Daftar Buku -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Buku</h5>
                </div>
                <div class="card-body">
                    @if($books->isEmpty())
                        <p class="text-center">Belum ada buku yang ditambahkan.</p>
                    @else
                    <div class="row g-3">
                        @foreach($books as $book)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative">
                                <!-- Badge Status -->
                                <span class="position-absolute top-0 end-0 m-2 badge {{ $book->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $book->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>

                                <img src="{{ asset('storage/' . $book->cover) }}" class="card-img-top rounded-top" alt="Book Cover" style="height: 160px; object-fit: cover; width: 100%;">

                                <div class="card-body px-2 py-3">
                                    <h6 class="fw-semibold mb-1" style="font-size: 0.8rem;">{{ $book->title }}</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $book->author }}</p>
                                </div>

                                <div class="text-center pb-3">
                                    @if($book->status == 'active')
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#bookDetailModal"
                                        data-id="{{ $book->id }}"
                                        data-title="{{ $book->title }}"
                                        data-author="{{ $book->author }}"
                                        data-publisher="{{ $book->publisher }}"
                                        data-price="{{ $book->price }}"
                                        data-description="{{ $book->description }}"
                                        data-category="{{ $book->category }}"
                                        data-stock="{{ $book->stock }}"
                                        data-book-type="{{ $book->book_type }}"
                                        data-status="{{ $book->status }}"
                                        data-cover="{{ asset('storage/' . $book->cover) }}">
                                        Lihat Detail
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-secondary" disabled>Buku Dinonaktifkan</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Buku -->
<div class="modal fade" id="bookDetailModal" tabindex="-1" aria-labelledby="bookDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookDetailModalLabel">Detail Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <!-- View Mode -->
        <div id="viewMode">
            <h5 id="modalBookTitle"></h5>
            <p><strong>Penulis:</strong> <span id="modalBookAuthor"></span></p>
            <p><strong>Penerbit:</strong> <span id="modalBookPublisher"></span></p>
            <p><strong>Harga:</strong> Rp <span id="modalBookPrice"></span></p>
            <p><strong>Kategori:</strong> <span id="modalBookCategory"></span></p>
            <p><strong>Stok:</strong> <span id="modalBookStock"></span></p>
            <p><strong>Jenis Buku:</strong> <span id="modalBookType"></span></p>
            <p><strong>Deskripsi:</strong></p>
            <p id="modalBookDescription"></p>

            <!-- Display Cover -->
            <div id="modalBookCoverContainer">
                <strong>Sampul Buku:</strong> <br>
                <img id="modalBookCover" src="" alt="Sampul Buku" class="img-fluid" style="max-height: 200px; object-fit: cover;">
            </div>

            <button class="btn btn-primary mt-3" id="editButton">Edit</button>
            <button class="btn btn-danger mt-2" id="deleteButton">Hapus</button>
        </div>

        <!-- Edit Mode -->
       <form action="editForm" method="POST" enctype="multipart/form-data" id="editBookForm">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title" id="editBookModalLabel">Edit Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="edit_title" class="form-label">Judul Buku</label>
              <input type="text" class="form-control rounded-3" name="title" id="edit_title" required>
            </div>
            <div class="col-md-6">
              <label for="edit_author" class="form-label">Penulis</label>
              <input type="text" class="form-control rounded-3" name="author" id="edit_author" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="edit_price" class="form-label">Harga (Rp)</label>
              <input type="number" class="form-control rounded-3" name="price" id="edit_price" step="0.01" min="0" max="99999999.99" required>
            </div>

            <div class="col-md-4">
              <label for="edit_stock" class="form-label">Stok</label>
              <input type="number" class="form-control rounded-3" name="stock" id="edit_stock" required>
            </div>

            <div class="col-md-4">
              <label for="edit_category" class="form-label">Kategori</label>
              <select class="form-select rounded-3" name="category" id="edit_category" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                  <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="edit_description" class="form-label">Deskripsi</label>
            <textarea class="form-control rounded-3" name="description" id="edit_description" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_publisher" class="form-label">Penerbit</label>
            <input type="text" class="form-control rounded-3" name="publisher" id="edit_publisher" required>
          </div>

          <div class="mb-3">
            <label for="edit_book_type" class="form-label">Jenis Buku <span class="text-danger">*</span></label>
            <select class="form-select rounded-3" name="book_type" id="edit_book_type" required>
              <option value="">-- Pilih Jenis Buku --</option>
              <option value="physical">Buku Fisik</option>
              <option value="ebook">E-Book</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_cover" class="form-label">Foto Sampul Buku</label>
            <input type="file" class="form-control rounded-3" name="cover" id="edit_cover" accept="image/*">
            <small class="text-muted">Format .jpg, .jpeg, .png. Maks. 2MB. Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="mb-3" id="edit_ebook_file_group" style="display: none;">
            <label for="edit_ebook_file" class="form-label">Upload File E-Book</label>
            <input type="file" class="form-control rounded-3" name="ebook_file" id="edit_ebook_file" accept=".pdf,.epub,.mobi">
            <small class="text-muted">Format: PDF, EPUB, MOBI. Maks. 10MB</small>
          </div>

          <div class="mb-3" id="edit_physical_file_group" style="display: none;">
            <label for="edit_physical_book_file" class="form-label">File Tambahan Buku Fisik (Opsional)</label>
            <input type="file" class="form-control rounded-3" name="physical_book_file" id="edit_physical_book_file" accept=".zip,.rar">
            <small class="text-muted">Contoh: preview, materi bonus (ZIP/RAR)</small>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update Buku</button>
        </div>

      </form>
      </div>
    </div>
  </div>
</div>

<!-- Form hapus tersembunyi -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var bookDetailModal = document.getElementById('bookDetailModal');

        bookDetailModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;

            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var author = button.getAttribute('data-author');
            var publisher = button.getAttribute('data-publisher');
            var price = button.getAttribute('data-price');
            var description = button.getAttribute('data-description');
            var category = button.getAttribute('data-category');
            var stock = button.getAttribute('data-stock');
            var bookType = button.getAttribute('data-book-type');
            var cover = button.getAttribute('data-cover');
            var status = button.getAttribute('data-status');

            // Tampilkan data di View Mode
            document.getElementById('modalBookTitle').textContent = title;
            document.getElementById('modalBookAuthor').textContent = author;
            document.getElementById('modalBookPublisher').textContent = publisher;
            document.getElementById('modalBookPrice').textContent = price;
            document.getElementById('modalBookCategory').textContent = category;
            document.getElementById('modalBookStock').textContent = stock;
            document.getElementById('modalBookType').textContent = bookType === 'physical' ? 'Buku Fisik' : 'E-Book';
            document.getElementById('modalBookDescription').textContent = description;
            document.getElementById('modalBookCover').src = cover;

            // Reset form
            document.getElementById('editBookForm').reset();
            document.getElementById('editBookForm').action = '/seller/books/' + id;

            // Isi Form Edit
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_publisher').value = publisher;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_book_type').value = bookType;

            toggleFileInput(bookType);

            // Hapus button
            document.getElementById('deleteButton').onclick = function () {
                var deleteForm = document.getElementById('deleteForm');
                deleteForm.action = '/seller/books/' + id;
                deleteForm.submit();
            };

            // Show View Mode
            viewMode.style.display = 'block';
            document.getElementById('editBookForm').style.display = 'none';
        });

        // Tombol edit diklik
        document.getElementById('editButton').addEventListener('click', function () {
            viewMode.style.display = 'none';
            document.getElementById('editBookForm').style.display = 'block';
        });

        // Handle jenis buku toggle input
        document.getElementById('edit_book_type').addEventListener('change', function () {
            toggleFileInput(this.value);
        });

        function toggleFileInput(type) {
            const ebookGroup = document.getElementById('edit_ebook_file_group');
            const physicalGroup = document.getElementById('edit_physical_file_group');

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
        }
    });
</script>
@endpush
@endsection
