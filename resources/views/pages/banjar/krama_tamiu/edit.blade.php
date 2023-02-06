@extends('layouts.banjar.banjar')
@push('css')
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endpush
@section('title', 'Edit Krama Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Krama Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-krama-tamiu-home') }}" class="text-decoration-none text-dark">Krama Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Krama Tamiu</li>
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
                                <div class="wizard-step-text-name text-dark">Data Krama Tamiu</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Krama Tamiu Berikut Ini</div>
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
                    <form id="form-create-krama-mipil" method="post" action="{{ route('banjar-krama-tamiu-update', $krama->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="mx-5 justify-content-center">
                            {{-- NIK - FOTO --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nomor Krama Tamiu</label>
                                                <input type="text" class="form-control @error ('nomor_krama_tamiu') is-invalid @enderror"  id="nomor_krama_tamiu" name="nomor_krama_tamiu" placeholder="Masukkan No. Krama Tamiu" value="{{ $krama->nomor_krama_tamiu }}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">NIK<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik', $penduduk->nik) }}" required @if($penduduk->nik != NULL) readonly @endif>
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
                                        <img src="@if($penduduk->foto != NULL) {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
                                        {{-- <button type="button" class="btn btn-primary btn-icon-split mt-1" id="btn-foto" data-target="#crop-image" data-toggle="modal">
                                            <span class="icon">
                                                <i class="fas fa-images"></i>
                                            </span>
                                            <span class="text">Pilih Foto</span>
                                        </button> --}}
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
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', date('d M Y', strtotime($penduduk->tanggal_lahir))) }}" placeholder="Masukkan Tanggal Lahir" required>
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
                                        <label for="title" class="small">Ayah</label>
                                        <select class="can-empty select2 custom-select select-krama @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung" style="width: 100%">
                                            @if($penduduk->ayah != '')
                                                <option value="{{ $penduduk->ayah->id }}">{{ $penduduk->ayah->nama }}</option>
                                            @else 
                                                <option value="">Cari Ayah</option>
                                            @endif                                             </select>
                                        @error('ayah_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group" class="small">
                                        <label for="title" class="small">Ibu</label>
                                        <select class="can-empty select2 custom-select select-krama @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%">
                                            @if($penduduk->ibu != '')
                                                <option value="{{ $penduduk->ibu->id }}">{{ $penduduk->ibu->nama }}</option>
                                            @else 
                                                <option value="">Cari Ibu</option>
                                            @endif                                          </select>                                
                                        @error('ibu_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- ALAMAT --}}
                            <div class="form-group">
                                <label for="title" class="small">Alamat Tempat Tinggal<span class="text-danger small">*</span></label>
                                <input type="text" class="can-empty form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat', $penduduk->alamat) }}" required>
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

                            {{-- KOORDINAT - KELURAHAN/DESA --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        @if($krama->cacah_krama_tamiu->penduduk->koordinat_alamat)
                                            @php 
                                                $koor = $krama->cacah_krama_tamiu->penduduk->koordinat_alamat;
                                                $koor_placeholder = json_decode($krama->cacah_krama_tamiu->penduduk->koordinat_alamat);
                                                $koor_placeholder = $koor_placeholder->lat.', '.$koor_placeholder->lng;
                                            @endphp
                                        @else 
                                            @php 
                                                $koor = '';
                                                $koor_placeholder = '';
                                                $koor_placeholder = '';
                                            @endphp
                                        @endif
                                        <label for="koordinat" class="small">Koordinat Alamat Tempat Tinggal</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder" id="koordinat_alamat_placeholder" placeholder="Pilih Koordinat" value="{{ old('koordinat_alamat_placeholder', $koor_placeholder) }}">
                                            <input type="text" hidden class="form-control" name="koordinat_alamat" id="koordinat_alamat" value="{{ old('koordinat_alamat', $koor) }}">
                                            <div class="input-group-append">
                                                <button type="button" class="can-empty btn btn-primary" onclick="map_modal()" data-toggle="tooltip" id="btn_pick_koordinat" data-placement="top" title="" data-original-title="Cari koordinat"><i class="fas fa-map-marker-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Desa/Kelurahan Tempat Tinggal<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required>
                                            <option value="">Pilih Desa/Kelurahan</option>
                                            @if(old('desa'))
                                                @foreach($desa_sekitars as $desa)
                                                    <option value="{{ $desa->id }}" @if(old('desa') == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                @endforeach
                                            @else 
                                                @foreach($desa_sekitars as $desa)
                                                    <option value="{{ $desa->id }}" @if($penduduk->desa_id == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                @endforeach
                                            @endif
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

                            {{-- BANJAR DINAS - ASAL --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Banjar Dinas<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required>
                                            <option value="">Pilih Banjar Dinas</option>
                                            @if(old('banjar_dinas_id'))
                                                @foreach($banjar_dinas as $dinas)
                                                    <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                                @endforeach
                                            @else 
                                                @foreach($banjar_dinas as $dinas)
                                                    <option value="{{ $dinas->id }}" @if($krama->cacah_krama_tamiu->banjar_dinas_id == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                                @endforeach
                                            @endif
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
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tanggal Registrasi<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_masuk') is-invalid @enderror" placeholder="Masukkan Tanggal Registrasi" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk', date('d M Y', strtotime($krama->tanggal_registrasi))) }}" placeholder="Masukkan Tanggal Masuk Krama Tamiu" required>
                                        @error('tanggal_masuk')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Tanggal Masuk wajib diisi
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Asal<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('asal') is-invalid @enderror" name="asal" id="asal"  style="width: 100%" required aria-placeholder="Pilih Asal" required>
                                            <option value="">Pilih Asal</option>
                                            @if(old('asal'))
                                                <option value="dalam_bali" @if(old('asal') == 'dalam_bali') selected @endif>Dalam Bali</option>
                                                <option value="luar_bali" @if(old('asal') == 'luar_bali') selected @endif>Luar Bali</option>
                                            @else 
                                                <option value="dalam_bali" @if($krama->cacah_krama_tamiu->asal == 'dalam_bali') selected @endif>Dalam Bali</option>
                                                <option value="luar_bali" @if($krama->cacah_krama_tamiu->asal == 'luar_bali') selected @endif>Luar Bali</option>
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

                            {{-- ALAMAT ASAL --}}
                            <div class="form-group" id="alamat_asal_row">
                                <label for="title" class="small">Alamat Asal<span class="text-danger small">*</span></label>
                                <input type="text" class="can-empty form-control @error ('alamat_asal') is-invalid @enderror"  id="alamat_asal" name="alamat_asal" placeholder="Masukkan Alamat Asal" value="{{ old('alamat_asal', $krama->cacah_krama_tamiu->alamat_asal) }}" required>
                                @error('alamat_asal')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Alamat Asal wajib diisi
                                    </div>
                                @enderror
                            </div>

                            @if($krama->cacah_krama_tamiu->asal == 'dalam_bali')
                                {{-- ASAL LUAR BALI --}}
                                <div id="asal_luar_bali" @if($krama->cacah_krama_tamiu->asal == 'dalam_bali') style="display: none;" @endif>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Provinsi Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('provinsi_asal') is-invalid @enderror" name="provinsi_asal" id="provinsi_asal"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                    <option value="">Pilih Provinsi</option>
                                                </select>
                                                @error('provinsi_asal')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kabupaten_asal') is-invalid @enderror" name="kabupaten_asal" id="kabupaten_asal"  style="width: 100%" aria-placeholder="Pilih Kabupaten">
                                                    <option value="">Pilih Kabupaten</option>
                                                </select>
                                                <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                @error('kabupaten_asal')
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kecamatan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kecamatan_asal') is-invalid @enderror" name="kecamatan_asal" id="kecamatan_asal"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                                <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                @error('kecamatan_asal')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Desa/Kelurahan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('desa_asal') is-invalid @enderror" name="desa_asal" id="desa_asal"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                    <option value="">Pilih Desa/Kelurahan</option>
                                                </select>
                                                <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                @error('desa_asal')
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
                                </div>
                                
                                {{-- ASAL DALAM BALI --}}
                                <div id="asal_dalam_bali" @if($krama->cacah_krama_tamiu->asal == 'luar_bali') style="display: none;" @endif>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kabupaten_asal_dalam_bali') is-invalid @enderror" name="kabupaten_asal_dalam_bali" id="kabupaten_asal_dalam_bali"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @foreach($kabupatens as $kabupaten)
                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten_asal->id == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kabupaten_asal_dalam_bali')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kecamatan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kecamatan_asal_dalam_bali') is-invalid @enderror" name="kecamatan_asal_dalam_bali" id="kecamatan_asal_dalam_bali"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required>
                                                    <option value="">Pilih Kecamatan</option>
                                                    @foreach($kecamatans as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" @if($kecamatan_asal->id == $kecamatan->id) selected @endif>{{ $kecamatan->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                @error('kecamatan_asal_dalam_bali')
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
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Desa Adat Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('desa_adat_asal') is-invalid @enderror" name="desa_adat_asal" id="desa_adat_asal"  style="width: 100%" required aria-placeholder="Pilih Desa Adat" required>
                                                    <option value="">Pilih Desa Adat</option>
                                                    @foreach($desa_adat as $desadat)
                                                        <option value="{{ $desadat->id }}" @if($desa_adat_asal->id == $desadat->id) selected @endif>{{ $desadat->desadat_nama }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Banjar Adat Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('banjar_adat_asal') is-invalid @enderror" name="banjar_adat_asal" id="banjar_adat_asal"  style="width: 100%" required aria-placeholder="Pilih Banjar Adat" required>
                                                    <option value="">Pilih Banjar Adat</option>
                                                    @foreach($banjar_adat as $banjaradat)
                                                        <option value="{{ $banjaradat->id }}" @if($banjar_adat_asal->id == $banjaradat->id) selected @endif>{{ $banjaradat->nama_banjar_adat }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih desa adat terlebih dahulu)</small>
                                                @error('banjar_adat_asal')
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
                                    </div>
                                </div>
                            @elseif($krama->cacah_krama_tamiu->asal == 'luar_bali')
                            {{-- ASAL LUAR BALI --}}
                                <div id="asal_luar_bali" @if($krama->cacah_krama_tamiu->asal == 'dalam_bali') style="display: none;" @endif>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Provinsi Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('provinsi_asal') is-invalid @enderror" name="provinsi_asal" id="provinsi_asal"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required>
                                                    <option value="">Pilih Provinsi</option>
                                                    @foreach($provinsis as $provinsi)
                                                        <option value="{{ $provinsi->id }}" @if($provinsi_asal->id == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('provinsi_asal')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kabupaten_asal') is-invalid @enderror" name="kabupaten_asal" id="kabupaten_asal"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @foreach($kabupatens as $kabupaten)
                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten_asal->id == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                @error('kabupaten_asal')
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kecamatan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kecamatan_asal') is-invalid @enderror" name="kecamatan_asal" id="kecamatan_asal"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required>
                                                    <option value="">Pilih Kecamatan</option>
                                                    @foreach($kecamatans as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" @if($kecamatan_asal->id == $kecamatan->id) selected @endif>{{ $kecamatan->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                @error('kecamatan_asal')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Desa/Kelurahan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('desa_asal') is-invalid @enderror" name="desa_asal" id="desa_asal"  style="width: 100%" required aria-placeholder="Pilih Desa" required>
                                                    <option value="">Pilih Desa/Kelurahan</option>
                                                    @foreach($desas as $desa)
                                                        <option value="{{ $desa->id }}" @if($desa_asal->id == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                @error('desa_asal')
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
                                </div>
                                
                                {{-- ASAL DALAM BALI --}}
                                <div id="asal_dalam_bali" @if($krama->cacah_krama_tamiu->asal == 'luar_bali') style="display: none;" @endif>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kabupaten_asal_dalam_bali') is-invalid @enderror" name="kabupaten_asal_dalam_bali" id="kabupaten_asal_dalam_bali"  style="width: 100%" aria-placeholder="Pilih Kabupaten">
                                                    <option value="">Pilih Kabupaten</option>
                                                    @foreach($kabupatens as $kabupaten)
                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten_asal->id == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kabupaten_asal_dalam_bali')
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Kecamatan Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('kecamatan_asal_dalam_bali') is-invalid @enderror" name="kecamatan_asal_dalam_bali" id="kecamatan_asal_dalam_bali"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                                <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                @error('kecamatan_asal_dalam_bali')
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
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Desa Adat Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('desa_adat_asal') is-invalid @enderror" name="desa_adat_asal" id="desa_adat_asal"  style="width: 100%" aria-placeholder="Pilih Desa Adat">
                                                    <option value="">Pilih Desa Adat</option>
                                                </select>
                                                <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title" class="small">Banjar Adat Asal<span class="text-danger small">*</span></label>
                                                <select class="can-empty select2 custom-select @error ('banjar_adat_asal') is-invalid @enderror" name="banjar_adat_asal" id="banjar_adat_asal"  style="width: 100%" aria-placeholder="Pilih Banjar Adat">
                                                    <option value="">Pilih Banjar Adat</option>
                                                </select>
                                                <small class="small">(Pilih desa adat terlebih dahulu)</small>
                                                @error('banjar_adat_asal')
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
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4" />
                            <div class="float-right">
                                <a class="btn btn-danger mr-2 btn-icon-split text-end" href="{{ route('banjar-krama-tamiu-detail', $krama->id) }}">
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
    @if($errors->has('nama') || $errors->has('nik') || $errors->has('tempat_lahir') || $errors->has('tanggal_lahir') || $errors->has('agama') || $errors->has('jenis_kelamin') || $errors->has('pendidikan') || $errors->has('pekerjaan') || $errors->has('golongan_darah') || $errors->has('alamat') || $errors->has('provinsi') || $errors->has('kabupaten') || $errors->has('kecamatan') || $errors->has('desa'))
        <script>
            $(".can-empty").prop('readonly', false);
            $(".can-empty").prop('disabled', false);
        </script>
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
            $("#koordinat_alamat_placeholder").val(koor_placeholder);
            $("#koordinat_alamat").val(JSON.stringify(koor));
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
            $('#koordinat_alamat_placeholder').val(latlng_placeholder);
            $('#koordinat_alamat').val(latlng);
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
            })
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
            $('#provinsi').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
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
                            console.log(result);
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
                            console.log(result);
                            $('#desa').empty();
                            $('#desa').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            //Asal Luar Bali On Change
            $('#provinsi_asal').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kabupaten_asal').empty();
                            $('#kabupaten_asal').append('<option value="">Pilih Kabupaten</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten_asal').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten_asal').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_asal').empty();
                            $('#kecamatan_asal').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_asal').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_asal').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_asal').empty();
                            $('#desa_asal').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa_asal').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            //Asal Dalam Bali On Change
            $('#kabupaten_asal_dalam_bali').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_asal_dalam_bali').empty();
                            $('#kecamatan_asal_dalam_bali').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_asal_dalam_bali').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_asal_dalam_bali').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-adat-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat_asal').empty();
                            $('#desa_asal_asal').append('<option value="">Pilih Desa Adat</option>');
                            result.desa_adats.forEach(element => {
                                $('#desa_adat_asal').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#desa_adat_asal').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-banjar-adat-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#banjar_adat_asal').empty();
                            $('#banjar_adat_asal').append('<option value="">Pilih Banjar Adat</option>');
                            result.banjar_adats.forEach(element => {
                                $('#banjar_adat_asal').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            //Asal On Change
            $('#asal').on('change', function(){
                if($(this).val()){
                    $('#alamat_asal_row').fadeIn();
                    if($(this).val() == 'luar_bali'){
                        $('#asal_dalam_bali').hide();
                        $('#asal_luar_bali').fadeIn();

                        //LUAR BALI
                        $('#provinsi_asal').prop('required', true);
                        $('#kabupaten_asal').prop('required', true);
                        $('#kecamatan_asal').prop('required', true);
                        $('#desa_asal').prop('required', true);

                        //DALAM BALI
                        $('#kabupaten_asal_dalam_bali').prop('required', false);
                        $('#kecamatan_asal_dalam_bali').prop('required', false);
                        $('#desa_adat_asal').prop('required', false);
                        $('#banjar_adat_asal').prop('required', false);
                    }else if($(this).val() == 'dalam_bali'){
                        $('#asal_luar_bali').hide();
                        $('#asal_dalam_bali').fadeIn();

                        //DALAM BALI
                        $('#kabupaten_asal_dalam_bali').prop('required', true);
                        $('#kecamatan_asal_dalam_bali').prop('required', true);
                        $('#desa_adat_asal').prop('required', true);
                        $('#banjar_adat_asal').prop('required', true);

                        //LUAR BALI
                        $('#provinsi_asal').prop('required', false);
                        $('#kabupaten_asal').prop('required', false);
                        $('#kecamatan_asal').prop('required', false);
                        $('#desa_asal').prop('required', false);
                    }
                }else{
                    $('#alamat_asal_row').hide();
                    $('#asal_luar_bali').hide();
                    $('#asal_dalam_bali').hide();
                }
            });

            //SELECT 2
            $(".select-krama").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    },
                    inputTooShort: function() {
                        return 'Masukkan NIK atau Nomor Induk Krama';
                    }
                },
                minimumInputLength: 16,
                ajax: {
                    url: '{{ route("api-banjar-cacah-krama-tamiu-ortu-search") }}',
                    dataType: 'json',
                },
            });

            //SIDE BAR CLASS
            $('#sidebarKrama').removeClass('collapsed');
            $('#collapseKrama').addClass('show');
            $('#collapseKrama').addClass('active');
            $('#nav-link-krama-tamiu').addClass('active');
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