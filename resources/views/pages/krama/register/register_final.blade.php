<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Neon is a bootstrap, laravel & php admin dashboard template">
    <meta name="keywords" content="admin, admin dashboard, admin panel, admin template, admin theme, bootstrap 4, laravel, php, crm, analytics, responsive, sass support, ui kits, web app, clean design">
    <meta name="author" content="Themesbox17">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>SIKRAMAT | Registrasi</title>

    <!-- Fevicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}" />

    <!-- Start CSS -->
    <link href="{{ asset('assets/auth/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/auth/css/icons.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/auth/css/style.css')}}" rel="stylesheet" type="text/css">
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase.js"></script>
    <!-- End CSS -->

</head>

<body class="xp-horizontal">

    <div class="xp-authenticate-bg"></div>
    <!-- Start XP Container -->
    <div id="xp-container" class="xp-container">

        <!-- Start Container -->
        <div class="container">

            <!-- Start XP Row -->
            <div class="row vh-100 align-items-center">
                <!-- Start XP Col -->
                <div class="col-lg-12 ">

                    <!-- Start XP Auth Box -->
                    <div class="xp-auth-box">

                        <div class="card">
                            <div class="card-body">
                                <h3 class="text-center mt-0 py-0 my-0">
                                    <a href="index.html" class="xp-web-logo"><img src="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}" height="100" alt="logo"></a>
                                </h3>
                                <div class="text-center mb-3">
                                    <h4 class="text-black">Registrasi</h4>
                                    <p class="text-muted">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</p>
                                </div> 
                                <div class="alert alert-success mx-3" role="alert">
                                    Registrasi berhasil dilakukan.
                                </div>
                                    <a class="btn btn-primary btn-rounded btn-lg btn-block mt-2" href="{{ route('login-form') }}">Menuju Halaman Login</a>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End XP Auth Box -->

                </div>
                <!-- End XP Col -->
            </div>
            <!-- End XP Row -->
        </div>
        <!-- End Container -->
    </div>
    <!-- End XP Container -->

    <!-- Start JS -->        
    <script src="{{ asset('assets/auth/js/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/auth/js/popper.min.js')}}"></script>
    <script src="{{ asset('assets/auth/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('assets/auth/js/modernizr.min.js')}}"></script>
    <script src="{{ asset('assets/auth/js/detect.js')}}"></script>
    <script src="{{ asset('assets/auth/js/jquery.slimscroll.js')}}"></script>
    <script src="{{ asset('assets/auth/js/horizontal-menu.js')}}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.6.0-2021111174650/firebase-app.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.6.0-2021111174650/firebase-database.min.js"></script> --}}

    <!-- Main JS -->
    <script src="{{ asset('assets/auth/js/main.js')}}"></script>
    <!-- End JS -->

    <script>
        function showPassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
                $("#lihat_password_icon").toggleClass('fas fa-eye').toggleClass('fas fa-eye-slash');
            } else {
                x.type = "password";
                $("#lihat_password_icon").toggleClass('fas fa-eye-slash').toggleClass('fas fa-eye');
            }
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
    </script>
</body>
</html>
