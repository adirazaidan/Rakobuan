@extends('layouts.customer')

@section('title', 'Selamat Datang di Rakobuan')

@section('content')
<div class="login-page">
    <header class="page-header-login">
        <h1>Selamat Datang</h1>
        <i class="fas fa-user-circle"></i>
    </header>

    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Pesan Menu Mandiri</h2>
            <p class="login-subtitle">Silakan masukkan nama dan nomor meja Anda untuk memulai.</p>

            {{-- Menampilkan pesan error dari middleware atau validasi --}}
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

            <form action="{{ route('customer.login') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="table_number">Nomor Meja</label>
                    <input type="text" id="table_number" name="table_number" class="form-control" value="{{ old('table_number') }}" placeholder="Contoh: M12" required>
                </div>
                <div class="form-group">
                    <label for="customer_name">Nama Anda</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Contoh: Budi" required>
                </div>
                <button type="submit" class="btn-submit">Lihat Menu</button>
            </form>
        </div>
    </div>
</div>
@endsection