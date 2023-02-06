@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner_center.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
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
                            Kelahiran
                        </h1>
                    </div>
                </div>
                <ol class="breadcrumb mb-0 mt-4">
                    <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('desa-kelahiran-home') }}" class="text-decoration-none">Kelahiran Krama</a></li>
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
                    <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
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
                            <div class="wizard-step-text-details text-dark">Orang Tua & Keluarga dari anak yang akan ditambahkan</div>
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
                <form id="form-create-kelahiran" method="post" action="{{ route('desa-kelahiran-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf 
                    <div class="tab-content" id="cardTabContent">
                        <!-- Wizard tab pane item 1-->
                        <div class="tab-pane py-5 fade show active" id="wizard1" role="tabpanel" aria-labelledby="wizard1-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-8">
                                    <h3 class="text-primary">Langkah 1</h3>
                                    <h5 class="card-title">Masukkan Data Anak yang akan Ditambahkan</h5>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title">NIK<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik') }}" required>
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title">No. Akta Kelahiran<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nomor_akta_kelahiran') is-invalid @enderror"  id="nomor_akta_kelahiran" name="nomor_akta_kelahiran" placeholder="Masukkan Nomor Akta Kelahiran" value="{{ old('nomor_akta_kelahiran') }}" required>
                                                @error('nomor_akta_kelahiran')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        No. Akta Kelahiran wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                        <label for="title">Nama<span class="text-danger small">*</span></label>
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
                                            <label for="title">Tempat Lahir<span class="text-danger small">*</span></label>
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
                                            <label for="title">Tanggal Lahir<span class="text-danger small">*</span></label>
                                            <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required>
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
                                            <label for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
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
                                            <label for="title">Agama<span class="text-danger small">*</span></label>
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
                                            <label for="title">Golongan Darah<span class="text-danger small">*</span></label>
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
                                            <label for="title">Alamat<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat') }}" required>
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Provinsi<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required>
                                                <option value="">Pilih Provinsi</option>
                                                @foreach($provinsis as $provinsi)
                                                    <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Kabupaten<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                                <option value="">Pilih Kabupaten</option>
                                            </select>
                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Kecamatan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required>
                                                <option value="">Pilih Desa/Kelurahan</option>
                                            </select>
                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Jenis Kependudukan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('jenis_kependudukan') is-invalid @enderror" name="jenis_kependudukan" id="jenis_kependudukan"  style="width: 100%" required aria-placeholder="Pilih jenis kependudukan" required>
                                                <option value="">Pilih Jenis Kependudukan</option>
                                                <option value="adat_&_dinas" @if(old('jenis_kependudukan') == 'adat_&_dinas') selected @endif>Adat dan Dinas</option>
                                                <option value="adat" @if(old('jenis_kependudukan') =='adat') selected @endif>Adat</option>
                                                {{-- <option value="dinas">Dinas</option> --}}
                                            </select>
                                            @error('jenis_kependudukan')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Jenis Kependudukan wajib dipilih
                                                </div>
                                            @enderror  
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2" id="banjar_adat_row" style="display:none;">
                                            <label for="title">Banjar Adat<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat_id"  style="width: 100%" aria-placeholder="Pilih Banjar Adat">
                                                <option value="">Pilih Banjar Adat</option>
                                                @foreach($banjar_adat as $adat)
                                                    <option value="{{ $adat->id }}" @if(old('banjar_adat_id') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                                @endforeach
                                            </select>
                                            <small class="small">(Pilih jenis kependudukan terlebih dahulu)</small>
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
                                        <div class="col-lg-6 col-sm-12 py-2" id="banjar_dinas_row" style="display:none;">
                                            <label for="title">Banjar Dinas<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas_id"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required>
                                                <option value="">Pilih Banjar Dinas</option>
                                                @foreach($banjar_dinas as $dinas)
                                                    <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                                @endforeach
                                            </select>
                                            <small class="small">(Pilih jenis kependudukan terlebih dahulu)</small>
                                            @error('banjar_dinas_id')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Banjar Dinas wajib dipilih
                                                </div>
                                            @enderror 
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="foto">Foto</label>
                                            <br>
                                            <input type="text" class="foto" name="foto" id="foto" placeholder="url" hidden>
                                            <img src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="rounded img-thumbnail" style="max-width:30%;" id="propic">
                                            @error('foto')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Foto wajib diisi
                                                </div>
                                            @enderror
                                            <div class="custom-file mt-1">
                                                <button type="button" class="btn btn-primary btn-icon-split mt-1" data-target="#crop-image" data-toggle="modal">
                                                    <span class="icon">
                                                        <i class="fas fa-images"></i>
                                                    </span>
                                                    <span class="text">Pilih Foto</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4" />
                                    <div class="d-flex float-right">
                                        {{-- <button class="btn btn-light" type="button">Previous</button> --}}
                                        <button class="btn btn-primary" type="button" id="btn-next-1">Selanjutnya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Wizard tab pane item 2-->
                        <div class="tab-pane py-5 py-xl-10 fade" id="wizard2" role="tabpanel" aria-labelledby="wizard2-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-8">
                                    <h3 class="text-primary">Langkah 2</h3>
                                    <h5 class="card-title">Masukkan Data Keluarga dari Anak yang Akan Ditambahkan</h5>
                                    <div class="form-group">
                                        <label for="title">Cari Keluarga Krama<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('cari_keluarga') is-invalid @enderror"  id="cari_keluarga" name="cari_keluarga" placeholder="Masukkan Nomor Keluarga atau Nama Kepala Keluarga" value="{{ old('nik') }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari Keluarga"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                        @error('keluarga_krama')
                                            <small class="text-danger" style="display:none;">
                                                {{ $message }}
                                            </small>
                                        @else
                                            <small class="text-danger" style="display:none;">
                                                Keluarga Krama wajib dipilih
                                            </small>
                                        @enderror
                                    </div>
                                    <div id="list_keluarga_krama"> 
                                    </div>
                                    <input type="text" class="form-control" id="keluarga_krama" name="keluarga_krama" required hidden>
                                    <div class="form-group" id="keluarga_terpilih_div" style="display:none;">
                                        <label class="small mb-1" for="keluarga_terpilih_div">Keluarga Krama<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('keluarga_terpilih_div') is-invalid @enderror" name="keluarga_terpilih" id="keluarga_terpilih"  style="width: 100%" disabled>
                                            <option value="">Pilih Keluarga</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="ayah_kandung_div" style="display:none;">
                                        <label class="small mb-1" for="ayah_kandung">Ayah Kandung<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%">
                                            <option value="">Pilih Ayah Kandung</option>
                                        </select>
                                        @error('ayah_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Ayah Kandung wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group" id="ibu_kandung_div" style="display:none;">
                                        <label class="small mb-1" for="ibu_kandung">Ibu Kandung<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%">
                                            <option value="">Pilih Ibu Kandung</option>
                                        </select>
                                        @error('ibu_kandung')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Ibu Kandung wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    <hr class="my-4" />
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-light" type="button" id="btn-prev-2">Sebelumnya</button>
                                        <button class="btn btn-primary" type="button" id="btn-next-2" onclick="konfirmasi_simpan()">Simpan</button>
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
<div class="modal fade" id="anggota_keluarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit-banjar-adat" method="post" action="{{route('desa-banjar-adat-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Anggota Keluarga</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="body_loading">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="body_anggota">
                    
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal" id="btnTutup">Tutup</button></div>
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
                    <label for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                </div>
                <div id="validasi-foto" class="text-danger small mt-2 text-end" style="display:none;">
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
            $('#nav-link-kelahiran').addClass('active');

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
                    }
                    if($("#nik").val().length != 16){
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

            //Jenis Kependudukan On Change
            $("#jenis_kependudukan").on('change', function(){
                if($(this).val() == 'adat'){
                    $("#banjar_dinas_id").prop('required', false);
                    $("#banjar_adat_id").prop('required', true);
                    $("#banjar_dinas_row").fadeOut();
                    $("#banjar_adat_row").fadeIn();
                }else if($(this).val() == ''){
                    $("#banjar_adat_row").fadeOut();
                    $("#banjar_dinas_row").fadeOut();
                    $("#banjar_adat_id").prop('required', false);
                    $("#banjar_dinas_id").prop('required', false);
                }else{
                    $("#banjar_adat_id").prop('required', true);
                    $("#banjar_dinas_id").prop('required', true);
                    $("#banjar_adat_row").fadeIn();
                    $("#banjar_dinas_row").fadeIn();
                }
            });

            //Regex NIK
            $('#search_button').on('click', function(){
                if($('#cari_keluarga').val() != ''){
                    $("#overlay").css('display', 'flex');
                    var url = "{{ route('desa-kelahiran-keluarga-search', ":keyword") }}";
                    url = url.replace(':keyword', $('#cari_keluarga').val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#ayah_kandung_div').fadeOut();
                            $('#ibu_kandung_div').fadeOut();
                            $('#keluarga_terpilih_div').fadeOut();
                            $('#ayah_kandung').empty();
                            $('#ibu_kandung').empty();
                            $('#ayah_kandung').prop('required', false);
                            $('#ibu_kandung').prop('required', false);
                            $('#keluarga_krama').val('');
                            if ($.fn.DataTable.isDataTable(".dataTable-keluarga")) {
                                $('.dataTable-keluarga').DataTable().clear().destroy();
                            }
                            if(result){
                                $('#list_keluarga_krama').html(result.hasil);  
                                $("#overlay").fadeOut();      
                                $("#dataTable-keluarga").DataTable({
                                    "responsive": false, "lengthChange": true, "autoWidth": false,
                                    "oLanguage": {
                                        "sSearch": "Cari:",
                                        "sZeroRecords": "Data tidak ditemukan",
                                        "sSearchPlaceholder": "Cari Keluarga...",
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
                                    },
                                });  
                            }else{
                                $("#list_keluarga_krama").empty();
                                $("#overlay").fadeOut();
                                Toast.fire({
                                    icon: 'warning',
                                    title: 'Keluarga tidak ditemukan'
                                })
                            }
                               
                        }
                    });
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

        function anggota_keluarga(id){
            $('#body_anggota').hide();
            $('#body_loading').show();
            $('#anggota_keluarga').modal('show');
            var url = "{{ route('desa-kelahiran-anggota-search', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    if ($.fn.DataTable.isDataTable(".dataTable-keluarga")) {
                        $('.dataTable-keluarga').DataTable().clear().destroy();
                    }
                    $('#body_anggota').html(result.hasil);       
                    $("#dataTable-anggota-keluarga").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Anggota Keluarga...",
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
                        },
                    }); 
                    $('#body_anggota').show();
                    $('#body_loading').hide();   
                }
            });
        }

        function pilih_keluarga(id){
            $("#overlay").css('display', 'flex');
            var url = "{{ route('desa-kelahiran-anggota-get', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    $('#keluarga_terpilih').empty();
                    $('#ayah_kandung').empty();
                    $('#ibu_kandung').empty();
                    $('#ayah_kandung').append('<option value="">Pilih Ayah Kandung</option>');
                    $('#ibu_kandung').append('<option value="">Pilih Ibu Kandung</option>');
                    result.anggota_keluarga.forEach(element => {
                        var nama = '';
                        if(element.krama.penduduk.gelar_depan != null && element.krama.penduduk.gelar_belakang != null){
                            nama = element.krama.penduduk.gelar_depan+' '+element.krama.penduduk.nama+', '+element.krama.penduduk.gelar_belakang; 
                        }else if(element.krama.penduduk.gelar_depan == null && element.krama.penduduk.gelar_belakang != null){
                            nama = element.krama.penduduk.nama+', '+element.krama.penduduk.gelar_belakang; 
                        }else if(element.krama.penduduk.gelar_depan != null && element.krama.penduduk.gelar_belakang == null){
                            nama = element.krama.penduduk.gelar_depan+' '+element.krama.penduduk.nama; 
                        }else if(element.krama.penduduk.gelar_depan == null && element.krama.penduduk.gelar_belakang == null){
                            nama = element.krama.penduduk.nama; 
                        } 
                        $('#ayah_kandung').append('<option value="'+element.krama.penduduk.id+'">'+element.krama.penduduk.nomor_induk_krama+' - '+nama+'</option>');
                        $('#ibu_kandung').append('<option value="'+element.krama.penduduk.id+'">'+element.krama.penduduk.nomor_induk_krama+' - '+nama+'</option>');
                    });
                    var nama = '';
                    if(result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan != null && result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang != null){
                        nama = result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan+' '+result.keluarga.kepala_keluarga.krama.penduduk.nama+', '+result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang; 
                    }else if(result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan == null && result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang != null){
                        nama = result.keluarga.kepala_keluarga.krama.penduduk.nama+', '+result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang; 
                    }else if(result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan != null && result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang == null){
                        nama = result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan+' '+result.keluarga.kepala_keluarga.krama.penduduk.nama; 
                    }else if(result.keluarga.kepala_keluarga.krama.penduduk.gelar_depan == null && result.keluarga.kepala_keluarga.krama.penduduk.gelar_belakang == null){
                        nama = result.keluarga.kepala_keluarga.krama.penduduk.nama; 
                    } 
                    $('#keluarga_terpilih').append('<option value="'+result.keluarga.id+'">'+result.keluarga.nomor_keluarga+' - '+nama+'</option>')
                    $('#ayah_kandung').prop('required', true);
                    $('#ibu_kandung').prop('required', true);
                    //SHOW HIDDEN
                    $('#ayah_kandung_div').fadeIn();
                    $('#ibu_kandung_div').fadeIn();
                    $('#keluarga_terpilih_div').fadeIn();
                    $('#list_keluarga_krama').empty();
                    $('#keluarga_krama').val(result.keluarga.id);
                    $("#overlay").fadeOut();
                }
            });
        }

        function konfirmasi_simpan(){
            Swal.fire({
                title: 'Simpan Kelahiran',
                text: "Kelahiran baru akan memperbaharui Keluarga Krama yang dipilih",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#form-create-kelahiran').submit();
                    }
                })
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
    </script>
@endpush