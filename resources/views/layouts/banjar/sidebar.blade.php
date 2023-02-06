<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <div class="sidenav-menu-heading">Dashboard</div>
                    <a class="nav-link collapsed" href="{{route('banjar-dashboard')}}">
                        <div class="nav-link-icon"><i class="fas fa-house-user"></i></div>
                        Dashboard
                    </a>

                    {{-- MASTER DATA --}}
                    <div class="sidenav-menu-heading">Master Data</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster">
                        <div class="nav-link-icon"><i class="fas fa-database mr-1"></i></div>
                        Master Data
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseMaster" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-tempekan" href="{{ route('banjar-tempekan-home') }}">Tempekan</a>
                        </nav>
                    </div>
                    {{-- MASTER DATA --}}

                    {{-- KRAMA --}}
                    <div class="sidenav-menu-heading">Krama</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseKrama" aria-expanded="false" aria-controls="collapseMasterKrama">
                        <div class="nav-link-icon"><i class="fas fa-user mr-1"></i></div>
                        Manajemen Krama
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseKrama" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-krama-mipil" href="{{ route('banjar-krama-mipil-home') }}">Krama Wed/Mipil</a>
                            <a class="nav-link" id="nav-link-krama-tamiu" href="{{ route('banjar-krama-tamiu-home') }}">Krama Tamiu</a>
                            <a class="nav-link" id="nav-link-tamiu" href="{{ route('banjar-tamiu-home') }}">Tamiu</a>
                        </nav>
                    </div>
                    {{-- KRAMA --}}
                    
                     {{-- CACAH KRAMA --}}
                     <div class="sidenav-menu-heading">Cacah Krama</div>
                     <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseCacahKrama" aria-expanded="false" aria-controls="collapseMasterKrama">
                         <div class="nav-link-icon"><i class="fas fa-user mr-1"></i></div>
                         Manajemen Cacah Krama
                         <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                     </a>
                     <div class="collapse" id="collapseCacahKrama" data-parent="#accordionSidenav">
                         <nav class="sidenav-menu-nested nav">
                             <a class="nav-link" id="nav-link-cacah-krama-mipil" href="{{ route('banjar-cacah-krama-mipil-home') }}">Cacah Krama Wed/Mipil</a>
                             <a class="nav-link" id="nav-link-cacah-krama-tamiu" href="{{ route('banjar-cacah-krama-tamiu-home') }}">Cacah Krama Tamiu</a>
                             <a class="nav-link" id="nav-link-cacah-tamiu" href="{{ route('banjar-cacah-tamiu-home') }}">Cacah Tamiu</a>
                         </nav>
                     </div>
                     {{-- CACAH KRAMA --}}

                    {{-- MUTASI --}}
                    <div class="sidenav-menu-heading">Mutasi</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePeristiwa" aria-expanded="false" aria-controls="collapsePeristiwa">
                        <div class="nav-link-icon"><i class="fas fa-users-cog"></i></div>
                        Manajemen Mutasi
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down" id="iconPeristiwa"></i></div>
                    </a>
                    <div class="collapse" id="collapsePeristiwa" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-kelahiran" href="{{ route('banjar-kelahiran-home') }}">Kelahiran</a>
                            <a class="nav-link" id="nav-link-kematian" href="{{ route('banjar-kematian-home') }}">Kematian</a>
                            <a class="nav-link" id="nav-link-perkawinan" href="{{ route('banjar-perkawinan-home') }}">Perkawinan</a>
                            <a class="nav-link" id="nav-link-perceraian" href="{{ route('banjar-perceraian-home') }}">Perceraian</a>
                            <a class="nav-link" id="nav-link-maperas" href="{{ route('banjar-maperas-home') }}">Maperas</a>
                        </nav>
                    </div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAjuan" aria-expanded="false" aria-controls="collapseAjuan">
                        <div class="nav-link-icon"><i class="fas fa-inbox"></i></div>
                        Ajuan Mutasi
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down" id="iconAjuan"></i></div>
                    </a>
                    <div class="collapse" id="collapseAjuan" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-ajuan-kelahiran" href="{{ route('banjar-ajuan-kelahiran-home') }}">Ajuan Data Kelahiran</a>
                            <a class="nav-link" id="nav-link-ajuan-kematian" href="{{ route('banjar-ajuan-kematian-home') }}">Ajuan Data Kematian</a>
                        </nav>
                    </div>
                    {{-- MUTASI --}}

                    {{-- PRAJURU --}}
                    @if(auth()->user()->role == 'admin_banjar_adat')
                        <div class="sidenav-menu-heading">Prajuru Banjar Adat</div>
                        <a class="nav-link collapsed" href="{{route('banjar-prajuru-home')}}">
                            <div class="nav-link-icon"><i class="fas fa-users-cog"></i></div>
                            Manajemen Prajuru
                        </a>
                    @endif
                    {{-- PRAJURU --}}

                    {{-- LAPORAN --}}
                    <div class="sidenav-menu-heading">Laporan/Rekap</div>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseRekap" aria-expanded="false" aria-controls="collapseRekap">
                        <div class="nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        Laporan
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down" id="iconRekap"></i></div>
                    </a>
                    <div class="collapse" id="collapseRekap" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-laporan-krama" href="{{ route('banjar-laporan-krama-home') }}">Laporan Krama</a>
                            <a class="nav-link" id="nav-link-laporan-cacah" href="{{ route('banjar-laporan-cacah-home') }}">Laporan Cacah Krama</a>
                            <a class="nav-link" id="nav-link-laporan-mutasi" href="{{ route('banjar-laporan-mutasi-home') }}">Laporan Mutasi</a>
                        </nav>
                    </div>
                    {{-- LAPORAN --}}
                </div>
            </div>
            <div class="sidenav-footer mt-3">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Login sebagai:</div>
                    <div class="sidenav-footer-title">
                        @if(auth()->guard()->user()->role == 'admin_banjar_adat')
                            Admin Banjar Adat
                        @elseif(auth()->guard()->user()->role == 'kelihan_adat')
                            Kelihan Adat
                        @elseif(auth()->guard()->user()->role == 'pangliman_banjar')
                            Pangliman/Patajuh
                        @elseif(auth()->guard()->user()->role == 'penyarikan_banjar')
                            Penyarikan/Juru Tulis
                        @elseif(auth()->guard()->user()->role == 'patengen_banjar')
                            Patengen/Juru Raksa
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </div>