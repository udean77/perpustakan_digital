@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $store->logo ? asset('storage/store_logo/' . $store->logo) : asset('images/store_default.png') }}" class="rounded mb-3" width="100" alt="Logo Toko">
                    <h5>{{ $store->name }}</h5>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    <p>{{ $store->phone }}</p>
                    <span class="badge bg-success">Terverifikasi</span>
                </div>
            </div>

            <div class="list-group">
                <a href="{{ route('seller.dashboard') }}" class="list-group-item list-group-item-action">📊 Dashboard</a>
                <a href="{{ route('seller.books.index') }}" class="list-group-item list-group-item-action">📚 Daftar Buku</a>
                <a href="{{ route('seller.books.create') }}" class="list-group-item list-group-item-action">➕ Tambah Buku</a>
                <a href="{{ route('seller.orders.index') }}" class="list-group-item list-group-item-action">📦 Daftar Pesanan</a>
                <a href="{{ route('seller.details.index') }}" class="list-group-item list-group-item-action">📈 Ringkasan Penjualan</a>
                <a href="{{ route('seller.reports.index') }}" class="list-group-item list-group-item-action">🚩 Laporan Buku</a>
                <a href="{{ route('seller.store.edit') }}" class="list-group-item list-group-item-action active">🏪 Pengaturan Toko</a>
            </div>
        </div>

        <!-- Form Pengaturan -->
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Pengaturan Toko</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('seller.store.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="store_name" class="form-label">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $store->name) }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="store_description" class="form-label">Deskripsi Toko</label>
                            <textarea name="store_description" class="form-control @error('store_description') is-invalid @enderror" rows="3">{{ old('store_description', $store->description) }}</textarea>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="store_address" class="form-label">Alamat Toko</label>
                            <input type="text" name="store_address" class="form-control @error('store_address') is-invalid @enderror" value="{{ old('store_address', $store->address) }}">
                            @error('store_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       <div class="mb-3">
                            <label for="phone" class="form-label">Nomor WhatsApp (Format: 628XXXXXXXXX)</label>
                            <input type="text" name="phone" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                value="{{ old('phone', $store->phone) }}" 
                                placeholder="Contoh: 6281234567890 (Tanpa tanda +)">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="store_logo" class="form-label">Logo Toko</label>
                            <input type="file" name="store_logo" class="form-control @error('store_logo') is-invalid @enderror">
                            @error('store_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($store->logo)
                                <small class="d-block mt-2">Logo saat ini: {{ $store->logo }}</small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <!-- Form Report ke Admin -->
            <div class="card">
                <div class="card-header">
                    <h5>Laporkan Masalah ke Admin</h5>
                </div>
                <div class="card-body">
                    @if(session('report_success'))
                        <div class="alert alert-info">{{ session('report_success') }}</div>
                    @endif
                    <form action="{{ route('seller.store.report') }}" method="POST" id="reportForm">
                        @csrf

                        <div class="mb-3">
                            <label for="bookSelect" class="form-label">Pilih Buku</label>
                            <select name="reportable_id" id="bookSelect" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Buku --</option>
                                @foreach ($books as $book)
                                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="reportable_type" value="book">
                        </div>

                        <div id="reasonSection" class="mb-3 d-none">
                            <label for="reason" class="form-label">Alasan Laporan</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Tulis alasan laporan..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger d-none" id="submitBtn">Laporkan Buku</button>
                    </form>



                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('bookSelect').addEventListener('change', function () {
        document.getElementById('reasonSection').classList.remove('d-none');
        document.getElementById('submitBtn').classList.remove('d-none');
    });
</script>
@endpush

@endsection
