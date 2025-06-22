<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Penting untuk AJAX --}}
    <title>Rakobuan - Pemesanan Mandiri</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <style>
        /* Style spesifik layout bisa di sini atau di customer.css */
        .page-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .cart-icon a { font-size: 1.5rem; color: var(--primary-color); text-decoration: none; position: relative; }
        #cart-count { position: absolute; top: -10px; right: -15px; background: var(--accent-color); color: var(--primary-color); border-radius: 50%; width: 22px; height: 22px; display: flex; justify-content: center; align-items: center; font-size: 0.8rem; font-weight: bold; }
    </style>
    <script>
    const addToCartUrl = "{{ route('cart.add') }}";
    </script>
</head>
<body>
    <header class="page-header">
        <div class="welcome-text">
            Selamat datang <strong>{{ session('customer_name') }}</strong> di Meja <strong>{{ session('table_number') }}</strong>
        </div>
        <div class="cart-icon">
            <a href="#">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">{{ count((array) session('cart')) }}</span>
            </a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @include('customer.partials.modal-add-to-cart') {{-- Panggil partial modal --}}

    <script src="{{ asset('js/customer.js') }}"></script>
    @stack('scripts')
</body>
</html>