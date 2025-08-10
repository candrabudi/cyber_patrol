<ul class="menu-inner">
    <li class="menu-item {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}">
        <a href="{{ route('reviewer.dashboard') }}"
            class="menu-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-smart-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('reviewer.gambling_deposits.*') ? 'active' : '' }}">
        <a href="{{ route('reviewer.gambling_deposits.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-wallet"></i>
            <div data-i18n="Akun Penampung">Akun Penampung</div>
        </a>
    </li>
</ul>
