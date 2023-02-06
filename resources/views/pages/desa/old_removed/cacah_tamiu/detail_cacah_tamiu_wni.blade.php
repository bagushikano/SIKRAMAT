@extends('layouts.desa.desa')
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
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-cacah-krama-tamiu-home') }}" class="text-decoration-none">Cacah Krama Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Detail Cacah Krama Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Data Pribadi Cacah Krama Tamiu</div>
                <div class="card-body">
                    <div class="row mx-5 mt-2">
                        <div class="col-lg-6 col-sm-12">
                            <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:30%;" id="propic">
                        </div>
                    </div>

                    <div class="row mx-5 mt-4">
                        <div class="col-lg-3 col-sm-3">
                            <label for="nomor_cacah_tamiu">Nomor Cacah Tamiu</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ substr($krama->nomor_cacah_tamiu,0,4) }}-{{ substr($krama->nomor_cacah_tamiu,4,2) }}-{{ substr($krama->nomor_cacah_tamiu,6,2) }}-{{ substr($krama->nomor_cacah_tamiu,8,6) }}-{{ substr($krama->nomor_cacah_tamiu,14,3) }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="nomor_cacah_tamiu">Nomor Induk Kependudukan</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold font-weight-bold">: {{ substr($penduduk->nik,0,6) }}-{{ substr($penduduk->nik,6,6) }}-{{ substr($penduduk->nik,12,6) }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="nama_lengkap">Nama Lengkap</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: @if($penduduk->gelar_depan != ''){{ $penduduk->gelar_depan }} @endif{{ $penduduk->nama }}@if($penduduk->gelar_belakang != ''), {{ $penduduk->gelar_belakang }}@endif</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="nama_lengkap">Nama Alias (Bhiseka)</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->nama_alias ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="tempat_lahir">Tempat Lahir</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->tempat_lahir ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="tempat_lahir">Tanggal Lahir</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($penduduk->tanggal_lahir)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="agama">Agama</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: @if($penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $penduduk->agama)) }} @else - @endif</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->jenis_kelamin)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="golongan_darah">Golongan Darah</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->golongan_darah)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->pendidikan->jenjang_pendidikan ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="pekerjaan">Pekerjaan</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->pekerjaan->profesi ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="status_perkawinan">Status Perkawinan</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: @if($penduduk->status_perkawinan != ''){{ ucwords(str_replace('_', ' ', $penduduk->status_perkawinan)) }} @else - @endif</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="telepon">Nomor Telepon</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->telepon ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="alamat">Alamat</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->alamat ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="provinsi">Provinsi</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($provinsi->name)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="kabupaten">Kabupaten</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kabupaten->name)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="kecamatan">Kecamatan</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($kecamatan->name)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="desa">Desa/Kelurahan</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ ucwords(strtolower($desa->name)) ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="ayah">Ayah Kandung</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->ayah->nama ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="ibu">Ibu Kandung</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $penduduk->ibu->nama ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="banjar_adat">Banjar Adat</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $krama->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="banjar_dinas">Banjar Dinas</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $krama->banjar_dinas->nama_banjar_dinas ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $krama->tanggal_masuk ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-3 col-sm-3">
                            <label for="tanggal_keluar">Tanggal Keluar</label>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <span class="text-dark font-weight-bold">: {{ $krama->tanggal_keluar ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="my-4 mx-5" />
                    <div class="row mx-5 mt-3 mb-2">
                        <div class="col-lg-6 col-sm-12">
                            <a class="btn btn-danger" href="{{ route('desa-cacah-tamiu-home') }}">Kembali</a><a class="btn btn-warning ml-2" href="{{ route('desa-cacah-tamiu-wni-edit', $krama->id) }}">Edit</a>
                        </div>
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
            $('#nav-link-cacah-tamiu').addClass('active');
        });
    </script>
@endpush