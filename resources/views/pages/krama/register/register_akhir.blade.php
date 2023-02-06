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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
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
                                    Selamat datang <span class="font-weight-bold">{{ $penduduk->nama }}</span>, silahkan masukkan email dan password untuk melanjutkan registrasi
                                </div>

                                @if (session()->has('error'))
                                    <div class="row mx-3 mt-3">
                                        <div class="col-sm-12 alert alert-danger alert-dismissible fade show" role="alert">
                                            {{session()->get('error')}}
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="p-3">
                                    <form method="POST" id="form-register-awal" action="{{ route('Register') }}">
                                        @csrf       
                                        <input type="penduduk_id" class="form-control" id="penduduk_id" name="penduduk_id" value="{{ $penduduk->id }}" required hidden>                                
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($errors->has('email'))
                                            <small class="text-danger">
                                                {{ $errors->first('email') }}
                                            </small>
                                        @endif
                                        <small class="text-danger" id="email-validate" style="display:none;">
                                            Email wajib diisi
                                        </small>
                                        
                                        <div class="input-group mt-3">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                            <input id="signup-token" name="_token" type="hidden" value="{{csrf_token()}}">
                                            <div class="input-group-append" onclick="showPassword()">
                                                <div class="input-group-text">
                                                    <span id="lihat_password_icon" class="fas fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($errors->has('password'))
                                            <small class="text-danger">
                                                {{ $errors->first('password') }}
                                            </small>
                                        @endif
                                        <small class="text-danger" id="password-validate" style="display:none;">
                                            Password wajib diisi
                                        </small>

                                        <div class="input-group mt-3">
                                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" placeholder="Konfirmasi Password" required>
                                            <div class="input-group-append" onclick="showConfirmPassword()">
                                                <div class="input-group-text">
                                                    <span id="lihat_confirm_pass_icon" class="fas fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($errors->has('confirm_pass'))
                                            <small class="text-danger">
                                                {{ $errors->first('confirm_pass') }}
                                            </small>
                                        @endif
                                        <small class="text-danger" id="confirm_pass-validate" style="display:none;">
                                            Konfirmasi Password wajib diisi
                                        </small>
                                    </form>
                                    <button type="button mt-3" class="btn btn-primary btn-rounded btn-lg btn-block mt-5" onclick="register()">Register<i class="fas fa-arrow-right ml-2"></i></button>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/admin/js/scripts.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
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

    function showConfirmPassword() {
        var x = document.getElementById("confirm_pass");
        if (x.type === "password") {
            x.type = "text";
            $("#lihat_confirm_pass_icon").toggleClass('fas fa-eye').toggleClass('fas fa-eye-slash');
        } else {
            x.type = "password";
            $("#lihat_confirm_pass_icon").toggleClass('fas fa-eye-slash').toggleClass('fas fa-eye');
        }
    }

    function register(){
        if($('#email').val() != ''){
            if($('#password').val() != ''){
                if($('#password').val().length >= 8){
                    if($('#confirm_pass').val() == $('#password').val()){
                        $('#form-register-awal').submit();
                    }else{
                        $('#confirm_pass-validate').text('Konfirmasi password tidak cocok');
                        $('#confirm_pass-validate').show();
                    }
                }else{
                    $('#password-validate').text('Password minimal terdiri dari 8 karakter');
                    $('#password-validate').show();
                }
            }else{
                $('#password-validate').text('Password wajib diisi');
                $('#password-validate').show();
            }
        }else{
            $('#email-validate').show();
        }
    }

    $(document).ready(function() {
        $('#email').on('input', function(event){
            if($(this).val().length > 3){
                $('#email-validate').fadeOut();
            }
        });

        $('#password').on('input', function(event){
            if($(this).val().length >= 8){
                $('#password-validate').fadeOut();
            }
        });

        $('#confirm_pass').on('input', function(event){
            if($(this).val() == $('#password').val()){
                $('#password-validate').fadeOut();
            }
        });
    });
    </script>
</body>
</html>