@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner_center.css')}}" rel="stylesheet" />
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
@section('title', 'Edit Maperas')
@section('content')
<main>
    <header class="page-header page-header-light pb-10">
        <div class="container">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-people-arrows mr-2"></i></div>
                            Manajemen Maperas
                        </h1>
                    </div>
                </div>
                <ol class="breadcrumb mb-0 mt-4">
                    <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('banjar-maperas-home') }}" class="text-decoration-none text-dark">Maperas</a></li>
                    <li class="breadcrumb-item active text-red-pastel">Edit Maperas</li>
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
                            <div class="wizard-step-text-name text-dark">Anak dan Orang Tua Lama</div>
                            <div class="wizard-step-text-details text-dark">Data anak dan orang tua yang akan maperas</div>
                        </div>
                    </a>
                    <!-- Wizard navigation item 2-->
                    <a class="nav-item nav-link" id="wizard2-tab" href="#wizard2" data-toggle="tab" role="tab" aria-controls="wizard2" aria-selected="true">
                        <div class="wizard-step-icon">2</div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Orang Tua Baru</div>
                            <div class="wizard-step-text-details text-dark">Data orang tua baru yang melakukan maperas</div>
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
                <form id="form-edit-maperas" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf 
                    <div class="tab-content" id="cardTabContent">
                        <!-- Wizard tab pane item 1-->
                        <div class="tab-pane mt-5 fade show active" id="wizard1" role="tabpanel" aria-labelledby="wizard1-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-11">
                                    <h3 class="text-primary">Langkah 1</h3>
                                    <h5 class="card-title">Masukkan Data Anak dan Orang Tua Lama</h5>

                                    {{-- NIK - FOTO --}}
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-12">
                                            <div class="row">
                                                <div class="col-lg-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="title" class="small">NIK<span class="text-danger small">*</span></label>
                                                        <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik', $penduduk->nik) }}" required>
                                                        @error('nik')
                                                            <div class="invalid-feedback text-start">
                                                                {{ $message }}
                                                            </div>
                                                        @else
                                                            <div class="invalid-feedback">
                                                                NIK wajib diisi
                                                            </div>
                                                        @enderror
                                                        <small class="text-danger" id="nik-validate" style="display:none;">
                                                            NIK harus terdiri dari 16 digit angka
                                                        </small>
                                                    </div>
                                                </div>
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
                                            </div>
                                            <div class="row">
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
                                            </div>
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Tercetak<span class="font-italic small"> (*dengan gelar jika ada)</span></label>
                                                <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ old('nama_tercetak', $penduduk->nama_tercetak) }}" readonly>
                                            </div>

                                        </div>
                                        <div class="col-lg-4 col-sm-12 text-center">
                                            <label for="foto" class="text-white">Foto</label>
                                            <br>
                                            <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                            <div id="img_container">
                                                <img src="@if($penduduk->foto != NULL) {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
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
                                                    @if(old('golongan_darah'))
                                                        <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
                                                        <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                                        <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                                        <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                                        <option value="O"  @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                                    @else
                                                        <option value="-"  @if($penduduk->golongan_darah == '-') selected @endif>-</option>
                                                        <option value="A" @if($penduduk->golongan_darah == 'A') selected @endif>A</option>
                                                        <option value="B" @if($penduduk->golongan_darah == 'B') selected @endif>B</option>
                                                        <option value="AB" @if($penduduk->golongan_darah == 'AB') selected @endif>AB</option>
                                                        <option value="O"  @if($penduduk->golongan_darah == 'O') selected @endif>O</option>
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
                                                        <option value="islam" @if($maperas->agama_lama == 'islam') selected @endif>Islam</option>
                                                        <option value="protestan" @if($maperas->agama_lama == 'protestan') selected @endif>Protestan</option>
                                                        <option value="katolik" @if($maperas->agama_lama == 'katolik') selected @endif>Katolik</option>
                                                        <option value="hindu" @if($maperas->agama_lama == 'hindu') selected @endif>Hindu</option>
                                                        <option value="buddha" @if($maperas->agama_lama == 'buddha') selected @endif>Buddha</option>
                                                        <option value="khonghucu" @if($maperas->agama_lama == 'khonghucu') selected @endif>Khonghucu</option>
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
                                                            <option value="{{ $pekerjaan->id }}" @if(old('pekerjaan') == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                                        @endforeach
                                                    @else 
                                                        @foreach($pekerjaans as $pekerjaan)
                                                            <option value="{{ $pekerjaan->id }}" @if($penduduk->profesi_id == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
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
                                                <input type="text" class="can-empty form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon', $penduduk->telepon) }}">
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
                                        <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat Asal" value="{{ old('alamat', $maperas->alamat_asal) }}" required>
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
                                                    @foreach($provinsis as $provinsi)
                                                        <option value="{{ $provinsi->id }}" @if($provinsi_asal->id == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
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
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Kabupaten/Kota Asal<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                                    <option value="">Pilih Kabupaten/Kota Asal</option>
                                                    @foreach($kabupaten_asals as $kabupaten)
                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten_asal->id == $kabupaten->id) selected @endif>{{ $kabupaten->name }}</option>
                                                    @endforeach
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
                                                    @foreach($kecamatan_asals as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" @if($kecamatan_asal->id == $kecamatan->id) selected @endif>{{ $kecamatan->name }}</option>
                                                    @endforeach
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
                                                    @foreach($desa_asals as $desa)
                                                        <option value="{{ $desa->id }}" @if($desa_asal->id == $desa->id) selected @endif>{{ $desa->name }}</option>
                                                    @endforeach
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
                                                <input type="text" class="form-control @error ('nik_ayah') is-invalid @enderror"  id="nik_ayah" name="nik_ayah" placeholder="Masukkan NIK Ayah" value="{{ old('nik_ayah', $maperas->nik_ayah_lama) }}">
                                                @error('nik_ayah')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        NIK Ayah wajib diisi
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate-ayah" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Ayah</label>
                                                <input type="text" class="form-control @error ('nama_ayah') is-invalid @enderror" id="nama_ayah" name="nama_ayah" placeholder="Masukkan Nama Ayah" value="{{ old('nama_ayah', $maperas->nama_ayah_lama) }}">
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
                                                <input type="text" class="form-control @error ('nik_ibu') is-invalid @enderror"  id="nik_ibu" name="nik_ibu" placeholder="Masukkan NIK Ibu" value="{{ old('nik_ibu', $maperas->nik_ibu_lama) }}">
                                                @error('nik_ibu')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        NIK Ibu wajib diisi
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate-ibu" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Ibu</label>
                                                <input type="text" class="form-control @error ('nama_ibu') is-invalid @enderror" id="nama_ibu" name="nama_ibu" placeholder="Masukkan Nama Ibu" value="{{ old('nama_ibu', $maperas->nama_ibu_lama) }}">
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
                                                    <input type="file" class="custom-file-input @error('file_sudhi_wadhani') is-invalid @enderror" id="file_sudhi_wadhani" name="file_sudhi_wadhani" accept=".pdf,.doc">
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
                                                @if($maperas->file_sudhi_wadhani != NULL)   
                                                    <a class="text-start text-primary small" href="{{$maperas->file_sudhi_wadhani}}"><p><i class="fas fa-download"></i> Unduh File Sudhi Wadhani</p></a>
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
                        <div class="tab-pane mt-5 fade" id="wizard2" role="tabpanel" aria-labelledby="wizard2-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-10">
                                    <h3 class="text-primary">Langkah 2</h3>
                                    <h5 class="card-title">Masukkan Data Orang Tua Baru dan Data Maperas</h5>

                                    <div class="form-group">
                                        <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('krama_mipil_baru_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_placeholder" name="krama_mipil_baru_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_placeholder', $krama_mipil_baru->cacah_krama_mipil->penduduk->nama) }}" required readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_modal()">
                                                    <span class="text">Pilih Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('krama_mipil_baru') is-invalid @enderror"  id="krama_mipil_baru" name="krama_mipil_baru"  value="{{ old('krama_mipil_baru', $krama_mipil_baru->id) }}" required hidden>
                                        @error('krama_mipil_baru')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Krama Mipil Baru wajib dipilih
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="ayah_baru" class="small">Ayah Baru<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('ayah_baru') is-invalid @enderror" name="ayah_baru" id="ayah_baru"  style="width: 100%" required>
                                                    <option value="">Pilih Ayah Baru</option>
                                                    @if(old('ayah_baru'))
                                                        @foreach($ayah_baru as $ayah)
                                                            <option value="{{ $ayah->id }}" @if(old('ayah_baru') == $ayah->id) selected @endif>{{ $ayah->penduduk->nama }}</option>
                                                        @endforeach
                                                    @else 
                                                        @foreach($ayah_baru as $ayah)
                                                            <option value="{{ $ayah->id }}" @if($maperas->ayah_baru_id == $ayah->id) selected @endif>{{ $ayah->penduduk->nama }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('ayah_baru')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Ayah Baru wajib dipilih
                                                    </div>
                                                @enderror 
                                                <small class="small">(Pilih Krama Mipil Baru terlebih dahulu)</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="ibu_baru" class="small">Ibu Baru<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('ibu_baru') is-invalid @enderror" name="ibu_baru" id="ibu_baru"  style="width: 100%" required>
                                                    <option value="">Pilih Ibu Baru</option>
                                                    @if(old('ibu_baru'))
                                                        @foreach($ibu_baru as $ibu)
                                                            <option value="{{ $ibu->id }}" @if(old('ibu_baru') == $ibu->id) selected @endif>{{ $ibu->penduduk->nama }}</option>
                                                        @endforeach
                                                    @else 
                                                        @foreach($ibu_baru as $ibu)
                                                            <option value="{{ $ibu->id }}" @if($maperas->ibu_baru_id == $ibu->id) selected @endif>{{ $ibu->penduduk->nama }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('ibu_baru')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Ibu wajib dipilih
                                                    </div>
                                                @enderror 
                                                <small class="small">(Pilih Krama Mipil Baru terlebih dahulu)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="tanggal_maperas" class="small">Tanggal Maperas<span class="text-danger small">*</span></label>
                                                <input type="text" class="datepicker-here form-control @error ('tanggal_maperas') is-invalid @enderror" placeholder="Masukkan Tanggal Maperas" name="tanggal_maperas" id="tanggal_maperas" value="{{ old('tanggal_maperas', date('d M Y', strtotime($maperas->tanggal_maperas))) }}" required>
                                                @error('tanggal_maperas')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Tanggal Maperas wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Pemuput Maperas<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nama_pemuput') is-invalid @enderror"  id="nama_pemuput" name="nama_pemuput" placeholder="Masukkan Nama Pemuput Maperas" value="{{ old('nama_pemuput', $maperas->nama_pemuput) }}" required>
                                                @error('nama_pemuput')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Pemuput Maperas wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Bukti Maperas<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nomor_bukti_maperas') is-invalid @enderror"  id="nomor_bukti_maperas" name="nomor_bukti_maperas" placeholder="Masukkan No. Bukti Maperas" value="{{ old('nomor_bukti_maperas', $maperas->nomor_maperas) }}" required>
                                                @error('nomor_bukti_maperas')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        No. Bukti Maperas wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="file_bukti_maperas" class="small">File Bukti Maperas<span class="text-danger small">*</span></label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_bukti_maperas') is-invalid @enderror" id="file_bukti_maperas" name="file_bukti_maperas" accept=".pdf,.jpg">
                                                    <label for="file_bukti_maperas_label" id="file_bukti_maperas_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_bukti_maperas')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Bukti Maperas wajib diisi
                                                        </div>
                                                    @enderror
                                                    @if($maperas->file_bukti_maperas != NULL)   
                                                        <a class="text-start text-primary small" href="{{$maperas->file_bukti_maperas}}"><p><i class="fas fa-download"></i> Unduh File Bukti Maperas</p></a>
                                                    @endif
                                                </div>
                                                <div id="validasi-file_bukti_maperas" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Bukti Maperas maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Akta Pengangkatan Anak</label>
                                                <input type="text" class="form-control @error ('nomor_akta_pengangkatan_anak') is-invalid @enderror"  id="nomor_akta_pengangkatan_anak" name="nomor_akta_pengangkatan_anak" placeholder="Masukkan No. Akta Pengangkatan Anak" value="{{ old('nomor_akta_pengangkatan_anak', $maperas->nomor_akta_pengangkatan_anak) }}">
                                                @error('nomor_akta_pengangkatan_anak')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="file_akta_pengangkatan_anak" class="small">File Akta Pengangkatan Anak</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_akta_pengangkatan_anak') is-invalid @enderror" id="file_akta_pengangkatan_anak" name="file_akta_pengangkatan_anak" accept=".pdf,.jpg">
                                                    <label for="file_akta_pengangkatan_anak_label" id="file_akta_pengangkatan_anak_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_akta_pengangkatan_anak')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @if($maperas->file_akta_pengangkatan_anak != NULL)   
                                                        <a class="text-start text-primary small" href="{{$maperas->file_akta_pengangkatan_anak}}"><p><i class="fas fa-download"></i> Unduh File Akta Pengangkatan Anak</p></a>
                                                    @endif
                                                </div>
                                                <div id="validasi-file_akta_pengangkatan_anak" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Akta Pengangkatan Anak maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="keterangan">Keterangan</label>
                                        <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan">{{ $maperas->keterangan }}</textarea>
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
                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_maperas({{ $maperas->id }}, '0')">
                                                <span class="icon">
                                                    <i class="fas fa-save"></i>
                                                </span>
                                                <span class="text">Simpan sebagai Draft</span>
                                            </button>

                                            <button class="btn btn-success btn-icon-split text-end" onclick="simpan_maperas({{ $maperas->id }}, '3')">
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
<div class="modal fade" id="select_krama_mipil_baru_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <table class="table table-bordered table-hover" id="dataTable-krama-mipil-baru" width="100%" cellspacing="0">
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
        $(document).ready(function() {
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-maperas').addClass('active');

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
                    if($(this).val() == "" && $(this).prop('required')) {
                        $(this).addClass("is-invalid");
                        isValid = false;
                    }else if($(this).val() != ""){
                        $(this).removeClass("is-invalid");
                        $(this).addClass("is-valid");
                    }
                    if($("#nik_ayah").val().length != 16 && $('#nik').val() != ''){
                        $("#nik-validate-ayah").show();
                        isValid = false
                    }
                    if($("#nik_ibu").val().length != 16 && $('#nik').val() != ''){
                        $("#nik-validate-ibu").show();
                        isValid = false
                    }
                    if($("#nik").val().length != 16 && $('#nik').val() != ''){
                        $("#nik-validate").show();
                        isValid = false
                    }
                });
                $('#wizard1 select').each(function () {
                    if($(this).prop('required')){
                        if ($(this).val() == "") {
                            $("#wizard1").addClass("was-validated");
                            $(this).addClass("is-invalid");
                            isValid = false;
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
            $('#nik_ayah').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik_ayah").val().length == 16){
                    $("#nik-validate-ayah").fadeOut();
                }
            });
            $('#nik_ibu').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik_ibu").val().length == 16){
                    $("#nik-validate-ibu").fadeOut();
                }
            });

            //VALIDASI LAMPIRAN
            $("#lampiran").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-lampiran').show();
                    $('#lampiran').val("");
                }else{
                    document.getElementById('lampiran_label').innerHTML = document.getElementById('lampiran').files[0].name;
                    $('#validasi-lampiran').hide();
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

            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //DatePicker
            $("#tanggal_lahir").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            $("#tanggal_maperas").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            //Daerah On Change
            $('#provinsi').on('change', function(){
                $('#kabupaten').empty();
                $('#kabupaten').append('<option value="">Pilih Kabupaten/Kota Asal</option>');
                $('#kecamatan').empty();
                $('#kecamatan').append('<option value="">Pilih Kecamatan Asal</option>');
                $('#desa').empty();
                $('#desa').append('<option value="">Pilih Desa/Kelurahan Asal</option>');
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#kabupaten').empty();
                            $('#kabupaten').append('<option value="">Pilih Kabupaten/Kota Asal</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten').on('change', function(){
                $('#kecamatan').empty();
                $('#kecamatan').append('<option value="">Pilih Kecamatan Asal</option>');
                $('#desa').empty();
                $('#desa').append('<option value="">Pilih Desa/Kelurahan Asal</option>');
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#kecamatan').empty();
                            $('#kecamatan').append('<option value="">Pilih Kecamatan Asal</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan').on('change', function(){
                $('#desa').empty();
                $('#desa').append('<option value="">Pilih Desa/Kelurahan Asal</option>');
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#desa').empty();
                            $('#desa').append('<option value="">Pilih Desa/Kelurahan Asal</option>');
                            result['0'].forEach(element => {
                                $('#desa').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });
        });

        //Datatable child
        function format_lama ( d ) {
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
                var table_krama_mipil_baru = $('#dataTable-krama-mipil-baru');
                var oTable_krama_mipil_baru = table_krama_mipil_baru.DataTable({
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
                        url : "{{ route('banjar-maperas-datatable-krama-mipil-baru') }}"
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_krama_mipil', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.penduduk.nama', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.penduduk.tempat_lahir', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.penduduk.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap", orderable: false },
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

                filter_krama_mipil_baru = () => {
                    oTable_krama_mipil_baru.ajax.reload();
                }

                $('#dataTable-krama-mipil-baru tbody').on('click', 'td.dt-control', function () {
                    var tr = $(this).closest('tr');
                    var row = oTable_krama_mipil_baru.row( tr );
            
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).html('<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>');
                    }
                    else {
                        // Open this row
                        row.child( format_lama(row.data()) ).show();
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

        //GET DATA BARU
        function pilih_krama_mipil_baru_modal(){
            $('#select_krama_mipil_baru_modal').on('show.bs.modal', function(e) {
                filter_krama_mipil_baru();
            }).modal('show');
        }

        function pilih_krama_mipil_baru(id, nama){
            $('#krama_mipil_baru').val(id);
            $('#krama_mipil_baru_placeholder').val(nama);
            $('#krama_mipil_baru_placeholder').prop('readonly', true);
            $('#select_krama_mipil_baru_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Mipil Baru Berhasil Dipilih'
            });
            var url = "{{ route('banjar-maperas-get-orangtua-baru-anak', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    //SET AYAH BARU
                    $('#ayah_baru').empty();
                    $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                    result.ayah.forEach(element=>{
                        $('#ayah_baru').append('<option value="'+element.id+'">'+element.penduduk.nama+'</option>');
                    });

                    //SET IBU BARU
                    $('#ibu_baru').empty();
                    $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');
                    result.ibu.forEach(element=>{
                        $('#ibu_baru').append('<option value="'+element.id+'">'+element.penduduk.nama+'</option>');
                    });
                }
            });
        }
        //GET DATA BARU

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

            //SET CALON KK
            var id_purusa = $('#purusa').val();
            if(id_purusa){
                get_calon_kepala_keluarga();
            }else{
                $('#calon_kepala_keluarga').empty();
                $('#calon_kepala_keluarga').append('<option value="">Pilih Kepala Keluarga</option>');
                $('#calon_kepala_keluarga').append('<option value="pradana">'+gelar_depan+nama+gelar_belakang+'</option>');
            }
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

        //xFungsi Simpan DraftSah
        function simpan_maperas(id, status){
            var url = "{{ route('banjar-maperas-campuran-masuk-update', [":id", ":status"]) }}";
            url = url.replace(':status', status);
            url = url.replace(':id', id);
            $("#form-edit-maperas").attr("action", url);
            $('#form-edit-maperas').submit(function (e){
                e.stopPropagation();
            });
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
    </script>
@endpush