<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Rakobuan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-table"></i></span>
                            <select name="dining_table_id" id="dining_table_id" class="form-control" required>
                                @include('customer.partials._table_options', ['tablesByLocation' => $tablesByLocation])
                            </select>
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
                    <div class="auth-link-container">
                        <a href="{{ route('admin.login') }}" class="auth-link">
                            Login sebagai Admin
                        </a>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-info-circle"></i>
                        <p class="mb-0">Mohon Maaf, Kami Sudah Tidak Menerima Orderan</p>
                        <p class="mb-0 small">Semua meja sedang penuh atau tidak tersedia. Silakan hubungi staf kami untuk informasi lebih lanjut.</p>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#dining_table_id').length) {
                $('#dining_table_id').select2({
                    placeholder: "-- Pilih atau Cari Meja --",
                    allowClear: true,
                    dropdownParent: $('body')
                });
            }
        });
    </script>
</body>
</html>
