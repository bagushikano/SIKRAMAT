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
                                @if($errors->any())
                                <div class="row mx-3 mt-3">
                                    <div class="col-sm-12 alert alert-danger alert-dismissible fade show" role="alert">
                                        Terjadi kesalahan, silahkan melakukan register ulang
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                @endif
                                <div class="p-3">
                                    <form method="POST" id="form-register-awal" action="{{ route('Register Akhir') }}">
                                        @csrf                                       
                                        <div class="input-group">
                                            <input type="nik" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-danger" id="nik-validate" style="display:none;">
                                            NIK harus terdiri dari 16 digit angka
                                        </small>
                                        <small class="text-danger" id="nik-validate2" style="display:none;">
                                            NIK wajib diisi
                                        </small>
                                        
                                        
                                        <div class="input-group mt-3">
                                            <input type="text" class="form-control" id="tanggal_lahir" name="tanggal_lahir" placeholder="Masukkan Tanggal Lahir" required>
                                            <input id="signup-token" name="_token" type="hidden" value="{{csrf_token()}}">
                                            <div class="input-group-append" onclick="showPassword()">
                                                <div class="input-group-text">
                                                    <span id="lihat_password_icon" class="fas fa-calendar"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-danger" id="tanggal_lahir-validate" style="display:none;">
                                            Tanggal lahir wajib diisi
                                        </small>
                                    </form>
                                    <button type="button" class="btn btn-primary btn-rounded btn-lg btn-block mt-5" onclick="register()">Selanjutnya<i class="fas fa-arrow-right ml-2"></i></button>
                                    <div class="text-center mt-3 mb-n3">
                                        <span>Sudah memiliki akun? </span><a id="forgot-psw" href="{{ route('login-form') }}"> Login</a>

                                    </div>
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
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });

    $(document).ready(function() {
        //Regex NIK
        $('#nik').on('input', function (event) { 
            this.value = this.value.replace(/[^0-9]/g, '');
            if($("#nik").val() == ''){
                $("#nik-validate2").show();
            }else{
                $("#nik-validate2").hide();
            }
            if($("#nik").val().length == 16 || $("#nik").val() == ''){
                $("#nik-validate").hide();
            }else{
                $("#nik-validate").show();
            }
        });

        $('#tanggal_lahir').on('input', function (event){
            if($(this).val() != ''){
                $("#tanggal_lahir-validate").fadeOut();
            }else{
                $("#tanggal_lahir-validate").show();
            }
        });

        //DatePicker
        $("#tanggal_lahir").datepicker({
            format: 'd M yyyy',
            language: 'id',
            autoclose: true,
        });
    });

    function register(){
        if($('#nik').val() != ''){
            if($('#nik').val().length == 16){
                if($('#tanggal_lahir').val() != ''){
                    $('#form-register-awal').submit();
                }else{
                    $("#tanggal_lahir-validate").show();
                }
            }else{
                $("#nik-validate").show();            
            }
        }else{
            $("#nik-validate2").show();            
        }
    }
    </script>
</body>
</html>