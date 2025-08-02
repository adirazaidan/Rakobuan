<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Rakobuan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/js/app.js', 'resources/css/admin.css', 'resources/js/admin.js' ])

</head>
<body>
    <div id="newOrderNotification" class="notification-bar"></div>

    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Rakobuan</h3>
            </div>
            <ul class="sidebar-menu">
                <li class="{{ request()->routeIs('admin.orders.index') ? 'active' : '' }}"><a href="{{ route('admin.orders.index') }}"><i class="fa-solid fa-receipt"></i><span>Orderan</span><span id="order-badge" class="notification-badge d-none"></span></a></li>
                <li class="{{ request()->routeIs('admin.calls.index') ? 'active' : '' }}"><a href="{{ route('admin.calls.index') }}"><i class="fa-solid fa-bell-concierge"></i><span>Panggilan</span> <span id="call-badge" class="notification-badge d-none"></span></a></li>
                <li class="{{ request()->routeIs('admin.orders.history') ? 'active' : '' }}"><a href="{{ route('admin.orders.history') }}"><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Orderan</span></a></li>
                <li class="{{ request()->routeIs('admin.calls.history') ? 'active' : '' }}"><a href="{{ route('admin.calls.history') }}"><i class="fa-solid fa-book"></i><span>Riwayat Panggilan</span></a></li>
                <li class="{{ request()->routeIs('admin.dining-tables.*') ? 'active' : '' }}"><a href="{{ route('admin.dining-tables.index') }}"><i class="fa-solid fa-chair"></i><span>Meja</span></a></li>
                <hr>
                <li class="{{ request()->routeIs('admin.outlets.*') ? 'active' : '' }}"><a href="{{ route('admin.outlets.index') }}"><i class="fa-solid fa-store"></i><span>Outlet</span></a></li>
                <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><a href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-tags"></i><span>Kategori</span></a></li>
                <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><a href="{{ route('admin.products.index') }}"><i class="fa-solid fa-utensils"></i><span>Menu</span></a></li>
                <li class="{{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}"><a href="{{ route('admin.discounts.index') }}"><i class="fa-solid fa-percent"></i><span>Diskon</span></a></li>
                <li class="{{ request()->routeIs('admin.sales.report.index') ? 'active' : '' }}"><a href="{{ route('admin.sales.report.index') }}"><i class="fa-solid fa-chart-line"></i><span>Hasil Penjualan</span></a></li>
                <hr>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar</span>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="main-header">
                <button id="menu-toggle" class="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <h2>@yield('title')</h2>
            </header>
            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    

    @stack('scripts')
</body>
</html>