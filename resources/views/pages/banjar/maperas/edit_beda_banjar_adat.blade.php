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
        <div class="container mt-n15">
            <div class="card mb-4 mt-4">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-edit text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Maperas</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Maperas Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-8 col-xl-10 mt-4">
                            <form id="form-edit-maperas" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf 
                                <div id="perkawinan_dalam_desa_adat">
                                    <div class="form-group">
                                        <label for="title" class="small">Jenis Maperas</label>
                                        <input type="text" class="form-control"  id="jenis_maperas" name="jenis_maperas" value="Maperas Beda Banjar Adat" required readonly>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="kabupaten">Kabupaten Asal Anak<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @foreach($kabupatens as $kab)
                                                        <option value="{{ $kab->id }}" @if($kab->id == $kabupaten->id) selected @endif>{{ $kab->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kabupaten_pradana')
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
                                                <label class="small" for="kecamatan">Kecamatan Asal Anak<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required>
                                                    <option value="">Pilih Kecamatan</option>
                                                    @foreach($kecamatans as $kec)
                                                        <option value="{{ $kec->id }}" @if($kec->id == $kecamatan->id) selected @endif>{{ $kec->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
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
    
                                    <div class="row mb-3">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group mb-n1">
                                                <label class="small" for="desa_adat">Desa Adat Asal Anak<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('desa_adat') is-invalid @enderror" name="desa_adat" id="desa_adat"  style="width: 100%" required>
                                                    <option value="">Pilih Desa Adat</option>
                                                    @foreach($desa_adats as $desadat)
                                                        <option value="{{ $desadat->id }}" @if($desadat->id == $desa_adat->id) selected @endif>{{ $desadat->desadat_nama }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                @error('desa_adat')
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
                                                <label class="small" for="banjar_adat">Banjar Adat Asal Anak<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('banjar_adat') is-invalid @enderror" name="banjar_adat" id="banjar_adat"  style="width: 100%" required>
                                                    <option value="">Pilih Banjar Adat</option>
                                                    @foreach($banjar_adats as $banjardat)
                                                        <option value="{{ $banjardat->id }}" @if($banjardat->id == $banjar_adat->id) selected @endif>{{ $banjardat->nama_banjar_adat }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
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
                                    </div>

                                    <div class="form-group">
                                        <label for="krama_mipil_lama" class="small">Krama Mipil Lama<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('krama_mipil_lama_placeholder') is-invalid @enderror form-custom"  id="krama_mipil_lama_placeholder" name="krama_mipil_lama_placeholder" placeholder="Pilih Krama Mipil Lama" value="{{ old('krama_mipil_lama_placeholder', $krama_mipil_lama->cacah_krama_mipil->penduduk->nama) }}" required readonly>
                                            <div class="input-group-append">
                                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_lama_modal()">
                                                    <span class="text">Pilih Krama</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('krama_mipil_lama') is-invalid @enderror"  id="krama_mipil_lama" name="krama_mipil_lama"  value="{{ old('krama_mipil_lama', $krama_mipil_lama->id) }}" required hidden>
                                        @error('krama_mipil_lama')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Krama Mipil Lama wajib dipilih
                                            </div>
                                        @enderror
                                        <small class="small">(Masukkan asal anak terlebih dahulu)</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="anak" class="small">Anak<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('anak') is-invalid @enderror" name="anak" id="anak"  style="width: 100%" required>
                                            <option value="">Pilih Anak</option>
                                            @if(old('anak'))
                                                @foreach($anggota_krama_mipil_lama as $anggota_lama)
                                                    <option value="{{ $anggota_lama->cacah_krama_mipil->id }}" @if(old('anak') == $anggota_lama->cacah_krama_mipil->id) selected @endif>{{ $anggota_lama->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            @else
                                                @foreach($anggota_krama_mipil_lama as $anggota_lama)
                                                    <option value="{{ $anggota_lama->cacah_krama_mipil->id }}" @if($maperas->cacah_krama_mipil_lama->id == $anggota_lama->cacah_krama_mipil->id) selected @endif>{{ $anggota_lama->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('anak')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Anak wajib dipilih
                                            </div>
                                        @enderror 
                                        <small class="small">(Pilih Krama Mipil Lama terlebih dahulu)</small>
                                    </div>

                                    <div class="row" id="orang_tua_lama_div">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Ayah Lama</label>
                                                <input type="text" class="form-control @error ('ayah_lama') is-invalid @enderror"  id="ayah_lama" name="ayah_lama" placeholder="Nama Ayah Lama" value="@if(old('ayah_lama')){{ old('ayah_lama') }}@elseif($ayah_lama != NULL){{ $ayah_lama->penduduk->nama }}@else-@endif" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Ibu Lama</label>
                                                <input type="text" class="form-control @error ('ibu_lama') is-invalid @enderror"  id="ibu_lama" name="ibu_lama" placeholder="Nama Ibu Lama" value="@if(old('ibu_lama')){{ old('ibu_lama') }}@elseif($ibu_lama != NULL){{ $ibu_lama->penduduk->nama }}@else-@endif" disabled>
                                            </div>
                                        </div>
                                    </div>

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
                                        <small class="small">(Pilih Krama Mipil Lama dan Anak terlebih dahulu)</small>
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
                                </div>
                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-maperas-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_maperas({{ $maperas->id }},'0')">
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
            </div>
        </div>
    </main>

    {{-- MODAL --}}
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

    <div class="modal fade" id="select_krama_mipil_lama_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <table class="table table-bordered table-hover" id="dataTable-krama-mipil-lama" width="100%" cellspacing="0">
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
            $("#tanggal_maperas").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-maperas').addClass('active');

            //VALIDASI LAMPIRAN
                $("#file_bukti_maperas").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_bukti_maperas').show();
                        $('#file_bukti_maperas').val("");
                    }else{
                        document.getElementById('file_bukti_maperas_label').innerHTML = document.getElementById('file_bukti_maperas').files[0].name;
                        $('#validasi-file_bukti_maperas').hide();
                    }
                });
                $("#file_akta_pengangkatan_anak").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_akta_pengangkatan_anak').show();
                        $('#file_akta_pengangkatan_anak').val("");
                    }else{
                        document.getElementById('file_akta_pengangkatan_anak_label').innerHTML = document.getElementById('file_akta_pengangkatan_anak').files[0].name;
                        $('#validasi-file_akta_pengangkatan_anak').hide();
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

            $('#kabupaten').on('change', function(){
                //EMPTY CHILD
                $('#kecamatan').empty();
                $('#desa_adat').empty();
                $('#banjar_adat').empty();
                $('#krama_mipil_lama').val('');
                $('#krama_mipil_lama_placeholder').val('');
                $('#anak').empty();
                $('#ayah_lama').val('');
                $('#ibu_lama').val('');
                $('#krama_mipil_baru').val('');
                $('#krama_mipil_baru_placeholder').val('');
                $('#ayah_baru').empty();
                $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                $('#ibu_baru').empty();
                $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');

                //SET CHILD PLACEHOLDER
                $('#kecamatan').append('<option value="">Pilih Kecamatan</option>');
                $('#desa_adat').append('<option value="">Pilih Desa Adat</option>');
                $('#banjar_adat').append('<option value="">Pilih Banjar Adat</option>');
                $('#anak').append('<option value="">Pilih Anak</option>');

                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
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
                //EMPTY CHILD
                $('#desa_adat').empty();
                $('#banjar_adat').empty();
                $('#krama_mipil_lama').val('');
                $('#krama_mipil_lama_placeholder').val('');
                $('#anak').empty();
                $('#ayah_lama').val('');
                $('#ibu_lama').val('');
                $('#krama_mipil_baru').val('');
                $('#krama_mipil_baru_placeholder').val('');
                $('#ayah_baru').empty();
                $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                $('#ibu_baru').empty();
                $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');

                //SET CHILD PLACEHOLDER
                $('#desa_adat').append('<option value="">Pilih Desa Adat</option>');
                $('#banjar_adat').append('<option value="">Pilih Banjar Adat</option>');
                $('#anak').append('<option value="">Pilih Anak</option>');

                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat').empty();
                            $('#desa_adat').append('<option value="">Pilih Desa Adat</option>');
                            result.desa_adats.forEach(element => {
                                $('#desa_adat').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                                  
                        }
                    });
                }
            });

            $('#desa_adat').on('change', function(){
                //GET CURR BANJAR ADAT ID
                var banjar_adat_id = {{ Session::get('banjar_adat_id')}};

                //EMPTY CHILD
                $('#banjar_adat').empty();
                $('#krama_mipil_lama').val('');
                $('#krama_mipil_lama_placeholder').val('');
                $('#anak').empty();
                $('#ayah_lama').val('');
                $('#ibu_lama').val('');
                $('#krama_mipil_baru').val('');
                $('#krama_mipil_baru_placeholder').val('');
                $('#ayah_baru').empty();
                $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                $('#ibu_baru').empty();
                $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');

                //SET CHILD PLACEHOLDER
                $('#banjar_adat').append('<option value="">Pilih Banjar Adat</option>');
                $('#anak').append('<option value="">Pilih Anak</option>');

                if($(this).val() != ""){
                    var url = "{{ route('admin-banjar-adat-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#banjar_adat').empty();
                            $('#banjar_adat').append('<option value="" selected>Pilih Banjar Adat</option>');
                            result.banjar_adats.forEach(element => {
                                if(element['id'] != banjar_adat_id){
                                    $('#banjar_adat').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                                }
                            });                     
                        }
                    });
                }
            });

            $('#banjar_adat').on('change', function(){
                //EMPTY CHILD
                $('#krama_mipil_lama').val('');
                $('#krama_mipil_lama_placeholder').val('');
                $('#anak').empty();
                $('#ayah_lama').val('');
                $('#ibu_lama').val('');
                $('#krama_mipil_baru').val('');
                $('#krama_mipil_baru_placeholder').val('');
                $('#ayah_baru').empty();
                $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                $('#ibu_baru').empty();
                $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');

                //SET CHILD PLACEHOLDER
                $('#anak').append('<option value="">Pilih Anak</option>');
            });

            //Anak On Change
            $('#anak').on('change', function(){
                var id = $(this).val();
                if(id){
                    var url = "{{ route('banjar-maperas-get-orangtua-lama-anak', ":id") }}";
                    url = url.replace(':id', id);
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            //KOSONGKAN FIELD DIBAWAHNYA
                            $('#ayah_lama').val(result.nama_ayah_lama);
                            $('#ibu_lama').val(result.nama_ibu_lama);
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
                var table_krama_mipil_lama = $('#dataTable-krama-mipil-lama');
                var oTable_krama_mipil_lama = table_krama_mipil_lama.DataTable({
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
                        url : "{{ route('banjar-maperas-datatable-krama-mipil-lama') }}",
                        data : function(d){
                            d.banjar_adat_id = $('#banjar_adat').val();
                        }
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

                filter_krama_mipil_lama = () => {
                    oTable_krama_mipil_lama.ajax.reload();
                }

                $('#dataTable-krama-mipil-lama tbody').on('click', 'td.dt-control', function () {
                    var tr = $(this).closest('tr');
                    var row = oTable_krama_mipil_lama.row( tr );
            
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

        //GET DATA LAMA
        function pilih_krama_mipil_lama_modal(){
            var banjar_adat_asal = $('#banjar_adat').val();
            if(banjar_adat_asal){
                $('#select_krama_mipil_lama_modal').on('show.bs.modal', function(e) {
                    filter_krama_mipil_lama();
                }).modal('show');
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'Masukkan Asal Anak Terlebih Dahulu'
                });
            }
        }

        function pilih_krama_mipil_lama(id, nama){
            $('#krama_mipil_lama').val(id);
            $('#krama_mipil_lama_placeholder').val(nama);
            $('#krama_mipil_lama_placeholder').prop('readonly', true);
            $('#select_krama_mipil_lama_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Mipil Lama Berhasil Dipilih'
            });
            var url = "{{ route('banjar-maperas-get-daftar-anak', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    $('#anak').empty();
                    $('#anak').append('<option value="">Pilih Anak</option>');
                    result.anggota_krama_mipil.forEach(element=>{
                        $('#anak').append('<option value="'+element.cacah_krama_mipil.id+'">'+element.cacah_krama_mipil.penduduk.nama+'</option>');
                    });

                    //KOSONGKAN FIELD DIBAWAHNYA
                    $('#ayah_lama').val('');
                    $('#ibu_lama').val('');
                    $('#krama_mipil_baru').val('');
                    $('#krama_mipil_baru_placeholder').val('');
                    $('#ayah_baru').empty();
                    $('#ayah_baru').append('<option value="">Pilih Ayah Baru</option>');
                    $('#ibu_baru').empty();
                    $('#ibu_baru').append('<option value="">Pilih Ibu Baru</option>');
                }
            });
        }
        //GET DATA LAMA

        //GET DATA BARU
        function pilih_krama_mipil_baru_modal(){
            var krama_mipil_lama = $('#krama_mipil_lama').val();
            var anak = $('#anak').val();
            if(krama_mipil_lama){
                if(anak){
                    $('#select_krama_mipil_baru_modal').on('show.bs.modal', function(e) {
                        filter_krama_mipil_baru();
                    }).modal('show');
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Anak Terlebih Dahulu'
                    });
                }
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Krama Mipil Lama Terlebih Dahulu'
                });
            }
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
        function simpan_maperas(id, status){
            var url = "{{ route('banjar-maperas-beda-banjar-adat-update', [":id", ":status"]) }}";
            url = url.replace(':status', status);
            url = url.replace(':id', id);
            $("#form-edit-maperas").attr("action", url);
            $('#form-edit-maperas').submit(function (e){
                e.stopPropagation();
            });
        }

    </script>
@endpush