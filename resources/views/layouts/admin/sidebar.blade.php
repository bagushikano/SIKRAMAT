<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    {{-- DASHBOARD --}}
                    <div class="sidenav-menu-heading">Dashboards</div>
                    <a class="nav-link collapsed" href="{{route('admin-dashboard')}}">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-house-user"></i>
                        </div>
                        Dashboards
                        {{-- <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div> --}}
                    </a>

                    {{-- MASTER DATA --}}
                    <div class="sidenav-menu-heading">Master Data</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseMasterData" aria-expanded="false" aria-controls="collapseMasterData">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        Master Data
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseMasterData" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('admin-pekerjaan-home') }}">Pekerjaan</a>
                            <a class="nav-link" href="{{ route('admin-pendidikan-home') }}">Jenjang Pendidikan</a>
                        </nav>
                    </div>

                    {{-- DATA AKUN --}}
                    <div class="sidenav-menu-heading">Akun</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAkun" aria-expanded="false" aria-controls="collapseAkun">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        Manajemen Akun Pengguna
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseAkun" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="sidebarAkunSuperAdmin" href="{{route('admin-admin-desa-home')}}">Akun Desa Adat</a>
                            {{-- <a class="nav-link" href="#">Akun Majelis Desa Adat</a> --}}
                        </nav>
                    </div>
                    {{-- DATA AKUN --}}

                    {{-- LAPORAN --}}
                    <div class="sidenav-menu-heading">Laporan/Rekap</div>
                    <a class="nav-link collapsed" id="nav-link-laporan" href="{{route('admin-laporan-home')}}">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        Laporan
                        {{-- <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div> --}}
                    </a>
                    {{-- LAPORAN --}}

                    
                    {{-- <div class="sidenav-menu-heading">Akun</div>
                    <a class="nav-link collapsed" href="{{route('admin-admin-desa-home')}}">
                        <div class="nav-link-icon"><i data-feather="user"></i></div>
                        Manajemen Akun Desa Adat
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a> --}}
                </div>
            </div>
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Login sebagai:</div>
                    <div class="sidenav-footer-title">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</div>
                </div>
            </div>
        </nav>
    </div>