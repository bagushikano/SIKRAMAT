{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Neon is a bootstrap, laravel & php admin dashboard template">
    <meta name="keywords" content="admin, admin dashboard, admin panel, admin template, admin theme, bootstrap 4, laravel, php, crm, analytics, responsive, sass support, ui kits, web app, clean design">
    <meta name="author" content="Themesbox17">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>SIKRAMAT | Reset Password</title>

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
                                    <h4 class="text-black">Reset Password</h4>
                                    <p class="text-muted">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</p>
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
                                @if ($errors->any())
                                    @if($errors->has('captcha'))
                                        <div class="row mx-3 mt-3">
                                            <div class="col-sm-12 alert alert-danger alert-dismissible fade show" role="alert">
                                                {{$errors->first('captcha')}}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @if (session('status'))
                                    <div class="alert alert-success mx-3" role="alert">
                                        Email reset password telah dikirimkan!
                                    </div>
                                @endif
                                <div class="p-3">
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf                                       
                                        <div class="input-group mb-4">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                      <button type="submit" class="btn btn-primary btn-rounded btn-lg btn-block mt-2">Kirim Email Reset Password</button>
                                      <a class="btn btn-danger btn-rounded btn-lg btn-block mt-2" href="{{ route('login-form') }}">Kembali</a>
                                    </form>
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
