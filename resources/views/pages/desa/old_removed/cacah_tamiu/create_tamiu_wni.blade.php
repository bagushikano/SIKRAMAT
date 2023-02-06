@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
@endpush
@section('title', 'Tambah Cacah Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Cacah Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-cacah-tamiu-home') }}" class="text-decoration-none">Cacah Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Tambah Cacah Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Masukkan Data Cacah Tamiu</div>
                <div class="card-body">
                    <div id="overlay">
                        <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <form id="form-create-krama-mipil" method="post" action="{{ route('desa-cacah-tamiu-wni-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <div class="form-group">
                                    <label for="title">NIK<span class="text-danger small">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" value="{{ old('nik') }}" placeholder="Masukkan NIK" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari penduduk"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama Tercetak<span class="font-italic small"> (*dengan gelar jika ada)</span></label>
                                <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ old('nama_tercetak') }}" readonly>
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Gelar Depan</label>
                                <input type="text" class="form-control @error ('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" placeholder="Masukkan Gelar Depan" value="{{ old('gelar_depan') }}" readonly>
                                @error('gelar_depan')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama<span class="text-danger small">*</span><span class="font-italic small"> (*tanpa gelar)</span></label>
                                <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}" required readonly>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Gelar Belakang</label>
                                <input type="text" class="form-control @error ('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" value="{{ old('gelar_belakang') }}" readonly>
                                @error('gelar_belakang')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tempat Lahir<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror"  id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan Tempat Lahir" value="{{ old('tempat_lahir') }}" required readonly>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Lahir<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required readonly>
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
                                <label for="title">Agama<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required disabled>
                                    <option value="">Pilih Agama</option>
                                    <option value="islam" @if(old('agama') == 'islam') selected @endif>Islam</option>
                                    <option value="protestan" @if(old('agama') == 'protestan') selected @endif>Protestan</option>
                                    <option value="katolik" @if(old('agama') == 'katolik') selected @endif>Katolik</option>
                                    <option value="hindu" @if(old('agama') == 'hindu') selected @endif>Hindu</option>
                                    <option value="buddha" @if(old('agama') == 'buddha') selected @endif>Buddha</option>
                                    <option value="khonghucu" @if(old('agama') == 'khonghucu') selected @endif>Khonghucu</option>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin" style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required disabled>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Pendidikan Tertinggi<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('pendidikan') is-invalid @enderror" name="pendidikan" id="pendidikan"  style="width: 100%" required aria-placeholder="Pilih Pendidikan" required disabled>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Pekerjaan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('pekerjaan') is-invalid @enderror" name="pekerjaan" id="pekerjaan"  style="width: 100%" required aria-placeholder="Pilih Pekerjaan" required disabled>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Golongan Darah<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required disabled>
                                    <<option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">No. Telepon</label>
                                <input type="text" class="form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon') }}" readonly>
                                @error('telepon')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Alamat<span class="text-danger small">*</span></label>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Status Perkawinan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="status_perkawinan"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required disabled>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Provinsi<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required disabled>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Kabupaten<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required disabled>
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
                                <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required disabled>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required disabled>
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
                                <label for="title">Ayah Kandung</label>
                                <select class="select2 custom-select select-krama @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%" disabled>
                                    <option value="">Cari Ayah Kandung</option>
                                </select>
                                @error('ayah_kandung')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Ibu Kandung</label>
                                <select class="select2 custom-select select-krama @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%" disabled>
                                    <option value="">Cari Ibu Kandung</option>
                                </select>                                
                                @error('ibu_kandung')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Masuk<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_masuk') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}" placeholder="Masukkan Tanggal Masuk Krama Mipil" required readonly>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Keluar</label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_keluar') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_keluar" id="tanggal_keluar" placeholder="Masukkan Keluar Masuk Krama Mipil" value="@if(old('tanggal_keluar')) {{ old('tanggal_keluar') }} @endif" readonly>
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal Keluar wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_dinas_row">
                                <label for="title">Banjar Dinas<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required disabled>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_adat_row">
                                <label for="title">Banjar Adat<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat"  style="width: 100%" aria-placeholder="Pilih Banjar Adat" required disabled>
                                    <option value="">Pilih Banjar Adat</option>
                                    @foreach($banjar_adat as $adat)
                                        <option value="{{ $adat->id }}" @if(old('banjar_adat_id') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                    @endforeach
                                </select>
                                @error('banjar_adat')
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="foto">Foto</label>
                                <br>
                                <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
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

                        <div class="row mx-5 mt-3">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <a class="btn btn-danger mr-2" href="{{ route('desa-cacah-tamiu-home') }}">Kembali</a><button class="btn btn-success" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
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
            $("input").prop('readonly', false);
            $("select").prop('disabled', false);
        </script>
    @endif
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
            $('#search_button').on('click', function(){
                if($("#nik").val().length != 16){
                    $("#nik-validate").show();
                }else{
                    var url = "{{ route('desa-cacah-tamiu-get-penduduk', ":nik") }}";
                    url = url.replace(':nik', $("#nik").val());
                    $("#nik-validate").fadeOut();
                    $("#overlay").css('display', 'flex');
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            if(result.status == 'ditemukan'){
                                $("#nik").prop('readonly', true);
                                $('#gelar_depan').val(result.penduduk.gelar_depan); 
                                $('#nama').val(result.penduduk.nama);
                                $('#gelar_belakang').val(result.penduduk.gelar_belakang);
                                if(result.penduduk.gelar_depan != null && result.penduduk.gelar_belakang != null){
                                    $('#nama_tercetak').val(result.penduduk.gelar_depan+' '+result.penduduk.nama+', '+result.penduduk.gelar_belakang); 
                                }else if(result.penduduk.gelar_depan == null && result.penduduk.gelar_belakang != null){
                                    $('#nama_tercetak').val(result.penduduk.nama+', '+result.penduduk.gelar_belakang); 
                                }else if(result.penduduk.gelar_depan != null && result.penduduk.gelar_belakang == null){
                                    $('#nama_tercetak').val(result.penduduk.gelar_depan+' '+result.penduduk.nama); 
                                }else if(result.penduduk.gelar_depan == null && result.penduduk.gelar_belakang == null){
                                    $('#nama_tercetak').val(result.penduduk.nama); 
                                } 
                                $('#nama_panggilan').val(result.penduduk.nama_panggilan)
                                $('#tempat_lahir').val(result.penduduk.tempat_lahir); 
                                $('#tanggal_lahir').datepicker("update", result.penduduk.tanggal_lahir); 
                                $('#agama').val(result.penduduk.agama).trigger('change');
                                $('#jenis_kelamin').val(result.penduduk.jenis_kelamin).trigger('change');
                                $('#pendidikan_terakhir').select2("val",result.penduduk.pendidikan_id);
                                $('#pekerjaan').val(result.penduduk.pekerjaan_id).trigger('change');  
                                $('#golongan_darah').val(result.penduduk.golongan_darah).trigger('change');
                                $('#status_perkawinan').val(result.penduduk.status_perkawinan).trigger('change');
                                $("#ayah_kandung").empty();
                                $("#ayah_kandung").append('<option value="'+result.penduduk.ayah.id+'">'+result.penduduk.ayah.nama+'</option>');
                                $("#ibu_kandung").empty();
                                $("#ibu_kandung").append('<option value="'+result.penduduk.ibu.id+'">'+result.penduduk.ibu.nama+'</option>');
                                $('#telepon').val(result.penduduk.telepon);
                                $('#alamat').val(result.penduduk.alamat);
                                if(result.penduduk.foto != null){
                                    $('#propic').attr("src", result.penduduk.foto);
                                    $('#image-preview').attr("src", result.penduduk.foto);
                                } 
                                $('#provinsi').empty();
                                result.provinsis.forEach(element => {
                                    var prov = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.provinsi.id){
                                        prov = prov + ' selected'
                                    }
                                    prov = prov + '>' + element['name'] + '</option>'
                                    $('#provinsi').append(prov);
                                });
                                $('#kabupaten').empty();
                                result.kabupatens.forEach(element => {
                                    var kab = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.kabupaten.id){
                                        kab = kab + ' selected'
                                    }
                                    kab = kab + '>' + element['name'] + '</option>'
                                    $('#kabupaten').append(kab);
                                });
                                $('#kecamatan').empty();
                                result.kecamatans.forEach(element => {
                                    var kec = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.kecamatan.id){
                                        kec = kec + ' selected'
                                    }
                                    kec = kec + '>' + element['name'] + '</option>'
                                    $('#kecamatan').append(kec);
                                });
                                $('#desa').empty();
                                result.desas.forEach(element => {
                                    var des = '<option value="' + element['id'] + '"';
                                    if(element['id'] == result.desa.id){
                                        des = des + ' selected'
                                    }
                                    des = des + '>' + element['name'] + '</option>'
                                    $('#desa').append(des);
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
                                $("input").prop('readonly', false);
                                $("select").prop('disabled', false);
                                $("#nama_tercetak").prop('readonly', true);
                                $("#jenis_kependudukan").prop('disabled', false);
                                $("#banjar_adat").prop('disabled', false);
                                $("#banjar_dinas").prop('disabled', false);
                                $("#tanggal_masuk").prop('readonly', false);
                                $("#tanggal_keluar").prop('readonly', false);
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
                                $("#overlay").fadeOut();
                                Toast.fire({
                                    icon: 'warning',
                                    title: 'Penduduk tidak ditemukan'
                                })
                            }else if(result.status == 'terdaftar_krama_mipil'){
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
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari akun...",
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
            //SIDE BAR CLASS
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-tamiu').addClass('active');
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
                    url: '{{ route("api-cacah-tamiu-wni-ortu-search") }}',
                    dataType: 'json',
                },
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