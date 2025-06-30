@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Daftar Transaksi Saya</h2>

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
@endsection
