<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ url('/') }}">SajiPOS</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('/') }}">SP</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>

            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
            </li>

            <li class="menu-header">Manajemen</li>

            @if(auth()->user()->roles == 'admin' || auth()->user()->roles == 'staff')
            <li class="{{ Request::is('users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}"><i class="fas fa-users"></i><span>Users</span></a>
            </li>
            @endif

            <li class="{{ Request::is('categories*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('categories.index') }}"><i class="fas fa-tags"></i><span>Kategori</span></a>
            </li>

            <li class="{{ Request::is('products*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-box"></i><span>Produk</span></a>
            </li>

            <li class="{{ Request::is('campaigns*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('campaigns.index') }}"><i class="fas fa-tag"></i><span>Campaign</span></a>
            </li>

            <li class="{{ Request::is('discounts*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('discounts.index') }}"><i class="fas fa-percent"></i><span>Diskon</span></a>
            </li>

            <li class="menu-header">Transaksi</li>

            <li class="{{ Request::is('orders*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('orders.index') }}"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
            </li>

            <li class="{{ Request::is('reports*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
            </li>

            @if(auth()->user()->roles == 'admin')
            <li class="menu-header">Pengaturan</li>
            <li class="{{ Request::is('settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-cog"></i><span>Toko</span></a>
            </li>
            @endif

        </ul>
    </aside>
</div>
