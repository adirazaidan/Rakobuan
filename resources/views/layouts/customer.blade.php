<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rakobuan - Pemesanan Mandiri')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">

    {{-- Ini akan kita gunakan untuk URL di JS --}}
    <script>
        const appConfig = {
            routes: {
                cartAdd: "{{ route('cart.add') }}",
                callWaiterStore: "{{ route('call.waiter.store') }}" // <-- TAMBAHKAN INI
            }
        };
    </script>
</head>
<body>
    <div class="customer-app-container">
    <aside class="sidebar-customer">
        <div class="sidebar-header">
            <h3>Rakobuan</h3>
            <span>{{ $currentOutlet->name ?? 'Pilih Outlet' }}</span>
        </div>
        <ul class="sidebar-menu">
            @foreach ($outletsForSidebar as $outlet)
                <li class="{{ isset($currentOutlet) && $currentOutlet->id == $outlet->id ? 'active' : '' }}">
                    <a href="{{ route('customer.menu.index', $outlet) }}">
                        <img src="{{ $outlet->image ? Storage::url('outlets/' . $outlet->image) : 'https://via.placeholder.com/40' }}" alt="{{ $outlet->name }}" class="sidebar-outlet-img">
                        <span>{{ $outlet->name }}</span>
                    </a>
                </li>
            @endforeach

            <hr style="margin: 1rem 1.5rem;">

            <li class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">
                <a href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Keranjang</span>
                    <span class="badge" id="sidebar-cart-count">{{ count((array) session('cart')) }}</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar dari Meja</span>
                </a>
                <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </aside>

        {{-- KONTEN UTAMA --}}
        <div class="content-wrapper">
            <header class="header-customer">
                <button id="customerMenuToggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-info">
                    Selamat datang <strong>{{ session('customer_name') }}</strong> di Meja <strong>{{ session('table_number') }}</strong>
                </div>
            </header>
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- MODAL AKAN KITA PINDAHKAN KE SINI AGAR LEBIH RAPI --}}
    @include('customer.partials.modal-add-to-cart')
    @include('customer.partials.modal-call-waiter')

    <script src="{{ asset('js/customer.js') }}"></script>
    @stack('scripts')

    <div id="imageLightbox" class="lightbox-overlay">
        <span class="lightbox-close">&times;</span>
        <img class="lightbox-content" id="lightboxImage">
    </div>
</body>
</html>