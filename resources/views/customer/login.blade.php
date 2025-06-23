<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Rakobuan</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Selamat Datang</h1>
                <p class="auth-subtitle">Silakan masukkan nama dan nomor meja untuk memulai.</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
             @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.login') }}" method="POST" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="table_number">Nomor Meja</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-chair"></i></span>
                        <input type="text" id="table_number" name="table_number" class="form-control" value="{{ old('table_number') }}" placeholder="Contoh: M12" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="customer_name">Nama Anda</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-user"></i></span>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Contoh: Budi" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Lihat Menu</button>
            </form>
        </div>
        <div class="auth-footer" style="position: absolute; bottom: 20px; text-align: center;">
            <a href="{{ route('admin.login') }}" style="color: #888; font-size: 14px; text-decoration: none;">
                Login sebagai Admin
            </a>
        </div>
        
    </div>
</body>
</html>