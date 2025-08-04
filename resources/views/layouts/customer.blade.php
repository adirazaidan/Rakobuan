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

            <hr class="margin-1rem">

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
                <a href="#" onclick="event.preventDefault(); showLogoutConfirm();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar dari Meja</span>
                </a>
                <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="display-none">
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
    <div class="modal-overlay" id="notesModal" class="display-none">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Catatan untuk <span id="notesModalProductName"></span></h4>
                <button class="modal-close" id="closeNotesModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="notesForm">
                    <input type="hidden" id="notesModalProductId">
                    <textarea id="notesModalTextArea" class="form-control" rows="4" placeholder="Contoh: Tidak pedas, banyakin bawang, dll."></textarea>
                    <button type="submit" class="btn-primary width-100 margin-top-1rem">Simpan Catatan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Alert Dialog Modal -->
    <div class="modal-overlay" id="customAlertModal" class="display-none">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="alertModalTitle">Pesan</h4>
                <button class="modal-close" id="closeAlertModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert-icon-wrapper">
                    <i id="alertModalIcon" class="fas fa-info-circle"></i>
                </div>
                <p id="alertModalMessage" class="alert-message"></p>
                <div class="alert-actions">
                    <button type="button" class="btn-primary" id="alertModalConfirmBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal-overlay" id="cancelOrderModal" class="display-none">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Pembatalan Pesanan</h4>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Untuk membatalkan pesanan ini, silakan panggil pelayan kami.</p>
                <p>Mohon informasikan nomor pesanan Anda: <strong class="text-danger">#<span id="modalOrderNumber"></span></strong>.</p>
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