@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Lapor Masalah</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('user.reports.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="reportable_type" class="form-label">Jenis Laporan</label>
            <select id="reportable_type" name="reportable_type" class="form-select @error('reportable_type') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="product" {{ old('reportable_type') == 'product' ? 'selected' : '' }}>Produk</option>
                <option value="seller" {{ old('reportable_type') == 'seller' ? 'selected' : '' }}>Penjual</option>
                <option value="order" {{ old('reportable_type') == 'order' ? 'selected' : '' }}>Transaksi</option>
            </select>
            @error('reportable_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="reportable_id" class="form-label">Pilih Item</label>
            <select id="reportable_id" name="reportable_id" class="form-select @error('reportable_id') is-invalid @enderror" required>
                <option value="">-- Pilih Item --</option>
            </select>
            @error('reportable_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Alasan Laporan</label>
            <textarea id="reason" name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
            @error('reason')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
    </form>
</div>

<script>
    const products = @json($products);
    const sellers = @json($sellers);
    const orders = @json($orders);

    const typeSelect = document.getElementById('reportable_type');
    const itemSelect = document.getElementById('reportable_id');

    function loadItems(type) {
        let options = '<option value="">-- Pilih Item --</option>';
        let list = [];

        if(type === 'product') {
            list = products;
            list.forEach(item => {
                options += `<option value="${item.id}">${item.title}</option>`;
            });
        } else if(type === 'seller') {
            list = sellers;
            list.forEach(item => {
                options += `<option value="${item.id}">${item.nama}</option>`;
            });
        } else if(type === 'order') {
            list = orders;
            list.forEach(item => {
                options += `<option value="${item.id}">#${item.id} - ${item.status}</option>`;
            });
        }

        itemSelect.innerHTML = options;
    }

    typeSelect.addEventListener('change', function() {
        loadItems(this.value);
    });

    // Load items jika ada old input (misal setelah validasi error)
    @if(old('reportable_type'))
        loadItems("{{ old('reportable_type') }}");
        @if(old('reportable_id'))
            itemSelect.value = "{{ old('reportable_id') }}";
        @endif
    @endif
</script>
@endsection
