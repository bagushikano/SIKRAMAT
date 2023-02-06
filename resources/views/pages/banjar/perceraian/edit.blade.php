@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
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
@section('title', 'Edit Perceraian')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-heart-broken mr-2"></i></div>
                                Manajemen Perceraian
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-perceraian-home') }}" class="text-decoration-none text-dark">Perceraian</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Perceraian</li>
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
                                <div class="wizard-step-text-name text-dark">Data Perceraian</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Perceraian Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-11 mt-4">
                            <form id="form-edit-perceraian" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf

                                @if($krama_mipil->kedudukan_krama_mipil == 'purusa')
                                    <div id="data-krama-mipil">
                                        <h5 class="card-title text-primary">Data Krama Mipil</h5>
                                        <div class="form-group">
                                            <label class="small"for="title">Krama Mipil<span class="text-danger small">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}" required>
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
                                            <input type="text" class="form-control @error ('krama_mipil') is-invalid @enderror"  id="krama_mipil" name="krama_mipil"  value="{{ $krama_mipil->id }}" required hidden>
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
                                        {{-- KEDUDUKAN - STATUS --}}
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-3">
                                                <div class="form-group">
                                                    <label class="small"for="kedudukan_krama_mipil">Kedudukan</label>
                                                    <input type="text" class="form-control @error ('kedudukan_krama_mipil') is-invalid @enderror" id="kedudukan_krama_mipil" name="kedudukan_krama_mipil" placeholder="Kedudukan Krama Mipil" value="{{ ucwords($krama_mipil->kedudukan_krama_mipil) }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-sm-9">
                                                <div class="form-group">
                                                    <label class="small"for="status_krama_mipil">Status<span class="text-danger small">*</span></label>
                                                    <select class="select2 custom-select @error ('status_krama_mipil') is-invalid @enderror" name="status_krama_mipil" id="status_krama_mipil"  style="width: 100%" required>
                                                        @if($krama_mipil->kedudukan_krama_mipil == 'purusa')
                                                            <option value="tetap_di_banjar_dan_kk_lama" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama') selected @endif>Tetap di Banjar Adat dan Keluarga Lama</option>
                                                            <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                        @else
                                                            <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                            <option value="keluar_banjar" @if($perceraian->status_pradana == 'keluar_banjar') selected @endif>Keluar dari Banjar Adat</option>
                                                            <option value="keluar_bali" @if($perceraian->status_pradana == 'keluar_bali') selected @endif>Keluar Bali</option>
                                                        @endif
                                                    </select>
                                                    <small class="small">(Pilih Krama Mipil terlebih dahulu)</small>
                                                    @error('status_krama_mipil')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Status Krama Mipil dipilih
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru')
                                            <div class="pembungkus">
                                                <div id="asal-krama_mipil-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_krama_mipil">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_krama_mipil') is-invalid @enderror" name="kabupaten_krama_mipil" id="kabupaten_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_krama_mipil')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_krama_mipil">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_krama_mipil') is-invalid @enderror" name="kecamatan_krama_mipil" id="kecamatan_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_krama_mipil')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_krama_mipil">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_krama_mipil') is-invalid @enderror" name="desa_adat_krama_mipil" id="desa_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_krama_mipil')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_krama_mipil">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_krama_mipil') is-invalid @enderror" name="banjar_adat_krama_mipil" id="banjar_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_krama_mipil')
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
                                                
                                                <div id="krama-mipil-baru-krama_mipil">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_krama_mipil_placeholder" name="krama_mipil_baru_krama_mipil_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_krama_mipil_placeholder', $krama_mipil_baru_purusa->cacah_krama_mipil->penduduk->nama) }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_krama_mipil_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil') is-invalid @enderror"  id="krama_mipil_baru_krama_mipil" name="krama_mipil_baru_krama_mipil"  value="{{ old('krama_mipil_baru_krama_mipil', $krama_mipil_baru_purusa->id) }}" required hidden>
                                                                @error('krama_mipil_baru_krama_mipil')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_krama_mipil') is-invalid @enderror" name="status_hubungan_baru_krama_mipil" id="status_hubungan_baru_krama_mipil"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_purusa == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_purusa == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_purusa == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_purusa == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_purusa == 'famili_lain') selected @endif>Famili Lain</option>
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
                                                    </div>
                                                </div>
        
                                                <div id="asal-krama_mipil-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_krama_mipil_keluar') is-invalid @enderror" name="provinsi_krama_mipil_keluar" id="provinsi_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi" >
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_krama_mipil_keluar') is-invalid @enderror" name="kabupaten_krama_mipil_keluar" id="kabupaten_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_krama_mipil_keluar') is-invalid @enderror" name="kecamatan_krama_mipil_keluar" id="kecamatan_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_krama_mipil_keluar') is-invalid @enderror" name="desa_krama_mipil_keluar" id="desa_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Desa" >
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_krama_mipil_keluar')
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
                                        @else
                                            <div class="pembungkus">
                                                <div id="asal-krama_mipil-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_krama_mipil">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_krama_mipil') is-invalid @enderror" name="kabupaten_krama_mipil" id="kabupaten_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_krama_mipil')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_krama_mipil">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_krama_mipil') is-invalid @enderror" name="kecamatan_krama_mipil" id="kecamatan_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_krama_mipil')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_krama_mipil">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_krama_mipil') is-invalid @enderror" name="desa_adat_krama_mipil" id="desa_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_krama_mipil')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_krama_mipil">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_krama_mipil') is-invalid @enderror" name="banjar_adat_krama_mipil" id="banjar_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_krama_mipil')
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
                                                
                                                <div id="krama-mipil-baru-krama_mipil" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_krama_mipil_placeholder" name="krama_mipil_baru_krama_mipil_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_krama_mipil_placeholder') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_krama_mipil_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil') is-invalid @enderror"  id="krama_mipil_baru_krama_mipil" name="krama_mipil_baru_krama_mipil"  value="{{ old('krama_mipil_baru_krama_mipil') }}" hidden>
                                                                @error('krama_mipil_baru_krama_mipil')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_krama_mipil') is-invalid @enderror" name="status_hubungan_baru_krama_mipil" id="status_hubungan_baru_krama_mipil"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
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
                                                    </div>
                                                </div>
        
                                                <div id="asal-krama_mipil-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_krama_mipil_keluar') is-invalid @enderror" name="provinsi_krama_mipil_keluar" id="provinsi_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi" >
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_krama_mipil_keluar') is-invalid @enderror" name="kabupaten_krama_mipil_keluar" id="kabupaten_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_krama_mipil_keluar') is-invalid @enderror" name="kecamatan_krama_mipil_keluar" id="kecamatan_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_krama_mipil_keluar') is-invalid @enderror" name="desa_krama_mipil_keluar" id="desa_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Desa" >
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_krama_mipil_keluar')
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
                                        @endif
                                    </div>

                                    <div id="data-pasangan">
                                        <hr class="my-4" />
                                        <h5 class="card-title text-primary">Data Pasangan</h5>
                                        <div class="form-group">
                                            <label class="small"for="pasangan">Pasangan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('pasangan') is-invalid @enderror" name="pasangan" id="pasangan"  style="width: 100%" required>
                                                @foreach($pasangan as $pas)
                                                    <option value="{{ $pas->cacah_krama_mipil_id }}" @if($pas->cacah_krama_mipil_id == $perceraian->pradana_id) selected @endif>{{ $pas->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('pasangan')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Pasangan dipilih
                                                </div>
                                            @enderror
                                            <small class="small">(Pilih Krama Mipil terlebih dahulu)</small>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-3">
                                                <div class="form-group">
                                                    <label class="small"for="kedudukan_pasangan">Kedudukan</label>
                                                    <input type="text" class="form-control @error ('kedudukan_pasangan') is-invalid @enderror" id="kedudukan_pasangan" name="kedudukan_pasangan" placeholder="Kedudukan Pasangan" value="Pradana" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-sm-9">
                                                <div class="form-group">
                                                    <label class="small"for="status_pasangan">Status<span class="text-danger small">*</span></label>
                                                    <select class="select2 custom-select @error ('status_pasangan') is-invalid @enderror" name="status_pasangan" id="status_pasangan"  style="width: 100%" required>
                                                        <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                        <option value="keluar_banjar" @if($perceraian->status_pradana == 'keluar_banjar') selected @endif>Keluar dari Banjar Adat</option>                                                        
                                                        <option value="keluar_bali" @if($perceraian->status_pradana == 'keluar_bali') selected @endif>Keluar Bali</option>                                                    
                                                    </select>
                                                    <small class="small">(Pilih Krama Mipil dan Pasangan terlebih dahulu)</small>
                                                    @error('status_pasangan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Status Pasangan wajib dipilih
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru')
                                            <div class="pembungkus">
                                                <div id="asal-pasangan-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_pasangan">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_pasangan') is-invalid @enderror" name="kabupaten_pasangan" id="kabupaten_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_pasangan')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_pasangan">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_pasangan') is-invalid @enderror" name="kecamatan_pasangan" id="kecamatan_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_pasangan')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_pasangan">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_pasangan') is-invalid @enderror" name="desa_adat_pasangan" id="desa_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_pasangan')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_pasangan">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_pasangan') is-invalid @enderror" name="banjar_adat_pasangan" id="banjar_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_pasangan')
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
                                                
                                                <div id="krama-mipil-baru-pasangan">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_pasangan_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_pasangan_placeholder" name="krama_mipil_baru_pasangan_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ $krama_mipil_baru_pradana->cacah_krama_mipil->penduduk->nama }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_pasangan_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_pasangan') is-invalid @enderror"  id="krama_mipil_baru_pasangan" name="krama_mipil_baru_pasangan"  value="{{ $krama_mipil_baru_pradana->id }}" required hidden>
                                                                @error('krama_mipil_baru_pasangan')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_pasangan') is-invalid @enderror" name="status_hubungan_baru_pasangan" id="status_hubungan_baru_pasangan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_pradana == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_pradana == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_pradana == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_pradana == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_pradana == 'famili_lain') selected @endif>Famili Lain</option>
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
                                                    </div>
                                                </div>

                                                <div id="asal-pasangan-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_pasangan_keluar') is-invalid @enderror" name="provinsi_pasangan_keluar" id="provinsi_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_pasangan_keluar') is-invalid @enderror" name="kabupaten_pasangan_keluar" id="kabupaten_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_pasangan_keluar') is-invalid @enderror" name="kecamatan_pasangan_keluar" id="kecamatan_pasangan_keluar"  style="width: 100%"  aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_pasangan_keluar') is-invalid @enderror" name="desa_pasangan_keluar" id="desa_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_pasangan_keluar')
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
                                        @elseif($perceraian->status_pradana == 'keluar_banjar')
                                            <div class="pembungkus">
                                                <div id="asal-pasangan-dalam-bali">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_pasangan">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_pasangan') is-invalid @enderror" name="kabupaten_pasangan" id="kabupaten_pasangan"  style="width: 100%" required>
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten->id == $kabupaten_pradana->id) selected @endif>{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_pasangan')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_pasangan">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_pasangan') is-invalid @enderror" name="kecamatan_pasangan" id="kecamatan_pasangan"  style="width: 100%" required>
                                                                    <option value="">Pilih Kecamatan</option>
                                                                    @foreach($kecamatan_pradanas as $kecamatan)
                                                                        <option value="{{ $kecamatan->id }}" @if($kecamatan->id == $kecamatan_pradana->id) selected @endif>{{ $kecamatan->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_pasangan')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_pasangan">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_pasangan') is-invalid @enderror" name="desa_adat_pasangan" id="desa_adat_pasangan"  style="width: 100%" required>
                                                                    <option value="">Pilih Desa Adat</option>
                                                                    @foreach($desa_adat_pradanas as $desa_adat)
                                                                        <option value="{{ $desa_adat->id }}" @if($desa_adat->id == $desa_adat_pradana->id) selected @endif>{{ $desa_adat->desadat_nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_pasangan')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_pasangan">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_pasangan') is-invalid @enderror" name="banjar_adat_pasangan" id="banjar_adat_pasangan"  style="width: 100%" required>
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                    @foreach($banjar_adat_pradanas as $banjar_adat)
                                                                        <option value="{{ $banjar_adat->id }}" @if($banjar_adat->id == $banjar_adat_pradana->id) selected @endif>{{ $banjar_adat->nama_banjar_adat }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_pasangan')
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
                                                
                                                <div id="krama-mipil-baru-pasangan">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_pasangan_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_pasangan_placeholder" name="krama_mipil_baru_pasangan_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ $krama_mipil_baru_pradana->cacah_krama_mipil->penduduk->nama }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_pasangan_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_pasangan') is-invalid @enderror"  id="krama_mipil_baru_pasangan" name="krama_mipil_baru_pasangan"  value="{{ $krama_mipil_baru_pradana->id }}" required hidden>
                                                                @error('krama_mipil_baru_pasangan')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_pasangan') is-invalid @enderror" name="status_hubungan_baru_pasangan" id="status_hubungan_baru_pasangan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_pradana == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_pradana == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_pradana == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_pradana == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_pradana == 'famili_lain') selected @endif>Famili Lain</option>
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
                                                    </div>
                                                </div>

                                                <div id="asal-pasangan-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_pasangan_keluar') is-invalid @enderror" name="provinsi_pasangan_keluar" id="provinsi_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_pasangan_keluar') is-invalid @enderror" name="kabupaten_pasangan_keluar" id="kabupaten_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_pasangan_keluar') is-invalid @enderror" name="kecamatan_pasangan_keluar" id="kecamatan_pasangan_keluar"  style="width: 100%"  aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_pasangan_keluar') is-invalid @enderror" name="desa_pasangan_keluar" id="desa_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_pasangan_keluar')
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
                                        @else 
                                            <div class="pembungkus">
                                                <div id="asal-pasangan-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_pasangan">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_pasangan') is-invalid @enderror" name="kabupaten_pasangan" id="kabupaten_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_pasangan')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_pasangan">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_pasangan') is-invalid @enderror" name="kecamatan_pasangan" id="kecamatan_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_pasangan')
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
                                            
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_pasangan">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_pasangan') is-invalid @enderror" name="desa_adat_pasangan" id="desa_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_pasangan')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_pasangan">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_pasangan') is-invalid @enderror" name="banjar_adat_pasangan" id="banjar_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_pasangan')
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
                                                
                                                <div id="krama-mipil-baru-pasangan" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_pasangan_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_pasangan_placeholder" name="krama_mipil_baru_pasangan_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_pasangan_placeholder') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_pasangan_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_pasangan') is-invalid @enderror"  id="krama_mipil_baru_pasangan" name="krama_mipil_baru_pasangan"  value="{{ old('krama_mipil_baru_pasangan') }}" hidden>
                                                                @error('krama_mipil_baru_pasangan')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_pasangan') is-invalid @enderror" name="status_hubungan_baru_pasangan" id="status_hubungan_baru_pasangan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
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
                                                    </div>
                                                </div>
                                            
                                                <div id="asal-pasangan-luar-bali">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_pasangan_keluar') is-invalid @enderror" name="provinsi_pasangan_keluar" id="provinsi_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}" @if($provinsi->id == $provinsi_pradana->id) selected @endif>{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_pasangan_keluar') is-invalid @enderror" name="kabupaten_pasangan_keluar" id="kabupaten_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                                @foreach($kabupaten_pradanas as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}" @if($kabupaten->id == $kabupaten_pradana->id) selected @endif>{{ $kabupaten->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_pasangan_keluar') is-invalid @enderror" name="kecamatan_pasangan_keluar" id="kecamatan_pasangan_keluar"  style="width: 100%"  aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                                @foreach($kecamatan_pradanas as $kecamatan)
                                                                    <option value="{{ $kecamatan->id }}" @if($kecamatan->id == $kecamatan_pradana->id) selected @endif>{{ $kecamatan->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_pasangan_keluar') is-invalid @enderror" name="desa_pasangan_keluar" id="desa_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                                @foreach($desa_pradanas as $desa)
                                                                    <option value="{{ $desa->id }}" @if($desa->id == $desa_pradana->id) selected @endif>{{ $desa->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_pasangan_keluar')
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
                                        @endif
                                    </div>
                                @else
                                    <div id="data-krama-mipil">
                                        <h5 class="card-title text-primary">Data Krama Mipil</h5>
                                        <div class="form-group">
                                            <label class="small"for="title">Krama Mipil<span class="text-danger small">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}" required>
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
                                            <input type="text" class="form-control @error ('krama_mipil') is-invalid @enderror"  id="krama_mipil" name="krama_mipil"  value="{{ $krama_mipil->id }}" required hidden>
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

                                        {{-- KEDUDUKAN - STATUS --}}
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-3">
                                                <div class="form-group">
                                                    <label class="small"for="kedudukan_krama_mipil">Kedudukan</label>
                                                    <input type="text" class="form-control @error ('kedudukan_krama_mipil') is-invalid @enderror" id="kedudukan_krama_mipil" name="kedudukan_krama_mipil" placeholder="Kedudukan Krama Mipil" value="{{ ucwords($krama_mipil->kedudukan_krama_mipil) }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-sm-9">
                                                <div class="form-group">
                                                    <label class="small"for="status_krama_mipil">Status<span class="text-danger small">*</span></label>
                                                    <select class="select2 custom-select @error ('status_krama_mipil') is-invalid @enderror" name="status_krama_mipil" id="status_krama_mipil"  style="width: 100%" required>
                                                        @if($krama_mipil->kedudukan_krama_mipil == 'purusa')
                                                            <option value="tetap_di_banjar_dan_kk_lama" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama') selected @endif>Tetap di Banjar Adat dan Keluarga Lama</option>
                                                            <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                        @else
                                                            <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                            <option value="keluar_banjar" @if($perceraian->status_pradana == 'keluar_banjar') selected @endif>Keluar dari Banjar Adat</option>
                                                            <option value="keluar_bali" @if($perceraian->status_pradana == 'keluar_bali') selected @endif>Keluar Bali</option>
                                                        @endif
                                                    </select>
                                                    <small class="small">(Pilih Krama Mipil terlebih dahulu)</small>
                                                    @error('status_krama_mipil')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Status Krama Mipil dipilih
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru')
                                            <div class="pembungkus">
                                                <div id="asal-krama_mipil-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_krama_mipil">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_krama_mipil') is-invalid @enderror" name="kabupaten_krama_mipil" id="kabupaten_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_krama_mipil')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_krama_mipil">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_krama_mipil') is-invalid @enderror" name="kecamatan_krama_mipil" id="kecamatan_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_krama_mipil')
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
                                            
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_krama_mipil">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_krama_mipil') is-invalid @enderror" name="desa_adat_krama_mipil" id="desa_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_krama_mipil')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_krama_mipil">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_krama_mipil') is-invalid @enderror" name="banjar_adat_krama_mipil" id="banjar_adat_krama_mipil"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_krama_mipil')
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
                                                
                                                <div id="krama-mipil-baru-krama_mipil">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_krama_mipil_placeholder" name="krama_mipil_baru_krama_mipil_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ $krama_mipil_baru_pradana->cacah_krama_mipil->penduduk->nama }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_krama_mipil_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil') is-invalid @enderror"  id="krama_mipil_baru_krama_mipil" name="krama_mipil_baru_krama_mipil"  value="{{ $krama_mipil_baru_pradana->id }}" required hidden>
                                                                @error('krama_mipil_baru_krama_mipil')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_krama_mipil') is-invalid @enderror" name="status_hubungan_baru_krama_mipil" id="status_hubungan_baru_krama_mipil"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_pradana == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_pradana == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_pradana == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_pradana == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_pradana == 'famili_lain') selected @endif>Famili Lain</option>
                                                                </select>
                                                                @error('status_hubungan_baru_krama_mipil')
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
                                                    </div>
                                                </div>
                                            
                                                <div id="asal-krama_mipil-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_krama_mipil_keluar') is-invalid @enderror" name="provinsi_krama_mipil_keluar" id="provinsi_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi" >
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_krama_mipil_keluar') is-invalid @enderror" name="kabupaten_krama_mipil_keluar" id="kabupaten_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_krama_mipil_keluar') is-invalid @enderror" name="kecamatan_krama_mipil_keluar" id="kecamatan_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_krama_mipil_keluar') is-invalid @enderror" name="desa_krama_mipil_keluar" id="desa_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Desa" >
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_krama_mipil_keluar')
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
                                        @elseif($perceraian->status_pradana == 'keluar_banjar')
                                            <div class="pembungkus">
                                                <div id="asal-krama_mipil-dalam-bali">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_krama_mipil">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_krama_mipil') is-invalid @enderror" name="kabupaten_krama_mipil" id="kabupaten_krama_mipil"  style="width: 100%" required>
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                        <option value="{{ $kabupaten->id }}" @if($kabupaten->id == $kabupaten_pradana->id) selected @endif>{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_krama_mipil')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_krama_mipil">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_krama_mipil') is-invalid @enderror" name="kecamatan_krama_mipil" id="kecamatan_krama_mipil"  style="width: 100%" required>
                                                                    <option value="">Pilih Kecamatan</option>
                                                                    @foreach($kecamatan_pradanas as $kecamatan)
                                                                        <option value="{{ $kecamatan->id }}" @if($kecamatan->id == $kecamatan_pradana->id) selected @endif>{{ $kecamatan->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_krama_mipil')
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
                                            
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_krama_mipil">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_krama_mipil') is-invalid @enderror" name="desa_adat_krama_mipil" id="desa_adat_krama_mipil"  style="width: 100%" required>
                                                                    <option value="">Pilih Desa Adat</option>
                                                                    @foreach($desa_adat_pradanas as $desa_adat)
                                                                        <option value="{{ $desa_adat->id }}" @if($desa_adat->id == $desa_adat_pradana->id) selected @endif>{{ $desa_adat->desadat_nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_krama_mipil')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_krama_mipil">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_krama_mipil') is-invalid @enderror" name="banjar_adat_krama_mipil" id="banjar_adat_krama_mipil"  style="width: 100%" required>
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                    @foreach($banjar_adat_pradanas as $banjar_adat)
                                                                        <option value="{{ $banjar_adat->id }}" @if($banjar_adat->id == $banjar_adat_pradana->id) selected @endif>{{ $banjar_adat->nama_banjar_adat }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_krama_mipil')
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
                                                
                                                <div id="krama-mipil-baru-krama_mipil">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_krama_mipil_placeholder" name="krama_mipil_baru_krama_mipil_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ $krama_mipil_baru_pradana->cacah_krama_mipil->penduduk->nama }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_krama_mipil_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil') is-invalid @enderror"  id="krama_mipil_baru_krama_mipil" name="krama_mipil_baru_krama_mipil"  value="{{ $krama_mipil_baru_pradana->id }}" required hidden>
                                                                @error('krama_mipil_baru_krama_mipil')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_krama_mipil') is-invalid @enderror" name="status_hubungan_baru_krama_mipil" id="status_hubungan_baru_krama_mipil"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
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
                                                    </div>
                                                </div>
                                            
                                                <div id="asal-krama_mipil-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_krama_mipil_keluar') is-invalid @enderror" name="provinsi_krama_mipil_keluar" id="provinsi_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi" >
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_krama_mipil_keluar') is-invalid @enderror" name="kabupaten_krama_mipil_keluar" id="kabupaten_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_krama_mipil_keluar') is-invalid @enderror" name="kecamatan_krama_mipil_keluar" id="kecamatan_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_krama_mipil_keluar') is-invalid @enderror" name="desa_krama_mipil_keluar" id="desa_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Desa" >
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_krama_mipil_keluar')
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
                                        @else 
                                            <div class="pembungkus">
                                                <div id="asal-krama_mipil-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_krama_mipil">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_krama_mipil') is-invalid @enderror" name="kabupaten_krama_mipil" id="kabupaten_krama_mipil"  style="width: 100%" >
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_krama_mipil')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_krama_mipil">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_krama_mipil') is-invalid @enderror" name="kecamatan_krama_mipil" id="kecamatan_krama_mipil"  style="width: 100%" >
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_krama_mipil')
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
                                            
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_krama_mipil">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_krama_mipil') is-invalid @enderror" name="desa_adat_krama_mipil" id="desa_adat_krama_mipil"  style="width: 100%" >
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_krama_mipil')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_krama_mipil">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_krama_mipil') is-invalid @enderror" name="banjar_adat_krama_mipil" id="banjar_adat_krama_mipil"  style="width: 100%" >
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_krama_mipil')
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
                                                
                                                <div id="krama-mipil-baru-krama_mipil" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_krama_mipil_placeholder" name="krama_mipil_baru_krama_mipil_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_krama_mipil_placeholder') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_krama_mipil_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_krama_mipil') is-invalid @enderror"  id="krama_mipil_baru_krama_mipil" name="krama_mipil_baru_krama_mipil"  value="{{ old('krama_mipil_baru_krama_mipil') }}" hidden>
                                                                @error('krama_mipil_baru_krama_mipil')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_krama_mipil') is-invalid @enderror" name="status_hubungan_baru_krama_mipil" id="status_hubungan_baru_krama_mipil"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if(old('status_hubungan') == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if(old('status_hubungan') == 'cucu') selected @endif>Cucu</option>
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
                                                    </div>
                                                </div>
                                            
                                                <div id="asal-krama_mipil-luar-bali">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_krama_mipil_keluar') is-invalid @enderror" name="provinsi_krama_mipil_keluar" id="provinsi_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi" required>
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}" @if($provinsi->id == $provinsi_pradana->id) selected @endif>{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_krama_mipil_keluar') is-invalid @enderror" name="kabupaten_krama_mipil_keluar" id="kabupaten_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" required>
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                                @foreach($kabupaten_pradanas as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}" @if($kabupaten->id == $kabupaten_pradana->id) selected @endif>{{ $kabupaten->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_krama_mipil_keluar') is-invalid @enderror" name="kecamatan_krama_mipil_keluar" id="kecamatan_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Kecamatan" required>
                                                                <option value="">Pilih Kecamatan</option>
                                                                @foreach($kecamatan_pradanas as $kecamatan)
                                                                    <option value="{{ $kecamatan->id }}" @if($kecamatan->id == $kecamatan_pradana->id) selected @endif>{{ $kecamatan->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_krama_mipil_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_krama_mipil_keluar') is-invalid @enderror" name="desa_krama_mipil_keluar" id="desa_krama_mipil_keluar"  style="width: 100%" aria-placeholder="Pilih Desa" required>
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                                @foreach($desa_pradanas as $desa)
                                                                    <option value="{{ $desa->id }}" @if($desa->id == $desa_pradana->id) selected @endif>{{ $desa->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_krama_mipil_keluar')
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
                                        @endif

                                        <hr class="my-4" />
                                        <h5 class="card-title text-primary">Data Pasangan</h5>
                                        <div class="form-group">
                                            <label class="small"for="pasangan">Pasangan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('pasangan') is-invalid @enderror" name="pasangan" id="pasangan"  style="width: 100%" required>
                                                @foreach($pasangan as $pas)
                                                    <option value="{{ $pas->cacah_krama_mipil_id }}" @if($pas->cacah_krama_mipil_id == $perceraian->purusa_id) selected @endif>{{ $pas->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('pasangan')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Pasangan dipilih
                                                </div>
                                            @enderror
                                            <small class="small">(Pilih Krama Mipil terlebih dahulu)</small>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-3">
                                                <div class="form-group">
                                                    <label class="small"for="kedudukan_pasangan">Kedudukan</label>
                                                    <input type="text" class="form-control @error ('kedudukan_pasangan') is-invalid @enderror" id="kedudukan_pasangan" name="kedudukan_pasangan" placeholder="Kedudukan Pasangan" value="Purusa" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-sm-9">
                                                <div class="form-group">
                                                    <label class="small"for="status_pasangan">Status<span class="text-danger small">*</span></label>
                                                    <select class="select2 custom-select @error ('status_pasangan') is-invalid @enderror" name="status_pasangan" id="status_pasangan"  style="width: 100%" required>
                                                        <option value="tetap_di_banjar_dan_kk_lama" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama') selected @endif>Tetap di Banjar Adat dan Keluarga Lama</option>
                                                        <option value="tetap_di_banjar_dan_kk_baru" @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru') selected @endif>Tetap di Banjar Adat dan Pindah Keluarga</option>
                                                    </select>
                                                    <small class="small">(Pilih Krama Mipil dan Pasangan terlebih dahulu)</small>
                                                    @error('status_pasangan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Status Pasangan wajib dipilih
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru')
                                            <div class="pembungkus">
                                                <div id="asal-pasangan-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_pasangan">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_pasangan') is-invalid @enderror" name="kabupaten_pasangan" id="kabupaten_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_pasangan')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_pasangan">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_pasangan') is-invalid @enderror" name="kecamatan_pasangan" id="kecamatan_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_pasangan')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_pasangan">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_pasangan') is-invalid @enderror" name="desa_adat_pasangan" id="desa_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_pasangan')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_pasangan">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_pasangan') is-invalid @enderror" name="banjar_adat_pasangan" id="banjar_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_pasangan')
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
                                                
                                                <div id="krama-mipil-baru-pasangan">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_pasangan_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_pasangan_placeholder" name="krama_mipil_baru_pasangan_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ $krama_mipil_baru_pradana->cacah_krama_mipil->penduduk->nama }}" required readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_pasangan_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_pasangan') is-invalid @enderror"  id="krama_mipil_baru_pasangan" name="krama_mipil_baru_pasangan"  value="{{ $krama_mipil_baru_pradana->id }}" required hidden>
                                                                @error('krama_mipil_baru_pasangan')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_pasangan') is-invalid @enderror" name="status_hubungan_baru_pasangan" id="status_hubungan_baru_pasangan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_purusa == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_purusa == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_purusa == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_purusa == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_purusa == 'famili_lain') selected @endif>Famili Lain</option>
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
                                                    </div>
                                                </div>

                                                <div id="asal-pasangan-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_pasangan_keluar') is-invalid @enderror" name="provinsi_pasangan_keluar" id="provinsi_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_pasangan_keluar') is-invalid @enderror" name="kabupaten_pasangan_keluar" id="kabupaten_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_pasangan_keluar') is-invalid @enderror" name="kecamatan_pasangan_keluar" id="kecamatan_pasangan_keluar"  style="width: 100%"  aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_pasangan_keluar') is-invalid @enderror" name="desa_pasangan_keluar" id="desa_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_pasangan_keluar')
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
                                        @else
                                            <div class="pembungkus">
                                                <div id="asal-pasangan-dalam-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kabupaten_pasangan">Kabupaten<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kabupaten_pasangan') is-invalid @enderror" name="kabupaten_pasangan" id="kabupaten_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kabupaten</option>
                                                                    @foreach($kabupatens as $kabupaten)
                                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('kabupaten_pasangan')
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
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label class="small" for="kecamatan_pasangan">Kecamatan<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('kecamatan_pasangan') is-invalid @enderror" name="kecamatan_pasangan" id="kecamatan_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Kecamatan</option>
                                                                </select>
                                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                                @error('kecamatan_pasangan')
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
                    
                                                    <div class="row mb-3">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="desa_adat_pasangan">Desa Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('desa_adat_pasangan') is-invalid @enderror" name="desa_adat_pasangan" id="desa_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Desa Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                                @error('desa_adat_pasangan')
                                                                    <div class="invalid-feedback text-start">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Desa Adat wajib dipilih
                                                                    </div>
                                                                @enderror 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12">
                                                            <div class="form-group mb-n1">
                                                                <label class="small" for="banjar_adat_pasangan">Banjar Adat<span class="text-danger small">*</span></label>
                                                                <select class="select2 custom-select @error ('banjar_adat_pasangan') is-invalid @enderror" name="banjar_adat_pasangan" id="banjar_adat_pasangan"  style="width: 100%">
                                                                    <option value="">Pilih Banjar Adat</option>
                                                                </select>
                                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                                @error('banjar_adat_pasangan')
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
                                                
                                                <div id="krama-mipil-baru-pasangan" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-sm-9">
                                                            <div class="form-group">
                                                                <label for="title" class="small">Krama Mipil Baru<span class="text-danger small">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error ('krama_mipil_baru_pasangan_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_baru_pasangan_placeholder" name="krama_mipil_baru_pasangan_placeholder" placeholder="Pilih Krama Mipil Baru" value="{{ old('krama_mipil_baru_pasangan_placeholder') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_baru_pasangan_modal()">
                                                                            <span class="text">Pilih Krama</span>
                                                                            <span class="icon">
                                                                                <i class="fas fa-user-plus"></i>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" class="form-control @error ('krama_mipil_baru_pasangan') is-invalid @enderror"  id="krama_mipil_baru_pasangan" name="krama_mipil_baru_pasangan"  value="{{ old('krama_mipil_baru_pasangan') }}" hidden>
                                                                @error('krama_mipil_baru_pasangan')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </div>
                                                                @else
                                                                    <div class="invalid-feedback">
                                                                        Krama Mipil Baru wajib dipilih
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3">
                                                            <div class="form-group">
                                                                <label for="status_hubungan" class="small">Status Hubungan<span class="text-danger small">*</span></label>
                                                                <select class="can-empty select2 custom-select @error ('status_hubungan_baru_pasangan') is-invalid @enderror" name="status_hubungan_baru_pasangan" id="status_hubungan_baru_pasangan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                                                    {{-- <option value="">Pilih Status Hubungan</option> --}}
                                                                    <option value="anak" @if($perceraian->status_hubungan_baru_purusa == 'anak') selected @endif>Anak</option>
                                                                    <option value="cucu" @if($perceraian->status_hubungan_baru_purusa == 'cucu') selected @endif>Cucu</option>
                                                                    <option value="ayah" @if($perceraian->status_hubungan_baru_purusa == 'ayah') selected @endif>Ayah</option>
                                                                    <option value="ibu" @if($perceraian->status_hubungan_baru_purusa == 'ibu') selected @endif>Ibu</option>
                                                                    <option value="famili_lain" @if($perceraian->status_hubungan_baru_purusa == 'famili_lain') selected @endif>Famili Lain</option>
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
                                                    </div>
                                                </div>
            
                                                <div id="asal-pasangan-luar-bali" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Provinsi</label>
                                                            <select class="select2 custom-select @error ('provinsi_pasangan_keluar') is-invalid @enderror" name="provinsi_pasangan_keluar" id="provinsi_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Provinsi">
                                                                <option value="">Pilih Provinsi</option>
                                                                @foreach($provinsis as $provinsi)
                                                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('provinsi_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Provinsi wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kabupaten/Kota Asal</label>
                                                            <select class="select2 custom-select @error ('kabupaten_pasangan_keluar') is-invalid @enderror" name="kabupaten_pasangan_keluar" id="kabupaten_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Kabupaten" >
                                                                <option value="">Pilih Kabupaten/Kota</option>
                                                            </select>
                                                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                                                            @error('kabupaten_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kabupaten/Kota wajib dipilih
                                                                </div>
                                                            @enderror 
                                                        </div>
                                                    </div>
                            
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Kecamatan Asal</label>
                                                            <select class="select2 custom-select @error ('kecamatan_pasangan_keluar') is-invalid @enderror" name="kecamatan_pasangan_keluar" id="kecamatan_pasangan_keluar"  style="width: 100%"  aria-placeholder="Pilih Kecamatan">
                                                                <option value="">Pilih Kecamatan</option>
                                                            </select>
                                                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                                                            @error('kecamatan_pasangan_keluar')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    Kecamatan wajib dipilih
                                                                </div>
                                                            @enderror  
                                                        </div>
                                                        <div class="col-lg-6 col-sm-12 py-2">
                                                            <label class="small" for="title">Desa/Kelurahan Asal</label>
                                                            <select class="select2 custom-select @error ('desa_pasangan_keluar') is-invalid @enderror" name="desa_pasangan_keluar" id="desa_pasangan_keluar"  style="width: 100%" aria-placeholder="Pilih Desa">
                                                                <option value="">Pilih Desa/Kelurahan</option>
                                                            </select>
                                                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                                                            @error('desa_pasangan_keluar')
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
                                        @endif
                                    </div>
                                @endif                                
                                <div id="data-anggota-keluarga">
                                    <hr class="my-4" />
                                    <h5 class="card-title text-primary">Data Anggota Keluarga</h5>
                                    <div id="anggota-keluarga-0" @if($anggota_krama_mipil->count() > 0) style="display:none;" @endif>
                                        <div class="alert alert-info text-center" id="alert-anggota-keluarga" role="alert">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Tidak terdapat anggota keluarga.
                                        </div>
                                    </div>
                                    <div id="anggota-keluarga-isi" @if($anggota_krama_mipil->count() == 0) style="display:none;" @endif>
                                        @foreach($anggota_krama_mipil as $anggota)
                                        <div class="row" id="anggota-keluarga-1">
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="small"for="title">Nama</label>
                                                    <input type="text" class="form-control" id="nama_{{ $anggota->id }}" name="nama_{{ $anggota->id }}" placeholder="Nama Anggota Keluarga" value="{{ $anggota->cacah_krama_mipil->penduduk->nama }}" required disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="form-group">
                                                    <label class="small"for="title">Status Hubungan</label>
                                                    <input type="text" class="form-control" id="status_hubungan_{{ $anggota->id }}'" name="status_hubungan_{{ $anggota->id }}" placeholder="Status Hubungan" value="{{ $anggota->status_hubungan }}" required readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="form-group">
                                                <label for="status" class="small">Status<span class="text-danger">*</span></label>
                                                    <select class="can-empty select2 custom-select" name="status_anggota_[{{ $anggota->id }}]" id="status_anggota_{{ $anggota->id }}"  style="width: 100%" required>
                                                        <option value="ikut_purusa" @if($anggota->status_baru == 'ikut_purusa') selected @endif>Ikut Purusa</option>
                                                        <option value="ikut_pradana" @if($anggota->status_baru == 'ikut_pradana') selected @endif>Ikut Pradana</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="data-perceraian">
                                    <hr class="my-4" />
                                    <h5 class="card-title text-primary">Data Perceraian</h5>

                                    {{-- TANGGAL - PEMUPUT --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="tanggal_perceraian">Tanggal Perceraian<span class="text-danger small">*</span></label>
                                                <input type="text" class="datepicker-here form-control @error ('tanggal_perceraian') is-invalid @enderror" placeholder="Masukkan Tanggal Perceraian" name="tanggal_perceraian" id="tanggal_perceraian" value="{{ old('tanggal_perceraian',date('d M Y', strtotime($perceraian->tanggal_perceraian))) }}" required>
                                                @error('tanggal_perceraian')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Tanggal Perceraian wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Pemuput Perceraian<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nama_pemuput') is-invalid @enderror"  id="nama_pemuput" name="nama_pemuput" placeholder="Masukkan Nama Pemuput Perceraian" value="{{ old('nama_pemuput', $perceraian->nama_pemuput) }}" required>
                                                @error('nama_pemuput')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Pemuput Perceraian wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- BUKTI PERCERAIAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="nomor_bukti_perceraian">No. Bukti Perceraian<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nomor_bukti_perceraian') is-invalid @enderror"  id="nomor_bukti_perceraian" name="nomor_bukti_perceraian" placeholder="Masukkan No. Bukti Perceraian" value="{{ old('nomor_bukti_perceraian', $perceraian->nomor_perceraian) }}" required>
                                                @error('nomor_bukti_perceraian')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        No. Bukti Perceraian wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="file_bukti_perceraian">File Bukti Perceraian<span class="text-danger small">*</span></label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_bukti_perceraian') is-invalid @enderror" id="file_bukti_perceraian" name="file_bukti_perceraian" accept=".pdf, .jpg">
                                                    <label for="file_bukti_perceraian_label" id="file_bukti_perceraian_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_bukti_perceraian')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Bukti Perceraian wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                @if($perceraian->file_bukti_perceraian != NULL)   
                                                    <a class="text-start text-primary small" target="_blank" href="{{$perceraian->file_bukti_perceraian}}"><p><i class="fas fa-download"></i> Unduh File Bukti Perceraian</p></a>
                                                @endif
                                                <div id="validasi-file_bukti_perceraian" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Bukti Perceraian maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AKTA PERCERAIAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="nomor_akta_perceraian">No. Akta Perceraian</label>
                                                <input type="text" class="form-control @error ('nomor_akta_perceraian') is-invalid @enderror"  id="nomor_akta_perceraian" name="nomor_akta_perceraian" placeholder="Masukkan No. Akta Perceraian" value="{{ old('nomor_akta_perceraian', $perceraian->nomor_akta_perceraian) }}">
                                                @error('nomor_akta_perceraian')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="file_akta_perceraian">File Akta Perceraian</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_akta_perceraian') is-invalid @enderror" id="file_akta_perceraian" name="file_akta_perceraian" accept=".pdf, .jpg">
                                                    <label for="file_akta_perceraian_label" id="file_akta_perceraian_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_akta_perceraian')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                @if($perceraian->file_akta_perceraian != NULL)   
                                                    <a class="text-start text-primary small" target="_blank" href="{{$perceraian->file_akta_perceraian}}"><p><i class="fas fa-download"></i> Unduh File Akta Perceraian</p></a>
                                                @endif
                                                <div id="validasi-file_akta_perceraian" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Akta Percerian maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="keterangan">Keterangan</label>
                                        <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan">{{ $perceraian->keterangan }}</textarea>
                                    </div>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-perceraian-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perceraian({{ $perceraian->id }}, '0')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan sebagai Draft</span>
                                        </button>

                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perceraian({{ $perceraian->id }}, '3')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan dan Sahkan</span>
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

    <!-- Select Krama Mipil Baru Modal -->
    <div class="modal fade" id="select_krama_mipil_baru_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <table class="table table-bordered table-hover" id="dataTable-krama-mipil-baru-krama-mipil" width="100%" cellspacing="0">
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

    <!-- Select Krama Mipil Baru Pasangan Modal -->
    <div class="modal fade" id="select_krama_mipil_baru_pasangan_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <table class="table table-bordered table-hover" id="dataTable-krama-mipil-baru-pasangan" width="100%" cellspacing="0">
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

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready( function () {
            //DATEPICKER
            $("#tanggal_perceraian").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perceraian').addClass('active');

            //VALIDASI LAMPIRAN
            $("#file_bukti_perceraian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file_bukti_perceraian').show();
                    $('#file_bukti_perceraian').val("");
                }else{
                    document.getElementById('file_bukti_perceraian_label').innerHTML = document.getElementById('file_bukti_perceraian').files[0].name;
                    $('#validasi-file_bukti_perceraian').hide();
                }
            });

            $("#file_akta_perceraian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file_akta_perceraian').show();
                    $('#file_akta_perceraian').val("");
                }else{
                    document.getElementById('file_akta_perceraian_label').innerHTML = document.getElementById('file_akta_perceraian').files[0].name;
                    $('#validasi-file_akta_perceraian').hide();
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

            //Status Pasangan on Change
            $('#status_pasangan').on('change', function(){
                if($(this).val() == 'tetap_di_banjar_dan_kk_lama'){
                    //Hide or Show
                    $('#krama-mipil-baru-pasangan').fadeOut();
                    $('#asal-pasangan-dalam-bali').fadeOut();
                    $('#asal-pasangan-luar-bali').fadeOut();

                    //Set Required or Not
                    $('#kabupaten_pasangan').prop('required', false);
                    $('#kecamatan_pasangan').prop('required', false);
                    $('#desa_adat_pasangan').prop('required', false);
                    $('#banjar_adat_pasangan').prop('required', false);
                    $('#krama_mipil_baru_pasangan').prop('required', false);
                    $('#krama_mipil_baru_pasangan').val('');
                    $('#krama_mipil_baru_pasangan_placeholder').val('');

                }
                else if($(this).val() == 'tetap_di_banjar_dan_kk_baru'){
                    //Hide or Show
                    $('#krama-mipil-baru-pasangan').fadeIn();
                    $('#asal-pasangan-dalam-bali').hide();
                    $('#asal-pasangan-luar-bali').hide();

                    //Set Required or Not
                    $('#kabupaten_pasangan').prop('required', false);
                    $('#kecamatan_pasangan').prop('required', false);
                    $('#desa_adat_pasangan').prop('required', false);
                    $('#banjar_adat_pasangan').prop('required', false);
                    $('#krama_mipil_baru_pasangan').prop('required', true);
                    $('#krama_mipil_baru_pasangan').val('');
                    $('#krama_mipil_baru_pasangan_placeholder').val('');
                }
                else if($(this).val() == 'keluar_banjar'){
                    //Hide or Show
                    $('#krama-mipil-baru-pasangan').fadeIn();
                    $('#asal-pasangan-dalam-bali').fadeIn();
                    $('#asal-pasangan-luar-bali').hide();

                    //Set Required or Not
                    $('#kabupaten_pasangan').prop('required', true);
                    $('#kecamatan_pasangan').prop('required', true);
                    $('#desa_adat_pasangan').prop('required', true);
                    $('#banjar_adat_pasangan').prop('required', true);
                    $('#krama_mipil_baru_pasangan').prop('required', true);
                    $('#krama_mipil_baru_pasangan').val('');
                    $('#krama_mipil_baru_pasangan_placeholder').val('');
                }
                else if($(this).val() == 'keluar_bali'){
                    //Hide or Show
                    $('#krama-mipil-baru-pasangan').hide();
                    $('#asal-pasangan-dalam-bali').hide();
                    $('#asal-pasangan-luar-bali').fadeIn();

                    //Set Required or Not
                    $('#kabupaten_pasangan').prop('required', false);
                    $('#kecamatan_pasangan').prop('required', false);
                    $('#desa_adat_pasangan').prop('required', false);
                    $('#banjar_adat_pasangan').prop('required', false);
                    $('#krama_mipil_baru_pasangan').prop('required', false);
                    $('#krama_mipil_baru_pasangan').val('');
                    $('#krama_mipil_baru_pasangan_placeholder').val('');
                }
                else{
                    //Hide or Show
                    $('#krama-mipil-baru-pasangan').fadeOut();
                    $('#asal-pasangan-dalam-bali').fadeOut();
                    $('#asal-pasangan-luar-bali').fadeOut();

                    //Set Required or Not
                    $('#kabupaten_pasangan').prop('required', false);
                    $('#kecamatan_pasangan').prop('required', false);
                    $('#desa_adat_pasangan').prop('required', false);
                    $('#banjar_adat_pasangan').prop('required', false);
                    $('#pasangan_baru_pasangan').prop('required', false);
                    $('#pasangan_baru_pasangan').val('');
                    $('#pasangan_baru_pasangan_placeholder').val('');
                }
            });

            //Status Krama Mipil on Change
            $('#status_krama_mipil').on('change', function(){
                if($(this).val() == 'tetap_di_banjar_dan_kk_lama'){
                    //Hide or Show
                    $('#krama-mipil-baru-krama_mipil').fadeOut();
                    $('#asal-krama_mipil-dalam-bali').fadeOut();
                    $('#asal-krama_mipil-luar-bali').fadeOut();

                    //Set Required or Not
                    $('#kabupaten_krama_mipil').prop('required', false);
                    $('#kecamatan_krama_mipil').prop('required', false);
                    $('#desa_adat_krama_mipil').prop('required', false);
                    $('#banjar_adat_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil_placeholder').val('');

                }
                else if($(this).val() == 'tetap_di_banjar_dan_kk_baru'){
                    //Hide or Show
                    $('#krama-mipil-baru-krama_mipil').fadeIn();
                    $('#asal-krama_mipil-dalam-bali').hide();
                    $('#asal-krama_mipil-luar-bali').hide();

                    //Set Required or Not
                    $('#kabupaten_krama_mipil').prop('required', false);
                    $('#kecamatan_krama_mipil').prop('required', false);
                    $('#desa_adat_krama_mipil').prop('required', false);
                    $('#banjar_adat_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').prop('required', true);
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil_placeholder').val('');
                }
                else if($(this).val() == 'keluar_banjar'){
                    //Hide or Show
                    $('#krama-mipil-baru-krama_mipil').fadeIn();
                    $('#asal-krama_mipil-dalam-bali').fadeIn();
                    $('#asal-krama_mipil-luar-bali').hide();

                    //Set Required or Not
                    $('#kabupaten_krama_mipil').prop('required', true);
                    $('#kecamatan_krama_mipil').prop('required', true);
                    $('#desa_adat_krama_mipil').prop('required', true);
                    $('#banjar_adat_krama_mipil').prop('required', true);
                    $('#krama_mipil_baru_krama_mipil').prop('required', true);
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil_placeholder').val('');
                }
                else if($(this).val() == 'keluar_bali'){
                    //Hide or Show
                    $('#krama-mipil-baru-krama_mipil').hide();
                    $('#asal-krama_mipil-dalam-bali').hide();
                    $('#asal-krama_mipil-luar-bali').fadeIn();

                    //Set Required or Not
                    $('#kabupaten_krama_mipil').prop('required', false);
                    $('#kecamatan_krama_mipil').prop('required', false);
                    $('#desa_adat_krama_mipil').prop('required', false);
                    $('#banjar_adat_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil_placeholder').val('');
                }
                else{
                    //Hide or Show
                    $('#krama-mipil-baru-krama_mipil').fadeOut();
                    $('#asal-krama_mipil-dalam-bali').fadeOut();
                    $('#asal-krama_mipil-luar-bali').fadeOut();

                                        //Set Required or Not
                    $('#kabupaten_krama_mipil').prop('required', false);
                    $('#kecamatan_krama_mipil').prop('required', false);
                    $('#desa_adat_krama_mipil').prop('required', false);
                    $('#banjar_adat_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').prop('required', false);
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil_placeholder').val('');
                }
            });

            $('#pasangan').on('change', function(){
                $('#status_pasangan').val('tetap_di_banjar_dan_kk_lama').trigger('change');
            });

            //Daerah Krama Mipil on Change
                $('#kabupaten_krama_mipil').on('change', function(){
                    //EMPTY CHILD
                    $('#kecamatan_krama_mipil').empty();
                    $('#desa_adat_krama_mipil').empty();
                    $('#banjar_adat_krama_mipil').empty();
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil').val('');

                    //SET CHILD PLACEHOLDER
                    $('#kecamatan_krama_mipil').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_adat_krama_mipil').append('<option value="">Pilih Desa Adat</option>');
                    $('#banjar_adat_krama_mipil').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        jQuery.ajax({
                            url: "/admin/master/kecamatan/"+$(this).val(),
                            method: 'get',
                            success: function(result){
                                $('#kecamatan_krama_mipil').empty();
                                $('#kecamatan_krama_mipil').append('<option value="">Pilih Kecamatan</option>');
                                result['0'].forEach(element => { 
                                    $('#kecamatan_krama_mipil').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });            
                            }
                        });
                    }
                });

                $('#kecamatan_krama_mipil').on('change', function(){
                    //EMPTY CHILD
                    $('#desa_adat_krama_mipil').empty();
                    $('#banjar_adat_krama_mipil').empty();
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil').val('');

                    //SET CHILD PLACEHOLDER
                    $('#desa_adat_krama_mipil').append('<option value="">Pilih Desa Adat</option>');
                    $('#banjar_adat_krama_mipil').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        jQuery.ajax({
                            url: "/admin/master/desa-adat/"+$(this).val(),
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#desa_adat_krama_mipil').empty();
                                $('#desa_adat_krama_mipil').append('<option value="">Pilih Desa Adat</option>');
                                result.desa_adats.forEach(element => {
                                    $('#desa_adat_krama_mipil').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                                });                                  
                            }
                        });
                    }
                });

                $('#desa_adat_krama_mipil').on('change', function(){
                    //GET CURR BANJAR ADAT ID
                    var banjar_adat_id = {{ Session::get('banjar_adat_id')}};

                    //EMPTY CHILD
                    $('#banjar_adat_krama_mipil').empty();
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil').val('');

                    //SET CHILD PLACEHOLDER
                    $('#banjar_adat_krama_mipil').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        var url = "{{ route('admin-banjar-adat-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#banjar_adat_krama_mipil').empty();
                                $('#banjar_adat_krama_mipil').append('<option value="" selected>Pilih Banjar Adat</option>');
                                result.banjar_adats.forEach(element => {
                                    if(element['id'] != banjar_adat_id){
                                        $('#banjar_adat_krama_mipil').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                                    }
                                });                     
                            }
                        });
                    }
                });

                $('#banjar_adat_krama_mipil').on('change', function(){
                    //EMPTY CHILD
                    $('#krama_mipil_baru_krama_mipil').val('');
                    $('#krama_mipil_baru_krama_mipil').val('');
                });

                $('#provinsi_krama_mipil_keluar').on('change', function(){
                    $('#kabupaten_krama_mipil_keluar').empty();
                    $('#kecamatan_krama_mipil_keluar').empty();
                    $('#desa_krama_mipil_keluar').empty();
                    $('#kabupaten_krama_mipil_keluar').append('<option value="">Pilih Kabupaten/Kota</option>');
                    $('#kecamatan_krama_mipil_keluar').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_krama_mipil_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-kabupaten-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                $('#kabupaten_krama_mipil_keluar').empty();
                                $('#kabupaten_krama_mipil_keluar').append('<option value="">Pilih Kabupaten/Kota</option>');
                                result['0'].forEach(element => {
                                    $('#kabupaten_krama_mipil_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });

                $('#kabupaten_krama_mipil_keluar').on('change', function(){
                    $('#kecamatan_krama_mipil_keluar').empty();
                    $('#desa_krama_mipil_keluar').empty();
                    $('#kecamatan_krama_mipil_keluar').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_krama_mipil_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-kecamatan-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#kecamatan_krama_mipil_keluar').empty();
                                $('#kecamatan_krama_mipil_keluar').append('<option value="">Pilih Kecamatan</option>');
                                result['0'].forEach(element => {
                                    $('#kecamatan_krama_mipil_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });

                $('#kecamatan_krama_mipil_keluar').on('change', function(){
                    $('#desa_krama_mipil_keluar').empty();
                    $('#desa_krama_mipil_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#desa_krama_mipil_keluar').empty();
                                $('#desa_krama_mipil_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                                result['0'].forEach(element => {
                                    $('#desa_krama_mipil_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });
            //Daerah Krama Mipil on Change

            //Daerah Pasangan on Change
                $('#kabupaten_pasangan').on('change', function(){
                    //EMPTY CHILD
                    $('#kecamatan_pasangan').empty();
                    $('#desa_adat_pasangan').empty();
                    $('#banjar_adat_pasangan').empty();
                    $('#pasangan_baru_pasangan').val('');
                    $('#pasangan_baru_pasangan').val('');

                    //SET CHILD PLACEHOLDER
                    $('#kecamatan_pasangan').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_adat_pasangan').append('<option value="">Pilih Desa Adat</option>');
                    $('#banjar_adat_pasangan').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        jQuery.ajax({
                            url: "/admin/master/kecamatan/"+$(this).val(),
                            method: 'get',
                            success: function(result){
                                $('#kecamatan_pasangan').empty();
                                $('#kecamatan_pasangan').append('<option value="">Pilih Kecamatan</option>');
                                result['0'].forEach(element => { 
                                    $('#kecamatan_pasangan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });            
                            }
                        });
                    }
                });

                $('#kecamatan_pasangan').on('change', function(){
                    //EMPTY CHILD
                    $('#desa_adat_pasangan').empty();
                    $('#banjar_adat_pasangan').empty();
                    $('#pasangan_baru_pasangan').val('');
                    $('#pasangan_baru_pasangan').val('');

                    //SET CHILD PLACEHOLDER
                    $('#desa_adat_pasangan').append('<option value="">Pilih Desa Adat</option>');
                    $('#banjar_adat_pasangan').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        jQuery.ajax({
                            url: "/admin/master/desa-adat/"+$(this).val(),
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#desa_adat_pasangan').empty();
                                $('#desa_adat_pasangan').append('<option value="">Pilih Desa Adat</option>');
                                result.desa_adats.forEach(element => {
                                    $('#desa_adat_pasangan').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                                });                                  
                            }
                        });
                    }
                });

                $('#desa_adat_pasangan').on('change', function(){
                    //GET CURR BANJAR ADAT ID
                    var banjar_adat_id = {{ Session::get('banjar_adat_id')}};

                    //EMPTY CHILD
                    $('#banjar_adat_pasangan').empty();
                    $('#pasangan_baru_pasangan').val('');
                    $('#pasangan_baru_pasangan').val('');

                    //SET CHILD PLACEHOLDER
                    $('#banjar_adat_pasangan').append('<option value="">Pilih Banjar Adat</option>');

                    if($(this).val() != ""){
                        var url = "{{ route('admin-banjar-adat-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#banjar_adat_pasangan').empty();
                                $('#banjar_adat_pasangan').append('<option value="" selected>Pilih Banjar Adat</option>');
                                result.banjar_adats.forEach(element => {
                                    if(element['id'] != banjar_adat_id){
                                        $('#banjar_adat_pasangan').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                                    }
                                });                     
                            }
                        });
                    }
                });

                $('#banjar_adat_pasangan').on('change', function(){
                    //EMPTY CHILD
                    $('#pasangan_baru_pasangan').val('');
                    $('#pasangan_baru_pasangan').val('');
                });

                $('#provinsi_pasangan_keluar').on('change', function(){
                    $('#kabupaten_pasangan_keluar').empty();
                    $('#kecamatan_pasangan_keluar').empty();
                    $('#desa_pasangan_keluar').empty();
                    $('#kabupaten_pasangan_keluar').append('<option value="">Pilih Kabupaten/Kota</option>');
                    $('#kecamatan_pasangan_keluar').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_pasangan_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-kabupaten-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                $('#kabupaten_pasangan_keluar').empty();
                                $('#kabupaten_pasangan_keluar').append('<option value="">Pilih Kabupaten/Kota</option>');
                                result['0'].forEach(element => {
                                    $('#kabupaten_pasangan_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });

                $('#kabupaten_pasangan_keluar').on('change', function(){
                    $('#kecamatan_pasangan_keluar').empty();
                    $('#desa_pasangan_keluar').empty();
                    $('#kecamatan_pasangan_keluar').append('<option value="">Pilih Kecamatan</option>');
                    $('#desa_pasangan_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-kecamatan-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#kecamatan_pasangan_keluar').empty();
                                $('#kecamatan_pasangan_keluar').append('<option value="">Pilih Kecamatan</option>');
                                result['0'].forEach(element => {
                                    $('#kecamatan_pasangan_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });

                $('#kecamatan_pasangan_keluar').on('change', function(){
                    $('#desa_pasangan_keluar').empty();
                    $('#desa_pasangan_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                    if($(this).val() != ""){
                        var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                        url = url.replace(':id', $(this).val());
                        jQuery.ajax({
                            url: url,
                            method: 'get',
                            success: function(result){
                                console.log(result);
                                $('#desa_pasangan_keluar').empty();
                                $('#desa_pasangan_keluar').append('<option value="">Pilih Desa/Kelurahan</option>');
                                result['0'].forEach(element => {
                                    $('#desa_pasangan_keluar').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                                });                     
                            }
                        });
                    }
                });
            //Daerah Pasangan on Change
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

        //Datatable Krama Mipil
            var TableDatatablesEditable = function () {
                var handleTable = function () {
                    //Datatable Krama Mipil Lama yang di Cerai
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
                            url : "{{ route('banjar-perceraian-datatable-krama-mipil') }}",
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

                    //Datatable Keluarga Krama Mipil Baru dari Krama yang Cerai
                    var table_baru_krama_mipil = $('#dataTable-krama-mipil-baru-krama-mipil');
                    var oTable_baru_krama_mipil = table_baru_krama_mipil.DataTable({
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
                            url : "{{ route('banjar-perceraian-datatable-krama-mipil-baru-krama-mipil') }}",
                            data : function(d){
                                d.krama_mipil_saat_ini = $('#krama_mipil').val();
                                d.banjar_adat_krama_mipil = $('#banjar_adat_krama_mipil').val();
                                d.krama_mipil_pasangan = $('#krama_mipil_baru_pasangan').val();
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

                    baru_krama_mipil_filter = () => {
                        oTable_baru_krama_mipil.ajax.reload();
                    }

                    $('#dataTable-krama-mipil-baru-krama-mipil tbody').on('click', 'td.dt-control', function () {
                        var tr = $(this).closest('tr');
                        var row = oTable_baru_krama_mipil.row( tr );
                
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

                    //Datatable Keluarga Krama Mipil Baru dari Pasangan yang Cerai
                    var table_baru_pasangan = $('#dataTable-krama-mipil-baru-pasangan');
                    var oTable_baru_pasangan = table_baru_pasangan.DataTable({
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
                            url : "{{ route('banjar-perceraian-datatable-krama-mipil-baru-pasangan') }}",
                            data : function(d){
                                d.krama_mipil_saat_ini = $('#krama_mipil').val();
                                d.banjar_adat_pasangan = $('#banjar_adat_pasangan').val();
                                d.krama_mipil_krama_mipil = $('#krama_mipil_baru_krama_mipil').val();
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

                    baru_pasangan_filter = () => {
                        oTable_baru_pasangan.ajax.reload();
                    }

                    $('#dataTable-krama-mipil-baru-pasangan tbody').on('click', 'td.dt-control', function () {
                        var tr = $(this).closest('tr');
                        var row = oTable_baru_pasangan.row( tr );
                
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

            //Pilih Krama Mipil Lama (Yang akan di cerai)
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
                    title: 'Krama Mipil Berhasil Dipilih'
                })
                var url = "{{ route('banjar-perceraian-pilih-krama-mipil', ":id") }}";
                url = url.replace(':id', id);
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        //SET DATA KRAMA MIPIL
                            $('#kedudukan_krama_mipil').val(result.krama_mipil.kedudukan_krama_mipil);
                            $('#status_krama_mipil').empty();
                            if(result.krama_mipil.kedudukan_krama_mipil == 'Purusa'){
                                $('#status_krama_mipil').append('<option value="tetap_di_banjar_dan_kk_lama">Tetap di Banjar Adat dan Keluarga Lama</option>');
                                $('#status_krama_mipil').append('<option value="tetap_di_banjar_dan_kk_baru">Tetap di Banjar Adat dan Pindah Keluarga</option>');
                            }else{
                                $('#status_krama_mipil').append('<option value="tetap_di_banjar_dan_kk_baru">Tetap di Banjar Adat dan Pindah Keluarga</option>');
                                $('#status_krama_mipil').append('<option value="keluar_banjar">Keluar dari Banjar Adat</option>');
                                $('#status_krama_mipil').append('<option value="keluar_bali">Keluar Bali</option>');
                            }
                        //SET DATA KRAMA MIPIL

                        //SET DATA PASANGAN
                            result.pasangan.forEach(element => {
                                $('#pasangan').empty();
                                $('#pasangan').append('<option value="'+element.cacah_krama_mipil.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                            });
                            $('#status_pasangan').empty();
                            if(result.krama_mipil.kedudukan_krama_mipil == 'Purusa'){
                                $('#kedudukan_pasangan').val('Pradana');
                                $('#status_pasangan').append('<option value="tetap_di_banjar_dan_kk_baru">Tetap di Banjar Adat dan Pindah Keluarga</option>');
                                $('#status_pasangan').append('<option value="keluar_banjar">Keluar dari Banjar Adat</option>');
                                $('#status_pasangan').append('<option value="keluar_bali">Keluar Bali</option>');
                            }else{
                                $('#kedudukan_pasangan').val('Purusa');
                                $('#status_pasangan').append('<option value="tetap_di_banjar_dan_kk_lama">Tetap di Banjar Adat dan Keluarga Lama</option>');
                                $('#status_pasangan').append('<option value="tetap_di_banjar_dan_kk_baru">Tetap di Banjar Adat dan Pindah Keluarga</option>');
                            }
                        //SET DATA PASANGAN

                        //SET DATA ANGGOTA KELUARGA
                            if(result.anggota_krama_mipil.length>0){
                                $('#anggota-keluarga-0').hide();
                                $('#anggota-keluarga-isi').empty();
                                result.anggota_krama_mipil.forEach((element, index) => {
                                    let html = '<div class="row" id="anggota-keluarga-1">';
                                        /*NAMA ANGGOTA*/
                                        html += '<div class="col-lg-6 col-sm-12">';
                                        html += '<div class="form-group">';
                                        html += '<label class="small"for="title">Nama</label>';
                                        html += '<input type="text" class="form-control" id="nama_'+element.id+'" name="nama_'+element.id+'" placeholder="Nama Anggota Keluarga" value="'+element.cacah_krama_mipil.penduduk.nama+'" required disabled>';
                                        html += '</div></div>';

                                        /*STATUS HUBUNGAN ANGGOTA*/
                                        html += '<div class="col-lg-3 col-sm-6">';
                                        html += '<div class="form-group">';
                                        html += '<label class="small"for="title">Status Hubungan</label>';
                                        html += '<input type="text" class="form-control" id="status_hubungan_'+element.id+'" name="status_hubungan_'+element.id+'" placeholder="Status Hubungan" value="'+element.status_hubungan+'" required readonly>';
                                        html += '</div></div>';

                                        /*IKUT PURUSA/PRADANA*/
                                        html += '<div class="col-lg-3 col-sm-6">';
                                        html += '<div class="form-group">';
                                        html += '<label for="status" class="small">Status<span class="text-danger">*</span></label>';
                                        html += '<select class="can-empty select2 custom-select" name="status_anggota_['+element.id+']" id="status_anggota_'+element.id+'"  style="width: 100%" required>'
                                        html += '<option value="ikut_purusa">Ikut Purusa</option>';
                                        html += '<option value="ikut_pradana">Ikut Pradana</option>';
                                        html += '</select>';
                                        html += '</div></div>';

                                        html += '</div>';
                                    $('#anggota-keluarga-isi').append(html);
                                });
                                $('#anggota-keluarga-isi').show();
                                /*REINIT CUSTOM SELECT*/
                                $(".custom-select").select2({
                                    language: {
                                        noResults: function (params) {
                                        return "Data tidak ditemukan";
                                        }
                                    }
                                });
                            }else{
                                $('#anggota-keluarga-isi').hide();
                                $('#anggota-keluarga-isi').empty();
                                $('#alert-anggota-keluarga').text('Tidak terdapat anggota keluarga');
                                $('#anggota-keluarga-0').show();
                            }
                        //SET DATA ANGGOTA KELUARGA

                        //RESET DATA KRAMA MIPIL
                        if(result.krama_mipil.kedudukan_krama_mipil == 'Purusa'){
                            $('#status_krama_mipil').val('tetap_di_banjar_dan_kk_lama').trigger('change');
                            $('#status_pasangan').val('tetap_di_banjar_dan_kk_baru').trigger('change');
                        }else{
                            $('#status_pasangan').val('tetap_di_banjar_dan_kk_lama').trigger('change');
                            $('#status_krama_mipil').val('tetap_di_banjar_dan_kk_baru').trigger('change');
                        }
                    }
                });
            }

            //Pilih Krama Mipil Baru Krama Mipil
            function pilih_krama_mipil_baru_krama_mipil_modal(){
                let krama_mipil_saat_ini = $('#krama_mipil').val();
                let status_krama_mipil = $('#status_krama_mipil').val();
                if(krama_mipil_saat_ini){
                    if(status_krama_mipil == 'keluar_banjar'){
                        let banjar_adat_krama_mipil_baru = $('#banjar_adat_krama_mipil').val();
                        if(banjar_adat_krama_mipil_baru){
                            $('#select_krama_mipil_baru_krama_mipil_modal').on('show.bs.modal', function(e) {
                                baru_krama_mipil_filter();
                            }).modal('show');
                        }else{
                            Toast.fire({
                                icon: 'warning',
                                title: 'Pilih Banjar Adat Asal dari Krama Mipil Baru Terlebih Dahulu'
                            });
                        }
                    }else{
                        $('#select_krama_mipil_baru_krama_mipil_modal').on('show.bs.modal', function(e) {
                            baru_krama_mipil_filter();
                        }).modal('show');
                    }
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Krama Mipil yang Dicerai Terlebih Dahulu'
                    });
                }
            }

            function pilih_krama_mipil_baru_krama_mipil(id, nama){
                $('#krama_mipil_baru_krama_mipil').val(id);
                $('#krama_mipil_baru_krama_mipil_placeholder').val(nama);
                $('#krama_mipil_baru_krama_mipil_placeholder').prop('readonly', true);
                $('#select_krama_mipil_baru_krama_mipil_modal').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Krama Mipil Baru Berhasil Dipilih'
                });
            }

            //Pilih Krama Mipil Baru Krama Mipil
            function pilih_krama_mipil_baru_pasangan_modal(){
                let krama_mipil_saat_ini = $('#krama_mipil').val();
                let status_pasangan = $('#status_pasangan').val();
                if(krama_mipil_saat_ini){
                    if(status_pasangan == 'keluar_banjar'){
                        let banjar_adat_pasangan = $('#banjar_adat_pasangan').val();
                        if(banjar_adat_pasangan){
                            $('#select_krama_mipil_baru_pasangan_modal').on('show.bs.modal', function(e) {
                                baru_pasangan_filter();
                            }).modal('show');
                        }else{
                            Toast.fire({
                                icon: 'warning',
                                title: 'Pilih Banjar Adat Asal dari Krama Mipil Baru Terlebih Dahulu'
                            });
                        }
                    }else{
                        $('#select_krama_mipil_baru_pasangan_modal').on('show.bs.modal', function(e) {
                            baru_pasangan_filter();
                        }).modal('show');
                    }
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Krama Mipil yang Dicerai Terlebih Dahulu'
                    });
                }
            }

            function pilih_krama_mipil_baru_pasangan(id, nama){
                $('#krama_mipil_baru_pasangan').val(id);
                $('#krama_mipil_baru_pasangan_placeholder').val(nama);
                $('#krama_mipil_baru_pasangan_placeholder').prop('readonly', true);
                $('#select_krama_mipil_baru_pasangan_modal').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Krama Mipil Baru Berhasil Dipilih'
                });
            }
        //Datatable Krama Mipil


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
        function simpan_perceraian(id, status){
            var url = "{{ route('banjar-perceraian-update', [":id", ":status"]) }}";
            url = url.replace(':id', id);
            url = url.replace(':status', status);
            $("#form-edit-perceraian").attr("action", url);
            $('#form-edit-perceraian').submit(function (e){
                e.stopPropagation();
            });
        }

    </script>
@endpush