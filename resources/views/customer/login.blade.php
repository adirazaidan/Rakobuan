<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Rakobuan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/customer.css', 'resources/js/customer-app.js'])

    <script>
        const appConfig = {
            routes: {
                getAvailableTables: "{{ route('customer.get-tables') }}"
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
                            @include('customer.partials._table_options', ['tablesByLocation' => $tablesByLocation])
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
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dining_table_id').select2({
                placeholder: "-- Pilih atau Cari Meja --",
                allowClear: true,
                dropdownParent: $('#dining_table_id').parent()
            });
        });
    </script>
</body>
</html>
