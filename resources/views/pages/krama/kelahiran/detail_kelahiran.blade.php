@extends('layouts.krama.krama')
@section('title', 'Detail Kelahiran')
@section('content')
<main>
    <header class="page-header page-header-light pb-10">
        <div class="container">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon">
                                <i class="fa-solid fa-baby mr-2"></i>
                            </div>
                            Data Kelahiran
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container mt-n15">
        <div class="card mb-4 mt-5">
            <div class="card-header border-bottom">
                <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                    <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                        <div class="wizard-step-icon"><i class="fas fa-baby text-dark"></i></div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Data Kelahiran</div>
                            <div class="wizard-step-text-details text-dark">Data Diri dan Kelahiran Anak</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-xxl-10 col-xl-10 mt-2">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="row mt-3">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Akta Kelahiran</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kelahiran->nomor_akta_kelahiran }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->nama }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Cacah Krama Mipil</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,0,4) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,4,2) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,6,2) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,8,6) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,14,3) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ substr($penduduk->nik,0,6) }}-{{ substr($penduduk->nik,6,6) }}-{{ substr($penduduk->nik,12,6) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($penduduk->tanggal_lahir)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->jenis_kelamin)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->golongan_darah ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Agama</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: @if($penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $penduduk->agama)) }} @else - @endif</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Alamat</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->alamat ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Jenis Kependudukan</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $cacah_krama_mipil->jenis_kependudukan)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->tempekan->nama_tempekan ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Banjar Dinas</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->banjar_dinas->nama_banjar_dinas ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->ayah->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->ibu->nama }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">File Akta Kelahiran</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: @if($kelahiran->file_akta_kelahiran != NULL)<a class="text-start text-primary small" href="{{ $kelahiran->file_akta_kelahiran }}" target="_blank"><i class="fas fa-download"></i> Unduh File Akta Kelahiran</a>@else - @endif</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Keterangan</label>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kelahiran->keterangan ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4" />
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-danger btn-icon-split mb-3 text-end" href="{{ route('Kelahiran Home') }}">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#sidebarAjuan').removeClass('collapsed');
        $('#collapseAjuan').addClass('show');
        $('#nav-link-ajuan-kelahiran').addClass('active');
    });
</script>
@endpush