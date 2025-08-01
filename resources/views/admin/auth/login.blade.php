<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Rakobuan</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Admin Login</h1>
                <p class="auth-subtitle">Selamat datang, silakan masuk.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Masuk</button>
            </form>
        </div>
        <div class="auth-footer" style="position: absolute; bottom: 20px; text-align: center;">
            <a href="{{ route('customer.login.form') }}" style="color: #888; font-size: 14px; text-decoration: none;">
                Login sebagai Pelanggan
            </a>
        </div>
    </div>
</body>
</html>