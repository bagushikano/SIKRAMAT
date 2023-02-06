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
@section('title', 'Edit Cacah Krama Mipil')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Cacah Krama Mipil
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-cacah-krama-mipil-home') }}" class="text-decoration-none text-dark">Cacah Krama Mipil</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Cacah Krama Mipil</li>
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
                                <div class="wizard-step-text-name text-dark">Data Cacah Krama Mipil</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Cacah Krama Mipil Berikut Ini</div>
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
                    <form id="form-create-krama-mipil" method="post" action="{{route('banjar-cacah-krama-mipil-update', $cacah_krama_mipil->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="mx-5 justify-content-center">
                            <h5 class="card-title text-primary">Data Krama Mipil</h5>
                            {{-- ALIAS - TTL --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Nomor Krama Mipil</label>
                                        <input type="text" class="form-control @error ('nomor_krama_mipil') is-invalid @enderror" id="nomor_krama_mipil" name="nomor_krama_mipil" placeholder="Masukkan Nomor Krama Mipil" value="{{ $krama_mipil->nomor_krama_mipil }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Nama Krama Mipil</label>
                                        <input type="text" class="form-control @error ('nama_krama_mipil') is-invalid @enderror" id="nama_krama_mipil" name="nama_krama_mipil" value="{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}" placeholder="Masukkan Nama Krama Mipil" required readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tanggal Registrasi Krama Mipil</label>
                                        <input type="text" class="datepicker-here form-control @error ('tanggal_registrasi_krama_mipil') is-invalid @enderror" placeholder="Masukkan Tanggal Registrasi" name="tanggal_registrasi_krama_mipil" id="tanggal_registrasi_krama_mipil" value="{{ date('d M Y', strtotime($krama_mipil->tanggal_registrasi)) ?? '-' }}" placeholder="Masukkan Tanggal Lahir" required readonly>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3 mb-4" />
                            <h5 class="card-title text-primary">Data Cacah Krama Mipil</h5>
                            {{-- NIK - FOTO --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nomor Cacah Krama Mipil</label>
                                                <input type="text" class="form-control @error ('nomor_cacah_krama_mipil') is-invalid @enderror"  id="nomor_cacah_krama_mipil" name="nomor_cacah_krama_mipil" placeholder="Masukkan No. Cacah Krama Mipil" value="{{ old('nomor_cacah_krama_mipil', $cacah_krama_mipil->nomor_cacah_krama_mipil) }}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">NIK<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik', $penduduk->nik) }}" required @if($penduduk->nik != NULL) readonly @endif>
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
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Gelar Depan</label>
                                                <input type="text" class="can-empty form-control @error ('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" placeholder="Masukkan Gelar Depan" value="{{ old('gelar_depan', $penduduk->gelar_depan) }}">
                                                @error('gelar_depan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama<span class="text-danger small">*</span><span class="font-italic small"> (*tanpa gelar)</span></label>
                                                <input type="text" class="can-empty form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama', $penduduk->nama) }}" required>
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
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Gelar Belakang</label>
                                                <input type="text" class="can-empty form-control @error ('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" value="{{ old('gelar_belakang', $penduduk->gelar_belakang) }}">
                                                @error('gelar_belakang')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Tercetak<span class="font-italic small"> (*dengan gelar jika ada)</span></label>
                                                <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ $penduduk->gelar_depan }} {{ $penduduk->nama }}@if($penduduk->gelar_belakang != ''), {{ $penduduk->gelar_belakang }} @endif" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 text-center">
                                    <label for="foto" class="text-white">Foto</label>
                                    <br>
                                    <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                    <div id="img_container">
                                        <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !=NULL) {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
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
                                        <input type="text" class="can-empty form-control @error ('nama_alias') is-invalid @enderror" id="nama_alias" name="nama_alias" placeholder="Masukkan Nama Alias" value="{{ old('nama_alias', $penduduk->nama_alias) }}">
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
                                        <input type="text" class="can-empty form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $penduduk->tempat_lahir) }}" placeholder="Masukkan Tempat Lahir" required>
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
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir',date('d M Y', strtotime($penduduk->tanggal_lahir))) }}" placeholder="Masukkan Tanggal Lahir" required>
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
                                        <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required disabled>
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
                                        <select class="can-empty select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            @if(old('jenis_kelamin'))
                                                <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                                <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
                                            @else
                                                <option value="laki-laki" @if($penduduk->jenis_kelamin == 'laki-laki') selected @endif>Laki-laki</option>
                                                <option value="perempuan" @if($penduduk->jenis_kelamin == 'perempuan') selected @endif>Perempuan</option>
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
                                            <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
                                            @if(old('golongan_darah'))
                                                <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                                <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                                <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                                <option value="O" @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                            @else 
                                                <option value="A" @if($penduduk->golongan_darah == 'A') selected @endif>A</option>
                                                <option value="B" @if($penduduk->golongan_darah == 'B') selected @endif>B</option>
                                                <option value="AB" @if($penduduk->golongan_darah == 'AB') selected @endif>AB</option>
                                                <option value="O" @if($penduduk->golongan_darah == 'O') selected @endif>O</option>
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
                            </div>

                            {{-- PENDIDIKAN - STATUS KAWIN --}}
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
                                                    <option value="{{ $pendidikan->id }}" @if($penduduk->pendidikan_id == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
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
                                                    <option value="{{ $pekerjaan->id }}"  @if(old('pekerjaan') == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                                @endforeach
                                            @else
                                                @foreach($pekerjaans as $pekerjaan)
                                                    <option value="{{ $pekerjaan->id }}"  @if($penduduk->profesi_id == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
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
                                        <label for="title" class="small">Status Perkawinan<span class="text-danger small">*</span></label>
                                    <select class="can-empty select2 custom-select @error ('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="status_perkawinan"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required>
                                        <option value="">Pilih Status Perkawinan</option>
                                        @if(old('status_perkawinan'))
                                            <option value="belum_kawin" @if(old('status_perkawinan') == 'belum_kawin') selected @endif>Belum Kawin</option>
                                            <option value="kawin" @if(old('status_perkawinan') == 'kawin') selected @endif>Kawin</option>
                                            <option value="cerai_hidup" @if(old('status_perkawinan') == 'cerai_hidup') selected @endif>Cerai Hidup</option>
                                            <option value="cerai_mati"  @if(old('status_perkawinan') == 'cerai_mati') selected @endif>Cerai Mati</option>
                                        @else 
                                            <option value="belum_kawin" @if($penduduk->status_perkawinan == 'belum_kawin') selected @endif>Belum Kawin</option>
                                            <option value="kawin" @if($penduduk->status_perkawinan == 'kawin') selected @endif>Kawin</option>
                                            <option value="cerai_hidup" @if($penduduk->status_perkawinan == 'cerai_hidup') selected @endif>Cerai Hidup</option>
                                            <option value="cerai_mati"  @if($penduduk->status_perkawinan == 'cerai_mati') selected @endif>Cerai Mati</option>
                                        @endif
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
                                        <input type="text" class="can-empty form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon', $penduduk->telepon) }}">
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
                                            <select class="can-empty select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%">
                                                <option value="">Pilih Ayah</option>
                                                <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}"  @if($penduduk->ayah_kandung_id == $krama_mipil->cacah_krama_mipil->penduduk->id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                                @foreach($anggota_krama_mipil as $anggota)
                                                    <option value="{{ $anggota->cacah_krama_mipil->penduduk->id }}" @if($penduduk->ayah_kandung_id == $anggota->cacah_krama_mipil->penduduk->id) selected @endif>{{ $anggota->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('ayah_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div id="ayah_manual_div" style="display:none;">
                                            <label for="title" class="small">Ayah</label>
                                            <select class="can-empty select2 custom-select select-krama @error ('ayah_kandung_manual') is-invalid @enderror" name="ayah_kandung_manual" id="ayah_kandung_manual"  style="width: 100%">
                                                <option value="">Cari Ayah</option>
                                            </select>
                                            @error('ayah_kandung_manual')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" id="cari_ayah_manual" type="checkbox">
                                            <label class="custom-control-label" for="cari_ayah_manual">Cari manual</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" class="small">
                                        <div id="ibu_div">
                                            <label for="ibu_kandung" class="small">Ibu</label>
                                            <select class="can-empty select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung" style="width: 100%">
                                                <option value="">Pilih Ibu</option>
                                                <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}" @if($penduduk->ibu_kandung_id == $krama_mipil->cacah_krama_mipil->penduduk->id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                                @foreach($anggota_krama_mipil as $anggota)
                                                    <option value="{{ $anggota->cacah_krama_mipil->penduduk->id }}" @if($penduduk->ibu_kandung_id == $anggota->cacah_krama_mipil->penduduk->id) selected @endif>{{ $anggota->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('ayah_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div id="ibu_manual_div" style="display:none;">
                                            <label for="title" class="small">Ibu</label>
                                            <select class="can-empty select2 custom-select select-krama @error ('ibu_kandung_manual') is-invalid @enderror" name="ibu_kandung_manual" id="ibu_kandung_manual"  style="width: 100%">
                                                <option value="">Cari Ibu</option>
                                            </select>                                
                                            @error('ibu_kandung_manual')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" id="cari_ibu_manual" type="checkbox">
                                            <label class="custom-control-label" for="cari_ibu_manual">Cari manual</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STAT HUBUNGAN ALAMAT --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="status_hubungan" class="small">Status Hubungan dengan Krama Mipil<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('status_hubungan') is-invalid @enderror" name="status_hubungan" id="status_hubungan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                            <option value="">Pilih Status Hubungan</option>
                                            @if(old('status_hubungan'))
                                                <option value="suami" @if(old('status_hubungan') == 'suami') selected @endif>Suami</option>
                                                <option value="istri" @if(old('status_hubungan') == 'istri') selected @endif>Istri</option>
                                                <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                                <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
                                                <option value="menantu" @if(old('status_hubungan') == 'menantu') selected @endif>Menantu</option>
                                                <option value="mertua" @if(old('status_hubungan') == 'mertua') selected @endif>Mertua</option>
                                                <option value="ayah" @if(old('status_hubungan') == 'ayah') selected @endif>Ayah</option>
                                                <option value="ibu" @if(old('status_hubungan') == 'ibu') selected @endif>Ibu</option>
                                                <option value="famili_lain" @if(old('status_hubungan') == 'famili_lain') selected @endif>Famili Lain</option>
                                            @else 
                                                <option value="suami" @if($anggota_krama->status_hubungan == 'suami') selected @endif>Suami</option>
                                                <option value="istri" @if($anggota_krama->status_hubungan == 'istri') selected @endif>Istri</option>
                                                <option value="anak" @if($anggota_krama->status_hubungan == 'anak') selected @endif>Anak</option>
                                                <option value="cucu" @if($anggota_krama->status_hubungan == 'cucu') selected @endif>Cucu</option>
                                                <option value="menantu" @if($anggota_krama->status_hubungan == 'menantu') selected @endif>Menantu</option>
                                                <option value="mertua" @if($anggota_krama->status_hubungan == 'mertua') selected @endif>Mertua</option>
                                                <option value="ayah" @if($anggota_krama->status_hubungan == 'ayah') selected @endif>Ayah</option>
                                                <option value="ibu" @if($anggota_krama->status_hubungan == 'ibu') selected @endif>Ibu</option>
                                                <option value="famili_lain" @if($anggota_krama->status_hubungan == 'famili_lain') selected @endif>Famili Lain</option>
                                            @endif
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
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <div id="alamat_div" @if($pisah_tinggal) style="display:none;" @endif>
                                            <label for="title" class="small">Alamat<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat', $krama_mipil->cacah_krama_mipil->penduduk->alamat) }}" required readonly>
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
                                        <div id="alamat_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                            <label for="title" class="small">Alamat<span class="text-danger small">*</span></label>
                                            <input type="text" class="can-empty form-control @error ('alamat_pisah') is-invalid @enderror"  id="alamat_pisah" name="alamat_pisah" placeholder="Masukkan Alamat" value="{{ old('alamat_pisah', $penduduk->alamat) }}">
                                            @error('alamat_pisah')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Alamat wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input class="can-empty custom-control-input" id="pisah_tinggal" name="pisah_tinggal" @if($pisah_tinggal) checked @endif type="checkbox">
                                            <label class="custom-control-label" for="pisah_tinggal">Tinggal terpisah dengan Krama Mipil</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- KOOR - KAB --}}
                            <div class="row">
                                @php 
                                    $koor = $krama_mipil->cacah_krama_mipil->penduduk->koordinat_alamat;
                                    $koor_placeholder = json_decode($krama_mipil->cacah_krama_mipil->penduduk->koordinat_alamat);
                                    $koor_placeholder = $koor_placeholder->lat.', '.$koor_placeholder->lng;
                                @endphp
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" id="koordinat_div" @if($pisah_tinggal) style="display:none;" @endif>
                                        <label for="koordinat" class="small">Koordinat Alamat</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder" id="koordinat_alamat_placeholder" value="{{ old('koordinat_alamat_placeholder', $koor_placeholder) }}" placeholder="Pilih Koordinat">
                                            <input type="text" hidden class="form-control" name="koordinat_alamat" id="koordinat_alamat" value="{{ old('koordinat_alamat', $krama_mipil->cacah_krama_mipil->penduduk->koordinat_alamat) }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="map_modal()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari koordinat" disabled><i class="fas fa-map-marker-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    @php 
                                        $koor = $penduduk->koordinat_alamat;
                                        $koor_placeholder = json_decode($krama_mipil->cacah_krama_mipil->penduduk->koordinat_alamat);
                                        $koor_placeholder = $koor_placeholder->lat.', '.$koor_placeholder->lng;
                                    @endphp
                                    <div class="form-group" id="koordinat_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                        <label for="koordinat" class="small">Koordinat Alamat</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder_pisah" id="koordinat_alamat_placeholder_pisah" placeholder="Pilih Koordinat" value="{{ old('koordinat_alamat_placeholder', $koor_placeholder) }}">
                                            <input type="text" hidden class="form-control" name="koordinat_alamat_pisah" id="koordinat_alamat_pisah" value="{{ old('koordinat_alamat', $krama_mipil->cacah_krama_mipil->penduduk->koordinat_alamat) }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="map_modal()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari koordinat"><i class="fas fa-map-marker-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" id="provinsi_div" @if($pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Provinsi<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required disabled>
                                            <option value="">Pilih Provinsi</option>
                                            @foreach($provinsis as $prov)
                                                <option value="{{ $prov->id }}" @if($provinsi->id == $prov->id) selected @endif>{{ $provinsi->name }}</option>
                                            @endforeach
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
                                    <div class="form-group" id="provinsi_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Provinsi<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('provinsi_pisah') is-invalid @enderror" name="provinsi_pisah" id="provinsi_pisah"  style="width: 100%" required aria-placeholder="Pilih Provinsi">
                                            <option value="">Pilih Provinsi</option>
                                            @foreach($provinsis as $prov)
                                                <option value="{{ $prov->id }}" @if($provinsi_penduduk->id == $prov->id) selected @endif>{{ $provinsi->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('provinsi_pisah')
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
                                    <div class="form-group" id="kabupaten_div" @if($pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Kabupaten<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required disabled>
                                            <option value="">Pilih Kabupaten</option>
                                            @foreach($kabupatens as $kab)
                                                <option value="{{ $kab->id }}" @if($kabupaten->id == $kab->id) selected @endif>{{ $kab->name }}</option>
                                            @endforeach
                                        </select>
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
                                    <div class="form-group" id="kabupaten_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Kabupaten<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('kabupaten_pisah') is-invalid @enderror" name="kabupaten_pisah" id="kabupaten_pisah"  style="width: 100%" required aria-placeholder="Pilih Kabupaten">
                                            <option value="">Pilih Kabupaten</option>
                                            @foreach($kabupatens_penduduk as $kab)
                                                <option value="{{ $kab->id }}" @if($kabupaten_penduduk->id == $kab->id) selected @endif>{{ $kab->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                        @error('kabupaten_pisah')
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
                            </div>

                            {{-- KECAMATAN-TANGGAL REGIS --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" id="kecamatan_div" @if($pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Kecamatan<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required disabled>
                                            <option value="">Pilih Kecamatan</option>
                                            @foreach($kecamatans as $kec)
                                                <option value="{{ $kec->id }}" @if($kec->id == $kecamatan->id) selected @endif>{{ $kec->name }}</option>
                                            @endforeach
                                        </select>
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
                                    <div class="form-group" id="kecamatan_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Kecamatan<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('kecamatan_pisah') is-invalid @enderror" name="kecamatan_pisah" id="kecamatan_pisah"  style="width: 100%" required aria-placeholder="Pilih Kecamatan">
                                            <option value="">Pilih Kecamatan</option>
                                            @foreach($kecamatans_penduduk as $kec)
                                                <option value="{{ $kec->id }}" @if($kec->id == $kecamatan_penduduk->id) selected @endif>{{ $kec->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                        @error('kecamatan_pisah')
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
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" id="desa_div" @if($pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required disabled>
                                            <option value="">Pilih Desa/Kelurahan</option>
                                            @foreach($desas as $des)
                                                <option value="{{ $des->id }}" @if($des->id == $desa->id) selected @endif>{{ $des->name }}</option>
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
                                    <div class="form-group" id="desa_pisah_div" @if(!$pisah_tinggal) style="display:none;" @endif>
                                        <label for="title" class="small">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('desa_pisah') is-invalid @enderror" name="desa_pisah" id="desa_pisah"  style="width: 100%" required aria-placeholder="Pilih Desa">
                                            <option value="">Pilih Desa/Kelurahan</option>
                                            @foreach($desas_penduduk as $des)
                                                <option value="{{ $des->id }}" @if($des->id == $desa_penduduk->id) selected @endif>{{ $des->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                        @error('desa_pisah')
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
                                        <label for="title" class="small">Tanggal Registrasi<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_registrasi') is-invalid @enderror" placeholder="Masukkan Tanggal Registrasi" name="tanggal_registrasi" id="tanggal_registrasi" value="{{ old('tanggal_registrasi',date('d M Y', strtotime($anggota_krama->tanggal_registrasi))) }}" placeholder="Masukkan Tanggal Registrasi" required>
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
                            </div>

                            <hr class="my-4" />
                            <div class="float-right">
                                <a class="btn btn-danger mr-2 btn-icon-split text-end" href="{{ route('banjar-krama-mipil-detail', $krama_mipil->id) }}">
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


    {{-- LEAFLET --}}
    <div class="modal fade" id="pick_koordinat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Koordinat Alamat</h5>
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
                <button type="button" id="modal-close" class="btn btn-danger" data-dismiss="modal">Batal</button>
                <button type="button" onclick="pick_koordinat()" class="btn btn-primary">Simpan</button>
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
        @if(old('jenis_kependudukan') == 'adat')
            <script>
                $("#jenis_kependudukan").prop('disabled', false);
                $("#banjar_adat_id").prop('required', true);
                $("#tempekan_row").show();

                $("#banjar_dinas_id").prop('required', false);
                $("#banjar_dinas_row").hide();
            </script>
        @else
            <script>
                $("#jenis_kependudukan").prop('disabled', false);
                $("#banjar_adat_id").prop('required', true);
                $("#tempekan_row").show();

                $("#banjar_dinas_id").prop('required', true);
                $("#tempekan_row").show();
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

        //ADD CONTROLL
        map.pm.addControls({  
            position: 'topleft',
            drawCircle: false,
            drawMarker: false,
            drawCircleMarker:false,
            drawRectangle: false,
            drawPolyline: false,
            drawPolygon: false,
            drawText: false,
            dragMode:false,
            editMode: false,
            cutPolygon: false,
            removalMode: true,
            rotateMode: false,
        });

        var marker;
        map.on('click', function(e){
            if (marker) { // check
                map.removeLayer(marker); // remove
            }
            marker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map);
            var koor = {lat:e.latlng.lat, lng:e.latlng.lng};
            document.getElementById('modal_koordinat_alamat_placeholder').value=e.latlng.lat+', '+e.latlng.lng;
            document.getElementById('modal_koordinat_alamat').value=JSON.stringify(koor);

            marker.on('dragend', function (e) {
                var koor = marker.getLatLng();
                document.getElementById('modal_koordinat_alamat_placeholder').value = marker.getLatLng().lat+', '+marker.getLatLng().lng;
                document.getElementById('modal_koordinat_alamat').value = JSON.stringify(koor);
            });
        });

        var penduduk = {!! json_encode($penduduk) !!}
        if(penduduk.koordinat_alamat){
            let koor = JSON.parse(penduduk.koordinat_alamat);
            let koor_placeholder = koor.lat+', '+koor.lng;
            $("#koordinat_alamat_placeholder_pisah").val(koor_placeholder);
            $("#koordinat_alamat_pisah").val(JSON.stringify(koor));
            $("#modal_koordinat_alamat_placeholder").val(koor_placeholder);
            $("#modal_koordinat_alamat").val(JSON.stringify(koor));

            var marker;
            if (marker) { // check
                map.removeLayer(marker); // remove
            }
            marker = L.marker([koor.lat, koor.lng], {
                draggable: true
            }).addTo(map);
            document.getElementById('modal_koordinat_alamat_placeholder').value=koor.lat+', '+koor.lng;
            document.getElementById('modal_koordinat_alamat').value=JSON.stringify(koor);

            marker.on('dragend', function (e) {
                var koor = marker.getLatLng();
                document.getElementById('modal_koordinat_alamat_placeholder').value = marker.getLatLng().lat+', '+marker.getLatLng().lng;
                document.getElementById('modal_koordinat_alamat').value = JSON.stringify(koor);
            });
        }

        function map_modal(){
            $('#pick_koordinat_modal').on('shown.bs.modal', function() {
                map.invalidateSize();
            });
            $('#pick_koordinat_modal').modal('show');
        }

        function pick_koordinat(){
            var latlng_placeholder = $('#modal_koordinat_alamat_placeholder').val();
            var latlng = $('#modal_koordinat_alamat').val();
            $('#koordinat_alamat_placeholder_pisah').val(latlng_placeholder);
            $('#koordinat_alamat_pisah').val(latlng);
            $('#pick_koordinat_modal').modal('hide');
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

            //Pisah Tinggal on Check
            $("#pisah_tinggal").click(function(){
                if($("#pisah_tinggal").is(':checked') ){
                    //ALAMAT
                    $('#alamat').prop('required', false);
                    $('#alamat_pisah').prop('required', true);
                    $('#alamat_div').hide();
                    $('#alamat_pisah_div').show();
                    //KOORDINAT
                    $('#koordinat_div').hide();
                    $('#koordinat_pisah_div').show();
                    //PROVINSI
                    $('#provinsi').prop('required', false);
                    $('#provinsi_pisah').prop('required', true);
                    $('#provinsi_div').hide();
                    $('#provinsi_pisah_div').show();
                    //KABUPATEN
                    $('#kabupaten').prop('required', false);
                    $('#kabupaten_pisah').prop('required', true);
                    $('#kabupaten_div').hide();
                    $('#kabupaten_pisah_div').show();
                    //KECAMATAN
                    $('#kecamatan').prop('required', false);
                    $('#kecamatan_pisah').prop('required', true);
                    $('#kecamatan_div').hide();
                    $('#kecamatan_pisah_div').show();
                    //DESA
                    $('#desa').prop('required', false);
                    $('#desa_pisah').prop('required', true);
                    $('#desa_div').hide();
                    $('#desa_pisah_div').show();
                } else {
                    //ALAMAT
                    $('#alamat_pisah').prop('required', false);
                    $('#alamat').prop('required', false);
                    $('#alamat_pisah_div').hide();
                    $('#alamat_div').show();
                    //KOORDINAT
                    $('#koordinat_pisah_div').hide();
                    $('#koordinat_div').show();
                    //PROVINSI
                    $('#provinsi_pisah').prop('required', false);
                    $('#provinsi').prop('required', false);
                    $('#provinsi_pisah_div').hide();
                    $('#provinsi_div').show();
                    //KABUPATEN
                    $('#kabupaten_pisah').prop('required', false);
                    $('#kabupaten').prop('required', false);
                    $('#kabupaten_pisah_div').hide();
                    $('#kabupaten_div').show();
                    //KECAMATAN
                    $('#kecamatan_pisah').prop('required', false);
                    $('#kecamatan').prop('required', false);
                    $('#kecamatan_pisah_div').hide();
                    $('#kecamatan_div').show();
                    //DESA
                    $('#desa_pisah').prop('required', false);
                    $('#desa').prop('required', false);
                    $('#desa_pisah_div').hide();
                    $('#desa_div').show();
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
            //Daerah On Change
            $('#provinsi_pisah').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kabupaten_pisah').empty();
                            $('#kabupaten_pisah').append('<option value="">Pilih Kabupaten</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten_pisah').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten_pisah').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_pisah').empty();
                            $('#kecamatan_pisah').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_pisah').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_pisah').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_pisah').empty();
                            $('#desa_pisah').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa_pisah').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#search_button').on('click', function(){
                if($("#nik").val().length != 16){
                    $("#nik-validate").show();
                }else{
                    var url = "{{ route('banjar-krama-mipil-get-penduduk', ":nik") }}";
                    url = url.replace(':nik', $("#nik").val());
                    $("#nik-validate").fadeOut();
                    $("#overlay").css('display', 'flex');
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            if(result.status == 'ditemukan'){
                                $("#nik").prop('readonly', true);
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
                                    $("#ayah_kandung").empty();
                                    $("#ayah_kandung").append('<option value="'+result.penduduk.ayah.id+'">'+result.penduduk.ayah.nama+'</option>');
                                }
                                if(result.penduduk.ibu_kandung_id){
                                    $("#ibu_kandung").empty();
                                    $("#ibu_kandung").append('<option value="'+result.penduduk.ibu.id+'">'+result.penduduk.ibu.nama+'</option>');
                                }
                                $('#alamat_pisah').val(result.penduduk.alamat);
                                if(result.penduduk.foto != null){
                                    $('#propic').attr("src", result.penduduk.foto);
                                    $('#image-preview').attr("src", result.penduduk.foto);
                                } 
                                $('#provinsi_pisah').empty();
                                result.provinsis.forEach(element => {
                                    var prov = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.provinsi.id){
                                        prov = prov + ' selected'
                                    }
                                    prov = prov + '>' + element['name'] + '</option>'
                                    $('#provinsi_pisah').append(prov);
                                });

                                $('#kabupaten_pisah').empty();
                                result.kabupatens.forEach(element => {
                                    var kab = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.kabupaten.id){
                                        kab = kab + ' selected'
                                    }
                                    kab = kab + '>' + element['name'] + '</option>'
                                    $('#kabupaten_pisah').append(kab);
                                });

                                $('#kecamatan_pisah').empty();
                                result.kecamatans.forEach(element => {
                                    var kec = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.kecamatan.id){
                                        kec = kec + ' selected'
                                    }
                                    kec = kec + '>' + element['name'] + '</option>'
                                    $('#kecamatan_pisah').append(kec);
                                });

                                $('#desa_pisah').empty();
                                result.desas.forEach(element => {
                                    var des = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.desa.id){
                                        des = des + ' selected'
                                    }
                                    des = des + '>' + element['name'] + '</option>'
                                    $('#desa_pisah').append(des);
                                });

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
                                if(result.penduduk.koordinat_alamat){
                                    let koor = JSON.parse(result.penduduk.koordinat_alamat);
                                    let koor_placeholder = koor.lat+', '+koor.lng;
                                    $("#koordinat_alamat_placeholder_pisah").val(koor_placeholder);
                                    $("#koordinat_alamat_pisah").val(JSON.stringify(koor));
                                    $("#modal_koordinat_alamat_placeholder").val(koor_placeholder);
                                    $("#modal_koordinat_alamat").val(JSON.stringify(koor));

                                    var marker;
                                    if (marker) { // check
                                        map.removeLayer(marker); // remove
                                    }
                                    marker = L.marker([koor.lat, koor.lng], {
                                        draggable: true
                                    }).addTo(map);
                                    document.getElementById('modal_koordinat_alamat_placeholder').value=koor.lat+', '+koor.lng;
                                    document.getElementById('modal_koordinat_alamat').value=JSON.stringify(koor);

                                    marker.on('dragend', function (e) {
                                        var koor = marker.getLatLng();
                                        document.getElementById('modal_koordinat_alamat_placeholder').value = marker.getLatLng().lat+', '+marker.getLatLng().lng;
                                        document.getElementById('modal_koordinat_alamat').value = JSON.stringify(koor);
                                    });
                                }
                                $("input").prop('readonly', false);
                                $("select").prop('disabled', false);
                                $("#nama_tercetak").prop('readonly', true);
                                $("#jenis_kependudukan").prop('disabled', false);
                                $("#banjar_adat_id").prop('disabled', false);
                                $("#banjar_dinas_id").prop('disabled', false);
                                $("#koordinat_alamat_placeholder").prop('readonly', true);
                                $("#koordinat_alamat_placeholder_pisah").prop('readonly', true);
                                $('#pisah_tinggal').prop('disabled', false);
                                $('#cari_ayah_manual').prop('disabled', false);
                                $('#cari_ibu_manual').prop('disabled', false);
                                //SET DATA KRAMA MIPIL DISABLED
                                $("#nomor_krama_mipil").prop('readonly', true);
                                $("#nama_krama_mipil").prop('readonly', true);
                                $("#tanggal_registrasi_krama_mipil").prop('readonly', true);
                                $("#alamat").prop('readonly', true);
                                $("#provinsi").prop('disabled', true);
                                $("#kabupaten").prop('disabled', true);
                                $("#kecamatan").prop('disabled', true);
                                $("#desa").prop('disabled', true);
                                $("#overlay").fadeOut();
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Penduduk ditemukan'
                                })
                            }else if(result.status == 'tidak_ditemukan'){
                                var nik = $("#nik").val();
                                $("input").prop('readonly', false);
                                $(".form-control").val('');
                                $("option ").attr('selected', false);
                                $("select").prop('disabled', false);
                                $("#nik").val(nik);
                                $("#nama_tercetak").prop('readonly', true);
                                $("#koordinat_alamat_placeholder").prop('readonly', true);
                                $("#koordinat_alamat_placeholder_pisah").prop('readonly', true);
                                $('#pisah_tinggal').prop('disabled', false);
                                $('#cari_ayah_manual').prop('disabled', false);
                                $('#cari_ibu_manual').prop('disabled', false);
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
            $('#nav-link-cacah-krama-mipil').addClass('active');
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