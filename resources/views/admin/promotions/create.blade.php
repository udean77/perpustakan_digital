@extends('layouts.admin')

@section('title', 'Tambah Promosi Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Form Tambah Promosi</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="title">Judul Promosi</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Gambar Promosi</label>
                <input type="file" name="image" id="image" class="form-control-file" required>
                <small class="form-text text-muted">Rekomendasi ukuran: 1200x400 piksel.</small>
            </div>
            <div class="form-group">
                <label for="redeem_code_id">Kaitkan Kode Redeem (Opsional)</label>
                <select name="redeem_code_id" id="redeem_code_id" class="form-control">
                    <option value="">Tidak ada</option>
                    @foreach($redeemCodes as $code)
                        <option value="{{ $code->id }}">{{ $code->code }} - {{ $code->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="expires_at">Tanggal Berakhir (Opsional)</label>
                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                <label for="is_active" class="form-check-label">Aktifkan promosi ini</label>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Promosi</button>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection 