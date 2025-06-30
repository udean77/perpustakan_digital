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
                    {{-- Debug info --}}
                    @if(config('app.debug'))
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong><br>
                        Order ID: {{ $order->id }}<br>
                        Items Count: {{ $order->items->count() }}<br>
                        Total Amount: {{ $order->total_amount }}<br>
                        @foreach($order->items as $item)
                            Item {{ $loop->iteration }}: {{ $item->book->title ?? 'No title' }} (Qty: {{ $item->quantity }}, Price: {{ $item->price }})<br>
                        @endforeach
                    </div>
                    @endif
                    
                    @if($order->items->count() > 0)
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>{{ $item->book->title ?? 'No Title' }}</strong> <br>
                                    <small class="text-muted">Jumlah: {{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</small>
                                </div>
                                <div>
                                    Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            <strong>Tidak ada item dalam pesanan ini!</strong><br>
                            Order ID: {{ $order->id }}<br>
                            Items Count: {{ $order->items->count() }}
                        </div>
                    @endif
                    
                    <hr>
                    
                    @php
                        $subtotal = $order->items->sum(function($item) {
                            return $item->price * $item->quantity;
                        });
                        
                        // Check if all items are ebooks
                        $allEbooks = $order->items->every(function($item) {
                            return $item->book->book_type === 'ebook';
                        });
                    @endphp
                    
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if(!$allEbooks)
                    <div class="d-flex justify-content-between">
                        <span>Ongkos Kirim</span>
                        <span class="text-info">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
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

            {{-- Redeem Code Section --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-tag"></i> Kode Voucher / Redeem</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="redeem-code-input" class="form-label">Punya kode redeem?</label>
                            @if($order->redeemCode)
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> 
                                    Kode <strong>{{ $order->redeemCode->code }}</strong> sudah diterapkan pada pesanan ini.
                                </div>
                            @endif
                            <div class="input-group">
                                <input type="text" id="redeem-code-input" class="form-control" placeholder="Masukkan kode di sini" 
                                       {{ $order->redeemCode ? 'disabled' : '' }}>
                                <button class="btn btn-outline-secondary" type="button" id="apply-redeem-code"
                                        {{ $order->redeemCode ? 'disabled' : '' }}>
                                    {{ $order->redeemCode ? 'Sudah Diterapkan' : 'Gunakan' }}
                                </button>
                            </div>
                            <div id="redeem-code-feedback" class="small mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium">Diskon dari Kode:</span>
                                <span id="redeem-discount-amount" class="text-success">
                                    - Rp {{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold mt-2">
                                <span>Total Setelah Diskon:</span>
                                <span id="final-total-amount">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($allEbooks)
            {{-- Info untuk Ebook --}}
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-download"></i> Informasi Ebook</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Ebook akan tersedia untuk diunduh setelah pembayaran berhasil.</strong></p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle"></i> 
                        Format file: PDF, EPUB, atau MOBI (tergantung ketersediaan)
                    </p>
                </div>
            </div>
            @else
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
            @endif

        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Pembayaran</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Total yang harus dibayar:</p>
                    <h3 id="total-amount-display" class="text-primary">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</h3>
                    
                    @if($order->discount_amount > 0)
                        <small class="text-success">
                            <i class="fas fa-tag"></i> Sudah termasuk diskon Rp{{ number_format($order->discount_amount, 0, ',', '.') }}
                            @if($order->redeemCode)
                                ({{ $order->redeemCode->code }})
                            @endif
                        </small>
                    @endif
                    
                    <button id="pay-button" class="btn btn-primary btn-lg w-100 mt-3" disabled>
                        @if($allEbooks)
                            <i class="bi bi-credit-card"></i> Bayar & Download Ebook
                        @else
                            <i class="bi bi-credit-card"></i> Pilih Pengiriman Dulu
                        @endif
                    </button>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Pembayaran aman dengan Midtrans
                        </small>
                    </div>
                    
                    {{-- Hidden input for redeem code --}}
                    <input type="hidden" id="applied_redeem_code" value="">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/redeem-code.js') }}"></script>
<script>
$(document).ready(function() {
    // AJAX setup for CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Enable payment button based on order type
    @if($allEbooks)
        $('#pay-button').prop('disabled', false).html('<i class="bi bi-credit-card"></i> Bayar & Download Ebook');
    @else
        $('#pay-button').prop('disabled', false).html('<i class="bi bi-credit-card"></i> Bayar Sekarang');
    @endif

    // Redeem code functionality
    const applyBtn = document.getElementById('apply-redeem-code');
    const redeemInput = document.getElementById('redeem-code-input');
    const feedbackDiv = document.getElementById('redeem-code-feedback');
    const redeemDiscountEl = document.getElementById('redeem-discount-amount');
    const finalTotalEl = document.getElementById('final-total-amount');
    const totalAmountDisplay = document.getElementById('total-amount-display');
    
    let originalTotal = {{ $order->total_amount }};
    let appliedCode = null;
    let redeemDiscount = 0;
    let existingDiscount = {{ $order->discount_amount ?? 0 }};

    // Initialize with existing discount if any
    @if($order->redeemCode)
        appliedCode = '{{ $order->redeemCode->code }}';
        redeemDiscount = {{ $order->discount_amount ?? 0 }};
        document.getElementById('applied_redeem_code').value = appliedCode;
    @endif

    function number_format(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    function updateFinalTotal() {
        const newTotal = originalTotal - redeemDiscount;
        finalTotalEl.textContent = `Rp ${number_format(newTotal)}`;
        totalAmountDisplay.textContent = `Rp${number_format(newTotal)}`;
    }

    // Initialize final total display
    updateFinalTotal();

    applyBtn.addEventListener('click', async function() {
        const code = redeemInput.value.trim().toUpperCase();
        if (!code) {
            feedbackDiv.innerHTML = '<span class="text-danger">Silakan masukkan kode.</span>';
            return;
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';

        // Use subtotal without existing discounts for validation
        const subtotalForValidation = originalTotal + existingDiscount;
        const result = await window.redeemCodeManager.validateCode(code, subtotalForValidation);
        
        this.disabled = false;
        this.innerHTML = 'Gunakan';
        
        if (result.success) {
            appliedCode = result.data.code;
            redeemDiscount = parseFloat(result.data.discount_amount);
            
            feedbackDiv.innerHTML = `<span class="text-success">✓ ${result.data.description || 'Kode berhasil diterapkan!'}</span>`;
            redeemDiscountEl.textContent = `- Rp ${number_format(redeemDiscount)}`;
            updateFinalTotal();

            // Save redeem code to hidden input
            document.getElementById('applied_redeem_code').value = appliedCode;

        } else {
            appliedCode = null;
            redeemDiscount = 0;
            feedbackDiv.innerHTML = `<span class="text-danger">✗ ${result.message}</span>`;
            redeemDiscountEl.textContent = `- Rp 0`;
            updateFinalTotal();

            // Clear redeem code from hidden input
            document.getElementById('applied_redeem_code').value = '';
        }
    });
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
            @if($allEbooks)
                document.getElementById('pay-button').innerHTML = '<i class="bi bi-credit-card"></i> Bayar & Download Ebook';
            @else
                document.getElementById('pay-button').innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
            @endif
        }
    });
});
</script>
@endsection 