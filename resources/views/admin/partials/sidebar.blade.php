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