@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
@endpush
@section('title', 'Detail Cacah Krama Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Cacah Krama Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-cacah-krama-tamiu-home') }}" class="text-decoration-none text-dark">Cacah Krama Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Detail Cacah Krama Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-info text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Cacah Krama Tamiu</div>
                                <div class="wizard-step-text-details text-dark">Detail Data Cacah Krama Tamiu</div>
                            </div>
                        </a>
                    </div>
                </div> 
                <div class="card-body">
                    <div class="row mx-5 mt-4">
                        <div class="col-lg-9 col-sm-12">
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="nomor_cacah_krama_tamiu">Nomor Cacah Krama Tamiu</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ substr($krama->nomor_cacah_krama_tamiu,0,4) }}-{{ substr($krama->nomor_cacah_krama_tamiu,4,2) }}-{{ substr($krama->nomor_cacah_krama_tamiu,6,2) }}-{{ substr($krama->nomor_cacah_krama_tamiu,8,6) }}-{{ substr($krama->nomor_cacah_krama_tamiu,14,3) }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="nomor_cacah_krama_tamiu">Nomor Induk Kependudukan</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold font-weight-bold">: {{ substr($penduduk->nik,0,6) }}-{{ substr($penduduk->nik,6,6) }}-{{ substr($penduduk->nik,12,6) }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="nama_lengkap">Nama Lengkap</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: @if($penduduk->gelar_depan != ''){{ $penduduk->gelar_depan }} @endif{{ $penduduk->nama }}@if($penduduk->gelar_belakang != ''), {{ $penduduk->gelar_belakang }}@endif</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="nama_lengkap">Nama Alias (Bhiseka)</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->nama_alias ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="tempat_lahir">Tempat/Tanggal Lahir</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->tempat_lahir ?? '-' }}, {{ date('d M Y', strtotime($penduduk->tanggal_lahir)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="agama">Agama</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: @if($penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $penduduk->agama)) }} @else - @endif</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->jenis_kelamin)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="golongan_darah">Golongan Darah</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->golongan_darah)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="pendidikan_terakhir">Pendidikan Tertinggi</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->pendidikan->jenjang_pendidikan ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="pekerjaan">Pekerjaan</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->pekerjaan->profesi ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="status_perkawinan">Status Perkawinan</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: @if($penduduk->status_perkawinan != ''){{ ucwords(str_replace('_', ' ', $penduduk->status_perkawinan)) }} @else - @endif</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="telepon">Nomor Telepon</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->telepon ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="alamat">Alamat Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->alamat ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="alamat">Koordinat Alamat Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    @if($penduduk->koordinat_alamat != NULL)
                                        <span class="text-dark font-weight-bold">: @if($penduduk->koordinat_alamat != NULL)<a class="text-start text-primary small" href="https://maps.google.com/?q={{ $penduduk->koordinat_alamat->lat }},{{ $penduduk->koordinat_alamat->lng }}" target="_blank">{{ $penduduk->koordinat_alamat->lat ?? '-' }}, {{ $penduduk->koordinat_alamat->lng ?? '-' }}</a>@endif</span>
                                    @else 
                                        <span class="text-dark font-weight-bold">: -</span>
                                    @endif
                                </div>
                            </div>
        
                            {{-- <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="provinsi">Provinsi Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($provinsi->name)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="kabupaten">Kabupaten Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kabupaten->name)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="kecamatan">Kecamatan Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kecamatan->name)) ?? '-' }}</span>
                                </div>
                            </div> --}}
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="desa">Desa/Kelurahan Tempat Tinggal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($desa->name)) ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="ayah">Nama Ayah</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->ayah->nama ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="ibu">Nama Ibu</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $penduduk->ibu->nama ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="banjar_adat">Banjar Adat</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $krama->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                </div>
                            </div>
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="banjar_dinas">Banjar Dinas</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $krama->banjar_dinas->nama_banjar_dinas ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="banjar_dinas">Alamat Asal</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ $krama->alamat_asal ?? '-' }}</span>
                                </div>
                            </div>
        
                            @if($krama->asal == 'luar_bali')
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="provinsi">Provinsi Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($provinsi_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="kabupaten">Kabupaten Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kabupaten_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="kecamatan">Kecamatan Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kecamatan_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="desa">Desa/Kelurahan Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($desa_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
                            @elseif($krama->asal == 'dalam_bali')
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="provinsi">Kabupaten Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kabupaten_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="kabupaten">Kecamatan Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kecamatan_asal->name)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="kecamatan">Desa Adat Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($desa_adat_asal->desadat_nama)) ?? '-' }}</span>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="desa">Banjar Adat Asal</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($banjar_adat_asal->nama_banjar_adat)) ?? '-' }}</span>
                                    </div>
                            </div>
                            @endif
        
                            <div class="row">
                                <div class="col-lg-5 col-sm-12">
                                    <label for="tanggal_masuk">Tanggal Registrasi</label>
                                </div>
                                <div class="col-lg-7 col-sm-12">
                                    <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($krama->tanggal_masuk)) ?? '-' }}</span>
                                </div>
                            </div>
                            @if($krama->tanggal_keluar != NULL)
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="tempat_lahir">Tanggal Keluar</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($krama->tanggal_keluar)) ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <label for="tempat_lahir">Alasan Keluar</label>
                                    </div>
                                    <div class="col-lg-7 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $krama->alasan_keluar }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:60%;" id="propic">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 mx-5" />
                    <div class="float-left mx-5">
                        <a class="btn btn-danger mr-2 my-1 btn-icon-split text-end" href="{{ url()->previous() }}">
                            <span class="icon">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            <span class="text">Kembali</span>
                        </a>
                        @if($krama->status != '0')
                            <a class="btn btn-warning mr-2 my-1 btn-icon-split text-end" href="{{ route('banjar-cacah-krama-tamiu-edit', $krama->id) }}">
                                <span class="icon">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <span class="text">Edit</span>
                            </a>
                        @endif
                        <a class="btn btn-primary mr-2 my-1 btn-icon-split text-end" href="{{ route('banjar-cacah-krama-tamiu-daftar-riwayat', $krama->id) }}">
                            <span class="icon">
                                <i class="fas fa-history"></i>
                            </span>
                            <span class="text">Riwayat Perubahan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@push('js')
    <script>
        $(document).ready( function () {


            //SIDE BAR CLASS
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-krama-tamiu').addClass('active');
        });
    </script>
@endpush