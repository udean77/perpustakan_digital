@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Daftar Transaksi Saya</h2>

    {{-- Payment Status Alerts --}}
    @if(request()->get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <strong>Pembayaran Berhasil!</strong> Pesanan Anda telah selesai dan akan segera diproses.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(request()->get('pending'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-clock"></i> <strong>Pembayaran Tertunda</strong> Mohon selesaikan pembayaran Anda sesuai instruksi yang diberikan.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(request()->get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <strong>Pembayaran Gagal</strong> Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($transactions->isEmpty())
        <div class="alert alert-info">
            Kamu belum memiliki transaksi.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Diskon</th>
                        <th>Kode Redeem</th>
                        <th>Status</th>
                        <th>Metode Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d M Y, H:i') }}</td>
                            <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($order->discount_amount > 0)
                                    <span class="text-success">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">Rp0</span>
                                @endif
                            </td>
                            <td>
                                @if($order->redeemCode)
                                    <span class="badge bg-info">{{ $order->redeemCode->code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</td>
                            <td>
                                <a href="{{ route('user.transaction.show', $order->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                                @if ($order->status === 'pending')
                                    <form action="{{ route('transactions.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Batalkan Transaksi</button>
                                    </form>

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
// Clean up URL parameters after showing alerts
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') || urlParams.has('pending') || urlParams.has('error')) {
        // Remove the parameters from URL without reloading the page
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>
@endsection
