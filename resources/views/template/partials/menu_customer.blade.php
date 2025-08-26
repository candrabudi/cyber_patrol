<ul class="menu-inner">
    <li class="menu-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <a href="{{ route('customer.dashboard') }}"
            class="menu-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="menu-icon icon-base ti tabler-smart-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('customer.gambling_deposits.*') ? 'active' : '' }}">
        <a href="{{ route('customer.gambling_deposits.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-wallet"></i>
            <div data-i18n="Akun Penampung">Akun Penampung</div>
        </a>
    </li>
    {{-- <li class="menu-item {{ request()->routeIs('customer.gambling_reports.*') ? 'active' : '' }}">
        <a href="{{ route('customer.gambling_reports.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-report-analytics"></i>
            <div data-i18n="Report">Report</div>
        </a>
    </li> --}}
</ul>
