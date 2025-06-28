@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Buat Kode Redeem Baru</h4>
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

                    <form action="{{ route('admin.redeem_code.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Kode (Opsional - akan digenerate otomatis)</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ old('code') }}" placeholder="Kosongkan untuk generate otomatis">
                                    <small class="form-text text-muted">Jika dikosongkan, kode akan digenerate otomatis</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipe Kode</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>Diskon</option>
                                        <option value="cashback" {{ old('type') == 'cashback' ? 'selected' : '' }}>Cashback</option>
                                        <option value="promo" {{ old('type') == 'promo' ? 'selected' : '' }}>Promo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Nilai</label>
                                    <input type="number" class="form-control" id="value" name="value" 
                                           step="0.01" min="0" value="{{ old('value') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value_type">Tipe Nilai</label>
                                    <select class="form-control" id="value_type" name="value_type" required>
                                        <option value="">Pilih Tipe Nilai</option>
                                        <option value="percentage" {{ old('value_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="fixed" {{ old('value_type') == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_usage">Maksimal Penggunaan</label>
                                    <input type="number" class="form-control" id="max_usage" name="max_usage" 
                                           min="1" value="{{ old('max_usage', 1) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_purchase">Minimal Pembelian (Opsional)</label>
                                    <input type="number" class="form-control" id="min_purchase" name="min_purchase" 
                                           step="0.01" min="0" value="{{ old('min_purchase') }}">
                                    <small class="form-text text-muted">Kosongkan jika tidak ada minimal pembelian</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_from">Berlaku Dari</label>
                                    <input type="date" class="form-control" id="valid_from" name="valid_from" 
                                           value="{{ old('valid_from') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_until">Berlaku Sampai</label>
                                    <input type="date" class="form-control" id="valid_until" name="valid_until" 
                                           value="{{ old('valid_until') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi (Opsional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      maxlength="500" placeholder="Deskripsi kode redeem...">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Maksimal 500 karakter</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan Kode Redeem
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

<script>
    // Set default dates
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        const nextMonthStr = nextMonth.toISOString().split('T')[0];
        
        if (!document.getElementById('valid_from').value) {
            document.getElementById('valid_from').value = today;
        }
        if (!document.getElementById('valid_until').value) {
            document.getElementById('valid_until').value = nextMonthStr;
        }
    });
</script>
@endsection 