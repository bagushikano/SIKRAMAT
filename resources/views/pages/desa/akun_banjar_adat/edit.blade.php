@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Edit Akun Banjar Adat')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Akun Banjar Adat
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-admin-banjar-home') }}" class="text-decoration-none">Akun Banjar Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Akun</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <div class="card mb-4 mt-4">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-edit text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Akun</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Akun Banjar Adat Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-10 mt-4">
                            <form id="form-edit-akun" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="banjar_adat_id">Banjar Adat<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat_id" style="width: 100%" required disabled>
                                                <option value="">Pilih Banjar Adat</option>
                                                @if(old('banjar_adat_id'))
                                                    @foreach($banjar_adat as $banjar)
                                                        <option value="{{ $banjar->id }}" @if(old('banjar_adat_id') == $banjar->id) selected @endif>{{ $banjar->nama_banjar_adat }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($banjar_adat as $banjar)
                                                        <option value="{{ $banjar->id }}" @if($admin_banjar_adat->banjar_adat_id == $banjar->id) selected @endif>{{ $banjar->nama_banjar_adat }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('banjar_adat_id')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Banjar Adat wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="email_desa_adat">Email<span class="text-danger small">*</span></label>
                                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="text" value="{{ old('email', $admin_banjar_adat->user->email) }}" placeholder="Masukkan Alamat Email" required>
                                            @error('email')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Email Desa Adat wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nama">Nama<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('nama') is-invalid @enderror" placeholder="Masukkan Nama Pengguna" name="nama" id="nama" value="{{ old('nama', $admin_banjar_adat->nama) }}" required>
                                            @error('nama')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Nama wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                @if(old('jenis_kelamin'))
                                                    <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                                    <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
                                                @else
                                                    <option value="laki-laki" @if($admin_banjar_adat->jenis_kelamin == 'laki-laki') selected @endif>Laki-laki</option>
                                                    <option value="perempuan" @if($admin_banjar_adat->jenis_kelamin == 'perempuan') selected @endif>Perempuan</option>
                                                @endif
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Jenis kelamin wajib dipilih
                                                </div>
                                            @enderror 
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="password">Password<span class="text-danger small">*</span></label>
                                            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" value="{{ old('password') }}" placeholder="Masukkan Password">
                                            @error('password')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Password wajib diisi
                                                </div>
                                            @enderror
                                            <div class="invalid-feedback" id="konfirmasi_pass1" style="display:none">
                                                Password tidak sama!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="password">Konfirmasi Password<span class="text-danger small">*</span></label>
                                            <input class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" type="password" value="{{ old('confirm_password') }}" placeholder="Masukkan Konfirmasi Password">
                                            @error('confirm_password')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Konfirmasi Password wajib diisi
                                                </div>
                                            @enderror
                                            <div class="invalid-feedback" id="konfirmasi_pass2" style="display:none">
                                                Password tidak sama!
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split mb-3 text-end" href="{{ route('desa-admin-banjar-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan({{ $admin_banjar_adat->id }})">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready( function () {
            //SELECT 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
            //SELECT 2

            //SIDE BAR CLASS
            $('#collapsePengguna').addClass('show');
            $('#nav-link-akun').addClass('active');

            $("#confirm_password").keyup(function() {
                let pass = $('#password').val();
                let confirm_pass = $('#confirm_password').val();
                if(pass == confirm_pass){
                    $('#konfirmasi_pass1').hide();
                    $('#konfirmasi_pass2').hide();
                }
            });

            $("#password").keyup(function() {
                let pass = $('#password').val();
                let confirm_pass = $('#confirm_password').val();
                if(pass == confirm_pass){
                    $('#konfirmasi_pass1').hide();
                    $('#konfirmasi_pass2').hide();
                }
            });
        });

        // Validasi Form
        (function () {
            'use strict'
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        //Validasi Form

        function simpan(id){
            let pass = $('#password').val();
            let confirm_pass = $('#confirm_password').val();
            if(pass == confirm_pass){
                var url = "{{ route('desa-admin-banjar-update', ":id") }}";
                url = url.replace(':id', id);
                $("#form-edit-akun").attr("action", url);
                $('#form-edit-akun').submit(function (e){
                    e.stopPropagation();
                });
            }else{
                $('#konfirmasi_pass1').show();
                $('#konfirmasi_pass2').show();
            }
            
        }
    </script>
@endpush