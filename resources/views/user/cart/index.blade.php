@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="p-4 bg-light rounded-4 shadow-sm">
        <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

        @if(count($cartItems) > 0)
            <form action="{{ route('checkout.index.post') }}" method="POST" id="checkout-form">
                @csrf
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th scope="col">Buku</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="form-check-input item-checkbox" data-price="{{ ($item->book->discount_price ?? $item->book->price) * $item->quantity }}">
                                </td>
                                <td class="d-flex align-items-center">
                                    <a href="{{ route('books.show', $item->book->id) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                        <img src="{{ asset('storage/' . $item->book->cover) }}" alt="cover" width="50" class="me-3 rounded">
                                        <div>
                                            <div class="fw-semibold">{{ $item->book->title }}</div>
                                        </div>
                                    </a>
                                </td>

                                <td>Rp {{ number_format($item->book->discount_price ?? $item->book->price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format(($item->book->discount_price ?? $item->book->price) * $item->quantity, 0, ',', '.') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem({{ $item->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <h5>Total Terpilih: <strong id="selected-total">Rp 0</strong></h5>
                        <small class="text-muted">Pilih item yang ingin dicheckout</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="clearSelection()">Batal Pilih</button>
                        <button type="submit" class="btn btn-success" id="checkout-btn" disabled>Lanjut ke Pembayaran</button>
                    </div>
                </div>
            </form>
            
            {{-- Hidden form for delete operations --}}
            <form id="delete-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @else
            <div class="text-center p-5">
                <img src="{{ asset('images/cart.png') }}" alt="Kosong" width="120">
                <h5 class="mt-3 fw-bold">Keranjangmu masih kosong</h5>
                <p class="text-muted">Yuk, cari buku favoritmu dan masukkan ke keranjang!</p>
                <a href="{{ route('user.homepage') }}" class="btn btn-outline-primary rounded-pill px-4">Mulai Belanja</a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedTotalElement = document.getElementById('selected-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutForm = document.getElementById('checkout-form');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotal();
        updateCheckoutButton();
    });

    // Individual checkbox functionality
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateTotal();
            updateCheckoutButton();
        });
    });

    // Update select all checkbox
    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const totalBoxes = itemCheckboxes.length;
        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    // Update total
    function updateTotal() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        let total = 0;
        
        checkedBoxes.forEach(checkbox => {
            total += parseInt(checkbox.dataset.price);
        });
        
        selectedTotalElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    // Update checkout button
    function updateCheckoutButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        checkoutBtn.disabled = checkedBoxes.length === 0;
    }

    // Form submission validation
    checkoutForm.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu item untuk checkout');
            return false;
        }
        // Remove name from unchecked checkboxes so only checked are sent
        document.querySelectorAll('.item-checkbox:not(:checked)').forEach(checkbox => {
            checkbox.removeAttribute('name');
        });
    });
});

function clearSelection() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('selected-total').textContent = 'Rp 0';
    document.getElementById('checkout-btn').disabled = true;
}

function removeItem(itemId) {
    if (confirm('Yakin ingin menghapus item ini?')) {
        const form = document.getElementById('delete-form');
        form.action = "{{ route('cart.remove', '') }}/" + itemId;
        form.submit();
    }
}
</script>
@endsection
