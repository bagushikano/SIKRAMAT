<nav class="topnav navbar navbar-expand shadow navbar-light" style="background-color: #E34234" id="sidenavAccordion">
    <div class="navbar-brand bg-white">
        <img style="height: 3.0rem;" src="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}">
        <a class="navbar-brand ml-n2" href="{{route('desa-dashboard')}}">SIKRAMAT</a>
    </div>
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#">
        <i class="fa-solid fa-bars text-light"></i>
    </button>
    <div class="d-none d-md-inline font-weight-500">
        <div class="nav-link-icon text-white">
            Desa Adat 
            <div class="d-none d-md-inline font-weight-500 text-white">
                {{ Session::get('desa_adat_nama') }}
            </div>
        </div>
    </div>
    <ul class="navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown no-caret mr-2 dropdown-user">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-circle-user text-light" style="font-size: 2.2rem"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <i class="fa-solid fa-address-card text-primary mr-2" style="font-size: 2rem"></i>
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">
                            @if(auth()->guard()->user()->role == 'bendesa')
                                {{ Session::get('nama_user') }}                            
                            @elseif(auth()->guard()->user()->role == 'pangliman')
                                {{ Session::get('nama_user') }}                            
                            @elseif(auth()->guard()->user()->role == 'penyarikan')
                                {{ Session::get('nama_user') }}                            
                            @elseif(auth()->guard()->user()->role == 'patengen')
                                {{ Session::get('nama_user') }}                            
                            @else
                                Desa Adat {{ Session::get('desa_adat_nama') }}
                            @endif                            
                        </div>
                        <div class="dropdown-user-details-email">
                            @if(auth()->guard()->user()->role == 'admin_desa_adat')
                                Admin Desa Adat
                            @elseif(auth()->guard()->user()->role == 'bendesa')
                                Bendesa Adat
                            @elseif(auth()->guard()->user()->role == 'pangliman')
                                Pangliman/Patajuh
                            @elseif(auth()->guard()->user()->role == 'penyarikan')
                                Penyarikan/Juru Tulis
                            @elseif(auth()->guard()->user()->role == 'patengen')
                                Patengen/Juru Raksa
                            @endif
                        </div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('desa-profile-prajuru') }}">
                    <div class="dropdown-item-icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    Profil
                </a>
                @if(auth()->guard()->user()->role != 'admin_desa_adat')
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