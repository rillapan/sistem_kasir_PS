<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fab fa-playstation"></i>
        </div>
        <div class="sidebar-brand-text mx-3">RentalPS</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if (auth()->user()->status === 'admin' || auth()->user()->status === 'owner')
        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

    @if (auth()->user()->status === 'admin')
        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Utama
        </div>

        <li class="nav-item {{ $active === 'play' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('playstation.index') }}">
                <i class="fas fa-gamepad"></i>
                <span>Jenis Playstation</span></a>
        </li>
    @endif

    @if (auth()->user()->status === 'user' || auth()->user()->status === 'admin')
        <li class="nav-item {{ $active === 'device' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('device.index') }}">
                <i class="fas fa-tv"></i>
                <span>Data Perangkat</span></a>
        </li>

                <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFnb"
                aria-expanded="true" aria-controls="collapseFnb">
                <i class="fas fa-utensils"></i>
                <span>Kelola FnB</span>
            </a>
            <div id="collapseFnb" class="collapse" aria-labelledby="headingFnb" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">FnB Management:</h6>
                    <a class="collapse-item {{ $active === 'fnb' ? 'active' : '' }}" href="{{ route('fnb.index') }}">Kelola Barang</a>
                    <a class="collapse-item {{ $active === 'price-group' ? 'active' : '' }}" href="{{ route('price-group.index') }}">Kelompok Harga</a>
                    <a class="collapse-item {{ $active === 'stock' ? 'active' : '' }}" href="{{ route('stock.index') }}">Manajemen Stok</a>
                    <a class="collapse-item {{ $active === 'fnb-laporan' ? 'active' : '' }}" href="{{ route('fnb.laporan') }}">Laporan Penjualan</a>
                </div>
            </div>
        </li>

        <li class="nav-item {{ $active === 'transaction' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('transaction.index') }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Transaksi</span></a>
        </li>

        <li class="nav-item {{ $active === 'custom-package' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('custom-package.index') }}">
                <i class="fas fa-box"></i>
                <span>Custom Paket</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExpense"
                aria-expanded="true" aria-controls="collapseExpense">
                <i class="fas fa-money-bill-wave"></i>
                <span>Pengeluaran</span>
            </a>
            <div id="collapseExpense" class="collapse" aria-labelledby="headingExpense" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Pengeluaran Management:</h6>
                    <a class="collapse-item {{ $active === 'expense-category' ? 'active' : '' }}" href="{{ route('expense-category.index') }}">Kategori Pengeluaran</a>
                    <a class="collapse-item {{ $active === 'expense' ? 'active' : '' }}" href="{{ route('expense.index') }}">Daftar Pengeluaran</a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Kelola FnB Collapse Menu -->


        <!-- Divider -->
        <hr class="sidebar-divider">
    @endif

    @if (auth()->user()->status === 'admin' || auth()->user()->status === 'owner')
        <!-- Heading -->
        <div class="sidebar-heading">
            Laporan
        </div>

        <!-- Nav Item - Pages Collapse Menu -->


        <!-- Nav Item - Charts -->
        <li class="nav-item {{ $active === 'report' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('report') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Laporan</span></a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <hr class="sidebar-divider">

<!-- Panduan -->
<div class="sidebar-heading">Panduan</div>

<li class="nav-item {{ $active === 'panduan' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('panduan.index') }}">
        <i class="fas fa-book"></i>
        <span>Panduan</span>
    </a>
</li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->