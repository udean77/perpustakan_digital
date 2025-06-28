@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Manajemen Kode Redeem</h4>
                        <div>
                            <a href="{{ route('admin.redeem_code.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Buat Kode Baru
                            </a>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#generateMultipleModal">
                                <i class="mdi mdi-plus-multiple"></i> Generate Multiple
                            </button>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Nilai</th>
                                    <th>Min. Pembelian</th>
                                    <th>Penggunaan</th>
                                    <th>Periode Berlaku</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($redeemCodes as $code)
                                <tr>
                                    <td>{{ $code->id }}</td>
                                    <td>
                                        <strong>{{ $code->code }}</strong>
                                        @if($code->description)
                                            <br><small class="text-muted">{{ $code->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($code->type)
                                            @case('discount')
                                                <span class="badge badge-info">Diskon</span>
                                                @break
                                            @case('cashback')
                                                <span class="badge badge-warning">Cashback</span>
                                                @break
                                            @case('promo')
                                                <span class="badge badge-success">Promo</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($code->value_type === 'percentage')
                                            {{ $code->value }}%
                                        @else
                                            Rp {{ number_format($code->value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($code->min_purchase)
                                            Rp {{ number_format($code->min_purchase, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $code->used_count }} / {{ $code->max_usage }}
                                    </td>
                                    <td>
                                        <small>
                                            {{ $code->valid_from->format('d/m/Y') }} - {{ $code->valid_until->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($code->isValid())
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($code->status === 'inactive')
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @elseif($code->status === 'expired')
                                            <span class="badge badge-danger">Kadaluarsa</span>
                                        @else
                                            <span class="badge badge-warning">Habis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.redeem_code.edit', $code->id) }}" 
                                               class="btn btn-sm btn-info" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.redeem_code.toggleStatus', $code->id) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $code->status === 'active' ? 'btn-warning' : 'btn-success' }}" 
                                                        title="{{ $code->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="mdi {{ $code->status === 'active' ? 'mdi-eye-off' : 'mdi-eye' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.redeem_code.destroy', $code->id) }}" 
                                                  style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kode ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Multiple Modal -->
<div class="modal fade" id="generateMultipleModal" tabindex="-1" role="dialog" aria-labelledby="generateMultipleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.redeem_code.generateMultiple') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateMultipleModalLabel">Generate Multiple Kode Redeem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="count">Jumlah Kode</label>
                                <input type="number" class="form-control" id="count" name="count" min="1" max="100" value="10" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Tipe Kode</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="discount">Diskon</option>
                                    <option value="cashback">Cashback</option>
                                    <option value="promo">Promo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value">Nilai</label>
                                <input type="number" class="form-control" id="value" name="value" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value_type">Tipe Nilai</label>
                                <select class="form-control" id="value_type" name="value_type" required>
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Nominal (Rp)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_usage">Maksimal Penggunaan</label>
                                <input type="number" class="form-control" id="max_usage" name="max_usage" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_purchase">Minimal Pembelian (Opsional)</label>
                                <input type="number" class="form-control" id="min_purchase" name="min_purchase" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_from">Berlaku Dari</label>
                                <input type="date" class="form-control" id="valid_from" name="valid_from" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_until">Berlaku Sampai</label>
                                <input type="date" class="form-control" id="valid_until" name="valid_until" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Set default dates for the modal
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        const nextMonthStr = nextMonth.toISOString().split('T')[0];
        
        document.getElementById('valid_from').value = today;
        document.getElementById('valid_until').value = nextMonthStr;
    });
</script>
@endsection
