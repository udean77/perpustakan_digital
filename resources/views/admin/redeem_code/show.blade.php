@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Detail Kode Redeem</h4>
                        <div>
                            <a href="{{ route('admin.redeem_code.edit', $redeemCode->id) }}" class="btn btn-info">
                                <i class="mdi mdi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('admin.redeem_code.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Kode:</strong></td>
                                    <td><span class="badge badge-primary" style="font-size: 1.2em;">{{ $redeemCode->code }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe:</strong></td>
                                    <td>
                                        @switch($redeemCode->type)
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
                                </tr>
                                <tr>
                                    <td><strong>Nilai:</strong></td>
                                    <td>
                                        @if($redeemCode->value_type === 'percentage')
                                            {{ $redeemCode->value }}%
                                        @else
                                            Rp {{ number_format($redeemCode->value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Min. Pembelian:</strong></td>
                                    <td>
                                        @if($redeemCode->min_purchase)
                                            Rp {{ number_format($redeemCode->min_purchase, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Status:</strong></td>
                                    <td>
                                        @if($redeemCode->isValid())
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($redeemCode->status === 'inactive')
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @elseif($redeemCode->status === 'expired')
                                            <span class="badge badge-danger">Kadaluarsa</span>
                                        @else
                                            <span class="badge badge-warning">Habis</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Penggunaan:</strong></td>
                                    <td>{{ $redeemCode->used_count }} / {{ $redeemCode->max_usage }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Berlaku Dari:</strong></td>
                                    <td>{{ $redeemCode->valid_from->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Berlaku Sampai:</strong></td>
                                    <td>{{ $redeemCode->valid_until->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat:</strong></td>
                                    <td>{{ $redeemCode->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $redeemCode->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($redeemCode->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Deskripsi:</h5>
                                <p class="text-muted">{{ $redeemCode->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informasi Kode:</h6>
                                    <ul class="mb-0">
                                        @if($redeemCode->isValid())
                                            <li class="text-success">✓ Kode ini masih aktif dan dapat digunakan</li>
                                        @else
                                            <li class="text-danger">✗ Kode ini tidak dapat digunakan</li>
                                        @endif
                                        
                                        @if($redeemCode->min_purchase)
                                            <li>Minimal pembelian: Rp {{ number_format($redeemCode->min_purchase, 0, ',', '.') }}</li>
                                        @else
                                            <li>Tidak ada minimal pembelian</li>
                                        @endif
                                        
                                        @if($redeemCode->used_count >= $redeemCode->max_usage)
                                            <li class="text-warning">Kode sudah habis digunakan</li>
                                        @else
                                            <li>Masih dapat digunakan {{ $redeemCode->max_usage - $redeemCode->used_count }} kali lagi</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 