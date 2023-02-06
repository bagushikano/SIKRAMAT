@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner_center.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
    <style>
        #mapid { height: 500px; }
        #img_container {
            position: relative;
            display: inline-block;
            text-align: center;
        }

        .btn-foto {
            position: absolute;
            bottom: 0rem;
            width: 100%;
        }
        .transparan{
            background-color: black;
            cursor: pointer;
            opacity: 0.6;
        }
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
@section('title', 'Edit Perkawinan')
@section('content')
<main>
    <header class="page-header page-header-light pb-10">
        <div class="container">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-heart mr-2"></i></div>
                            Manajemen Perkawinan
                        </h1>
                    </div>
                </div>
                <ol class="breadcrumb mb-0 mt-4">
                    <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('banjar-perkawinan-home') }}" class="text-decoration-none text-dark">Perkawinan</a></li>
                    <li class="breadcrumb-item active text-red-pastel">Edit Perkawinan</li>
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
                            <div class="wizard-step-text-name text-dark">Calon Pradana</div>
                            <div class="wizard-step-text-details text-dark">Data calon Pradana yang akan ditambahkan</div>
                        </div>
                    </a>
                    <!-- Wizard navigation item 2-->
                    <a class="nav-item nav-link" id="wizard2-tab" href="#wizard2" data-toggle="tab" role="tab" aria-controls="wizard2" aria-selected="true">
                        <div class="wizard-step-icon">2</div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Purusa dan Data Perkawinan</div>
                            <div class="wizard-step-text-details text-dark">Data calon Purusa serta data perkawinan yang akan ditambahkan</div>
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
                <form id="form-edit-perkawinan" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf 
                    <div class="tab-content" id="cardTabContent">
                        <!-- Wizard tab pane item 1-->
                        <div class="tab-pane py-5 fade show active" id="wizard1" role="tabpanel" aria-labelledby="wizard1-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-11">
                                    <h3 class="text-primary">Langkah 1</h3>
                                    <h5 class="card-title">Masukkan Data Calon Pradana yang akan Ditambahkan</h5>
                                     {{-- NIK - FOTO --}}
                                     <div class="row">
                                        <div class="col-lg-8 col-sm-12">
                                            <div class="row">
                                                <div class="col-lg-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="title" class="small">NIK<span class="text-danger small">*</span></label>
                                                        <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik', $perkawinan->pradana->penduduk->nik) }}" required>
                                                        @error('nik')
                                                            <small class="text-danger" style="display:none;">
                                                                {{ $message }}
                                                            </small>
                                                        @else
                                                            <small class="text-danger" style="display:none;">
                                                                NIK wajib diisi
                                                            </small>
                                                        @enderror
                                                        <small class="text-danger" id="nik-validate" style="display:none;">
                                                            NIK harus terdiri dari 16 digit angka
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="title" class="small">Gelar Depan</label>
                                                        <input type="text" class="form-control @error ('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" placeholder="Masukkan Gelar Depan" value="{{ old('gelar_depan', $perkawinan->pradana->penduduk->gelar_depan) }}">
                                                        @error('gelar_depan')
                                                            <div class="invalid-feedback text-start">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="title" class="small">Nama<span class="text-danger small">*</span><span class="font-italic small"> (*tanpa gelar)</span></label>
                                                        <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama', $perkawinan->pradana->penduduk->nama_penggal) }}" required>
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
                                                <div class="col-lg-5 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="title" class="small">Gelar Belakang</label>
                                                        <input type="text" class="form-control @error ('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" value="{{ old('gelar_belakang', $perkawinan->pradana->penduduk->gelar_belakang) }}">
                                                        @error('gelar_belakang')
                                                            <div class="invalid-feedback text-start">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Tercetak<span class="font-italic small"> (*dengan gelar jika ada)</span></label>
                                                <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ old('nama_tercetak', $perkawinan->pradana->penduduk->nama) }}" readonly>
                                            </div>

                                        </div>
                                        <div class="col-lg-4 col-sm-12 text-center">
                                            <label for="foto" class="text-white">Foto</label>
                                            <br>
                                            <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                            <div id="img_container">
                                                <img src="@if($perkawinan->pradana->penduduk->foto != NULL) {{ $perkawinan->pradana->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
                                                <div class="transparan btn-foto">
                                                    <a data-target="#crop-image" data-toggle="modal" class="text-decoration-none text-white my-2">Pilih Foto</a>
                                                </div>
                                            </div>
                                            @error('foto')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Foto wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ALIAS - TTL --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Alias (Bhiseka)</label>
                                                <input type="text" class="form-control @error ('nama_alias') is-invalid @enderror" id="nama_alias" name="nama_alias" placeholder="Masukkan Nama Alias" value="{{ old('nama_alias', $perkawinan->pradana->penduduk->nama_alias) }}">
                                                @error('nama_alias')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Alias wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Tempat Lahir<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $perkawinan->pradana->penduduk->tempat_lahir) }}" placeholder="Masukkan Tempat Lahir" required>
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
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Tanggal Lahir<span class="text-danger small">*</span></label>
                                                <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', date('d M Y', strtotime($perkawinan->pradana->penduduk->tanggal_lahir))) }}" placeholder="Masukkan Tanggal Lahir" required>
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
                                        </div>
                                    </div>

                                    {{-- JK - AGAMA --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Jenis Kelamin<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                                    <option value="">Pilih Jenis Kelamin</option>
                                                    @if(old('jenis_kelamin'))
                                                        <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                                        <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
                                                    @else 
                                                        <option value="laki-laki" @if($perkawinan->pradana->penduduk->jenis_kelamin == 'laki-laki') selected @endif>Laki-laki</option>
                                                        <option value="perempuan" @if($perkawinan->pradana->penduduk->jenis_kelamin == 'perempuan') selected @endif>Perempuan</option>
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
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Golongan Darah<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required>
                                                    @if(old('golongan_darah'))
                                                        <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
                                                        <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                                        <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                                        <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                                        <option value="O"  @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                                    @else
                                                        <option value="-"  @if($perkawinan->pradana->penduduk->golongan_darah == '-') selected @endif>-</option>
                                                        <option value="A" @if($perkawinan->pradana->penduduk->golongan_darah == 'A') selected @endif>A</option>
                                                        <option value="B" @if($perkawinan->pradana->penduduk->golongan_darah == 'B') selected @endif>B</option>
                                                        <option value="AB" @if($perkawinan->pradana->penduduk->golongan_darah == 'AB') selected @endif>AB</option>
                                                        <option value="O"  @if($perkawinan->pradana->penduduk->golongan_darah == 'O') selected @endif>O</option>
                                                    @endif
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
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Agama Sebelumnya<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required>
                                                    <option value="">Pilih Agama Sebelumnya</option>
                                                    @if(old('agama'))
                                                        <option value="islam" @if(old('agama') == 'islam') selected @endif>Islam</option>
                                                        <option value="protestan" @if(old('agama') == 'protestan') selected @endif>Protestan</option>
                                                        <option value="katolik" @if(old('agama') == 'katolik') selected @endif>Katolik</option>
                                                        <option value="hindu" @if(old('agama') == 'hindu') selected @endif>Hindu</option>
                                                        <option value="buddha" @if(old('agama') == 'buddha') selected @endif>Buddha</option>
                                                        <option value="khonghucu" @if(old('agama') == 'khonghucu') selected @endif>Khonghucu</option>
                                                    @else 
                                                        <option value="islam" @if($perkawinan->agama_asal_pradana == 'islam') selected @endif>Islam</option>
                                                        <option value="protestan" @if($perkawinan->agama_asal_pradana == 'protestan') selected @endif>Protestan</option>
                                                        <option value="katolik" @if($perkawinan->agama_asal_pradana == 'katolik') selected @endif>Katolik</option>
                                                        <option value="hindu" @if($perkawinan->agama_asal_pradana == 'hindu') selected @endif>Hindu</option>
                                                        <option value="buddha" @if($perkawinan->agama_asal_pradana == 'buddha') selected @endif>Buddha</option>
                                                        <option value="khonghucu" @if($perkawinan->agama_asal_pradana == 'khonghucu') selected @endif>Khonghucu</option>
                                                    @endif
                                                </select>
                                                @error('agama')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Agama Sebelumnya wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PENDIDIKAN - NOTELP --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Pendidikan Tertinggi<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('pendidikan') is-invalid @enderror" name="pendidikan" id="pendidikan"  style="width: 100%" required aria-placeholder="Pilih Pendidikan" required>
                                                    <option value="">Pilih Pendidikan</option>
                                                    @if(old('pendidikan'))
                                                        @foreach($pendidikans as $pendidikan)
                                                            <option value="{{ $pendidikan->id }}" @if(old('pendidikan') == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
                                                        @endforeach
                                                    @else 
                                                        @foreach($pendidikans as $pendidikan)
                                                            <option value="{{ $pendidikan->id }}" @if($perkawinan->pradana->penduduk->pendidikan_id == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('pendidikan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Pendidikan tertinggi wajib dipilih
                                                    </div>
                                                @enderror  
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Pekerjaan<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('pekerjaan') is-invalid @enderror" name="pekerjaan" id="pekerjaan"  style="width: 100%" required aria-placeholder="Pilih Pekerjaan" required>
                                                    <option value="">Pilih Pekerjaan</option>
                                                    @if(old('pekerjaan'))
                                                        @foreach($pekerjaans as $pekerjaan)
                                                            <option value="{{ $pekerjaan->id }}" @if(old('pekerjaan') == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                                        @endforeach
                                                    @else 
                                                        @foreach($pekerjaans as $pekerjaan)
                                                            <option value="{{ $pekerjaan->id }}" @if($perkawinan->pradana->penduduk->profesi_id == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('pekerjaan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Pekerjaan wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Telepon</label>
                                                <input type="text" class="can-empty form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon', $perkawinan->pradana->penduduk->telepon) }}">
                                                @error('telepon')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ALAMAT ASAL --}}
                                    <div class="form-group">
                                        <label for="title" class="small">Alamat Asal<span class="text-danger small">*</span></label>
                                        <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat Asal" value="{{ old('alamat', $perkawinan->alamat_asal_pradana) }}" required>
                                        @error('alamat')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Alamat wajib diisi
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- PROV - KEC --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Provinsi Asal<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required>
                                                    <option value="">Pilih Provinsi Asal</option>
                                                    @if(old('provinsi'))
                                                        @foreach($provinsis as $provinsi)
                                                            <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($provinsis as $provinsi)
                                                            <option value="{{ $provinsi->id }}" @if($provinsi_asal->id == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('provinsi')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Provinsi wajib dipilih
                                                    </div>
                                                @enderror  
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten/Kota Asal<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                                    <option value="">Pilih Kabupaten Asal</option>
                                                    @if(old('kabupaten'))
                                                        @foreach($kabupatens as $kabupaten)
                                                            <option value="{{ $kabupaten->id }}" @if(old('kabupaten') == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($kabupatens as $kabupaten)
                                                            <option value="{{ $kabupaten->id }}" @if($kabupaten_asal->id == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <small class="small">(Pilih provinsi asal terlebih dahulu)</small>
                                                @error('kabupaten')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Kabupaten wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Kecamatan Asal<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required>
                                                    <option value="">Pilih Kecamatan Asal</option>
                                                    @if(old('kecamatan'))
                                                        @foreach($kecamatans as $kecamatan)
                                                            <option value="{{ $kecamatan->id }}" @if(old('kecamatan') == $kecamatan->id) selected @endif>{{ $kecamatan->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($kecamatans as $kecamatan)
                                                            <option value="{{ $kecamatan->id }}" @if($kecamatan_asal->id == $kecamatan->id) selected @endif>{{ $kecamatan->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <small class="small">(Pilih kabupaten/kota asal terlebih dahulu)</small>
                                                @error('kecamatan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Kecamatan wajib dipilih
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DES - AYAH --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Desa/Kelurahan Asal<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required>
                                                    <option value="">Pilih Desa/Kelurahan Asal</option>
                                                    @if(old('desa'))
                                                        @foreach($desas as $desa)
                                                            <option value="{{ $desa->id }}" @if(old('desa') == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($desas as $desa)
                                                            <option value="{{ $desa->id }}" @if($desa_asal->id == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <small class="small">(Pilih kecamatan asal terlebih dahulu)</small>
                                                @error('desa')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Desa wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">NIK Ayah</label>
                                                <input type="text" class="form-control @error ('nik_ayah') is-invalid @enderror"  id="nik_ayah" name="nik_ayah" placeholder="Masukkan NIK Ayah" value="{{ old('nik_ayah', $perkawinan->nik_ayah_pradana) }}">
                                                @error('nik_ayah')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        NIK Ayah wajib diisi
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Ayah</label>
                                                <input type="text" class="form-control @error ('nama_ayah') is-invalid @enderror" id="nama_ayah" name="nama_ayah" placeholder="Masukkan Nama Ayah" value="{{ old('nama_ayah', $perkawinan->nama_ayah_pradana) }}">
                                                @error('nama_ayah')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Ayah wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- IBU - FILESUDHI --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">NIK Ibu</label>
                                                <input type="text" class="form-control @error ('nik_ibu') is-invalid @enderror"  id="nik_ibu" name="nik_ibu" placeholder="Masukkan NIK Ibu" value="{{ old('nik_ibu', $perkawinan->nik_ibu_pradana) }}">
                                                @error('nik_ibu')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        NIK Ibu wajib diisi
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Ibu</label>
                                                <input type="text" class="form-control @error ('nama_ibu') is-invalid @enderror" id="nama_ibu" name="nama_ibu" placeholder="Masukkan Nama Ibu" value="{{ old('nama_ibu', $perkawinan->nama_ibu_pradana) }}">
                                                @error('nama_ibu')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Ayah wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="file_sudhi_wadhani" class="small">Bukti Upacara Sudhi Wadhani</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_sudhi_wadhani') is-invalid @enderror" id="file_sudhi_wadhani" name="file_sudhi_wadhani" accept=".pdf,.jpg">
                                                    <label for="file_sudhi_wadhani" id="file_sudhi_wadhani_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_sudhi_wadhani')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Bukti Upacara Sudhi Wadhani wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                @if($perkawinan->file_sudhi_wadhani != NULL)   
                                                    <a class="text-start text-primary small" href="{{$perkawinan->file_sudhi_wadhani}}"><p><i class="fas fa-download"></i> Unduh File Sudhi Wadhani</p></a>
                                                @endif
                                                <div id="validasi-file_sudhi_wadhani" class="text-danger small text-end" style="display:none;">
                                                    Ukuran Bukti Upacara Sudhi Wadhani maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <hr class="my-4" />
                                    <div class="d-flex float-right">
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
                        <div class="tab-pane py-5 fade" id="wizard2" role="tabpanel" aria-labelledby="wizard2-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-8 col-xl-10">
                                    <h3 class="text-primary">Langkah 2</h3>
                                    <h5 class="card-title">Masukkan Data Calon Purusa dan Data Perkawinan Lainnya</h5>

                                    <div class="form-group">
                                        <label for="title">Pilih Cacah Krama Purusa<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('purusa_placeholder') is-invalid @enderror form-custom"  id="purusa_placeholder" name="purusa_placeholder" placeholder="Pilih Cacah Krama Purusa" value="{{ old('purusa_placeholder', $perkawinan->purusa->penduduk->nama) }}" required readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_purusa_modal()">
                                                    <span class="text">Pilih Purusa</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('purusa') is-invalid @enderror"  id="purusa" name="purusa"  value="{{ old('purusa', $perkawinan->purusa->id) }}" required hidden>
                                        @error('purusa')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Purusa wajib dipilih
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="status_kekeluargaan">Status Kekeluargaan<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('status_kekeluargaan') is-invalid @enderror" name="status_kekeluargaan" id="status_kekeluargaan"  style="width: 100%" required>
                                            <option value="">Pilih Status Kekeluargaan</option>
                                            @if(old('status_kekeluargaan')) 
                                                <option value="tetap" @if(old('status_kekeluargaan') == 'tetap') selected @endif>Tetap di Krama Mipil (Kepala Keluarga) Lama</option>
                                                <option value="baru" @if(old('status_kekeluargaan') == 'baru') selected @endif>Pembentukan Krama Mipil (Kepala Keluarga) Baru</option>
                                            @else 
                                                <option value="tetap" @if($perkawinan->status_kekeluargaan == 'tetap') selected @endif>Tetap di Krama Mipil (Kepala Keluarga) Lama</option>
                                                <option value="baru" @if($perkawinan->status_kekeluargaan == 'baru') selected @endif>Pembentukan Krama Mipil (Kepala Keluarga) Baru</option>
                                            @endif
                                        </select>
                                        @error('status_kekeluargaan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Status Kekeluargaan wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>

                                    <div class="form-group" id="calon_kepala_keluarga_div" @if($perkawinan->status_kekeluargaan == 'tetap') style="display: none;" @endif>
                                        <label for="status_kekeluargaan" class="small">Kepala Keluarga<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('calon_kepala_keluarga') is-invalid @enderror" name="calon_kepala_keluarga" id="calon_kepala_keluarga"  style="width: 100%">
                                            <option value="">Pilih Kepala Keluarga</option>
                                            <option value="{{ $perkawinan->purusa->id }}" @if($perkawinan->calon_krama_id == $perkawinan->purusa_id) selected @endif>{{ $perkawinan->purusa->penduduk->nama }}</option>
                                            <option value="{{ $perkawinan->pradana->id }}" @if($perkawinan->calon_krama_id == $perkawinan->pradana_id) selected @endif>{{ $perkawinan->pradana->penduduk->nama }}</option>
                                        </select>
                                        @error('status_kekeluargaan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Kepala keluarga wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>

                                    {{-- TANGGAL PEMUPUT --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="tanggal_perkawinan" class="small">Tanggal Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="datepicker-here form-control @error ('tanggal_perkawinan') is-invalid @enderror" placeholder="Masukkan Tanggal Perkawinan" name="tanggal_perkawinan" id="tanggal_perkawinan" value="{{ old('tanggal_perkawinan',date('d M Y', strtotime($perkawinan->tanggal_perkawinan))) }}" required>
                                                @error('tanggal_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Tanggal Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Pemuput Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nama_pemuput') is-invalid @enderror"  id="nama_pemuput" name="nama_pemuput" placeholder="Masukkan Nama Pemuput Perkawinan" value="{{ old('nama_pemuput', $perkawinan->nama_pemuput) }}" required>
                                                @error('nama_pemuput')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Pemuput Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>     
                                        </div>
                                    </div>

                                    {{-- BUKTI PERKAWINAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nomor_bukti_serah_terima_perkawinan') is-invalid @enderror"  id="nomor_bukti_serah_terima_perkawinan" name="nomor_bukti_serah_terima_perkawinan" placeholder="Masukkan No. Bukti Serah Terima Perkawinan" value="{{ old('nomor_bukti_serah_terima_perkawinan', $perkawinan->nomor_perkawinan) }}" required>
                                                @error('nomor_bukti_serah_terima_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        No. Bukti Serah Terima Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="lampiran" class="small">File Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_bukti_serah_terima_perkawinan') is-invalid @enderror" id="file_bukti_serah_terima_perkawinan" name="file_bukti_serah_terima_perkawinan" accept=".pdf,.jpg">
                                                    <label for="file_bukti_serah_terima_perkawinan_label" id="file_bukti_serah_terima_perkawinan_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @if($perkawinan->file_bukti_serah_terima_perkawinan != NULL)   
                                                        <a class="text-start text-primary small" href="{{$perkawinan->file_bukti_serah_terima_perkawinan}}"><p><i class="fas fa-download"></i> Unduh File Bukti Serah Terima Perkawinan</p></a>
                                                    @endif
                                                    @error('file_bukti_serah_terima_perkawinan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Bukti Serah Terima Perkawinan wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file_bukti_serah_terima_perkawinan" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Bukti Serah Terima Perkawinan maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AKTA PERKAWINAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Akta Perkawinan</label>
                                                <input type="text" class="form-control @error ('nomor_akta_perkawinan') is-invalid @enderror"  id="nomor_akta_perkawinan" name="nomor_akta_perkawinan" placeholder="Masukkan No. Akta Perkawinan" value="{{ old('nomor_akta_perkawinan', $perkawinan->nomor_akta_perkawinan) }}">
                                                @error('nomor_akta_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="file_akta_perkawinan" class="small">File Akta Perkawinan</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_akta_perkawinan') is-invalid @enderror" id="file_akta_perkawinan" name="file_akta_perkawinan" accept=".pdf,.jpg">
                                                    <label for="file_akta_perkawinan_label" id="file_akta_perkawinan_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @if($perkawinan->file_akta_perkawinan != NULL)   
                                                        <a class="text-start text-primary small" href="{{$perkawinan->file_akta_perkawinan}}"><p><i class="fas fa-download"></i> Unduh File Akta Perkawinan</p></a>
                                                    @endif
                                                    @error('file_akta_perkawinan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file_akta_perkawinan" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Akta Perkawinan maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="keterangan">Keterangan</label>
                                        <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan">{{ $perkawinan->keterangan }}</textarea>
                                    </div>

                                    <hr class="my-4" />
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-light btn-icon-split text-end" type="button" id="btn-prev-2">
                                            <span class="icon">
                                                <i class="fas fa-arrow-left"></i>
                                            </span>
                                            <span class="text">Sebelumnya</span>
                                        </button>
                                        <div>
                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perkawinan({{ $perkawinan->id }}, '0')">
                                                <span class="icon">
                                                    <i class="fas fa-save"></i>
                                                </span>
                                                <span class="text">Simpan sebagai Draft</span>
                                            </button>

                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perkawinan({{ $perkawinan->id }}, '3')">
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
<!-- Select Cacah Krama Mipil Purusa -->
<div class="modal fade" id="select_purusa_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gray-100">
                <h5 class="modal-title" id="pilih_cacah_title">Pilih Purusa</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="datatable">
                    <table class="table table-bordered table-hover w-100" id="dataTable-purusa" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:5%">No.</th>
                                <th>NIC Krama Mipil</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th style="width: 13%">Jenis Kelamin</th>
                                <th style="width: 13%">Tempekan</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
                    <input type="file" class="custom-file-input" id="profile-image" name="foto" accept="image/*" required>
                    <label for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                </div>
                <small class="small">(Foto maksimal berukuran 2 MB)</small>
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
    <script>
        $(document).ready( function () {
            //DATEPICKER
                $("#tanggal_perkawinan").datepicker({
                    format: 'd M yyyy',
                    language: 'id',
                    autoclose: true,
                });
                $("#tanggal_lahir").datepicker({
                    format: 'd M yyyy',
                    language: 'id',
                    autoclose: true,
                });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perkawinan').addClass('active');

            //VALIDASI LAMPIRAN
                $("#file_bukti_serah_terima_perkawinan").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_bukti_serah_terima_perkawinan').show();
                        $('#file_bukti_serah_terima_perkawinan').val("");
                    }else{
                        document.getElementById('file_bukti_serah_terima_perkawinan_label').innerHTML = document.getElementById('file_bukti_serah_terima_perkawinan').files[0].name;
                        $('#validasi-file_bukti_serah_terima_perkawinan').hide();
                    }
                });
                $("#file_akta_perkawinan").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_akta_perkawinan').show();
                        $('#file_akta_perkawinan').val("");
                    }else{
                        document.getElementById('file_akta_perkawinan_label').innerHTML = document.getElementById('file_akta_perkawinan').files[0].name;
                        $('#validasi-file_akta_perkawinan').hide();
                    }
                });
                $("#file_sudhi_wadhani").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_sudhi_wadhani').show();
                        $('#file_sudhi_wadhani').val("");
                    }else{
                        document.getElementById('file_sudhi_wadhani_label').innerHTML = document.getElementById('file_sudhi_wadhani').files[0].name;
                        $('#validasi-file_sudhi_wadhani').hide();
                    }
                });
            //VALIDASI LAMPIRAN

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //Status Kekeluargaan On Change
            $('#status_kekeluargaan').on('change', function(){
                if($(this).val() == 'baru'){
                    get_calon_kepala_keluarga();
                    $('#calon_kepala_keluarga_div').fadeIn();
                    $('#calon_kepala_keluarga').prop('required', true);
                }else{
                    $('#calon_kepala_keluarga_div').fadeOut();
                    $('#calon_kepala_keluarga').prop('required', false);
                }
            });
        });

        //DATATABLE KRAMA MIPIL
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //CACAH KRAMA MIPIL PURUSA
                var table_purusa = $('#dataTable-purusa');
                var oTable_purusa = table_purusa.DataTable({
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
                        "sSearchPlaceholder": "Cari Mempelai Purusa...",
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
                        url : "{{ route('banjar-perkawinan-datatable-purusa') }}",
                        data : function(d){
                            d.pradana = $('#pradana').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_krama_mipil', class: "wrap" },
                        { data: 'penduduk.nik', class: "wrap" },
                        { data: 'penduduk.nama', class: "wrap", width:2000 },
                        { data: 'penduduk.jenis_kelamin', class: "wrap" },
                        { data: 'tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.penduduk.gelar_depan){
                                    nama = nama + row.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.penduduk.gelar_belakang;
                                }
                                return nama;
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

                purusa_filter = () => {
                    oTable_purusa.columns.adjust();
                    oTable_purusa.ajax.reload();
                }

                //CACAH KRAMA MIPIL PURUSA
                var table_pradana = $('#dataTable-pradana');
                var oTable_pradana = table_pradana.DataTable({
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
                        "sSearchPlaceholder": "Cari Mempelai Pradana...",
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
                        url : "{{ route('banjar-perkawinan-datatable-pradana') }}",
                        data : function(d){
                            d.banjar_adat_id = $('#banjar_adat_pradana').val();
                            d.purusa = $('#purusa').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_krama_mipil', class: "wrap" },
                        { data: 'penduduk.nik', class: "wrap" },
                        { data: 'penduduk.nama', class: "wrap", width:2000 },
                        { data: 'penduduk.jenis_kelamin', class: "wrap" },
                        { data: 'tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.penduduk.gelar_depan){
                                    nama = nama + row.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.penduduk.gelar_belakang;
                                }
                                return nama;
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

                pradana_filter = () => {
                    oTable_pradana.columns.adjust();
                    oTable_pradana.ajax.reload();
                }
            }

            return {
                //main function to initiate the module
                init: function () {
                    handleTable();
                }

            };

        }();

        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
        });

        //Swal Toast
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
        //Swal Toast

        //Pilih Purusa
        function pilih_purusa_modal(){
            $('#select_purusa_modal').on('show.bs.modal', function(e) {
                purusa_filter();
            }).modal('show');
        }

        function pilih_purusa(id, nama){
            $('#purusa').val(id);
            $('#purusa_placeholder').val(nama);
            $('#purusa_placeholder').prop('readonly', true);
            $('#select_purusa_modal').modal('hide');
            get_calon_kepala_keluarga();
            Toast.fire({
                icon: 'success',
                title: 'Purusa Berhasil Dipilih'
            })
        }
        //Pilih Purusa

        //Pilih Pradana
        function pilih_pradana_modal(){
            var purusa = $('#purusa').val();
            if(purusa){
                $('#select_pradana_modal').on('show.bs.modal', function(e) {
                    pradana_filter();
                }).modal('show');
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Purusa Terlebih Dahulu'
                })
            }
        }

        function pilih_pradana(id, nama){
            $('#pradana').val(id);
            $('#pradana_placeholder').val(nama);
            $('#pradana_placeholder').prop('readonly', true);
            $('#select_pradana_modal').modal('hide');
            $('#status_kekeluargaan').prop('disabled', false);
            get_calon_kepala_keluarga();
            Toast.fire({
                icon: 'success',
                title: 'Pradana Berhasil Dipilih'
            })
        }
        //Pilih Pradana

        //Fungsi Get Calon Kepala Keluarga
        function get_calon_kepala_keluarga(){
            var purusa = $('#purusa').val();
            var pradana = $('#pradana').val();
            var url = "{{ route('banjar-perkawinan-get-calon-kk', ['purusa_id'=>":purusa_id", 'pradana_id'=>":pradana_id"]) }}";
            url = url.replace(':purusa_id', purusa);
            url = url.replace(':pradana_id', pradana);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    $('#calon_kepala_keluarga').empty();
                    $('#calon_kepala_keluarga').append('<option value="">Pilih Kepala Keluarga</option>');
                    result['0'].forEach(element=>{
                        $('#calon_kepala_keluarga').append('<option value="'+element.id+'">'+element.penduduk.nama+'</option>');
                    })
                }
            });
        }
        //Fungsi Get Calon Kepala Keluarga


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

        //Fungsi Simpan Draft/Sah
        function simpan_perkawinan(id, status){
            var url = "{{ route('banjar-perkawinan-campuran-masuk-update', [":id", ":status"]) }}";
            url = url.replace(':id', id);
            url = url.replace(':status', status);
            $("#form-edit-perkawinan").attr("action", url);
            $('#form-edit-perkawinan').submit(function (e){
                e.stopPropagation();
            });
        }

    </script>
@endpush