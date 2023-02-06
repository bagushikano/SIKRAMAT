<nav class="topnav navbar navbar-expand shadow navbar-light" style="background-color: #E34234" id="sidenavAccordion">
    <div class="navbar-brand bg-white">
        <img style="height: 3.0rem;" src="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}">
        <a class="navbar-brand ml-n3 my-auto" href="{{ route('Dashboard Krama') }}">SIKRAMAT</a>
    </div>
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#">
        <i class="fa-solid fa-bars text-light"></i>
    </button>
    <div class="d-none d-md-inline font-weight-500">
        <div class="nav-link-icon">
            <div class="d-none d-md-inline font-weight-500 text-white">
                {{ Session::get('nama_user') }}
            </div>
        </div>
    </div>
    <ul class="navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown no-caret mr-3 dropdown-notifications">
            <button type="button" class="btn btn-icon btn-transparent-dark dropdown-toggle" id="banjar-notifikasi-jumlah" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell text-light"></i>                
                <span class="badge badge-yellow navbar-badge small mt-n2" style="font-size: 0.6rem;" id="jumlah_notif_badge">{{ auth()->user()->count_notifikasi_krama()  ?? '0' }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownAlerts" style="width:350px;">
                <h6 class="dropdown-header dropdown-notifications-header bg-red-pastel">
                    <i class="fas fa-bell mr-2"></i>
                    Notifikasi
                </h6>
                <div id="loading_notification" style="display:none;">
                    <div class="d-flex justify-content-center my-5">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="body_notification" style="display:none;">                        
                </div>
                <div id="empty_notification" style="display:none;">
                    <a class="dropdown-item dropdown-notifications-footer" href="javascript:void(0);"><i class="fas fa-check mr-1"></i> Tidak terdapat notifikasi</a>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown no-caret mr-2 dropdown-user">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-circle-user text-light" style="font-size: 2.2rem"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <i class="fa-solid fa-address-card text-primary mr-2" style="font-size: 2rem"></i>
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">
                            {{ Session::get('nama_user') }}
                        </div>
                        <div class="dropdown-user-details-email">
                            Krama
                        </div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('Profile Krama') }}">
                    <div class="dropdown-item-icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    Profil
                </a>
                @if(auth()->guard()->user()->role != 'krama')
                    <a class="dropdown-item" href="javascript:void(0);" onclick="ganti_hak_akses_modal()">
                        <div class="dropdown-item-icon">
                            <i class="fa-solid fa-repeat"></i>
                        </div>
                        Ganti Hak Akses
                    </a>
                @endif
                <a class="dropdown-item text-danger" type="button" href="#" onclick="logout()">
                    <div class="dropdown-item-icon">
                        <i class="fa-solid fa-power-off text-danger"></i>
                    </div>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>