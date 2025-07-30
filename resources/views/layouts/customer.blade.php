<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rakobuan - Pemesanan Mandiri')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/customer.css', 'resources/js/customer-app.js'])

    <script>
        const appConfig = {
            sessionId: "{{ session()->getId() }}",
            loginTimestamp: {{ session('login_timestamp') ?? 'null' }},
            sessionLifetime: {{ config('session.lifetime') * 60 }},
            tableId: {{ session('dining_table_id') ?? 'null' }},

            routes: {
                cartAdd: "{{ route('cart.add') }}",
                callWaiterStore: "{{ route('call.waiter.store') }}",
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

            <li class="{{ request()->routeIs('order.status') ? 'active' : '' }}">
                <a href="{{ route('order.status') }}">
                    <i class="fas fa-receipt"></i>
                    <span>Aktivitas</span>
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
    @include('customer.partials.modal-call-waiter')
    <div class="modal-overlay" id="notesModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Catatan untuk <span id="notesModalProductName"></span></h4>
                <button class="modal-close" id="closeNotesModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="notesForm">
                    <input type="hidden" id="notesModalProductId">
                    <textarea id="notesModalTextArea" class="form-control" rows="4" placeholder="Contoh: Tidak pedas, banyakin bawang, dll."></textarea>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Simpan Catatan</button>
                </form>
            </div>
        </div>
    </div>

    <div id="mini-cart-bar" 
        class="mini-cart-bar">
        <a href="{{ route('cart.index') }}" class="mini-cart-link">
            <div class="mini-cart-info">
                <i class="fas fa-shopping-basket"></i>
                <span id="mini-cart-item-count">0 Item</span>
            </div>
            <div class="mini-cart-total">
                <span id="mini-cart-total-price">Rp 0</span>
                <span>Lihat Keranjang <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
    </div>

    @stack('scripts')

    <div id="imageLightbox" class="lightbox-overlay">
        <span class="lightbox-close">&times;</span>
        <img class="lightbox-content" id="lightboxImage">
    </div>
</body>
</html>