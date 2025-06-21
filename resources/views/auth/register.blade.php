<!DOCTYPE html>
<html>
<head>
    <title>Register - BukuKita</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="container">
    <div class="left-panel">
        <img src="{{ asset('backend/images/logo1.png') }}" alt="Ilustrasi Register">
    </div>
    <div class="right-panel">
        <h2>Daftar Akun Baru</h2>

        @if($errors->any())
            <ul class="error">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <label>Nama:</label>
            <input type="text" name="nama" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Konfirmasi Password:</label>
            <input type="password" name="password_confirmation" required>

            <input type="hidden" name="role" value="pembeli">

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="{{ route('login') }}">Login disini</a></p>
    </div>
</div>
</body>
</html>
