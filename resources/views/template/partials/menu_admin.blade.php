<ul class="menu-inner">
    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}"
            class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-smart-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.gambling_deposits.*') ? 'active' : '' }}">
        <a href="{{ route('admin.gambling_deposits.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-wallet"></i>
            <div data-i18n="Akun Penampung">Akun Penampung</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.request_gambling_deposits.*') ? 'active' : '' }}">
        <a href="{{ route('admin.request_gambling_deposits.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-wallet"></i>
            <div data-i18n="Permintaan Rekening">Permintaan Rekening</div>
        </a>
    </li>
</ul>
