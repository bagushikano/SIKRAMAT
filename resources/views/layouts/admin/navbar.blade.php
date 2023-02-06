<nav class="topnav navbar navbar-expand shadow navbar-light" style="background-color: #E34234" id="sidenavAccordion">
    <div class="navbar-brand bg-white">
        <img style="height: 3.0rem;" src="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}">
        <a class="navbar-brand ml-n3" href="{{route('admin-dashboard')}}">SIKRAMAT</a>
    </div>
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#">
        <i class="fa-solid fa-bars text-light"></i>
    </button>
    <ul class="navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown no-caret mr-2 dropdown-user">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-circle-user text-light" style="font-size: 2.2rem"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <i class="fa-solid fa-address-card text-primary mr-2" style="font-size: 2rem"></i>
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">Super Admin</div>
                        <div class="dropdown-user-details-email">{{ auth()->user()->email }}</div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                {{-- <a class="dropdown-item" href="#!">
                    <div class="dropdown-item-icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    Akun
                </a> --}}
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