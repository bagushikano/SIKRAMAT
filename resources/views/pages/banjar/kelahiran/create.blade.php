@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner_center.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
    <style>
        .form-custom[readonly] {
            display: block;
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #687281;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #c5ccd6;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
@endpush
@section('title', 'Tambah Kelahiran')
@section('content')
<main>
    <header class="page-header page-header-light pb-10">
        <div class="container">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-baby mr-2"></i></div>
                            Manajemen Kelahiran
                        </h1>
                    </div>
                </div>
                <ol class="breadcrumb mb-0 mt-4">
                    <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('banjar-kelahiran-home') }}" class="text-decoration-none text-dark">Manajemen Kelahiran</a></li>
                    <li class="breadcrumb-item active text-red-pastel">Tambah Kelahiran</li>
                </ol>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container mt-n10">
        <!-- Wizard card example with navigation-->
        <div class="card">
            <div class="card-header border-bottom">
                <!-- Wizard navigation-->
                <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                    <!-- Wizard navigation item 1-->
                    <a class="nav-item nav-link active" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                        <div class="wizard-step-icon">1</div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Anak</div>
                            <div class="wizard-step-text-details text-dark">Data anak yang akan ditambahkan</div>
                        </div>
                    </a>
                    <!-- Wizard navigation item 2-->
                    <a class="nav-item nav-link" id="wizard2-tab" href="#wizard2" data-toggle="tab" role="tab" aria-controls="wizard2" aria-selected="true">
                        <div class="wizard-step-icon">2</div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Orang Tua</div>
                            <div class="wizard-step-text-details text-dark">Orang Tua & Keluarga Krama dari anak yang akan ditambahkan</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="overlay">
                    <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                      <div class="spinner"></div>
                    </div>
                </div>
                <form id="form-create-kelahiran" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf 
                    <div class="tab-content" id="cardTabContent">
                        <!-- Wizard tab pane item 1-->
                        <div class="tab-pane mt-5 fade show active" id="wizard1" role="tabpanel" aria-labelledby="wizard1-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-10">
                                    <h3 class="text-primary">Langkah 1</h3>
                                    <h5 class="card-title">Masukkan Data Anak yang akan Ditambahkan</h5>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label class="small"for="title">NIK</label>
                                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik') }}">
                                                @error('nik')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label class="small"for="title">No. Akta Kelahiran</label>
                                                <input type="text" class="form-control @error ('nomor_akta_kelahiran') is-invalid @enderror"  id="nomor_akta_kelahiran" name="nomor_akta_kelahiran" placeholder="Masukkan Nomor Akta Kelahiran" value="{{ old('nomor_akta_kelahiran') }}">
                                                @error('nomor_akta_kelahiran')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="title">Nama<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}" required>
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="title">Tempat Lahir<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir" required>
                                            @error('tempat_lahir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Tempat lahir wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="title">Tanggal Lahir<span class="text-danger small">*</span></label>
                                            <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required>
                                            @error('tanggal_lahir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Tanggal lahir wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                                <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="title">Agama<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required>
                                                {{-- <option value="">Pilih Agama</option>
                                                <option value="islam">Islam</option>
                                                <option value="protestan">Protestan</option>
                                                <option value="katolik">Katolik</option> --}}
                                                <option value="hindu" selected>Hindu</option>
                                                {{-- <option value="buddha">Buddha</option>
                                                <option value="khonghucu">Khonghucu</option> --}}
                                            </select>
                                            @error('agama')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Agama wajib dipilih
                                                </div>
                                            @enderror  
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="title">Golongan Darah<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required>
                                                <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
                                                <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                                <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                                <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                                <option value="O"  @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                            </select>
                                            @error('golongan_darah')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Golongan darah wajib dipilih
                                                </div>
                                            @enderror  
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small"for="lampiran">File Akta Kelahiran</label>
                                            <br>    
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input @error('file_akta_kelahiran') is-invalid @enderror" id="file_akta_kelahiran" name="file_akta_kelahiran" accept=".pdf,.doc,.jpg,.png">
                                                <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                            </div>
                                            <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                            <div id="validasi-lampiran" class="text-danger small text-end" style="display:none;">
                                                Ukuran File Akta Kelahiran maksimal 2 MB.
                                            </div>
                                        </div>
                                    </div>
            
                
                                    <hr class="my-4" />
                                    <div class="d-flex float-right my-2">
                                        {{-- <button class="btn btn-light" type="button">Previous</button> --}}
                                        {{-- <button class="btn btn-primary" type="button" id="btn-next-1">Selanjutnya</button> --}}
                                        <button class="btn btn-primary btn-icon-split text-end" type="button" id="btn-next-1">
                                            <span class="icon">
                                                <i class="fas fa-arrow-right"></i>
                                            </span>
                                            <span class="text">Selanjutnya</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Wizard tab pane item 2-->
                        <div class="tab-pane mt-5 fade" id="wizard2" role="tabpanel" aria-labelledby="wizard2-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-8 col-xl-10">
                                    <h3 class="text-primary">Langkah 2</h3>
                                    <h5 class="card-title">Masukkan Data Keluarga Krama dari Anak yang Akan Ditambahkan</h5>
                                    <div class="form-group">
                                        <label class="small"for="title">Krama Mipil (Kepala Keluarga)<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="" required>
                                            <div class="input-group-append">
                                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_modal()">
                                                    <span class="text">Pilih Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('krama_mipil') is-invalid @enderror"  id="krama_mipil" name="krama_mipil"  value="" required hidden>
                                        @error('krama_mipil')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Krama Mipil wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                    </div>
                                    <div class="form-group" id="ayah_kandung_div">
                                        <label class="small"for="ayah_kandung">Ayah<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%" required>
                                            <option value="">Pilih Ayah</option>
                                        </select>
                                        <div class="custom-control custom-checkbox mb-n2">
                                            <input class="custom-control-input" id="tampilkan_semua_ayah" type="checkbox">
                                            <label class="custom-control-label" for="tampilkan_semua_ayah">Tampilkan semua</label>
                                        </div>
                                        @error('ayah_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Ayah wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group" id="ibu_kandung_div">
                                        <label class="small"for="ibu_kandung">Ibu<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%" required>
                                            <option value="">Pilih Ibu</option>
                                        </select>
                                        <div class="custom-control custom-checkbox mb-n2">
                                            <input class="custom-control-input" id="tampilkan_semua_ibu" type="checkbox">
                                            <label class="custom-control-label" for="tampilkan_semua_ibu">Tampilkan semua</label>
                                        </div>
                                        @error('ibu_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Ibu wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="small" for="keterangan">Keterangan</label>
                                        <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan"></textarea>
                                    </div>
                                    <hr class="my-4" />
                                    <div class="d-flex justify-content-between my-2">
                                        {{-- <button class="btn btn-light" type="button" id="btn-prev-2">Sebelumnya</button> --}}
                                        <button class="btn btn-light btn-icon-split text-end" type="button" id="btn-prev-2">
                                            <span class="icon">
                                                <i class="fas fa-arrow-left"></i>
                                            </span>
                                            <span class="text">Sebelumnya</span>
                                        </button>
                                        <div>
                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_kelahiran('0')">
                                                <span class="icon">
                                                    <i class="fas fa-save"></i>
                                                </span>
                                                <span class="text">Simpan sebagai Draft</span>
                                            </button>

                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_kelahiran('1')">
                                                <span class="icon">
                                                    <i class="fas fa-save"></i>
                                                </span>
                                                <span class="text">Simpan dan Sahkan</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

{{-- MODAL --}}
<!-- Select Krama Mipil Modal -->
<div class="modal fade" id="select_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
        <form id="form-create-prajuru-desa-adat" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Krama Mipil (Kepala Keluarga)</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-krama-mipil" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th style="width: 15%">No. Krama Mipil</th>
                                    <th>Nama</th>
                                    <th style="width: 18%">Tempat/Tanggal Lahir</th>
                                    <th style="width: 13%">Jenis Kelamin</th>
                                    <th style="width: 13%">Tempekan</th>
                                    <th style="width: 8%">Anggota</th>
                                    <th style="width: 8%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- MODAL --}}

{{-- CROPPER --}}
<div class="modal fade" id="crop-image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Foto</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row" style="margin: 20px">
                <img  src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="text-center" id="image-preview" width="50%" height="100%" alt="">
                <div class="custom-file" style="margin-top: 20px">
                    <input type="file" class="custom-file-input" id="profile-image" name="foto" accept="images/*" required>
                    <label class="small"for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                </div>
                <div id="validasi-foto" class="text-danger small text-end" style="display:none;">
                    Ukuran gambar maksimal 2 MB.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="modal-close" class="btn btn-danger" data-dismiss="modal">Kembali</button>
            <button type="button" id="update-foto-profile" class="btn btn-primary" data-dismiss="modal">Pilih</button>
        </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type='text/javascript' src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8/jquery.validate.min.js"></script>
    @if(old('jenis_kependudukan') == 'adat')
        <script>
            $("#tempekan_id").prop('required', true);
            $("#tempekan_row").show();

            $("#banjar_dinas_id").prop('required', false);
            $("#banjar_dinas_row").hide();
        </script>
    @elseif(old('jenis_kependudukan') == '')
        <script>
            $("#tempekan_id").prop('required', false);
            $("#tempekan_row").hide();

            $("#banjar_dinas_id").prop('required', false);
            $("#banjar_dinas_row_row").hide();
        </script> 
    @else
        <script>
            $("#tempekan_id").prop('required', true);
            $("#tempekan_row").show();

            $("#banjar_dinas_id").prop('required', true);
            $("#banjar_dinas_row").show();
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-kelahiran').addClass('active');

            $('input').on('keyup change', function() {
                if($(this).val() != ""){
                    $(this).removeClass('is-invalid');
                }
            });

            $('select').on('change', function() {
                if($(this).val() != ""){
                    $(this).removeClass("is-invalid");
                }
            });

            $("#btn-next-1").on('click', function() {
                var isValid = true;
                $('#wizard1 input.form-control').each(function () {
                    if ($(this).val() == "") {
                        $(this).addClass("is-invalid");
                        isValid = false;
                    }else if($(this).val() != ""){
                        $(this).removeClass("is-invalid");
                        $(this).addClass("is-valid");
                        isValid = true;
                    }
                    if($("#nik").val() != ""){
                        if($("#nik").val().length != 16){
                            $("#nik-validate").show();
                            isValid = false
                        }
                    }
                });
                $('#wizard1 select').each(function () {
                    if($(this).prop('required')){
                        if ($(this).val() == "") {
                            $("#wizard1").addClass("was-validated");
                            $(this).addClass("is-invalid");
                        }else if($(this).val() != ""){
                            $("#wizard1").addClass("was-validated");
                            $(this).removeClass("is-invalid");
                            $(this).addClass("is-valid");
                        }
                    }
                });
                if(isValid){
                    $('#cardTab a[href="#wizard2"]').tab('show');
                }
            });
            $("#btn-prev-2").on('click', function() {
                $('#cardTab a[href="#wizard1"]').tab('show');
            });

            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //Regex NIK
            $('#nik').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik").val().length == 16){
                    $("#nik-validate").fadeOut();
                }
            });

            //DatePicker
            $("#tanggal_lahir").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            //Jenis Kependudukan On Change
            $("#jenis_kependudukan").on('change', function(){
                if($(this).val() == 'adat'){
                    $("#banjar_dinas_id").prop('required', false);
                    $("#tempekan_id").prop('required', true);
                    $("#banjar_dinas_row").fadeOut();
                    $("#tempekan_row").fadeIn();
                }else if($(this).val() == ''){
                    $("#tempekan_row").fadeOut();
                    $("#banjar_dinas_row").fadeOut();
                    $("#tempekan_id").prop('required', false);
                    $("#banjar_dinas_id").prop('required', false);
                }else{
                    $("#banjar_dinas_id").prop('required', true);
                    $("#tempekan_id").prop('required', true);
                    $("#tempekan_row").fadeIn();
                    $("#banjar_dinas_row").fadeIn();
                }
            });

            //VALIDASI LAMPIRAN
            $("#file_akta_kelahiran").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-lampiran').show();
                    $('#file_akta_kelahiran').val("");
                }else{
                    document.getElementById('lampiran_label').innerHTML = document.getElementById('file_akta_kelahiran').files[0].name;
                    $('#validasi-lampiran').hide();
                }
            });
            //VALIDASI LAMPIRAN

            //Daerah On Change
            $('#provinsi').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#kabupaten').empty();
                            $('#kabupaten').append('<option value="">Pilih Kabupaten</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#kecamatan').empty();
                            $('#kecamatan').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#desa').empty();
                            $('#desa').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });
        });

        //Datatable child
        function format ( d ) {
            console.log(d);
            // `d` is the original data object for the row
            var child = '<table class="table table-bordered table-hover" id="dataTable-krama-mipil" width="100%" cellspacing="0">';
            child += '<thead><tr><th style="width: 5%;">No.</th><th>Nama</th><th style="width: 15%;">Status Hubungan</th><th style="width: 16%;">Tanggal Registrasi</th></tr></thead>';
            child += '<tbody>';
            if(d.anggota_keluarga){
                d.anggota_keluarga.forEach(function (value, i) {
                    //CONVERT
                    var index = i + 1;
                    var status_hubungan = value.status_hubungan.charAt(0).toUpperCase() + value.status_hubungan.slice(1);
                    var tanggal_registrasi = moment(value.tanggal_registrasi).format('DD MMM YYYY');
                    var nama = '';
                    if(value.cacah_krama_mipil.penduduk.gelar_depan){
                        nama = nama + value.cacah_krama_mipil.penduduk.gelar_depan; 
                    }
                    nama = nama + ' ' + value.cacah_krama_mipil.penduduk.nama;
                    if(value.cacah_krama_mipil.penduduk.gelar_belakang){
                        nama = nama + ', ' + value.cacah_krama_mipil.penduduk.gelar_belakang;
                    }

                    //ASSIGN
                    child += '<tr>';
                    child += '<td>'+index+'</td>';
                    child += '<td>'+nama+'</td>'; 
                    child += '<td>'+status_hubungan+'</td>'; 
                    child += '<td>'+tanggal_registrasi+'</td>'; 
                    child += '</tr>';
                });
            }else{
                child += '<tr class="text-center">Tidak Ada Anggota Keluarga</tr>';
            }
            child += '</tbody></table>';
            return child;
        }

        //Datatable
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                var table = $('#dataTable-krama-mipil');
                var oTable = table.DataTable({
                    "autoWidth": false,
                    "lengthMenu": [
                    [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"] // change per page values here
                        ],

                    // set the initial value
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "language": {
                        "lengthMenu": " _MENU_ records"
                    },
                    "oLanguage": {
                        "sSearch": "Cari:",
                        "sZeroRecords": "Data tidak ditemukan",
                        "sSearchPlaceholder": "Cari Krama Mipil...",
                        "infoEmpty": "Menampilkan 0 data",
                        "infoFiltered": "(dari _MAX_ data)",
                        "sLengthMenu": "Tampilkan _MENU_ data",
                    },
                    "language": {
                        "paginate": {
                            "previous": 'Sebelumnya',
                            "next": 'Berikutnya'
                        },
                        "info": "Menampilkan _START_ s/d _END_ dari _MAX_ data",
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-kelahiran-datatable-krama-mipil') }}",
                        data : function(d){
                            d.tempekan_id = $('#tempekan_id').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_krama_mipil', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.nama', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.tempat_lahir', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.jenis_kelamin', class: "wrap" },
                        { data: 'cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap" },
                        { data: 'anggota', "className": 'dt-control text-center', "orderable": false, "defaultContent": ''},
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 2,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.cacah_krama_mipil.penduduk.gelar_depan){
                                    nama = nama + row.cacah_krama_mipil.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.cacah_krama_mipil.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.cacah_krama_mipil.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                return data+', '+moment(row.cacah_krama_mipil.penduduk.tanggal_lahir).format('DD MMM YYYY');
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                if(data == 'laki-laki'){
                                    return 'Laki-laki';
                                }else{
                                    return 'Perempuan';
                                }
                            }
                        },
                        
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
                            }
                        },
                        {
                            'orderable': true,
                            'targets': [0]
                        }, {
                            "searchable": true,
                            "targets": [0]
                        }
                    ],
                    "order": [
                    [0, "asc"]
                    ] // set first column as a default sort by asc
                });

                filter = () => {
                    oTable.ajax.reload();
                }

                $('#dataTable-krama-mipil tbody').on('click', 'td.dt-control', function () {
                    var tr = $(this).closest('tr');
                    var row = oTable.row( tr );
            
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).html('<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>');
                    }
                    else {
                        // Open this row
                        row.child( format(row.data()) ).show();
                        tr.addClass('shown');
                        $(this).html('<button type="button" class="btn btn-danger btn-sm"><i class="fas fa-eye-slash"></i></button>');
                    }
                } );
            }

            return {
                //main function to initiate the module
                init: function () {
                    handleTable();
                }

            };

        }();

        //Datatable Init
        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
        });

        //Toast Init
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        //Fungsi Pilih Krama Mipil (Kepala Keluarga)
        function pilih_krama_mipil_modal(){
            $('#select_krama_mipil_modal').on('show.bs.modal', function(e) {
                filter();
            }).modal('show');
        }

        function pilih_krama_mipil(id, nama){
            $('#krama_mipil').val(id);
            $('#krama_mipil_placeholder').val(nama);
            $('#krama_mipil_placeholder').prop('readonly', true);
            $('#anggota_keluarga').modal('hide');
            $('#select_krama_mipil_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Mipil (Kepala Keluarga) Berhasil Dipilih'
            })
            var url = "{{ route('banjar-kelahiran-get-anggota-keluarga', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    $('#ayah_kandung').empty();
                    $('#ibu_kandung').empty();
                    $('#ayah_kandung').append('<option value="">Pilih Ayah</option>');
                    $('#ibu_kandung').append('<option value="">Pilih Ibu</option>');
                    if(result.krama_mipil.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                        $('#ayah_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                    }else{
                        $('#ibu_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                    }

                    result.anggota_krama_mipil.forEach(element => {
                        if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                            $('#ayah_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                        }else{
                            $('#ibu_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                        }
                    });
                   
                    //SHOW HIDDEN
                    $('#ayah_kandung').prop('disabled', false);
                    $('#ibu_kandung').prop('disabled', false);

                    $('#tampilkan_semua_ayah').prop('checked', false);
                    $('#tampilkan_semua_ibu').prop('checked', false);
                    $("#tampilkan_semua_ayah").click(function(){
                        if($("#tampilkan_semua_ayah").is(':checked') ){
                            $('#ayah_kandung').empty();
                            $('#ayah_kandung').append('<option value="">Pilih Ayah</option>');
                            if(result.krama_mipil.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                                $('#ayah_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                            }
                            result.anggota_krama_mipil.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                                    $('#ayah_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                            result.anggota_krama_mipil_meninggal.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                                    $('#ayah_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                        } else {
                            $('#ayah_kandung').empty();
                            $('#ayah_kandung').append('<option value="">Pilih Ayah</option>');
                            if(result.krama_mipil.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                                $('#ayah_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                            }
                            result.anggota_krama_mipil.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'laki-laki'){
                                    $('#ayah_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                        }
                    });

                    $("#tampilkan_semua_ibu").click(function(){
                        if($("#tampilkan_semua_ibu").is(':checked') ){
                            $('#ibu_kandung').empty();
                            $('#ibu_kandung').append('<option value="">Pilih Ibu</option>');
                            if(result.krama_mipil.cacah_krama_mipil.penduduk.jenis_kelamin == 'perempuan'){
                                $('#ibu_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                            }
                            result.anggota_krama_mipil.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'perempuan'){
                                    $('#ibu_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                            result.anggota_krama_mipil_meninggal.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin == 'perempuan'){
                                    $('#ibu_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                        } else {
                            $('#ibu_kandung').empty();
                            $('#ibu_kandung').append('<option value="">Pilih Ibu</option>');
                            if(result.krama_mipil.cacah_krama_mipil.penduduk.jenis_kelamin == 'perempuan'){
                                $('#ibu_kandung').append('<option value="'+result.krama_mipil.cacah_krama_mipil.penduduk.id+'">'+result.krama_mipil.cacah_krama_mipil.penduduk.nama+'</option>');
                            }
                            result.anggota_krama_mipil.forEach(element => {
                                if(element.cacah_krama_mipil.penduduk.jenis_kelamin){
                                    $('#ibu_kandung').append('<option value="'+element.cacah_krama_mipil.penduduk.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                                }
                            });
                        }
                    });
                }
            });
        }

        //Fungsi Simpan Draft/Sah
        function simpan_kelahiran(status){
            var url = "{{ route('banjar-kelahiran-store', ":status") }}";
            url = url.replace(':status', status);
            $("#form-create-kelahiran").attr("action", url);
            $('#form-create-kelahiran').submit(function (e){
                e.stopPropagation();
            });
        }

        //CROPPER
        function changeProfile(){
            $('#profile-image').trigger('click');
        }

        var cropper;
        var image = document.getElementById('image-preview');

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept-Encoding' : 'gzip',
                }
            });
            $('#profile-image').on('change', function(){
                var filedata = this.files[0];
                var imgtype = filedata.type;
                var match = ['image/jpg', 'image/jpeg', 'image/png'];
                if (!(filedata.type==match[0]||filedata.type==match[1]||filedata.type==match[2])) {
                    alert("Format gambar Salah");
                }else if(filedata.size > (2097152)){
                    $('#validasi-foto').show();
                }else{
                    $('#validasi-foto').hide();
                    var reader=new FileReader();
                    reader.onload=function(ev){
                        $('#image-preview').attr('src', ev.target.result);
                        cropper.destroy();
                        cropper = null;
                        cropper = new Cropper(image, {
                            aspectRatio: 3/4,
                            viewMode: 2,
                            preview: '.preview'
                        });
                    }
                    reader.readAsDataURL(this.files[0]);
                    var postData=new FormData();
                    postData.append('file', this.files[0]);
                }
            });
            $('#crop-image').on('shown.bs.modal', function(){
                cropper = new Cropper(image, {
                    aspectRatio: 3/4,
                    viewMode: 2,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function(){
                cropper.destroy();
                cropper = null;
            });

            $('#update-foto-profile').on('click', function(){
                canvas = cropper.getCroppedCanvas({
                    width: 1080,
                    height: 1920,
                });
                canvas.toBlob(function(blob){
                    url = URL.createObjectURL(blob);
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    
                    reader.onloadend = function() {
                        $('#propic').attr('src', reader.result);
                        var base64data = reader.result;
                        $('#foto').val(reader.result);
                        
                    }
                });
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
    </script>
@endpush