@extends('layouts.app')

@section('title', 'Checkout - Order #' . $order->id)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detail Pesanan #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    @foreach ($order->items as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ $item->book->title }}</strong> <br>
                                <small class="text-muted">Jumlah: {{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</small>
                            </div>
                            <div>
                                Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                    
                    <hr>
                    
                    @php
                        $subtotal = $order->items->sum(function($item) {
                            return $item->price * $item->quantity;
                        });
                    @endphp
                    
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span>Diskon</span>
                        <span class="text-success">
                            @if($order->discount_amount > 0)
                                -Rp{{ number_format($order->discount_amount, 0, ',', '.') }}
                                @if($order->redeemCode)
                                    <small class="text-muted">({{ $order->redeemCode->code }})</small>
                                @endif
                            @else
                                Rp0
                            @endif
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if ($order->address)
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Alamat Pengiriman</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>{{ $order->address->label }}</strong> <br>
                        Penerima: {{ $order->address->nama_penerima }} <br>
                        {{ $order->address->alamat_lengkap }} <br>
                        Telepon: {{ $order->address->no_hp }}
                    </p>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Pilih Alamat Pengiriman</h5>
                </div>
                <div class="card-body">
                    @foreach(auth()->user()->addresses as $address)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_address"
                                id="address{{ $address->id }}"
                                value="{{ $address->id }}"
                                {{ $loop->first ? 'checked' : '' }}>
                            <label class="form-check-label" for="address{{ $address->id }}">
                                {{ $address->label }} - {{ $address->alamat_lengkap }}
                            </label>
                        </div>
                    @endforeach
                    <a href="{{ route('user.profile') }}" class="btn btn-link mt-2">Tambah/ubah alamat di profil</a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="shipping_address">Alamat Pengiriman</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required>{{ auth()->user()->addresses->first()->address ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_phone">Nomor Telepon</label>
                        <input type="text" class="form-control" id="shipping_phone" name="shipping_phone" value="{{ auth()->user()->phone ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_note">Catatan Pengiriman (Opsional)</label>
                        <textarea class="form-control" id="shipping_note" name="shipping_note" rows="2"></textarea>
                    </div>
                    
                    <a href="{{ route('user.profile') }}" class="btn btn-link mt-2">Tambah/ubah alamat di profil</a>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Pembayaran</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Total yang harus dibayar:</p>
                    <h3 id="total-amount-display" class="text-primary">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</h3>
                    
                    <button id="pay-button" class="btn btn-primary btn-lg w-100 mt-3" disabled>
                        <i class="bi bi-credit-card"></i> Pilih Pengiriman Dulu
                    </button>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Pembayaran aman dengan Midtrans
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // AJAX setup for CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Enable payment button by default since we removed ongkir
    $('#pay-button').prop('disabled', false).html('<i class="bi bi-credit-card"></i> Bayar Sekarang');
});
</script>

<!-- Midtrans Snap Script -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.getElementById('pay-button').addEventListener('click', function() {
    // Disable button to prevent double click
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
    
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            console.log('Payment success:', result);
            // Handle success - redirect to transaction history
            window.location.href = '{{ route("user.transaction.index") }}?success=true&order_id=' + result.order_id;
        },
        onPending: function(result) {
            console.log('Payment pending:', result);
            // Handle pending
            window.location.href = '{{ route("user.transaction.index") }}?pending=true&order_id=' + result.order_id;
        },
        onError: function(result) {
            console.log('Payment error:', result);
            // Handle error
            window.location.href = '{{ route("user.transaction.index") }}?error=true&order_id=' + result.order_id;
        },
        onClose: function() {
            console.log('Payment popup closed');
            // Handle customer closed the popup without finishing payment
            document.getElementById('pay-button').disabled = false;
            document.getElementById('pay-button').innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
        }
    });
});
</script>
@endsection 