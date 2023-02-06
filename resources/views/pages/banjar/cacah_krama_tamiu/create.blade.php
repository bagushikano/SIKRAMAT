@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>

    {{-- LEAFLET --}}
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
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endpush
@section('title', 'Tambah Cacah Krama Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Cacah Krama Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-cacah-krama-tamiu-home') }}" class="text-decoration-none text-dark">Krama Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Tambah Cacah Krama Tamiu</li>
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
                            <div class="wizard-step-icon"><i class="fas fa-plus text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Cacah Krama Tamiu</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Penambahan Data Cacah Krama Tamiu Berikut Ini</div>
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
                    <form id="form-create-krama-mipil" method="post" action="{{route('banjar-cacah-krama-tamiu-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="mx-5 justify-content-center">
                            <h5 class="card-title text-primary">Data Krama Tamiu</h5>
                            {{-- ALIAS - TTL --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Nomor Krama Tamiu</label>
                                        <input type="text" class="form-control @error ('nomor_krama_tamiu') is-invalid @enderror" id="nomor_krama_tamiu" name="nomor_krama_tamiu" placeholder="Nomor Krama Tamiu" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Krama Tamiu<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('krama_tamiu_placeholder') is-invalid @enderror"  id="krama_tamiu_placeholder" name="krama_tamiu_placeholder" placeholder="Pilih Krama Tamiu" value="{{ old('krama_tamiu_placeholder') }}" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_tamiu_modal()">
                                                    <span class="text">Pilih Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('krama_tamiu') is-invalid @enderror"  id="krama_tamiu" name="krama_tamiu"  value="{{ old('krama_tamiu') }}" required hidden>
                                        @error('krama_tamiu')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Krama Tamiu wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3 mb-4" />
                            <h5 class="card-title text-primary">Data Cacah Krama Tamiu</h5>
                            {{-- NIK - FOTO --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-7 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">NIK<span class="text-danger small">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik') }}" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari penduduk"><i class="fas fa-search"></i></button>
                                                    </div>
                                                </div>
                                                @error('nik')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
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
                                                <input type="text" class="can-empty form-control @error ('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" placeholder="Masukkan Gelar Depan" value="{{ old('gelar_depan') }}" readonly>
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
                                                <input type="text" class="can-empty form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}" required readonly>
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
                                                <input type="text" class="can-empty form-control @error ('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" value="{{ old('gelar_belakang') }}" readonly>
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
                                        <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ old('nama_tercetak') }}" readonly>
                                    </div>

                                </div>
                                <div class="col-lg-4 col-sm-12 text-center">
                                    <label for="foto" class="text-white">Foto</label>
                                    <br>
                                    <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                    <div id="img_container">
                                        <img src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
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
                                        <input type="text" class="can-empty form-control @error ('nama_alias') is-invalid @enderror" id="nama_alias" name="nama_alias" placeholder="Masukkan Nama Alias" value="{{ old('nama_alias') }}" readonly>
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
                                        <input type="text" class="can-empty form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir" required readonly>
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
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required readonly>
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

                            {{-- AGAMA - GOLDAR --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Agama<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required disabled>
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
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Jenis Kelamin<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required disabled>
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
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Golongan Darah<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required disabled>
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
                            </div>

                            {{-- PENDIDIKAN - STATUS KAWIN --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Pendidikan Tertinggi<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('pendidikan') is-invalid @enderror" name="pendidikan" id="pendidikan"  style="width: 100%" required aria-placeholder="Pilih Pendidikan" required disabled>
                                            <option value="">Pilih Pendidikan</option>
                                            @foreach($pendidikans as $pendidikan)
                                                <option value="{{ $pendidikan->id }}" @if(old('pendidikan') == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
                                            @endforeach
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
                                        <select class="can-empty select2 custom-select @error ('pekerjaan') is-invalid @enderror" name="pekerjaan" id="pekerjaan"  style="width: 100%" required aria-placeholder="Pilih Pekerjaan" required disabled>
                                            <option value="">Pilih Pekerjaan</option>
                                            @foreach($pekerjaans as $pekerjaan)
                                                <option value="{{ $pekerjaan->id }}" @if(old('pekerjaan') == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                            @endforeach
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
                                        <label for="title" class="small">Status Perkawinan<span class="text-danger small">*</span></label>
                                    <select class="can-empty select2 custom-select @error ('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="status_perkawinan"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required disabled>
                                        <option value="">Pilih Status Perkawinan</option>
                                        <option value="belum_kawin" @if(old('status_perkawinan') == 'belum_kawin') selected @endif>Belum Kawin</option>
                                        <option value="kawin" @if(old('status_perkawinan') == 'kawin') selected @endif>Kawin</option>
                                        <option value="cerai_hidup" @if(old('status_perkawinan') == 'cerai_hidup') selected @endif>Cerai Hidup</option>
                                        <option value="cerai_mati"  @if(old('status_perkawinan') == 'cerai_mati') selected @endif>Cerai Mati</option>
                                    </select>
                                    @error('status_perkawinan')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Status perkawinan wajib dipilih
                                        </div>
                                    @enderror 
                                    </div>
                                </div>
                            </div>

                            {{-- TELEPON - IBU --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">No. Telepon</label>
                                        <input type="text" class="can-empty form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon') }}" readonly>
                                        @error('telepon')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <div id="ayah_div">
                                            <label for="ayah_kandung" class="small">Ayah</label>
                                            <select class="can-empty select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%" disabled>
                                                <option value="">Pilih Ayah</option>
                                            </select>
                                            @error('ayah_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div id="ayah_manual_div" style="display:none;">
                                            <label for="title" class="small">Ayah</label>
                                            <select class="can-empty select2 custom-select select-krama @error ('ayah_kandung_manual') is-invalid @enderror" name="ayah_kandung_manual" id="ayah_kandung_manual"  style="width: 100%" disabled>
                                                <option value="">Cari Ayah</option>
                                            </select>
                                            @error('ayah_kandung_manual')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input class="can-empty custom-control-input" id="cari_ayah_manual" disabled type="checkbox">
                                            <label class="custom-control-label" for="cari_ayah_manual">Cari manual</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" class="small">
                                        <div id="ibu_div">
                                            <label for="ibu_kandung" class="small">Ibu</label>
                                            <select class="can-empty select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung" style="width: 100%" disabled>
                                                <option value="">Pilih Ibu</option>
                                             </select>
                                            @error('ayah_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div id="ibu_manual_div" style="display:none;">
                                            <label for="title" class="small">Ibu</label>
                                            <select class="can-empty select2 custom-select select-krama @error ('ibu_kandung_manual') is-invalid @enderror" name="ibu_kandung_manual" id="ibu_kandung_manual"  style="width: 100%" disabled>
                                                <option value="">Cari Ibu</option>
                                            </select>                                
                                            @error('ibu_kandung_manual')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input class="can-empty custom-control-input" id="cari_ibu_manual" disabled type="checkbox">
                                            <label class="custom-control-label" for="cari_ibu_manual">Cari manual</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STAT HUBUNGAN - DESA/KEL --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="status_hubungan" class="small">Status Hubungan dengan Krama Tamiu<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('status_hubungan') is-invalid @enderror" name="status_hubungan" id="status_hubungan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required disabled>
                                            <option value="">Pilih Status Hubungan</option>
                                            <option value="suami" @if(old('status_hubungan') == 'suami') selected @endif>Suami</option>
                                            <option value="istri" @if(old('status_hubungan') == 'istri') selected @endif>Istri</option>
                                            <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                            <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
                                            <option value="menantu" @if(old('status_hubungan') == 'menantu') selected @endif>Menantu</option>
                                            <option value="mertua" @if(old('status_hubungan') == 'mertua') selected @endif>Mertua</option>
                                            <option value="ayah" @if(old('status_hubungan') == 'ayah') selected @endif>Ayah</option>
                                            <option value="ibu" @if(old('status_hubungan') == 'ibu') selected @endif>Ibu</option>
                                            <option value="famili_lain" @if(old('status_hubungan') == 'famili_lain') selected @endif>Famili Lain</option>
                                        </select>
                                        @error('status_hubungan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Status Hubungan wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tanggal Registrasi<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_registrasi') is-invalid @enderror" placeholder="Masukkan Tanggal Registrasi" name="tanggal_registrasi" id="tanggal_registrasi" value="{{ old('tanggal_registrasi') }}" placeholder="Masukkan Tanggal Registrasi" required readonly>
                                        @error('tanggal_registrasi')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Tanggal registrasi wajib diisi
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required disabled>
                                            <option value="">Pilih Desa/Kelurahan</option>
                                            @foreach($desas as $des)
                                                <option value="{{ $des->id }}" @if($des->id == old('desa')) selected @endif>{{ $des->name }}</option>
                                            @endforeach
                                        </select>
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
                            </div>

                            {{-- DINAS - KOOR --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Banjar Dinas<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas_id"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required disabled>
                                            <option value="">Pilih Banjar Dinas</option>
                                            @foreach($banjar_dinas as $dinas)
                                                <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                            @endforeach
                                        </select>
                                        @error('banjar_dinas')
                                            <div class="invalid-feedback text-start">
                                                {{ $errors->first('banjar_dinas') }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Banjar Dinas wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <label for="koordinat" class="small">Koordinat Alamat</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder" id="koordinat_alamat_placeholder" placeholder="Pilih Koordinat" value="{{ old('koordinat_alamat_placeholder') }}">
                                            <input type="text" hidden class="form-control" name="koordinat_alamat" id="koordinat_alamat" value="{{ old('koordinat_alamat') }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="map_modal()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari koordinat"><i class="fas fa-map-marker-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ALAMAT --}}
                            <div class="form-group">
                                <div id="alamat_div">
                                    <label for="title" class="small">Alamat Tempat Tinggal<span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat') }}" required readonly>
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
                            </div>

                            <hr class="my-4" />
                            <div class="float-right">
                                <a class="btn btn-danger mr-2 btn-icon-split text-end" href="{{ route('banjar-cacah-krama-tamiu-home') }}">
                                    <span class="icon">
                                        <i class="fas fa-arrow-left"></i>
                                    </span>
                                    <span class="text">Kembali</span>
                                </a>
                                <button class="btn btn-success btn-icon-split text-end" type="submit">
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
    </main>
    {{-- <div id="mapid"></div> --}}

    <!-- Select Krama Tamiu Modal -->
    <div class="modal fade" id="select_krama_tamiu_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <form id="form-create-prajuru-desa-adat" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Pilih Krama Tamiu (Kepala Keluarga)</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="datatable">
                            <table class="table table-bordered table-hover" id="dataTable-krama-tamiu" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No.</th>
                                        <th style="width: 15%">No. Krama Tamiu</th>
                                        <th>Nama</th>
                                        <th style="width: 18%">Tempat/Tanggal Lahir</th>
                                        <th style="width: 13%">Jenis Kelamin</th>
                                        <th style="width: 13%">Banjar Dinas</th>
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

    {{-- LEAFLET --}}
    <div class="modal fade" id="pick_koordinat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Koordinat Alamat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mx-2" id="mapid"></div>
                <div class="form-group mx-2 mt-3">
                    <input type="text" readonly class="form-control" name="modal_koordinat_alamat_placeholder" id="modal_koordinat_alamat_placeholder" placeholder="Pilih Koordinat">
                    <input type="text" hidden class="form-control" name="modal_koordinat_alamat" id="modal_koordinat_alamat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="modal-close" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                {{-- <button type="button" onclick="pick_koordinat()" class="btn btn-primary">Simpan</button> --}}
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
    {{-- LEAFLET --}}
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

    {{-- ALERT --}}
    @if($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('nama') || $errors->has('nik') || $errors->has('tempat_lahir') || $errors->has('tanggal_lahir') || $errors->has('agama') || $errors->has('jenis_kelamin') || $errors->has('pendidikan') || $errors->has('pekerjaan') || $errors->has('golongan_darah') || $errors->has('alamat') || $errors->has('provinsi') || $errors->has('kabupaten') || $errors->has('kecamatan') || $errors->has('desa'))
            <script>
                $(".can-empty").prop('readonly', false);
                $(".can-empty").prop('disabled', false);
            </script>
        @endif
        {{-- <script>
        </script> --}}
    @endif

    {{-- LEAFLET --}}
    <script>
        //MAP INIT
        var map = L.map('mapid').setView([-8.359888288543399, 115.08508295752111], 10);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
        {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>', 
            maxZoom: 18, 
            id: 'mapbox/streets-v11', 
            tileSize: 512, 
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoiYWJkaXB1cm5hd2FuIiwiYSI6ImNrbGJ6bHBxMDBrOWQydnAwZnFtZGIxZjMifQ.Dskf87XP5VuPW2Srnrz8tw'
        }).addTo(map);

        function map_modal(){
            $('#pick_koordinat_modal').on('shown.bs.modal', function() {
                map.invalidateSize();
            });
            $('#pick_koordinat_modal').modal('show');
        }
    </script>

    {{-- SELECT KRAMA TAMIU --}}
    <script>
        //Datatable child
        function format ( d ) {
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
                    if(value.cacah_krama_tamiu.penduduk.gelar_depan){
                        nama = nama + value.cacah_krama_tamiu.penduduk.gelar_depan; 
                    }
                    nama = nama + ' ' + value.cacah_krama_tamiu.penduduk.nama;
                    if(value.cacah_krama_tamiu.penduduk.gelar_belakang){
                        nama = nama + ', ' + value.cacah_krama_tamiu.penduduk.gelar_belakang;
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
                var table = $('#dataTable-krama-tamiu');
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
                        "sSearchPlaceholder": "Cari Krama Tamiu...",
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
                        url : "{{ route('banjar-cacah-krama-tamiu-datatable-krama-tamiu') }}"
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_krama_tamiu', class: "wrap", orderable: false },
                        { data: 'cacah_krama_tamiu.penduduk.nama', class: "wrap", orderable: false },
                        { data: 'cacah_krama_tamiu.penduduk.tempat_lahir', class: "wrap", orderable: false },
                        { data: 'cacah_krama_tamiu.penduduk.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'cacah_krama_tamiu.banjar_dinas.nama_banjar_dinas', class: "wrap", orderable: false },
                        { data: 'anggota', "className": 'dt-control text-center', "orderable": false, "defaultContent": ''},
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 2,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.cacah_krama_tamiu.penduduk.gelar_depan){
                                    nama = nama + row.cacah_krama_tamiu.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.cacah_krama_tamiu.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.cacah_krama_tamiu.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                return data+', '+moment(row.cacah_krama_tamiu.penduduk.tanggal_lahir).format('DD MMM YYYY');
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

                $('#dataTable-krama-tamiu tbody').on('click', 'td.dt-control', function () {
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
        //Toast
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
        });

        //Fungsi Pilih Krama Mipil (Kepala Keluarga)
        function pilih_krama_tamiu_modal(){
            $('#select_krama_tamiu_modal').on('show.bs.modal', function(e) {
                //filter();
            }).modal('show');
        }

        function pilih_krama_tamiu(id, nama){
            $('#krama_tamiu').val(id);
            $('#krama_tamiu_placeholder').val(nama);
            $('#krama_tamiu_placeholder').prop('readonly', true);
            $('#select_krama_tamiu_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Tamiu Berhasil Dipilih'
            })
            var url = "{{ route('banjar-cacah-krama-tamiu-get-anggota-keluarga', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    //ASSIGN NO KRAMA TAMIU
                    $('#nomor_krama_tamiu').val(result.krama_tamiu.nomor_krama_tamiu);

                    //ASSIGN ANGGOTA AS AYAH IBU
                    $('#ayah_kandung').empty();
                    $('#ibu_kandung').empty();
                    $('#ayah_kandung').append('<option value="">Pilih Ayah Kandung</option>');
                    $('#ibu_kandung').append('<option value="">Pilih Ibu Kandung</option>');
                    $('#ayah_kandung').append('<option value="'+result.krama_tamiu.cacah_krama_tamiu.penduduk.id+'">'+result.krama_tamiu.cacah_krama_tamiu.penduduk.nama+'</option>');
                    $('#ibu_kandung').append('<option value="'+result.krama_tamiu.cacah_krama_tamiu.penduduk.id+'">'+result.krama_tamiu.cacah_krama_tamiu.penduduk.nama+'</option>');
                    result.anggota_krama_tamiu.forEach(element => {
                        $('#ayah_kandung').append('<option value="'+element.cacah_krama_tamiu.penduduk.id+'">'+element.cacah_krama_tamiu.penduduk.nama+'</option>');
                        $('#ibu_kandung').append('<option value="'+element.cacah_krama_tamiu.penduduk.id+'">'+element.cacah_krama_tamiu.penduduk.nama+'</option>');
                    });

                    //ASSIGN ALAMAT
                    console.log(result.krama_tamiu.cacah_krama_tamiu.penduduk);
                    $('#alamat').val(result.krama_tamiu.cacah_krama_tamiu.penduduk.alamat);
                    $('#desa').val(result.krama_tamiu.cacah_krama_tamiu.penduduk.desa_id).trigger('change');
                    $('#banjar_dinas_id').val(result.krama_tamiu.cacah_krama_tamiu.banjar_dinas_id).trigger('change');

                    let koor = JSON.parse(result.krama_tamiu.cacah_krama_tamiu.penduduk.koordinat_alamat);
                    let koor_placeholder = koor.lat+', '+koor.lng;
                    $("#koordinat_alamat_placeholder").val(koor_placeholder);
                    $("#modal_koordinat_alamat_placeholder").val(koor_placeholder);
                    $("#koordinat_alamat").val(JSON.stringify(koor));
                    var marker;
                    if (marker) { // check
                        map.removeLayer(marker); // remove
                    }
                    marker = L.marker([koor.lat, koor.lng], {
                        draggable: false
                    }).addTo(map);
                }
            });
        }
    </script>

    <script>
        $(document).ready( function () {
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
            });

            //Ayah Manual on Check
            $("#cari_ayah_manual").click(function(){
                if($("#cari_ayah_manual").is(':checked') ){
                    $('#ayah_div').hide();
                    $('#ayah_manual_div').show();
                }else{
                    $('#ayah_manual_div').hide();
                    $('#ayah_div').show();
                }
            });

            //Ibu Manual on Check
            $("#cari_ibu_manual").click(function(){
                if($("#cari_ibu_manual").is(':checked') ){
                    $('#ibu_div').hide();
                    $('#ibu_manual_div').show();
                }else{
                    $('#ibu_manual_div').hide();
                    $('#ibu_div').show();
                }
            });


            //DatePicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            //Nama On Change
            $('#gelar_depan,#nama,#gelar_belakang').on('keyup', function() {
                nama_tercetak();
            });

            //Regex NIK
            $('#nik').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik").val().length == 16){
                    $("#nik-validate").fadeOut();
                }
            });

            $('#search_button').on('click', function(){
                if($('#krama_tamiu').val()){
                    if($("#nik").val().length != 16){
                        $("#nik-validate").show();
                    }else{
                        var url = "{{ route('banjar-cacah-krama-tamiu-get-penduduk', ":nik") }}";
                        url = url.replace(':nik', $("#nik").val());
                        $("#nik-validate").fadeOut();
                        $("#overlay").css('display', 'flex');
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                if(result.status == 'ditemukan'){
                                    //$("#nik").prop('readonly', true);
                                    $('#gelar_depan').val(result.penduduk.gelar_depan); 
                                    $('#nama').val(result.penduduk.nama);
                                    $('#gelar_belakang').val(result.penduduk.gelar_belakang);
                                    $('#nama_alias').val(result.penduduk.nama_alias); 
                                    if(result.penduduk.gelar_depan != null && result.penduduk.gelar_belakang != null){
                                        $('#nama_tercetak').val(result.penduduk.gelar_depan+' '+result.penduduk.nama+', '+result.penduduk.gelar_belakang); 
                                    }else if(result.penduduk.gelar_depan == null && result.penduduk.gelar_belakang != null){
                                        $('#nama_tercetak').val(result.penduduk.nama+', '+result.penduduk.gelar_belakang); 
                                    }else if(result.penduduk.gelar_depan != null && result.penduduk.gelar_belakang == null){
                                        $('#nama_tercetak').val(result.penduduk.gelar_depan+' '+result.penduduk.nama); 
                                    }else if(result.penduduk.gelar_depan == null && result.penduduk.gelar_belakang == null){
                                        $('#nama_tercetak').val(result.penduduk.nama); 
                                    }   
                                    $('#tempat_lahir').val(result.penduduk.tempat_lahir); 
                                    $('#tanggal_lahir').datepicker("update", result.penduduk.tanggal_lahir); 
                                    $('#tanggal_registrasi').datepicker("update", result.penduduk.tanggal_lahir); 
                                    $('#agama').val(result.penduduk.agama).trigger('change');
                                    $('#jenis_kelamin').val(result.penduduk.jenis_kelamin).trigger('change');
                                    $('#pendidikan_terakhir').select2("val",result.penduduk.pendidikan_id);
                                    $('#pekerjaan').val(result.penduduk.pekerjaan_id).trigger('change');  
                                    $('#golongan_darah').val(result.penduduk.golongan_darah).trigger('change');
                                    $('#telepon').val(result.penduduk.telepon); 
                                    $('#status_perkawinan').val(result.penduduk.status_perkawinan).trigger('change');
                                    if(result.penduduk.ayah_kandung_id){
                                        $("#ayah_kandung_manual").empty();
                                        $("#ayah_kandung_manual").append('<option value="'+result.penduduk.ayah.id+'">'+result.penduduk.ayah.nama+'</option>');
                                    }
                                    if(result.penduduk.ibu_kandung_id){
                                        $("#ibu_kandung_manual").empty();
                                        $("#ibu_kandung_manual").append('<option value="'+result.penduduk.ibu.id+'">'+result.penduduk.ibu.nama+'</option>');
                                    }

                                    $('#pekerjaan').empty();
                                    result.pekerjaans.forEach(element => {
                                        var pekerjaan = '<option value="' + element['id'] + '"';
                                        if(element['id'] == result.penduduk.pekerjaan_id){
                                            pekerjaan = pekerjaan + ' selected'
                                        }
                                        pekerjaan = pekerjaan + '>' + element['profesi'] + '</option>'
                                        $('#pekerjaan').append(pekerjaan);
                                    });

                                    $('#pendidikan').empty();
                                    result.pendidikans.forEach(element => {
                                        var pendidikan = '<option value="' + element['id'] + '"';
                                        if(element['id'] == result.penduduk.pekerjaan_id){
                                            pendidikan = pendidikan + ' selected'
                                        }
                                        pendidikan = pendidikan + '>' + element['jenjang_pendidikan'] + '</option>'
                                        $('#pendidikan').append(pendidikan);
                                    });

                                    if(result.penduduk.foto){
                                        $("#propic").attr('src', result.penduduk.foto);
                                    }
                                    
                                    $(".can-empty").prop('readonly', false);
                                    $(".can-empty").prop('disabled', false);
                                    $("#nik").prop('readonly', true);
                                    $("#overlay").fadeOut();
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Penduduk ditemukan'
                                    })
                                }else if(result.status == 'tidak_ditemukan'){
                                    var nik = $("#nik").val();
                                    $(".can-empty").prop('readonly', false);
                                    $(".can-empty").prop('disabled', false);
                                    $(".can-empty").val('').trigger('change');
                                    $("#nama_tercetak").val('');
                                    $("#propic").attr('src', '/assets/admin/assets/img/foto_placeholder.png');
                                    $("#agama").val('hindu').trigger('change');
                                    $("#golongan_darah").val('-').trigger('change');
                                    $("#pendidikan").val('1').trigger('change');
                                    $("#pekerjaan").val('1').trigger('change');
                                    $("#nik").prop('readonly', true);
                                    $("#nik").val(nik);
                                    $("#overlay").fadeOut();
                                    Toast.fire({
                                        icon: 'warning',
                                        title: 'Penduduk tidak ditemukan'
                                    })
                                }
                                else if(result.status == 'terdaftar_krama_mipil'){
                                    $("#overlay").fadeOut();
                                    Toast.fire({
                                        icon: 'warning',
                                        title: 'Penduduk telah terdaftar sebagai Cacah Krama Mipil'
                                    })
                                }
                                else if(result.status == 'terdaftar_krama_tamiu'){
                                    $("#overlay").fadeOut();
                                    Toast.fire({
                                        icon: 'warning',
                                        title: 'Penduduk telah terdaftar sebagai Cacah Krama Tamiu'
                                    })
                                }
                                else if(result.status == 'terdaftar_tamiu'){
                                    $("#overlay").fadeOut();
                                    Toast.fire({
                                        icon: 'warning',
                                        title: 'Penduduk telah terdaftar sebagai Cacah Tamiu'
                                    })
                                }
                            }
                        });
                    }
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Krama Tamiu terlebih dahulu'
                    })
                } 
            });

            //SELECT 2
            $(".select-krama").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    },
                    inputTooShort: function() {
                        return 'Masukkan NIK';
                    }
                },
                minimumInputLength: 16,
                ajax: {
                    url: '{{ route("api-banjar-cacah-krama-mipil-ortu-search") }}',
                    dataType: 'json',
                },
            });

            //SIDE BAR CLASS
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-krama-tamiu').addClass('active');
        });

        function nama_tercetak(){
            var gelar_depan = "";
            var nama = $("#nama").val();
            var gelar_belakang = "";

            if ($("#gelar_depan").val()!="") {
                gelar_depan += $("#gelar_depan").val() + " ";
            }

            if ($("#gelar_belakang").val()!="") {
                gelar_belakang += ", " + $("#gelar_belakang").val();
            }

            $("#nama_tercetak").val(gelar_depan+nama+gelar_belakang);
        }

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

        //Select 2
        $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
            }
        });

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
    </script>
@endpush