<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Rakobuan</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <!-- <style>
        /* CSS Khusus untuk halaman login */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #F8F8F8;
            font-family: 'Poppins', sans-serif; /* Pastikan font sudah di-import */
        }
        .login-card {
            background-color: #fff;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h1 {
            color: #0A2647;
            margin-bottom: 0.5rem;
        }
        .login-card p {
            color: #555;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            border: none;
            background-color: #0A2647;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #1a3e6f;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: left;
        }
    </style> -->
</head>
<body>
    <div class="login-card">
        <h1>Admin Login</h1>
        <p>Selamat datang, silakan masuk.</p>

        @if($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>