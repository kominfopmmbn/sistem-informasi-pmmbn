<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="{{ asset('assets/img/logo/pmmbn.png') }}" alt="Logo" width="30">
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">PMMBN</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base bx bx-chevron-left"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-item {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
            <a href="{{ route('admin.articles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div>Berita & Opini</div>
            </a>
        </li>

    </ul>
</aside>
