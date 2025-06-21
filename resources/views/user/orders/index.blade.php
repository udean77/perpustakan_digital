@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🛒 Checkout</h2>

    {{-- Informasi Pembeli --}}
    <div class="card mb-4">
        <div class="card-header">Informasi Pembeli</div>
        <div class="card-body">
            <p><strong>Nama:</strong> {{ auth()->user()->nama }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        </div>
    </div>

    {{-- Rincian Pesanan --}}
    <div class="card mb-4">
        <div class="card-header bg-light fw-semibold">Rincian Pesanan</div>
        <div class="card-body">
            @if ($cartItems->isNotEmpty())
                @foreach($cartItems as $item)
                    <div class="d-flex mb-3 p-2 border rounded shadow-sm">
                        <img src="{{ asset('storage/' . $item->book->cover) }}" 
                            alt="Cover Buku" 
                            class="me-3 rounded" 
                            style="width: 80px; height: 100px; object-fit: cover;">

                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->book->title }}</h6>
                            <div class="text-muted small">
                                <div>Harga: Rp{{ number_format($item->book->price, 0, ',', '.') }}</div>
                                <div>Jumlah: {{ $item->quantity }}</div>
                                <div class="fw-semibold mt-1">Subtotal: Rp{{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-medium">Subtotal</span>
                    <span id="subtotal-amount" data-value="{{ $total }}">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- Redeem Code Section --}}
                <div class="mt-3">
                    <label for="redeem-code-input" class="form-label">Punya kode redeem?</label>
                    <div class="input-group">
                        <input type="text" id="redeem-code-input" class="form-control" placeholder="Masukkan kode di sini">
                        <button class="btn btn-outline-secondary" type="button" id="apply-redeem-code">Gunakan</button>
                    </div>
                    <div id="redeem-code-feedback" class="small mt-2"></div>
                </div>

                <div id="discount-row" class="d-flex justify-content-between mt-2">
                    <span class="fw-medium">Diskon</span>
                    <span id="discount-amount" class="text-success">- Rp 0</span>
                </div>

                <div class="d-flex justify-content-between fw-bold mt-2">
                    <span>Total</span>
                    <span id="total-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="text-center text-muted">Tidak ada item dalam pesanan.</div>
            @endif
        </div>
    </div>

    {{-- Form Checkout --}}
    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <input type="hidden" name="redeem_code" id="applied_redeem_code" value="">

        {{-- Alamat Pengiriman --}}
        <div class="card mb-4">
            <div class="card-header">Alamat Pengiriman</div>
            <div class="card-body">
               @forelse ($addresses as $address)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="shipping_address"
                            id="address{{ $address->id }}"
                            value="{{ $address->alamat_lengkap }}"
                            {{ old('shipping_address') == $address->alamat_lengkap ? 'checked' : '' }}>
                        <label class="form-check-label" for="address{{ $address->id }}">
                            {{ $address->label }} - {{ $address->alamat_lengkap }}
                        </label>
                    </div>
                @empty
                    <div class="text-danger">
                        Anda belum memiliki alamat pengiriman.
                        <a href="{{ route('user.address.index') }}" class="btn btn-sm btn-outline-primary mt-2">Tambah Alamat</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Metode Pembayaran Dihapus, pemilihan via Midtrans Snap --}}

        <button type="submit" class="btn btn-primary w-100 py-2"
            onclick="return confirm('Anda akan diarahkan ke halaman pembayaran aman. Pastikan semua data sudah benar.');">
            Lanjutkan ke Pembayaran
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/redeem-code.js') }}"></script>
<script>
    // Helper function to format numbers as currency
    function number_format(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const applyBtn = document.getElementById('apply-redeem-code');
        const redeemInput = document.getElementById('redeem-code-input');
        const feedbackDiv = document.getElementById('redeem-code-feedback');
        const subtotalEl = document.getElementById('subtotal-amount');
        const totalEl = document.getElementById('total-amount');
        const discountRow = document.getElementById('discount-row');
        const discountAmountEl = document.getElementById('discount-amount');
        const checkoutForm = document.querySelector('form');
        
        let subtotal = parseFloat(subtotalEl.getAttribute('data-value'));
        let appliedCode = null;

        // Initialize discount display
        discountAmountEl.textContent = `- Rp 0`;
        discountRow.style.display = 'flex';

        applyBtn.addEventListener('click', async function() {
            const code = redeemInput.value.trim().toUpperCase();
            if (!code) {
                feedbackDiv.innerHTML = '<span class="text-danger">Silakan masukkan kode.</span>';
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';

            const result = await window.redeemCodeManager.validateCode(code, subtotal);
            
            this.disabled = false;
            this.innerHTML = 'Gunakan';
            
            if (result.success) {
                appliedCode = result.data.code;
                const discount = parseFloat(result.data.discount_amount);
                
                feedbackDiv.innerHTML = `<span class="text-success">✓ ${result.data.description || 'Kode berhasil diterapkan!'}</span>`;
                discountAmountEl.textContent = `- Rp ${number_format(discount)}`;
                discountRow.style.display = 'flex';
                totalEl.textContent = `Rp ${number_format(subtotal - discount)}`;

                // Update hidden input for the code
                const hiddenInput = document.getElementById('applied_redeem_code');
                hiddenInput.value = appliedCode;

            } else {
                appliedCode = null;
                feedbackDiv.innerHTML = `<span class="text-danger">✗ ${result.message}</span>`;
                discountAmountEl.textContent = `- Rp 0`;
                discountRow.style.display = 'flex';
                totalEl.textContent = `Rp ${number_format(subtotal)}`;

                // Clear hidden input if it exists
                const hiddenInput = document.getElementById('applied_redeem_code');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            }
        });
    });
</script>
@endpush
