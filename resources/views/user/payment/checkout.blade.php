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
                    <h5>Opsi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="city-search" class="form-label">Cari Kota/Kabupaten Tujuan</label>
                        <input type="text" class="form-control" id="city-search" placeholder="Ketik nama kota...">
                        <div id="city-search-results" class="list-group mt-1"></div>
                        <input type="hidden" id="destination-city-id" name="destination_city_id">
                    </div>
                    <div class="mb-3">
                        <label for="courier" class="form-label">Kurir</label>
                        <select class="form-control" id="courier" name="courier">
                            <option value="">Pilih Kurir</option>
                            <option value="jne">JNE</option>
                            <option value="tiki">TIKI</option>
                            <option value="pos">POS Indonesia</option>
                        </select>
                    </div>
                    <button id="check-ongkir" class="btn btn-secondary">Cek Ongkir</button>
                    <div id="ongkir-results" class="mt-3"></div>
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

    // Real-time city search
    $('#city-search').on('keyup', function() {
        let searchTerm = $(this).val();
        if (searchTerm.length < 3) {
            $('#city-search-results').empty();
            return;
        }

        $.ajax({
            url: '{{ route("ongkir.search_cities") }}',
            type: 'GET',
            data: { q: searchTerm },
            success: function(response) {
                let results = response.rajaongkir.results;
                $('#city-search-results').empty();
                $.each(results, function(key, value) {
                    $('#city-search-results').append(
                        '<a href="#" class="list-group-item list-group-item-action city-option" data-id="' + value.city_id + '" data-name="' + value.city_name + '">' + value.type + ' ' + value.city_name + ', ' + value.province + '</a>'
                    );
                });
            }
        });
    });

    // Handle city selection from search results
    $(document).on('click', '.city-option', function(e) {
        e.preventDefault();
        let cityId = $(this).data('id');
        let cityName = $(this).data('name');
        
        $('#destination-city-id').val(cityId);
        $('#city-search').val(cityName);
        $('#city-search-results').empty();
    });

    // Handle check ongkir button click
    $('#check-ongkir').on('click', function() {
        let origin = '152'; // Example: Jakarta Selatan, needs to be dynamic from store address
        let destination = $('#destination-city-id').val();
        let weight = 1000; // Example weight in grams, needs to be dynamic from cart items
        let courier = $('#courier').val();

        if (destination && weight && courier) {
            $.ajax({
                url: '{{ route("ongkir.check") }}',
                type: 'POST',
                data: {
                    origin: origin,
                    destination: destination,
                    weight: weight,
                    courier: courier
                },
                success: function(response) {
                    $('#ongkir-results').empty();
                    if (response && response.rajaongkir && response.rajaongkir.results && response.rajaongkir.results.length > 0) {
                        let costs = response.rajaongkir.results[0].costs;
                        let html = '<h5>Pilih Layanan Pengiriman:</h5>';
                        html += '<div class="list-group">';
                        $.each(costs, function(key, cost) {
                            let price = cost.cost[0].value;
                            html += '<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ongkir-option" data-cost="' + price + '">';
                            html += '<div><strong>' + cost.service.toUpperCase() + '</strong><br><small>' + cost.description + ' ('+cost.cost[0].etd+' hari)</small></div>';
                            html += '<span>Rp' + new Intl.NumberFormat('id-ID').format(price) + '</span>';
                            html += '</a>';
                        });
                        html += '</div>';
                        $('#ongkir-results').html(html);
                    } else {
                        var errorMessage = "Layanan tidak tersedia.";
                        if (response && response.rajaongkir && response.rajaongkir.status) {
                            errorMessage += " Pesan: " + response.rajaongkir.status.description;
                        }
                        $('#ongkir-results').html('<p class="text-danger">' + errorMessage + '</p>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                     $('#ongkir-results').html('<p class="text-danger">Gagal mengecek ongkir. Silakan coba lagi. (' + textStatus + ')</p>');
                }
            });
        } else {
            alert('Harap lengkapi semua pilihan pengiriman.');
        }
    });

    // Handle ongkir option selection
    $(document).on('click', '.ongkir-option', function(e) {
        e.preventDefault();
        $('.ongkir-option').removeClass('active');
        $(this).addClass('active');

        let shippingCost = $(this).data('cost');
        let subtotal = {{ $order->total_amount }};
        let totalAmount = subtotal + shippingCost;
        
        $('#total-amount-display').text('Rp' + new Intl.NumberFormat('id-ID').format(totalAmount));
        $('#pay-button').prop('disabled', false).html('<i class="bi bi-credit-card"></i> Bayar Sekarang');
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
            document.getElementById('pay-button').innerHTML = '<i class="bi bi-credit-card"></i> Pilih Pengiriman Dulu';
        }
    });
});
</script>
@endsection 