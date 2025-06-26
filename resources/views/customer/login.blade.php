<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Rakobuan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    @vite(['resources/js/app.js'])

    <script>
        const appConfig = {
            routes: {
                cartAdd: "{{ route('cart.add') }}",
                callWaiterStore: "{{ route('call.waiter.store') }}",
                getAvailableTables: "{{ route('customer.get-tables') }}" // Tambahkan rute ini
            }
        };
    </script>
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Selamat Datang</h1>
                <p class="auth-subtitle">Silakan masukkan nama dan nomor meja Anda untuk memulai.</p>
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

                @if($tablesByLocation->isNotEmpty())

                <div class="form-group">
                    <label for="dining_table_id">Pilih Meja</label>
                    <select name="dining_table_id" id="dining_table_id" class="form-control" required>
                        <option value="" disabled selected>-- Meja yang Tersedia --</option>
                        @foreach ($tablesByLocation as $location => $tables)
                            <optgroup label="{{ $location ?: 'Lainnya' }}">
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="customer_name">Nama Anda</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Contoh: Budi" required>
                </div>
                <button type="submit" class="btn-submit">Lihat Menu</button>
                    @else
                        <div class="no-tables-available">
                            <i class="fas fa-info-circle fa-2x"></i>
                            <p class="message-title">Mohon Maaf, Kami Sudah Tidak Menerima Orderan</p>
                            <p class="message-subtitle">Semua meja sedang penuh atau tidak tersedia. Silakan hubungi staf kami untuk informasi lebih lanjut.</p>
                        </div>
                    @endif
            </form>
        </div>
        <div class="auth-footer" style="position: absolute; bottom: 20px; text-align: center;">
            <a href="{{ route('admin.login') }}" style="color: #888; font-size: 14px; text-decoration: none;">
                Login sebagai Admin
            </a>
        </div>
    </div>

    <script src="{{ asset('js/customer.js') }}"></script>
</body>
</html>