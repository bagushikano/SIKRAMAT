<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <div class="sidenav-menu-heading">Dashboards</div>
                    <a class="nav-link collapsed" href="{{route('desa-dashboard')}}">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-house-user"></i>
                        </div>
                        Dashboards
                        {{-- <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div> --}}
                    </a>
                    {{-- @permission('manajemen_banjar')
                        <div class="sidenav-menu-heading">Banjar</div>
                        <a class="nav-link collapsed" href="{{ route('desa-banjar-home') }}">
                            <div class="nav-link-icon"><i class="fas fa-university mr-1"></i></div>
                            Manajemen Banjar
                        </a>
                    @endpermission --}}

                    {{-- MASTER DATA --}}
                    <div class="sidenav-menu-heading">Master Data</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster">
                        <div class="nav-link-icon"><i class="fas fa-database mr-1"></i></div>
                        Master Data
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseMaster" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-banjar" href="{{ route('desa-banjar-home') }}">Banjar</a>
                        </nav>
                    </div>
                    {{-- MASTER DATA --}}

                    {{-- PENGGUNA --}}
                    <div class="sidenav-menu-heading">PENGGUNA</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePengguna" aria-expanded="false" aria-controls="collapsePengguna">
                        <div class="nav-link-icon"><i class="fas fa-users-cog mr-1"></i></div>
                        Pengguna
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapsePengguna" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            @if(auth()->user()->role == 'admin_desa_adat')
                            <a class="nav-link" id="nav-link-prajuru" href="{{ route('desa-prajuru-home') }}">Prajuru Desa Adat</a>
                            @endif
                            <a class="nav-link" id="nav-link-akun" href="{{ route('desa-admin-banjar-home') }}">Admin Banjar Adat</a>
                        </nav>
                    </div>
                    {{-- PENGGUNA --}}

                    {{-- DATA AKUN --}}
                    {{-- <div class="sidenav-menu-heading">Akun</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAkun" aria-expanded="false" aria-controls="collapseAkun">
                        <div class="nav-link-icon"><i class="fas fa-users-cog"></i></div>
                        Manajemen Akun Pengguna
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseAkun" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-akun" href="{{ route('desa-admin-banjar-home') }}">Akun Banjar Adat</a>
                        </nav>
                    </div> --}}
                    {{-- DATA AKUN --}}

                    {{-- LAPORAN --}}
                    <div class="sidenav-menu-heading">Laporan/Rekap</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseRekap" aria-expanded="false" aria-controls="collapseRekap">
                        <div class="nav-link-icon"><i class="fas fa-clipboard-list mr-2"></i></div>
                        Laporan
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down" id="iconRekap"></i></div>
                    </a>
                    <div class="collapse" id="collapseRekap" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-laporan-krama" href="{{ route('Pelaporan Krama Desa Adat') }}">Laporan Krama</a>
                            <a class="nav-link" id="nav-link-laporan-cacah-krama" href="{{ route('Pelaporan Cacah Krama Desa Adat') }}">Laporan Cacah Krama</a>
                            <a class="nav-link" id="nav-link-laporan-mutasi" href="{{ route('Pelaporan Mutasi') }}">Laporan Mutasi</a>
                        </nav>
                    </div>
                    {{-- LAPORAN --}}

                    {{-- <div class="sidenav-menu-heading">Krama</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseKrama" aria-expanded="false" aria-controls="collapseMasterKrama">
                        <div class="nav-link-icon"><i class="fas fa-user mr-1"></i></div>
                        Manajemen Krama
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseKrama" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-krama-mipil" href="{{ route('desa-krama-mipil-home') }}">Krama Wed/Mipil</a>
                            <a class="nav-link" id="nav-link-krama-tamiu" href="{{ route('desa-krama-tamiu-home') }}">Krama Tamiu</a>
                            <a class="nav-link" id="nav-link-tamiu" href="{{ route('desa-tamiu-home') }}">Tamiu</a>
                        </nav>
                    </div>

                    <div class="sidenav-menu-heading">Cacah Krama</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseCacahKrama" aria-expanded="false" aria-controls="collapseMasterKrama">
                        <div class="nav-link-icon"><i class="fas fa-user mr-1"></i></div>
                        Manajemen Cacah Krama
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseCacahKrama" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-cacah-krama-mipil" href="{{ route('desa-cacah-krama-mipil-home') }}">Cacah Krama Wed/Mipil</a>
                            <a class="nav-link" id="nav-link-cacah-krama-tamiu" href="{{ route('desa-cacah-krama-tamiu-home') }}">Cacah Krama Tamiu</a>
                            <a class="nav-link" id="nav-link-cacah-tamiu" href="{{ route('desa-cacah-tamiu-home') }}">Cacah Tamiu</a>
                        </nav>
                    </div>

                    @permission('manajemen_mutasi')
                        {{-- Mutasi Krama --}}
                        {{-- <div class="sidenav-menu-heading">Mutasi Krama</div>
                        <a class="nav-link collapsed" id="nav-link-kelahiran" href="{{ route('desa-kelahiran-home') }}">
                            <div class="nav-link-icon"><i class="fas fa-baby mr-2"></i></div>
                            Kelahiran
                        </a>
                        <a class="nav-link collapsed" id="nav-link-kematian" href="{{ route('desa-kematian-home') }}">
                            <div class="nav-link-icon"><i class="fas fa-book-dead mr-2"></i></div>
                            Kematian
                        </a> --}}
                        {{-- <a class="nav-link collapsed" id="nav-link-perkawinan" href="{{ route('desa-perkawinan-home') }}">
                            <div class="nav-link-icon"><i class="fas fa-heart mr-1"></i></div>
                            Perkawinan
                        </a> --}}
                        {{-- <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePerkawinan" aria-expanded="false" aria-controls="collapsePerkawinan">
                            <div class="nav-link-icon"><i class="fas fa-heart mr-1"></i></div>
                            Perkawinan
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePerkawinan" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link" id="nav-link-perkawinan-dalam-desa" href="{{ route('desa-perkawinan-dalam-desa-adat-home') }}">Perkawinan Dalam Desa Adat</a>
                                <a class="nav-link" id="nav-link-perkawinan-masuk-desa" href="{{ route('desa-perkawinan-masuk-desa-adat-home') }}">Perkawinan Masuk Desa Adat</a>
                                <a class="nav-link" id="nav-link-perkawinan-keluar-desa" href="{{ route('desa-perkawinan-keluar-desa-adat-home') }}">Perkawinan Keluar Desa Adat</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="javascript:void(0);">
                            <div class="nav-link-icon"><i class="fas fa-heart-broken mr-1"></i></div>
                            Perceraian
                        </a>
                        <a class="nav-link collapsed" href="javascript:void(0);">
                            <div class="nav-link-icon"><i class="fas fa-people-arrows mr-1"></i></div>
                            Maperas
                        </a>
                    @endpermission --}}

                    {{-- @permission('manajemen_prajuru')
                        <div class="sidenav-menu-heading">Prajuru</div>
                        <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePrajuru" aria-expanded="false" aria-controls="collapsePrajuru">
                            <div class="nav-link-icon"><i class="fas fa-users-cog mr-1"></i></div>
                            Manajemen Prajuru
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePrajuru" data-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link" id="nav-link-permission-prajuru" href="{{ route('desa-prajuru-permission-home') }}">Manajemen Hak Akses Prajuru</a>
                                <a class="nav-link" id="nav-link-akun-prajuru" href="{{ route('desa-prajuru-home') }}">Manajemen Akun Prajuru</a>
                            </nav>
                        </div>
                    @endpermission --}}

                    {{-- <div class="sidenav-menu-heading">Prajuru</div>
                    <a class="nav-link collapsed" href="{{ route('desa-prajuru-home') }}">
                        <div class="nav-link-icon"><i class="fas fa-users-cog mr-1"></i></div>
                         Manajemen Prajuru Desa dan Banjar Adat
                    </a> --}}
                </div>
            </div>
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Login sebagai:</div>
                    <div class="sidenav-footer-title">
                        @if(auth()->guard()->user()->role == 'admin_desa_adat')
                            Admin Desa Adat
                        @elseif(auth()->guard()->user()->role == 'bendesa')
                            Bendesa
                        @elseif(auth()->guard()->user()->role == 'pangliman')
                            Pangliman/Patajuh
                        @elseif(auth()->guard()->user()->role == 'penyarikan')
                            Penyarikan/Juru Tulis
                        @elseif(auth()->guard()->user()->role == 'patengen')
                            Patengen/Juru Raksa
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </div>