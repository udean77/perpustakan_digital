@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow rounded">
        <div class="card-header text-white" style="background-color: #212529;">
            <h4 class="mb-0">Buka Toko Gratis</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('seller.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="store_name" class="form-label">Nama Toko</label>
                    <input type="text" class="form-control" id="store_name" name="store_name" required>
                </div>

                <div class="mb-3">
                    <label for="store_description" class="form-label">Deskripsi Toko</label>
                    <textarea class="form-control" id="store_description" name="store_description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="store_logo" class="form-label">Logo Toko (Opsional)</label>
                    <input type="file" class="form-control" id="store_logo" name="store_logo" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="store_address" class="form-label">Alamat Toko</label>
                    <input type="text" class="form-control" id="store_address" name="store_address" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor WhatsApp (Format: 628XXXXXXXXXX)</label>
                    <input type="text" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        placeholder="Contoh: 6281234567890 (Tanpa +)" 
                        required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <button type="submit" class="btn btn-success">Buat Toko</button>
            </form>
        </div>
    </div>
</div>
@endsection
