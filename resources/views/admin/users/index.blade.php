@extends('layouts.admin') {{-- Sesuaikan dengan layout admin kamu --}}

@section('title', 'Daftar Pengguna')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Pengguna</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">+ Tambah Pengguna</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                 <td>
                    @if($user->status === 'active')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </td>

                <td>
                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm {{ $user->status === 'active' ? 'btn-secondary' : 'btn-success' }}">
                            {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password pengguna ini ke default?')">
                        @csrf
                        <button class="btn btn-sm btn-info">Reset Password</button>
                    </form>
                    <form action="{{ route('admin.users.change-role', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengubah peran pengguna ini menjadi Admin?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="btn btn-sm btn-warning">Ubah ke Admin</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada pengguna.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
