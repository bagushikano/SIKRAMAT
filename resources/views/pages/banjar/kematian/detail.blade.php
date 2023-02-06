@extends('layouts.banjar.banjar')
@push('css')
    <style>
        .btn-custom { justify-content: flex-start !important;}
    </style>
@endpush
@section('title', 'Detail Kematian')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-book-dead mr-2"></i></div>
                                Manajemen Kematian
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-kematian-home') }}" class="text-decoration-none text-dark">Manajemen Kematian</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Detail Kematian</li>
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
                            <div class="wizard-step-icon"><i class="fas fa-book-dead text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Kematian</div>
                                <div class="wizard-step-text-details text-dark">Detail Data Kematian yang Telah Sah</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-10 mt-4">
                            <form id="form-create-kematian" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="title">Nomor Cacah Krama Mipil</label>
                                            <input type="text" class="form-control"  id="cacah_krama_mipil_placeholder" name="cacah_krama_mipil_placeholder" placeholder="Pilih Cacah Krama Mipil" value="{{ $kematian->cacah_krama_mipil->nomor_cacah_krama_mipil }}" required disabled>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="title">Nama Cacah Krama Mipil</label>
                                            <input type="text" class="form-control"  id="cacah_krama_mipil_placeholder" name="cacah_krama_mipil_placeholder" placeholder="Pilih Cacah Krama Mipil" value="{{ $kematian->cacah_krama_mipil->penduduk->nama }}" required disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="small" for="tanggal_kematian">Tanggal Kematian</label>
                                    <input type="text" class="datepicker-here form-control" placeholder="Masukkan Tanggal Kematian" name="tanggal_kematian" id="tanggal_kematian" value="{{ date('d M Y', strtotime($kematian->tanggal_kematian)) }}" required disabled>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_suket_kematian">No. Surat Keterangan Kematian</label>
                                            <input class="form-control @error('nomor_suket_kematian') is-invalid @enderror" id="nomor_suket_kematian" name="nomor_suket_kematian" type="text" value="{{ $kematian->nomor_suket_kematian ?? '-' }}" placeholder="Masukkan Nomor Surat Keterangan Kematian" disabled>
                                            @error('nomor_suket_kematian')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Nomor Surat Keterangan Kematian wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="lampiran">File Surat Keterangan Kematian</label>
                                            <br>    
                                            @if($kematian->file_suket_kematian != NULL)
                                            <a class="btn btn-primary btn-block btn-icon-split mb-3 text-end btn-custom" href="{{ $kematian->file_suket_kematian }}">
                                                <span class="icon text-white-50 pull-left">
                                                    <i class="fas fa-download"></i>
                                                </span>
                                                <span class="text">Unduh File Surat Keterangan Kematian</span>
                                            </a>
                                            @else
                                            <button class="btn btn-light btn-block btn-icon-split mb-3 text-end btn-custom" type="button" disabled>
                                                <span class="icon text-white-50 pull-left">
                                                    <i class="fas fa-folder-minus"></i>
                                                </span>
                                                <span class="text">Tidak Ada File Surat Keterangan Kematian</span>
                                            </button>                                   
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_akta_kematian">No. Akta Kematian</label>
                                            <input class="form-control" id="nomor_akta_kematian" name="nomor_akta_kematian" type="text" value="{{ $kematian->nomor_akta_kematian ?? '-' }}" placeholder="Masukkan Nomor Akta Kematian" disabled>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="lampiran">File Akta Kematian</label>
                                            <br>    
                                            @if($kematian->file_akta_kematian != NULL)
                                            <a class="btn btn-primary btn-block btn-icon-split mb-3 text-end btn-custom" href="{{ $kematian->file_akta_kematian }}">
                                                <span class="icon text-white-50 pull-left">
                                                    <i class="fas fa-download"></i>
                                                </span>
                                                <span class="text">Unduh File Akta Kematian</span>
                                            </a>
                                            @else
                                            <button class="btn btn-light btn-block btn-icon-split mb-3 text-end btn-custom" type="button" disabled>
                                                <span class="icon text-white-50 pull-left">
                                                    <i class="fas fa-folder-minus"></i>
                                                </span>
                                                <span class="text">Tidak Ada File Akta Kematian</span>
                                            </button>                                    
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="form-group">
                                    <label class="small" for="penyebab_kematian">Penyebab Kematian</label>
                                    <textarea type="text" class="form-control @error ('penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="penyebab_kematian" id="penyebab_kematian" required disabled>{{ $kematian->penyebab_kematian }}</textarea>
                                    @error('penyebab_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Penyebab Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="small" for="keterangan">Keterangan</label>
                                    <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan (Jika Ada)" rows="3" name="keterangan" id="keterangan" disabled>{{ $kematian->keterangan }}</textarea>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-kematian-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready( function () {
            //DATEPICKER
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-kematian').addClass('active');
        });
    </script>
@endpush