@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2>Riwayat Chat User Dengan PustawanAI</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Pesan</th>
                <th>Intent</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $h)
            <tr>
                <td>
                    @if($h->user)
                        ID: {{ $h->user->id }}<br>
                        Nama: {{ $h->user->nama ?? '-' }}<br>
                        Email: {{ $h->user->email ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $h->message }}</td>
                <td>{{ $h->intent }}</td>
                <td>{{ $h->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $histories->links() }}
</div>
@endsection 