<!DOCTYPE html>
<html>
<head>
    <title>Pustaka Digital</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="container">
    <div class="left-panel">
        <img src="{{ asset('backend/images/logo1.png') }}" alt="Ilustrasi Login">
        <h2>Selamat Datang</h2>
    </div>
    <div class="right-panel">
        <h2>Masuk Akun Pustaka Digital</h2>

        @if ($errors->has('email'))
            <p class="error">{{ $errors->first('email') }}</p>
        @endif



        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar disini</a></p>
    </div>
</div>
</body>
</html>
