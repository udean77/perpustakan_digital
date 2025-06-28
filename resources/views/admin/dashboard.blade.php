@extends('layouts.admin')
@section('content')

<!-- Ringkasan Data -->
<div class="container-fluid mt-4">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h3 class="card-title mb-0">{{ $userCount }}</h3>
                    <div class="small">Total Pengguna</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h3 class="card-title mb-0">{{ $bookCount }}</h3>
                    <div class="small">Total Buku</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h3 class="card-title mb-0">{{ $sellerCount }}</h3>
                    <div class="small">Total Penjual</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h3 class="card-title mb-0">{{ $storeCount }}</h3>
                    <div class="small">Total Toko</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
