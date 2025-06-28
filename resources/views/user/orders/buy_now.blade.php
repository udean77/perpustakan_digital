@extends('layouts.app')

@section('title', 'Beli Sekarang')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">⚡ Beli Sekarang</h2>

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
            <div class="d-flex mb-3 p-2 border rounded shadow-sm">
                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover Buku" class="me-3 rounded" style="width: 80px; height: 100px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $book->title }}</h6>
                    <div class="text-muted small">
                        <div>Harga: Rp{{ number_format($book->price, 0, ',', '.') }}</div>
                        <div>Jumlah: <span id="quantity-display">1</span></div>
                        <div class="fw-semibold mt-1">Subtotal: <span id="item-subtotal">Rp{{ number_format($book->price, 0, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>

            <hr>
            @php
                $subtotal = $book->price; // Default quantity is 1
            @endphp
            <div class="d-flex justify-content-between">
                <span class="fw-medium">Subtotal</span>
                <span id="subtotal-amount" data-value="{{ $subtotal }}">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            {{-- Ongkir Section --}}
            @php
                $shippingCost = rand(10000, 30000);
            @endphp
            <div class="d-flex justify-content-between mt-2">
                <span class="fw-medium">Ongkos Kirim</span>
                <span id="shipping-cost" class="text-info">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
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
                <span id="total-amount">Rp {{ number_format($subtotal + $shippingCost, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Form Checkout --}}
    <form action="{{ route('checkout.processBuyNow') }}" method="POST">
        @csrf
        <input type="hidden" name="book_id" value="{{ $book->id }}">
        <input type="hidden" name="quantity" id="quantity-input" value="1">
        <input type="hidden" name="redeem_code" id="applied_redeem_code" value="">
        <input type="hidden" name="shipping_cost" value="{{ $shippingCost ?? 0 }}">

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
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-outline-primary mt-2">Tambah Alamat</a>
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
    function number_format(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Sama seperti di user.orders.index
        const applyBtn = document.getElementById('apply-redeem-code');
        const redeemInput = document.getElementById('redeem-code-input');
        const feedbackDiv = document.getElementById('redeem-code-feedback');
        const subtotalEl = document.getElementById('subtotal-amount');
        const totalEl = document.getElementById('total-amount');
        const discountRow = document.getElementById('discount-row');
        const discountAmountEl = document.getElementById('discount-amount');
        const checkoutForm = document.querySelector('form');
        
        let subtotal = parseFloat(subtotalEl.getAttribute('data-value'));
        let shippingCost = {{ $shippingCost ?? 0 }};
        let appliedCode = null;

        // Initialize discount display
        discountAmountEl.textContent = `- Rp 0`;
        discountRow.style.display = 'flex';

        // Update total calculation function
        function updateTotal() {
            let discount = 0;
            if (appliedCode) {
                const discountText = discountAmountEl.textContent;
                discount = parseInt(discountText.replace(/[^\d]/g, '')) || 0;
            }
            const newTotal = subtotal + shippingCost - discount;
            totalEl.textContent = `Rp ${number_format(newTotal)}`;
            
            // Update hidden input for shipping cost
            const shippingCostInput = document.querySelector('input[name="shipping_cost"]');
            if (shippingCostInput) {
                shippingCostInput.value = shippingCost;
            }
        }

        // Initial total calculation
        updateTotal();

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
                updateTotal();

                const hiddenInput = document.getElementById('applied_redeem_code');
                hiddenInput.value = appliedCode;

            } else {
                appliedCode = null;
                feedbackDiv.innerHTML = `<span class="text-danger">✗ ${result.message}</span>`;
                discountAmountEl.textContent = `- Rp 0`;
                updateTotal();

                const hiddenInput = document.getElementById('applied_redeem_code');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            }
        });
    });
</script>
@endpush 