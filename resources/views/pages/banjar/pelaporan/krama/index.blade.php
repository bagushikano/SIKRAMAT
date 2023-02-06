@extends('layouts.banjar.banjar')

@push('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>
    
    <style>
        .select2-selection__choice{
            border-radius: 0.3rem !important;
            height: 1.8rem !important;
            text-align: justify !important;
        }
        .select2-selection__choice__remove{
            color: rgb(206, 81, 81) !important;
        }
        .select2-selection__choice{
            font-size: 1rem !important;
        }
        .select2-search__field{
            padding-bottom: 1.25rem !important;
        }
        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
            color: #fff !important;
            background-color: #0d6efd !important;
        }
    </style>
@endpush

@section('title', 'Laporan Data Krama')

@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fas fa-file mr-2"></i>
                                </div>
                                Laporan Data Krama
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Krama</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="card my-1">
                <div class="card-header p-2 d-flex justify-content-center justify-content-lg-start justify-content-sm-start">
                    <ul class="nav nav-pills small">
                        <li class="nav-item"><a class="nav-link active" id="tabKramaMipil" href="#krama-mipil" data-toggle="tab">Filter Krama Mipil</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabKramaTamiu" href="#krama-tamiu" data-toggle="tab">Filter Krama Tamiu</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabTamiu" href="#tamiu" data-toggle="tab">Filter Tamiu</a></li>
                    </ul>
                </div>
                <div class="card-body py-auto">
                    <div class="tab-content">
                        <div class="tab-pane active" id="krama-mipil">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_krama_mipil" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_mipil_awal" class="form-label small mb-1">Rentang Awal Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_mipil_awal') is-invalid @enderror" name="tgl_lahir_mipil_awal" id="tgl_lahir_mipil_awal" placeholder="Pilih rentang awal tanggal lahir" readonly>
                                            @error('tgl_lahir_mipil_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_mipil_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_mipil_akhir') is-invalid @enderror" name="tgl_lahir_mipil_akhir" id="tgl_lahir_mipil_akhir" placeholder="Pilih rentang akhir tanggal lahir" readonly>
                                            @error('tgl_lahir_mipil_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_mipil_awal" class="form-label small mb-1">Rentang Awal Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_mipil_awal') is-invalid @enderror" name="tgl_registrasi_mipil_awal" id="tgl_registrasi_mipil_awal" placeholder="Pilih rentang awal tanggal registrasi" readonly>
                                            @error('tgl_registrasi_mipil_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_mipil_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_mipil_akhir') is-invalid @enderror" name="tgl_registrasi_mipil_akhir" id="tgl_registrasi_mipil_akhir" placeholder="Pilih rentang akhir tanggal registrasi" readonly>
                                            @error('tgl_registrasi_mipil_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @if ($data['tempekan']->count() > 0)
                                    <div class="form-group">
                                        <label for="tempekan" class="form-label small mb-1">Tempekan<small class="text-danger">*</small></label>
                                        <select class="select2 select-tempekan form-select @error('tempekan[]') is-invalid @enderror" id="tempekan" multiple name="tempekan[]" required style="width: 100%">
                                            @foreach ($data['tempekan'] as $tempekan)
                                                <option value="{{ $tempekan->id }}">{{ $tempekan->nama_tempekan }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select_tempekan">
                                            <label class="form-check-label small" for="select_tempekan">
                                                Pilih semua tempekan
                                            </label>
                                        </div>
                                        @error('tempekan[]')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Pendidikan wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="pekerjaan_mipil" class="form-label small mb-1">Pekerjaan<small class="text-danger">*</small></label>
                                    <select class="select2 select-pekerjaan-mipil form-select @error('pekerjaan_mipil[]') is-invalid @enderror" id="pekerjaan_mipil" multiple name="pekerjaan_mipil[]" required style="width: 100%">
                                        @foreach ($data['pekerjaan'] as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}">{{ $pekerjaan->profesi }}</option>
                                        @endforeach
                                    </select>
                                    @error('pekerjaan_mipil[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pekerjaan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pendidikan_mipil" class="form-label small mb-1">Pendidikan Tertinggi<small class="text-danger">*</small></label>
                                    <select class="select2 select-pendidikan-mipil form-select @error('pendidikan_mipil[]') is-invalid @enderror" id="pendidikan_mipil" multiple name="pendidikan_mipil[]" required style="width: 100%">
                                        @foreach ($data['pendidikan'] as $pendidikan)
                                            <option value="{{ $pendidikan->id }}">{{ $pendidikan->jenjang_pendidikan }}</option>
                                        @endforeach
                                    </select>
                                    @error('pendidikan_mipil[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pendidikan tertinggi wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="goldar_mipil" class="form-label small mb-1">Golongan Darah<small class="text-danger">*</small></label>
                                    <select class="select2 select-goldar-mipil form-select @error('goldar_mipil[]') is-invalid @enderror" id="goldar_mipil" multiple name="goldar_mipil[]" required style="width: 100%">
                                        <option value="-">-</option>
                                        <option value="O">Golongan Darah O</option>
                                        <option value="A">Golongan Darah A</option>
                                        <option value="B">Golongan Darah B</option>
                                        <option value="AB">Golongan Darah AB</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_goldar_mipil">
                                        <label class="form-check-label small" for="select_goldar_mipil">
                                            Pilih semua golongan darah
                                        </label>
                                    </div>
                                    @error('goldar_mipil[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Golongan darah wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="status_mipil" class="form-label small mb-1">Status<small class="text-danger">*</small></label>
                                    <select class="select2 select-status-mipil form-select @error('status_mipil[]') is-invalid @enderror" id="status_mipil" multiple name="status_mipil[]" required style="width: 100%">
                                        <option selected value="1">Aktif</option>
                                        <option value="0">Keluar/Non Aktif</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_status_mipil">
                                        <label class="form-check-label small" for="select_status_mipil">
                                            Pilih semua status krama mipil
                                        </label>
                                    </div>
                                    @error('status_mipil[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Status krama wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_mipil()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none dropdown-toggle p-0 pr-2" type="button" id="download_data_mipil" data-toggle="dropdown" aria-expanded="false">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="download_pdf">
                                                <button class="dropdown-item text-dark" style="cursor: pointer" onclick="download_pdf_mipil()">
                                                    <i class="fa-solid fa-file-pdf mr-3"></i>
                                                    Download PDF
                                                </button>
                                                <button onclick="download_excel_mipil()" style="cursor: pointer" class="dropdown-item text-dark">
                                                    <i class="fa-solid fa-file-excel mr-3"></i>
                                                    Download Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="krama-tamiu">
                            <form id="form_laporan_krama_tamiu" action="" method="POST" class="form-horizontal needs-validation my-0" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_tamiu_awal" class="form-label small mb-1">Rentang Awal Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_tamiu_awal') is-invalid @enderror" name="tgl_lahir_tamiu_awal" id="tgl_lahir_tamiu_awal" placeholder="Pilih rentang akhir tanggal lahir" readonly>
                                            @error('tgl_lahir_tamiu_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_tamiu_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_tamiu_akhir') is-invalid @enderror" name="tgl_lahir_tamiu_akhir" id="tgl_lahir_tamiu_akhir" placeholder="Pilih rentang awal tanggal lahir" readonly>
                                            @error('tgl_lahir_tamiu_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_tamiu_awal" class="form-label small mb-1">Rentang Awal Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_tamiu_awal') is-invalid @enderror" name="tgl_registrasi_tamiu_awal" id="tgl_registrasi_tamiu_awal" placeholder="Pilih rentang akhir tanggal registrasi" readonly>
                                            @error('tgl_registrasi_tamiu_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_tamiu_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_tamiu_akhir') is-invalid @enderror" name="tgl_registrasi_tamiu_akhir" id="tgl_registrasi_tamiu_akhir" placeholder="Pilih rentang awal tanggal registrasi" readonly>
                                            @error('tgl_registrasi_tamiu_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banjar_dinas_tamiu" class="form-label small mb-1">Banjar Dinas<small class="text-danger">*</small></label>
                                    <select class="select2 select-banjar-dinas-tamiu form-select @error('banjar_dinas_tamiu[]') is-invalid @enderror" id="banjar_dinas_tamiu" multiple name="banjar_dinas_tamiu[]" required style="width: 100%">
                                        @foreach ($data['banjar_dinas'] as $banjar_dinas)
                                            <option value="{{ $banjar_dinas->id }}">{{ $banjar_dinas->nama_banjar_dinas }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_banjar_dinas_tamiu">
                                        <label class="form-check-label small" for="select_banjar_dinas_tamiu">
                                            Pilih semua banjar dinas
                                        </label>
                                    </div>
                                    @error('banjar_dinas_tamiu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Banjar dinas wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pekerjaan_tamiu" class="form-label small mb-1">Pekerjaan<small class="text-danger">*</small></label>
                                    <select class="select2 select-pekerjaan-tamiu form-select @error('pekerjaan_tamiu[]') is-invalid @enderror" id="pekerjaan_tamiu" multiple name="pekerjaan_tamiu[]" required style="width: 100%">
                                        @foreach ($data['pekerjaan'] as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}">{{ $pekerjaan->profesi }}</option>
                                        @endforeach
                                    </select>
                                    @error('pekerjaan_tamiu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pekerjaan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pendidikan_tamiu" class="form-label small mb-1">Pendidikan Tertinggi<small class="text-danger">*</small></label>
                                    <select class="select2 select-pendidikan-tamiu form-select @error('pendidikan_tamiu[]') is-invalid @enderror" id="pendidikan_tamiu" multiple name="pendidikan_tamiu[]" required style="width: 100%">
                                        @foreach ($data['pendidikan'] as $pendidikan)
                                            <option value="{{ $pendidikan->id }}">{{ $pendidikan->jenjang_pendidikan }}</option>
                                        @endforeach
                                    </select>
                                    @error('pendidikan_tamiu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pendidikan tertinggi wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="asal" class="form-label small mb-1">Asal<small class="text-danger">*</small></label>
                                    <select class="select2 select-asal form-select @error('asal[]') is-invalid @enderror" id="asal" multiple name="asal[]" required style="width: 100%">
                                        <option value="dalam_bali">Asli Bali</option>
                                        <option value="luar_bali">Luar Bali</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_asal">
                                        <label class="form-check-label small" for="select_asal">
                                            Pilih semua asal
                                        </label>
                                    </div>
                                    @error('asal[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Asal wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="goldar_tamiu" class="form-label small mb-1">Golongan Darah<small class="text-danger">*</small></label>
                                    <select class="select2 select-goldar-tamiu form-select @error('goldar_tamiu[]') is-invalid @enderror" id="goldar_tamiu" multiple name="goldar_tamiu[]" required style="width: 100%">
                                        <option value="-">-</option>
                                        <option value="O">Golongan Darah O</option>
                                        <option value="A">Golongan Darah A</option>
                                        <option value="B">Golongan Darah B</option>
                                        <option value="AB">Golongan Darah AB</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_goldar_tamiu">
                                        <label class="form-check-label small" for="select_goldar_tamiu">
                                            Pilih semua golongan darah
                                        </label>
                                    </div>
                                    @error('goldar_tamiu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Golongan darah wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="status_tamiu" class="form-label small mb-1">Status<small class="text-danger">*</small></label>
                                    <select class="select2 select-status-tamiu form-select @error('status_tamiu[]') is-invalid @enderror" id="status_tamiu" multiple name="status_tamiu[]" required style="width: 100%">
                                        <option selected value="1">Aktif</option>
                                        <option value="0">Keluar/Non Aktif</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_status_tamiu">
                                        <label class="form-check-label small" for="select_status_tamiu">
                                            Pilih semua status krama tamiu
                                        </label>
                                    </div>
                                    @error('status_tamiu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Status krama wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_tamiu()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none dropdown-toggle p-0 pr-2" type="button" id="download_data_mipil" data-toggle="dropdown" aria-expanded="false">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="download_pdf">
                                                <button class="dropdown-item text-dark" style="cursor: pointer" onclick="download_pdf_tamiu()">
                                                    <i class="fa-solid fa-file-pdf mr-3"></i>
                                                    Download PDF
                                                </button>
                                                <button onclick="download_excel_tamiu()" style="cursor: pointer" class="dropdown-item text-dark">
                                                    <i class="fa-solid fa-file-excel mr-3"></i>
                                                    Download Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tamiu">
                            <form id="form_laporan_tamiu" action="" method="POST" class="form-horizontal needs-validation my-0" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_tamu_awal" class="form-label small mb-1">Rentang Awal Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_tamu_awal') is-invalid @enderror" name="tgl_lahir_tamu_awal" id="tgl_lahir_tamu_awal" placeholder="Pilih rentang akhir tanggal lahir" readonly>
                                            @error('tgl_lahir_tamu_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Rentang awal tanggal lahir wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_tamu_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_tamu_akhir') is-invalid @enderror" name="tgl_lahir_tamu_akhir" id="tgl_lahir_tamu_akhir" placeholder="Pilih rentang awal tanggal lahir" readonly>
                                            @error('tgl_lahir_tamu_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Rentang akhir tanggal lahir wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_tamu_awal" class="form-label small mb-1">Rentang Awal Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_tamu_awal') is-invalid @enderror" name="tgl_registrasi_tamu_awal" id="tgl_registrasi_tamu_awal" placeholder="Pilih rentang akhir tanggal registrasi" readonly>
                                            @error('tgl_registrasi_tamu_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Rentang awal tanggal registrasi wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_registrasi_tamu_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Registrasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_registrasi_tamu_akhir') is-invalid @enderror" name="tgl_registrasi_tamu_akhir" id="tgl_registrasi_tamu_akhir" placeholder="Pilih rentang awal tanggal registrasi" readonly>
                                            @error('tgl_registrasi_tamu_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Rentang akhir tanggal registrasi wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banjar_dinas_tamu" class="form-label small mb-1">Banjar Dinas<small class="text-danger">*</small></label>
                                    <select class="select2 select-banjar-dinas-tamu form-select @error('banjar_dinas_tamu[]') is-invalid @enderror" id="banjar_dinas_tamu" multiple name="banjar_dinas_tamu[]" required style="width: 100%">
                                        @foreach ($data['banjar_dinas'] as $banjar_dinas)
                                            <option value="{{ $banjar_dinas->id }}">{{ $banjar_dinas->nama_banjar_dinas }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_banjar_dinas_tamu">
                                        <label class="form-check-label small" for="select_banjar_dinas_tamu">
                                            Pilih semua banjar dinas
                                        </label>
                                    </div>
                                    @error('banjar_dinas_tamu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Banjar dinas wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pekerjaan_tamu" class="form-label small mb-1">Pekerjaan<small class="text-danger">*</small></label>
                                    <select class="select2 select-pekerjaan-tamu form-select @error('pekerjaan_tamu[]') is-invalid @enderror" id="pekerjaan_tamu" multiple name="pekerjaan_tamu[]" required style="width: 100%">
                                        @foreach ($data['pekerjaan'] as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}">{{ $pekerjaan->profesi }}</option>
                                        @endforeach
                                    </select>
                                    @error('pekerjaan_tamu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pekerjaan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pendidikan_tamu" class="form-label small mb-1">Pendidikan Tertinggi<small class="text-danger">*</small></label>
                                    <select class="select2 select-pendidikan-tamu form-select @error('pendidikan_tamu[]') is-invalid @enderror" id="pendidikan_tamu" multiple name="pendidikan_tamu[]" required style="width: 100%">
                                        @foreach ($data['pendidikan'] as $pendidikan)
                                            <option value="{{ $pendidikan->id }}">{{ $pendidikan->jenjang_pendidikan }}</option>
                                        @endforeach
                                    </select>
                                    @error('pendidikan_tamu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Pendidikan terakhir wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="goldar_tamu" class="form-label small mb-1">Golongan Darah<small class="text-danger">*</small></label>
                                    <select class="select2 select-goldar-tamu form-select @error('goldar_tamu[]') is-invalid @enderror" id="goldar_tamu" multiple name="goldar_tamu[]" required style="width: 100%">
                                        <option value="-">-</option>
                                        <option value="O">Golongan Darah O</option>
                                        <option value="A">Golongan Darah A</option>
                                        <option value="B">Golongan Darah B</option>
                                        <option value="AB">Golongan Darah AB</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_goldar_tamu">
                                        <label class="form-check-label small" for="select_goldar_tamu">
                                            Pilih semua golongan darah
                                        </label>
                                    </div>
                                    @error('goldar_tamu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Golongan darah wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="status_tamu" class="form-label small mb-1">Status<small class="text-danger">*</small></label>
                                    <select class="select2 select-status-tamu form-select @error('status_tamu[]') is-invalid @enderror" id="status_tamu" multiple name="status_tamu[]" required style="width: 100%">
                                        <option selected value="1">Aktif</option>
                                        <option value="0">Keluar/Non Aktif</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_status_tamu">
                                        <label class="form-check-label small" for="select_status_tamu">
                                            Pilih semua status tamiu
                                        </label>
                                    </div>
                                    @error('status_tamu[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Status krama wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_tamu()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none dropdown-toggle p-0 pr-2" type="button" id="download_data_tamu" data-toggle="dropdown" aria-expanded="false">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="download_pdf">
                                                <button class="dropdown-item text-dark" style="cursor: pointer" onclick="download_pdf_tamu()">
                                                    <i class="fa-solid fa-file-pdf mr-3"></i>
                                                    Download PDF
                                                </button>
                                                <button onclick="download_excel_tamu()" style="cursor: pointer" class="dropdown-item text-dark">
                                                    <i class="fa-solid fa-file-excel mr-3"></i>
                                                    Download Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('banjar-dashboard') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
                        <span class="icon">
                            <i class="fa-solid fa-circle-arrow-left"></i>
                        </span>
                        <span class="text">Kembali/Batal</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    @if($message = Session::get('success'))
        <script>
            $(document).ready(function(){
                alertSuccess('{{$message}}');
            });
        </script>
    @endif

    @if($message = Session::get('failed'))
        <script>
            $(document).ready(function(){
                alertError('{{$message}}');
            });
        </script>
    @endif

    @if ($data['tempekan']->count() > 0)
        <script>
            $(document).ready( function () {
                $('.select-tempekan').select2({
                    placeholder: "Pilih tempekan",
                    closeOnSelect: true,
                    language: {
                        noResults: function () {
                            return 'Tidak terdapat data yang sesuai';
                        }
                    }
                })

                $("#select_tempekan").click(function(){
                    if($("#select_tempekan").is(':checked') ){
                        $(".select-tempekan").find('option').prop("selected",true);
                        $(".select-tempekan").trigger('change');
                    } else {
                        $(".select-tempekan").find('option').prop("selected",false);
                        $(".select-tempekan").trigger('change');
                    }
                });
            })
        </script>
    @endif

    <script>
        $(document).ready( function () {
            $('#collapseRekap').addClass('show');
            $('#nav-link-laporan-krama').addClass('active');      


            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            

            $('.select-goldar-mipil').select2({
                placeholder: "Pilih golongan darah",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-pekerjaan-mipil').select2({
                placeholder: "Pilih pekerjaan",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 pekerjaan';
                    }
                }
            })

            $('.select-pendidikan-mipil').select2({
                placeholder: "Pilih pendidikan tertinggi",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 jenjang pendidikan';
                    }
                }
            })

            $('.select-status-mipil').select2({
                placeholder: "Pilih status krama mipil",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })


            $('.select-banjar-dinas-tamiu').select2({
                placeholder: "Pilih banjar dinas",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-goldar-tamiu').select2({
                placeholder: "Pilih golongan darah",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-pekerjaan-tamiu').select2({
                placeholder: "Pilih pekerjaan",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 pekerjaan';
                    }
                }
            })

            $('.select-pendidikan-tamiu').select2({
                placeholder: "Pilih pendidikan tertinggi",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 jenjang pendidikan';
                    }
                }
            })

            $('.select-asal').select2({
                placeholder: "Pilih asal",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-status-tamiu').select2({
                placeholder: "Pilih status krama tamiu",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })


            $('.select-banjar-dinas-tamu').select2({
                placeholder: "Pilih banjar dinas",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-goldar-tamu').select2({
                placeholder: "Pilih golongan darah",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-pekerjaan-tamu').select2({
                placeholder: "Pilih pekerjaan",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 pekerjaan';
                    }
                }
            })

            $('.select-pendidikan-tamu').select2({
                placeholder: "Pilih pendidikan tertinggi",
                closeOnSelect: true,
                maximumSelectionLength: 5,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    },
                    maximumSelected: function () {
                        return 'Maksimal hanya memilih 5 jenjang pendidkan';
                    }
                }
            })

            $('.select-status-tamu').select2({
                placeholder: "Pilih status tamiu",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })


            $("#select_goldar_mipil").click(function(){
                if($("#select_goldar_mipil").is(':checked') ){
                    $(".select-goldar-mipil").find('option').prop("selected",true);
                    $(".select-goldar-mipil").trigger('change');
                } else {
                    $(".select-goldar-mipil").find('option').prop("selected",false);
                    $(".select-goldar-mipil").trigger('change');
                }
            });

            $("#select_status_mipil").click(function(){
                if($("#select_status_mipil").is(':checked') ){
                    $(".select-status-mipil").find('option').prop("selected",true);
                    $(".select-status-mipil").trigger('change');
                } else {
                    $(".select-status-mipil").find('option').prop("selected",false);
                    $(".select-status-mipil").trigger('change');
                }
            });


            $("#select_goldar_tamiu").click(function(){
                if($("#select_goldar_tamiu").is(':checked') ){
                    $(".select-goldar-tamiu").find('option').prop("selected",true);
                    $(".select-goldar-tamiu").trigger('change');
                } else {
                    $(".select-goldar-tamiu").find('option').prop("selected",false);
                    $(".select-goldar-tamiu").trigger('change');
                }
            });

            $("#select_banjar_dinas_tamiu").click(function(){
                if($("#select_banjar_dinas_tamiu").is(':checked') ){
                    $(".select-banjar-dinas-tamiu").find('option').prop("selected",true);
                    $(".select-banjar-dinas-tamiu").trigger('change');
                } else {
                    $(".select-banjar-dinas-tamiu").find('option').prop("selected",false);
                    $(".select-banjar-dinas-tamiu").trigger('change');
                }
            });

            $("#select_asal").click(function(){
                if($("#select_asal").is(':checked') ){
                    $(".select-asal").find('option').prop("selected",true);
                    $(".select-asal").trigger('change');
                } else {
                    $(".select-asal").find('option').prop("selected",false);
                    $(".select-asal").trigger('change');
                }
            });

            $("#select_status_tamiu").click(function(){
                if($("#select_status_tamiu").is(':checked') ){
                    $(".select-status-tamiu").find('option').prop("selected",true);
                    $(".select-status-tamiu").trigger('change');
                } else {
                    $(".select-status-tamiu").find('option').prop("selected",false);
                    $(".select-status-tamiu").trigger('change');
                }
            });


            $("#select_goldar_tamu").click(function(){
                if($("#select_goldar_tamu").is(':checked') ){
                    $(".select-goldar-tamu").find('option').prop("selected",true);
                    $(".select-goldar-tamu").trigger('change');
                } else {
                    $(".select-goldar-tamu").find('option').prop("selected",false);
                    $(".select-goldar-tamu").trigger('change');
                }
            });

            $("#select_banjar_dinas_tamu").click(function(){
                if($("#select_banjar_dinas_tamu").is(':checked') ){
                    $(".select-banjar-dinas-tamu").find('option').prop("selected",true);
                    $(".select-banjar-dinas-tamu").trigger('change');
                } else {
                    $(".select-banjar-dinas-tamu").find('option').prop("selected",false);
                    $(".select-banjar-dinas-tamu").trigger('change');
                }
            });

            $("#select_status_tamu").click(function(){
                if($("#select_status_tamu").is(':checked') ){
                    $(".select-status-tamu").find('option').prop("selected",true);
                    $(".select-status-tamu").trigger('change');
                } else {
                    $(".select-status-tamu").find('option').prop("selected",false);
                    $(".select-status-tamu").trigger('change');
                }
            });
        });
    </script>

    <script>
        function filter_mipil() {
            $('#form_laporan_krama_mipil').removeAttr('target');
            $('#form_laporan_krama_mipil').attr('action', "{{ route('Laporan Krama Mipil') }}");
            $("#form_laporan_krama_mipil").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_pdf_mipil() {
            $('#form_laporan_krama_mipil').attr('target', '_blank');
            $('#form_laporan_krama_mipil').attr('action', "{{ route('Download PDF Laporan Krama Mipil') }}");
            $("#form_laporan_krama_mipil").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_excel_mipil() {
            $('#form_laporan_krama_mipil').attr('target', '_blank');
            $('#form_laporan_krama_mipil').attr('action', "{{ route('Download Excel Laporan Krama Mipil') }}");
            $("#form_laporan_krama_mipil").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    <script>
        function filter_tamiu() {
            $('#form_laporan_krama_tamiu').removeAttr('target');
            $('#form_laporan_krama_tamiu').attr('action', "{{ route('Laporan Krama Tamiu') }}");
            $("#form_laporan_krama_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }
        
        function download_pdf_tamiu() {
            $('#form_laporan_krama_tamiu').attr('target', '_blank');
            $('#form_laporan_krama_tamiu').attr('action', "{{ route('Download PDF Laporan Krama Tamiu') }}");
            $("#form_laporan_krama_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }
        
        function download_excel_tamiu() {
            $('#form_laporan_krama_tamiu').attr('target', '_blank');
            $('#form_laporan_krama_tamiu').attr('action', "{{ route('Download Excel Laporan Krama Tamiu') }}");
            $("#form_laporan_krama_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    <script>
        function filter_tamu() {
            $('#form_laporan_tamiu').removeAttr('target');
            $('#form_laporan_tamiu').attr('action', "{{ route('Laporan Tamiu') }}");
            $("#form_laporan_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_pdf_tamu() {
            $('#form_laporan_tamiu').attr('target', '_blank');
            $('#form_laporan_tamiu').attr('action', "{{ route('Download PDF Laporan Tamiu') }}");
            $("#form_laporan_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_excel_tamu() {
            $('#form_laporan_tamiu').attr('target', '_blank');
            $('#form_laporan_tamiu').attr('action', "{{ route('Download Excel Laporan Tamiu') }}");
            $("#form_laporan_tamiu").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>
@endpush