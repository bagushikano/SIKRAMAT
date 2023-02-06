@extends('layouts.banjar.banjar')
@push('css')
    <style>
        @media (min-width: 768px) {
            .h-md-100 {
                height: 100% !important;
            }
        }
    </style>
@endpush
@section('title', 'Detail Perceraian')
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
                        <li class="breadcrumb-item active text-red-pastel">Detail Perceraian</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            @csrf
            <div class="row">
                <div class="col-xxl-4 col-xl-12">
                    <div class="card mh-75 mb-4">
                        <div class="card-body h-100 d-flex justify-content-center py-5 py-xl-4">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <p class="text-gray-700 mb-0">Detail Data <span class="text-primary font-weight-bold">Perceraian</span><br>Banjar Adat <span class="text-primary font-weight-bold">{{ session()->get('banjar_adat_nama') }}</span></p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/population.png')}}" style="max-width: 20rem; max-height: 18rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12 mb-4">
                    <div class="card">
                        <div class="card-header">Data Perceraian</div>
                        <div class="card-body px-5 mt-2">

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $perceraian->nomor_perceraian ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Akta Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $perceraian->nomor_akta_perceraian ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Tanggal Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ date('d M Y', strtotime($perceraian->tanggal_perceraian)) ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Pemuput Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $perceraian->nama_pemuput ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">File Bukti Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: @if($perceraian->file_bukti_perceraian != NULL)<a class="text-start text-primary" href="{{ $perceraian->file_bukti_perceraian }}" target="_blank"><i class="fas fa-download"></i> Unduh File Bukti Perceraian</a>@else - @endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">File Akta Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: @if($perceraian->file_akta_perceraian != NULL)<a class="text-start text-primary" href="{{ $perceraian->file_akta_perceraian }}" target="_blank"><i class="fas fa-download"></i> Unduh File Akta Perceraian</a>@else - @endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Status Perceraian</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    @if($perceraian->status_perceraian == '3')
                                        <span>: Sah</span>
                                    @elseif($perceraian->status_perceraian == '1')
                                        <span>: Menunggu Konfirmasi</span>
                                    @else
                                        <span>: Draft</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Keterangan</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $perceraian->keterangan ?? '-' }}</span>
                                </div>
                            </div>

                            <hr class="my-4" />
                            <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-perceraian-home') }}">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali</span>
                            </a>
                            @if($perceraian->status_perceraian == '1')
                                <button class="btn btn-warning btn-icon-split my-1 mx-1 text-end" onclick="tolak_modal()">
                                    <span class="icon">
                                        <i class="fas fa-file-excel"></i>
                                    </span>
                                    <span class="text">Belum Dapat Dikonfirmasi</span>
                                </button>

                                <a class="btn btn-success btn-icon-split my-1 mx-1 text-end" href="{{ route('banjar-perceraian-konfirmasi', $perceraian->id) }}">
                                    <span class="icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </span>
                                    <span class="text">Konfirmasi</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Data Dampati</div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-8">
                            <div id="data-purusa">
                                <h5 class="card-title text-primary mt-3">Data Purusa</h5>
                                <div class="row">
                                    <div class="col-lg-8 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->nama }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">No. Induk Cacah Krama Mipil</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ substr($perceraian->purusa->nomor_cacah_krama_mipil,0,4) }}-{{ substr($perceraian->purusa->nomor_cacah_krama_mipil,4,2) }}-{{ substr($perceraian->purusa->nomor_cacah_krama_mipil,6,2) }}-{{ substr($perceraian->purusa->nomor_cacah_krama_mipil,8,6) }}-{{ substr($perceraian->purusa->nomor_cacah_krama_mipil,14,3) }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ substr($perceraian->purusa->penduduk->nik,0,6) }}-{{ substr($perceraian->purusa->penduduk->nik,6,6) }}-{{ substr($perceraian->purusa->penduduk->nik,12,6) }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($perceraian->purusa->penduduk->tanggal_lahir)) ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $perceraian->purusa->penduduk->jenis_kelamin)) ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->golongan_darah ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Agama</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: @if($perceraian->purusa->penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $perceraian->purusa->penduduk->agama)) }} @else - @endif</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Alamat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->alamat ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->tempekan->nama_tempekan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->ayah->nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->purusa->penduduk->ibu->nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <img src="@if($perceraian->purusa->penduduk->foto != NULL) {{ $perceraian->purusa->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4"/>
                            </div>

                            <div id="data-pradana">
                                <h5 class="card-title text-primary mt-3">Data Pradana</h5>
                                <div class="row">
                                    <div class="col-lg-8 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->nama }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">No. Induk Cacah Krama Mipil</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ substr($perceraian->pradana->nomor_cacah_krama_mipil,0,4) }}-{{ substr($perceraian->pradana->nomor_cacah_krama_mipil,4,2) }}-{{ substr($perceraian->pradana->nomor_cacah_krama_mipil,6,2) }}-{{ substr($perceraian->pradana->nomor_cacah_krama_mipil,8,6) }}-{{ substr($perceraian->pradana->nomor_cacah_krama_mipil,14,3) }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ substr($perceraian->pradana->penduduk->nik,0,6) }}-{{ substr($perceraian->pradana->penduduk->nik,6,6) }}-{{ substr($perceraian->pradana->penduduk->nik,12,6) }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($perceraian->pradana->penduduk->tanggal_lahir)) ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $perceraian->pradana->penduduk->jenis_kelamin)) ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->golongan_darah ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Agama</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: @if($perceraian->pradana->penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $perceraian->pradana->penduduk->agama)) }} @else - @endif</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Alamat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->alamat ?? '-' }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->tempekan->nama_tempekan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->ayah->nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12">
                                                <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                            </div>
                                            <div class="col-lg-7 col-sm-12">
                                                <span class="text-dark font-weight-bold">: {{ $perceraian->pradana->penduduk->ibu->nama ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <img src="@if($perceraian->pradana->penduduk->foto != NULL) {{ $perceraian->pradana->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4" />
                            </div>

                            <div id="data-anggota-keluarga">
                                <h5 class="card-title text-primary">Data Anggota Keluarga</h5>
                                @if($anggota_krama_mipil->isNotEmpty())
                                <div class="datatable">
                                    <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No.</th>
                                                <th style="width: 18%">No. Cacah Krama Mipil</th>
                                                <th>Nama</th>
                                                <th style="width: 15%">Status Hubungan</th>
                                                <th style="width: 15%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($anggota_krama_mipil as $anggota_krama)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $anggota_krama->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                                    <td>{{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $anggota_krama->status_hubungan)) }}</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $anggota_krama->status_baru)) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                    <div id="anggota-keluarga-0">
                                        <div class="alert alert-info text-center" id="alert-anggota-keluarga" role="alert">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Tidak terdapat anggota keluarga.
                                        </div>
                                    </div>
                                @endif
                                <hr class="my-4 mb-5" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="alasan_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Perceraian Belum Dapat Dikonfirmasi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form-tolak-perkawinan" method="post" action="{{ route('banjar-perceraian-tolak', $perceraian->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="alasan_penolakan" class="text-dark">Alasan Tidak Mengkonfirmasi Perceraian<span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control @error ('alasan_penolakan') is-invalid @enderror" placeholder="Masukkan Alasan" rows="3" name="alasan_penolakan" id="alasan_penolakan" required></textarea>
                            @error('alasan_penolakan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Alasan wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready( function () {
            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perceraian').addClass('active');
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

        function tolak_modal(){
            $('#alasan_modal').modal('show');
        }
    </script>
@endpush