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

        @can('articles.view')
            <li class="menu-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.articles.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-news"></i>
                    <div>Berita & Opini</div>
                </a>
            </li>
        @endcan

        @can('org_regions.view')
            <li class="menu-item {{ request()->routeIs('admin.org-regions.*') ? 'active' : '' }}">
                <a href="{{ route('admin.org-regions.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-map"></i>
                    <div>Wilayah Organisasi</div>
                </a>
            </li>
        @endcan

        @can('colleges.view')
            <li class="menu-item {{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}">
                <a href="{{ route('admin.colleges.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-building-house"></i>
                    <div>Perguruan Tinggi</div>
                </a>
            </li>
        @endcan

        @canany(['users.view', 'roles.view'])
            <!-- Authorization -->
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Authorization">Authorization</span>
            </li>
            @can('users.view')
                <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div>Users</div>
                    </a>
                </li>
            @endcan
            @can('roles.view')
                <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-check-shield"></i>
                        <div>Peran</div>
                    </a>
                </li>
            @endcan
        @endcanany

        @canany(['provinces.view', 'cities.view', 'districts.view', 'villages.view'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Data Wilayah">Data Wilayah</span>
            </li>
            @can('provinces.view')
                <li class="menu-item {{ request()->routeIs('admin.provinces.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.provinces.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-map-alt"></i>
                        <div>Provinsi</div>
                    </a>
                </li>
            @endcan
            @can('cities.view')
                <li class="menu-item {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.cities.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-buildings"></i>
                        <div>Kota / kabupaten</div>
                    </a>
                </li>
            @endcan
            @can('districts.view')
                <li class="menu-item {{ request()->routeIs('admin.districts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.districts.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-git-repo-forked"></i>
                        <div>Kecamatan</div>
                    </a>
                </li>
            @endcan
            @can('villages.view')
                <li class="menu-item {{ request()->routeIs('admin.villages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.villages.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-map-pin"></i>
                        <div>Desa / kelurahan</div>
                    </a>
                </li>
            @endcan
        @endcanany
    </ul>
</aside>
