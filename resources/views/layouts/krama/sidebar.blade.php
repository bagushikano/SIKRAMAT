<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <div class="sidenav-menu-heading">Personal</div>
                    <a class="nav-link collapsed" id="link-profile" href="{{ route('Profile Krama') }}">
                        <div class="nav-link-icon"><i class="fas fa-user"></i></div>
                        Profile
                    </a>
                    <div class="sidenav-menu-heading">Menu</div>
                    <a class="nav-link collapsed" id="link-dashboard" href="{{ route('Dashboard Krama') }}">
                        <div class="nav-link-icon">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                        Dashboard
                    </a>
                    <a class="nav-link collapsed" id="link-anggota-keluarga" href="{{route('Keluarga Krama')}}">
                        <div class="nav-link-icon"><i class="fas fa-users"></i></div>
                        Anggota Keluarga
                    </a>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAjuan" aria-expanded="false" aria-controls="collapseAjuan">
                        <div class="nav-link-icon"><i class="fas fa-inbox"></i></div>
                        Ajuan Mutasi
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down" id="iconAjuan"></i></div>
                    </a>
                    <div class="collapse" id="collapseAjuan" data-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav">
                            <a class="nav-link" id="nav-link-ajuan-kelahiran" href="{{ route('Kelahiran Home') }}">Ajuan Data Kelahiran</a>
                            <a class="nav-link" id="nav-link-ajuan-kematian" href="{{ route('Kematian Home') }}">Ajuan Data Kematian</a>
                        </nav>
                    </div>

                </div>
            </div>
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Login sebagai:</div>
                    <div class="sidenav-footer-title">
                        Krama
                    </div>
                </div>
            </div>
        </nav>
    </div>