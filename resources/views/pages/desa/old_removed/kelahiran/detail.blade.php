@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner_center.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
@endpush
@section('title', 'Detail Kelahiran')
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
                    <li class="breadcrumb-item"><a href="{{ route('desa-kelahiran-home') }}" class="text-decoration-none">Kelahiran</a></li>
                    <li class="breadcrumb-item active text-red-pastel">Detail Kelahiran</li>
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
                            {{-- <div class="wizard-step-text-details text-dark">Data anak yang akan ditambahkan</div> --}}
                        </div>
                    </a>
                    <!-- Wizard navigation item 2-->
                    <a class="nav-item nav-link" id="wizard2-tab" href="#wizard2" data-toggle="tab" role="tab" aria-controls="wizard2" aria-selected="true">
                        <div class="wizard-step-icon">2</div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Orang Tua</div>
                            {{-- <div class="wizard-step-text-details text-dark">Orang Tua & Keluarga dari anak yang akan ditambahkan</div> --}}
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
                <form id="form-create-kelahiran" method="post" action="{{ route('desa-kelahiran-update', $kelahiran->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf 
                    <div class="tab-content" id="cardTabContent">
                        <!-- Wizard tab pane item 1-->
                        <div class="tab-pane py-5 fade show active" id="wizard1" role="tabpanel" aria-labelledby="wizard1-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-8">
                                    <h3 class="text-primary">Data Anak</h3>
                                    {{-- <h5 class="card-title">Masukkan Data Anak yang akan Ditambahkan</h5> --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label for="title">NIK<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik', $penduduk->nik) }}" required disabled>
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
                                                <input type="text" class="form-control @error ('nomor_akta_kelahiran') is-invalid @enderror"  id="nomor_akta_kelahiran" name="nomor_akta_kelahiran" placeholder="Masukkan Nomor Akta Kelahiran" value="{{ old('nomor_akta_kelahiran', $kelahiran->nomor_akta_kelahiran) }}" required disabled>
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
                                            <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama', $penduduk->nama) }}" required disabled>
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
                                            <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $penduduk->tempat_lahir) }}" placeholder="Masukkan Tempat Lahir" required disabled>
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
                                            <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir',date('d M Y', strtotime($penduduk->tanggal_lahir))) }}" placeholder="Masukkan Tanggal Lahir" required disabled>
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
                                            <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required disabled aria-placeholder="Pilih Jenis Kelamin" required disabled>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Agama<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required disabled aria-placeholder="Pilih Agama" required disabled>
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
                                            <select class="select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required disabled aria-placeholder="Pilih Golongan Darah" required disabled>
                                                @if(old('golongan_darah'))
                                                    <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                                    <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                                    <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                                    <option value="O" @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                                    <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>

                                                @else 
                                                    <option value="-"  @if($penduduk->golongan_darah == '-') selected @endif>-</option>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Alamat<span class="text-danger small">*</span></label>
                                            <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat', $penduduk->alamat) }}" required disabled>
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
                                            <select class="select2 custom-select @error ('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required disabled aria-placeholder="Pilih Provinsi" required disabled>
                                                <option value="">Pilih Provinsi</option>
                                                @if(old('provinsi'))
                                                    @foreach($provinsis as $prov)
                                                        <option value="{{ $prov->id }}" @if(old('provinsi') == $prov->id) selected @endif>{{ $prov->name }}</option>
                                                    @endforeach
                                                @else 
                                                    @foreach($provinsis as $prov)
                                                        <option value="{{ $prov->id }}" @if($prov->id == $provinsi->id) selected @endif>{{ $prov->name }}</option>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label for="title">Kabupaten<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required disabled aria-placeholder="Pilih Kabupaten" required disabled>
                                                <option value="">Pilih Kabupaten</option>
                                                @if(old('kabupaten'))
                                                    @foreach($kabupatens as $kab)
                                                        <option value="{{ $kab->id }}" @if(old('kabupaten') == $kab->id) selected @endif>{{ $kab->name }}</option>
                                                    @endforeach
                                                @else 
                                                    @foreach($kabupatens as $kab)
                                                        <option value="{{ $kab->id }}" @if($kab->id == $kabupaten->id) selected @endif>{{ $kab->name }}</option>
                                                    @endforeach
                                                @endif
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
                                            <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required disabled aria-placeholder="Pilih Kecamatan" required disabled>
                                                <option value="">Pilih Kecamatan</option>
                                                @if(old('kecamatan'))
                                                    @foreach($kecamatans as $kec)
                                                        <option value="{{ $kec->id }}" @if(old('kecamatan') == $kec->id) selected @endif>{{ $kec->name }}</option>
                                                    @endforeach
                                                @else 
                                                    @foreach($kecamatans as $kec)
                                                        <option value="{{ $kec->id }}" @if($kec->id == $kecamatan->id) selected @endif>{{ $kec->name }}</option>
                                                    @endforeach
                                                @endif
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
                                            <select class="select2 custom-select @error ('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required disabled aria-placeholder="Pilih Desa" required disabled>
                                                <option value="">Pilih Desa/Kelurahan</option>
                                                @if(old('desa'))
                                                    @foreach($desas as $des)
                                                        <option value="{{ $des->id }}" @if($des->id == $desa->id || old('desa') == $desa->id) selected @endif>{{ $des->name }}</option>
                                                    @endforeach
                                                @else 
                                                    @foreach($desas as $des)
                                                        <option value="{{ $des->id }}" @if($des->id == $desa->id || old('desa') == $desa->id) selected @endif>{{ $des->name }}</option>
                                                    @endforeach
                                                @endif
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
                                            <select class="select2 custom-select @error ('jenis_kependudukan') is-invalid @enderror" name="jenis_kependudukan" id="jenis_kependudukan"  style="width: 100%" required disabled aria-placeholder="Pilih jenis kependudukan" required disabled>
                                                <option value="">Pilih Jenis Kependudukan</option>
                                                @if(old('jenis_kependudukan'))
                                                    <option value="adat_&_dinas" @if(old('jenis_kependudukan') == 'adat_&_dinas') selected @endif>Adat dan Dinas</option>
                                                    <option value="adat" @if(old('jenis_kependudukan') == 'adat') selected @endif>Adat</option>
                                                @else 
                                                    <option value="adat_&_dinas" @if($cacah_krama_mipil->jenis_kependudukan == 'adat_&_dinas') selected @endif>Adat dan Dinas</option>
                                                    <option value="adat" @if($cacah_krama_mipil->jenis_kependudukan == 'adat') selected @endif>Adat</option>
                                                @endif
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
                                        <div class="col-lg-6 col-sm-12 py-2" id="banjar_adat_row">
                                            <label for="title">Banjar Adat<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat_id"  style="width: 100%" aria-placeholder="Pilih Banjar Adat" disabled>
                                                <option value="">Pilih Banjar Adat</option>
                                                @if(old('banjar_adat_id'))
                                                    @foreach($banjar_adat as $adat)
                                                        <option value="{{ $adat->id }}" @if(old('banjar_adat_id') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                                    @endforeach
                                                @else 
                                                    @foreach($banjar_adat as $adat)
                                                        <option value="{{ $adat->id }}" @if($adat->id == $cacah_krama_mipil->banjar_adat_id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                                    @endforeach
                                                @endif
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
                                        <div class="col-lg-6 col-sm-12 py-2" id="banjar_dinas_row"  @if($cacah_krama_mipil->jenis_kependudukan != 'adat_&_dinas') style="display:none;" @endif>
                                            <label for="title">Banjar Dinas<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas_id"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required disabled>
                                                <option value="">Pilih Banjar Dinas</option>
                                                @if(old('banjar_dinas_id'))
                                                    @foreach($banjar_dinas as $dinas)
                                                        <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($banjar_dinas as $dinas)
                                                        <option value="{{ $dinas->id }}" @if($dinas->id == $cacah_krama_mipil->banjar_dinas_id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                                    @endforeach 
                                                @endif
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
                                            <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:30%;" id="propic">
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
                                    <div class="d-flex justify-content-between">
                                        <a class="btn btn-light" href="{{ route('desa-kelahiran-home') }}">Kembali</a>
                                        <button class="btn btn-primary" type="button" id="btn-next-1">Selanjutnya</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Wizard tab pane item 2-->
                        <div class="tab-pane py-5 py-xl-10 fade" id="wizard2" role="tabpanel" aria-labelledby="wizard2-tab">
                            <div class="row justify-content-center">
                                <div class="col-xxl-10 col-xl-8">
                                    <h3 class="text-primary">Data Keluarga Krama</h3>
                                    {{-- <h5 class="card-title">Masukkan Data Keluarga dari Anak yang Akan Ditambahkan</h5> --}}
                                    <div class="form-group">
                                        <label for="title">Krama Mipil<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder', $krama_mipil->cacah_krama_mipil->penduduk->nama) }}" required disabled readonly>
                                            {{-- <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button>
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_modal()">
                                                    <span class="text">Pilih Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div> --}}
                                        </div>
                                        <input type="text" class="form-control @error ('krama_mipil') is-invalid @enderror"  id="krama_mipil" name="krama_mipil"  value="{{ old('krama_mipil', $krama_mipil->id) }}" required disabled hidden>
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
                                    <div class="form-group" id="ayah_kandung_div">
                                        <label class="small mb-1" for="ayah_kandung">Ayah Kandung<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%" disabled>
                                            <option value="">Pilih Ayah Kandung</option>
                                            <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}"@if($krama_mipil->cacah_krama_mipil->penduduk->id == $cacah_krama_mipil->penduduk->ayah_kandung_id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                            @foreach($anggota_krama_mipil as $anggota)
                                                @if($anggota->cacah_krama_mipil_id != $cacah_krama_mipil->id)
                                                    <option value="{{ $anggota->cacah_krama_mipil->penduduk->id }}" @if($anggota->cacah_krama_mipil->penduduk->id == $penduduk->ayah_kandung_id) selected @endif>{{ $anggota->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endif
                                            @endforeach
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
                                    <div class="form-group" id="ibu_kandung_div">
                                        <label class="small mb-1" for="ibu_kandung">Ibu Kandung<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%" disabled>
                                            <option value="">Pilih Ibu Kandung</option>
                                            <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}"@if($krama_mipil->cacah_krama_mipil->penduduk->id == $cacah_krama_mipil->penduduk->ibu_kandung_id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                            @foreach($anggota_krama_mipil as $anggota)
                                                @if($anggota->cacah_krama_mipil_id != $cacah_krama_mipil->id)
                                                    <option value="{{ $anggota->cacah_krama_mipil->penduduk->id }}" @if($anggota->cacah_krama_mipil->penduduk->id == $penduduk->ibu_kandung_id) selected @endif>{{ $anggota->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endif
                                            @endforeach
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
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
            if($(this).prop('required disabled')){
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
</script>
@endpush