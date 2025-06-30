@extends('layouts.admin')
@section('content')

<!-- Ringkasan Data -->
<div class="container-fluid mt-4">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Buku</h5>
                    <p class="card-text fs-4">{{ $bookCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Pengguna</h5>
                    <p class="card-text fs-4">{{ $userCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Penjual</h5>
                    <p class="card-text fs-4">{{ $sellerCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Toko</h5>
                    <p class="card-text fs-4">{{ $storeCount }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
