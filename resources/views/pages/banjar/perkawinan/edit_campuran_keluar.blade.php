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
        <div class="container mt-n15">
            <div class="card mb-4 mt-4">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-edit text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Perkawinan</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Perkawinan Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-9 col-xl-10 mt-4">
                            <form id="form-create-perkawinan" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf 
                                <div id="perkawinan_dalam_desa_adat">
                                    
                                    <h5 class="card-title text-primary">Data Dampati</h5>
                                    <div class="form-group">
                                        <label class="small" for="title">Pilih Cacah Krama<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('pradana_placeholder') is-invalid @enderror form-custom"  id="pradana_placeholder" name="pradana_placeholder" placeholder="Pilih Cacah Krama" value="{{ old('purusa_placeholder', $perkawinan->pradana->penduduk->nama) }}" required readonly>
                                            <div class="input-group-append">
                                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_pradana_modal()">
                                                    <span class="text">Pilih Cacah Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('pradana') is-invalid @enderror"  id="pradana" name="pradana"  value="{{ old('pradana', $perkawinan->pradana_id) }}" required hidden>
                                        @error('pradana')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Cacah Krama wajib dipilih
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="nama_pasangan">Nama Pasangan<span class="text-danger small">*</span></label>
                                        <input type="text" class="form-control @error ('nama_pasangan') is-invalid @enderror" placeholder="Masukkan Nama Pasangan" name="nama_pasangan" id="nama_pasangan" value="{{ old('nama_pasangan', $perkawinan->nama_pasangan) }}" required>
                                        @error('nama_pasangan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Nama Pasangan wajib diisi
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label class="small" for="nik_pasangan">NIK Pasangan<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nik_pasangan') is-invalid @enderror" placeholder="Masukkan NIK Pasangan" name="nik_pasangan" id="nik_pasangan" value="{{ old('nik_pasangan', $perkawinan->nik_pasangan) }}" required>
                                                @error('nik_pasangan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        NIK Pasangan wajib diisi
                                                    </div>
                                                @enderror
                                                <small class="text-danger" id="nik-validate" style="display:none;">
                                                    NIK harus terdiri dari 16 digit angka
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <div class="form-group">
                                                <label class="small" for="title">Agama Pasangan</label>
                                                <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama">
                                                    <option value="">Pilih Agama Pasangan</option>
                                                    @if(old('agama'))
                                                        <option value="islam" @if(old('agama') == 'islam') selected @endif>Islam</option>
                                                        <option value="protestan" @if(old('agama') == 'protestan') selected @endif>Protestan</option>
                                                        <option value="katolik" @if(old('agama') == 'katolik') selected @endif>Katolik</option>
                                                        <option value="hindu" @if(old('agama') == 'hindu') selected @endif>Hindu</option>
                                                        <option value="buddha" @if(old('agama') == 'buddha') selected @endif>Buddha</option>
                                                        <option value="khonghucu" @if(old('agama') == 'khonghucu') selected @endif>Khonghucu</option>
                                                    @else
                                                        <option value="islam" @if($perkawinan->agama_pasangan == 'islam') selected @endif>Islam</option>
                                                        <option value="protestan" @if($perkawinan->agama_pasangan == 'protestan') selected @endif>Protestan</option>
                                                        <option value="katolik" @if($perkawinan->agama_pasangan == 'katolik') selected @endif>Katolik</option>
                                                        <option value="hindu" @if($perkawinan->agama_pasangan == 'hindu') selected @endif>Hindu</option>
                                                        <option value="buddha" @if($perkawinan->agama_pasangan == 'buddha') selected @endif>Buddha</option>
                                                        <option value="khonghucu" @if($perkawinan->agama_pasangan == 'khonghucu') selected @endif>Khonghucu</option>
                                                    @endif
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
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="alamat_pasangan">Alamat Asal Pasangan</label>
                                        <input type="text" class="form-control @error ('alamat_pasangan') is-invalid @enderror" placeholder="Masukkan Alamat Asal Pasangan" name="alamat_pasangan" id="alamat_pasangan" value="{{ old('alamat_pasangan', $perkawinan->alamat_asal_pasangan) }}">
                                    </div>

                                    @if($perkawinan->desa_asal_pasangan_id == NULL)
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-12 py-2">
                                                <label class="small" for="title">Provinsi Asal Pasangan</label>
                                                <select class="select2 custom-select @error ('provinsi_asal') is-invalid @enderror" name="provinsi_asal" id="provinsi_asal"  style="width: 100%" required aria-placeholder="Pilih Provinsi">
                                                    <option value="">Pilih Provinsi</option>
                                                    @foreach($provinsis as $provinsi)
                                                        <option value="{{ $provinsi->id }}" @if(old('provinsi_asal') == $provinsi->id) selected @endif>{{ $provinsi->name }}</option>
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
                                            <div class="col-lg-6 col-sm-12 py-2">
                                                <label class="small" for="title">Kabupaten Asal Pasangan</label>
                                                <select class="select2 custom-select @error ('kabupaten_asal') is-invalid @enderror" name="kabupaten_asal" id="kabupaten_asal"  style="width: 100%" required aria-placeholder="Pilih Kabupaten">
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
                
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-12 py-2">
                                                <label class="small" for="title">Kecamatan Asal Pasangan</label>
                                                <select class="select2 custom-select @error ('kecamatan_asal') is-invalid @enderror" name="kecamatan_asal" id="kecamatan_asal"  style="width: 100%" required aria-placeholder="Pilih Kecamatan">
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
                                            <div class="col-lg-6 col-sm-12 py-2">
                                                <label class="small" for="title">Desa/Kelurahan Asal Pasangan</label>
                                                <select class="select2 custom-select @error ('desa_asal') is-invalid @enderror" name="desa_asal" id="desa_asal"  style="width: 100%" required aria-placeholder="Pilih Desa">
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
                                @else
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small" for="title">Provinsi Asal Pasangan</label>
                                            <select class="select2 custom-select @error ('provinsi_asal') is-invalid @enderror" name="provinsi_asal" id="provinsi_asal"  style="width: 100%" required aria-placeholder="Pilih Provinsi">
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small" for="title">Kabupaten Asal Pasangan</label>
                                            <select class="select2 custom-select @error ('kabupaten_asal') is-invalid @enderror" name="kabupaten_asal" id="kabupaten_asal"  style="width: 100%" required aria-placeholder="Pilih Kabupaten">
                                                <option value="">Pilih Kabupaten?Kota</option>
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
            
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small" for="title">Kecamatan Asal Pasangan</label>
                                            <select class="select2 custom-select @error ('kecamatan_asal') is-invalid @enderror" name="kecamatan_asal" id="kecamatan_asal"  style="width: 100%" required aria-placeholder="Pilih Kecamatan">
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
                                        <div class="col-lg-6 col-sm-12 py-2">
                                            <label class="small" for="title">Desa/Kelurahan Asal Pasangan</label>
                                            <select class="select2 custom-select @error ('desa_asal') is-invalid @enderror" name="desa_asal" id="desa_asal"  style="width: 100%" required aria-placeholder="Pilih Desa">
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
                                @endif

                                <hr class="my-4" />
                                    <h5 class="card-title text-primary">Data Perkawinan</h5>

                                    {{-- TANGGAL PEMUPUT --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="title">Jenis Perkawinan</label>
                                                <input type="text" class="form-control"  id="jenis_perkawinan" name="jenis_perkawinan" value="Campuran Keluar" required readonly>
                                            </div>   
                                        </div>
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
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-perkawinan-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Select Mempelai Mipil Pradana -->
    <div class="modal fade" id="select_pradana_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="pilih_cacah_title">Pilih Mempelai Pradana</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-pradana" cellspacing="0">
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
    {{-- MODAL --}}

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
    <script>
        $(document).ready( function () {
            //DATEPICKER
            $("#tanggal_perkawinan").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perkawinan').addClass('active');

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
            //VALIDASI LAMPIRAN

            //NIK PASANGAN KEYUP
            $('#nik_pasangan').on('keyup change', function() {
                if($(this).val().length != 16){
                    $('#nik-validate').show();
                }else if($(this).val().length == ""){
                    $('#nik-validate').hide();
                }else{
                    $('#nik-validate').hide();
                }
            });

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //Daerah On Change
            $('#provinsi_asal').on('change', function(){
                $('#kabupaten_asal').empty();
                $('#kecamatan_asal').empty();
                $('#desa_asal').empty();
                $('#kabupaten_asal').append('<option value="">Pilih Kabupaten/Kota</option>');
                $('#kecamatan_asal').append('<option value="">Pilih Kecamatan</option>');
                $('#desa_asal').append('<option value="">Pilih Desa/Kelurahan</option>');
                if($(this).val() != ""){
                    var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kabupaten_asal').empty();
                            $('#kabupaten_asal').append('<option value="">Pilih Kabupaten/Kota</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten_asal').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten_asal').on('change', function(){
                $('#kecamatan_asal').empty();
                $('#desa_asal').empty();
                $('#kecamatan_asal').append('<option value="">Pilih Kecamatan</option>');
                $('#desa_asal').append('<option value="">Pilih Desa/Kelurahan</option>');
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
                $('#desa_asal').empty();
                $('#desa_asal').append('<option value="">Pilih Desa/Kelurahan</option>');
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

        });

        //DATATABLE KRAMA MIPIL
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //Mempelai MIPIL PURUSA
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
                        "sSearchPlaceholder": "Cari Pradana...",
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

        //Pilih Pradana
        function pilih_pradana_modal(){
            var banjar_adat_id = $('#banjar_adat_pradana').val();
            if(banjar_adat_id != ''){
                $('#select_pradana_modal').on('show.bs.modal', function(e) {
                    pradana_filter();
                }).modal('show');
            }else if(banjar_adat_id == ''){
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Banjar Adat Pradana Terlebih Dahulu'
                })
            }
        }

        function pilih_pradana(id, nama){
            $('#pradana').val(id);
            $('#pradana_placeholder').val(nama);
            $('#pradana_placeholder').prop('readonly', true);
            $('#select_pradana_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Pradana Berhasil Dipilih'
            })
        }
        //Pilih Pradana

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
            var url = "{{ route('banjar-perkawinan-campuran-keluar-update', [":id", ":status"]) }}";
            url = url.replace(':id', id);
            url = url.replace(':status', status);
            $("#form-create-perkawinan").attr("action", url);
            $('#form-create-perkawinan').submit(function (e){
                e.stopPropagation();
            });
        }

    </script>
@endpush