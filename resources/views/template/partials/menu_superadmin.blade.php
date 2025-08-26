<ul class="menu-inner">
    <li class="menu-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('superadmin.dashboard') }}"
            class="menu-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-smart-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('superadmin.gambling_deposits.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.gambling_deposits.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-wallet"></i>
            <div data-i18n="Akun Penampung">Akun Penampung</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('superadmin.channels.index') ? 'active' : '' }}">
        <a href="{{ route('superadmin.channels.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-brand-youtube"></i>
            <div data-i18n="Channel">Channel</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('superadmin.users.index') ? 'active' : '' }}">
        <a href="{{ route('superadmin.users.index') }}"
            class="menu-link {{ request()->routeIs('superadmin.users.index') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-users"></i>
            <div data-i18n="Pengguna">Pengguna</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('superadmin.customers.index') ? 'active' : '' }}">
        <a href="{{ route('superadmin.customers.index') }}"
            class="menu-link {{ request()->routeIs('superadmin.customers.index') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-user-circle"></i>
            <div data-i18n="Pelanggan">Pelanggan</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.gambling_reports.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.gambling_reports.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-report-analytics"></i>
            <div data-i18n="Laporan">Laporan</div>
        </a>
    </li>
</ul>
