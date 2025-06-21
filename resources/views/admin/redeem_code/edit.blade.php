@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Edit Kode Redeem</h4>
                        <a href="{{ route('admin.redeem_code.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.redeem_code.update', $redeemCode->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Kode</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ $redeemCode->code }}" readonly>
                                    <small class="form-text text-muted">Kode tidak dapat diubah</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipe Kode</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="discount" {{ $redeemCode->type == 'discount' ? 'selected' : '' }}>Diskon</option>
                                        <option value="cashback" {{ $redeemCode->type == 'cashback' ? 'selected' : '' }}>Cashback</option>
                                        <option value="free_shipping" {{ $redeemCode->type == 'free_shipping' ? 'selected' : '' }}>Gratis Ongkir</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Nilai</label>
                                    <input type="number" class="form-control" id="value" name="value" 
                                           step="0.01" min="0" value="{{ $redeemCode->value }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value_type">Tipe Nilai</label>
                                    <select class="form-control" id="value_type" name="value_type" required>
                                        <option value="">Pilih Tipe Nilai</option>
                                        <option value="percentage" {{ $redeemCode->value_type == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="fixed" {{ $redeemCode->value_type == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_usage">Maksimal Penggunaan</label>
                                    <input type="number" class="form-control" id="max_usage" name="max_usage" 
                                           min="{{ $redeemCode->used_count }}" value="{{ $redeemCode->max_usage }}" required>
                                    <small class="form-text text-muted">Minimal: {{ $redeemCode->used_count }} (sudah digunakan)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_purchase">Minimal Pembelian (Opsional)</label>
                                    <input type="number" class="form-control" id="min_purchase" name="min_purchase" 
                                           step="0.01" min="0" value="{{ $redeemCode->min_purchase }}">
                                    <small class="form-text text-muted">Kosongkan jika tidak ada minimal pembelian</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_from">Berlaku Dari</label>
                                    <input type="date" class="form-control" id="valid_from" name="valid_from" 
                                           value="{{ $redeemCode->valid_from->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_until">Berlaku Sampai</label>
                                    <input type="date" class="form-control" id="valid_until" name="valid_until" 
                                           value="{{ $redeemCode->valid_until->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active" {{ $redeemCode->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ $redeemCode->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                        <option value="expired" {{ $redeemCode->status == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Penggunaan Saat Ini</label>
                                    <input type="text" class="form-control" value="{{ $redeemCode->used_count }} / {{ $redeemCode->max_usage }}" readonly>
                                    <small class="form-text text-muted">Tidak dapat diubah</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi (Opsional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      maxlength="500" placeholder="Deskripsi kode redeem...">{{ $redeemCode->description }}</textarea>
                            <small class="form-text text-muted">Maksimal 500 karakter</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update Kode Redeem
                            </button>
                            <a href="{{ route('admin.redeem_code.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 