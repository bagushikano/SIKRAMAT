@extends('layouts.banjar.banjar')
@push('css')
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <style>
        @media (min-width: 768px) {
            .h-md-100 {
                height: 100% !important;
            }
        }
    </style>
@endpush
@section('title', 'Detail Perkawinan')
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
                        <li class="breadcrumb-item active text-red-pastel">Detail Perkawinan Masuk Banjar</li>
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
                                    <p class="text-gray-700 mb-0">Detail Data <span class="text-primary font-weight-bold">Perkawinan</span><br>Banjar Adat <span class="text-primary font-weight-bold">{{ session()->get('banjar_adat_nama') }}</span></p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/population.png')}}" style="max-width: 20rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">Data Perkawinan</div>
                        <div class="card-body px-5 mt-2">
                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Jenis Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: Beda Banjar Adat (Masuk Banjar Adat)</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $perkawinan->nomor_perkawinan ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Akta Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $perkawinan->nomor_akta_perkawinan  ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Tanggal Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ date('d M Y', strtotime($perkawinan->tanggal_perkawinan)) ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Pemuput Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $perkawinan->nama_pemuput ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">File Bukti Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: @if($perkawinan->file_bukti_serah_terima_perkawinan != NULL)<a class="text-start text-primary" href="{{ $perkawinan->file_bukti_serah_terima_perkawinan }}" target="_blank"><i class="fas fa-download"></i> Unduh File Bukti Serah Terima Perkawinan</a>@else-@endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">File Akta Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: @if($perkawinan->file_akta_perkawinan != NULL)<a class="text-start text-primary" href="{{ $perkawinan->file_akta_perkawinan }}" target="_blank"><i class="fas fa-download"></i> Unduh File Akta Perkawinan</a>@else-@endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Status Perkawinan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: Terkonfirmasi oleh Pihak Pradana</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Keterangan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $perkawinan->keterangan ?? '-' }}</span>
                                </div>
                            </div>

                            <hr class="my-4" />
                            <a class="btn btn-danger btn-icon-split my-1 mx-1 text-end" href="{{ route('banjar-perkawinan-home') }}">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali</span>
                            </a>
                            <a class="btn btn-success btn-icon-split my-1 mx-1" href="{{ route('banjar-perkawinan-masuk-konfirmasi', $perkawinan->id) }}">
                                <span class="icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </span>
                                <span class="text">Sahkan Perkawinan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Data Dampati</div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-8">
                            {{-- <h3 class="text-primary">Langkah 1</h3> --}}
                            <h5 class="card-title text-primary mt-3">Data Purusa</h5>
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Cacah Krama Mipil</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ substr($perkawinan->purusa->nomor_cacah_krama_mipil,0,4) }}-{{ substr($perkawinan->purusa->nomor_cacah_krama_mipil,4,2) }}-{{ substr($perkawinan->purusa->nomor_cacah_krama_mipil,6,2) }}-{{ substr($perkawinan->purusa->nomor_cacah_krama_mipil,8,6) }}-{{ substr($perkawinan->purusa->nomor_cacah_krama_mipil,14,3) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ substr($perkawinan->purusa->penduduk->nik,0,6) }}-{{ substr($perkawinan->purusa->penduduk->nik,6,6) }}-{{ substr($perkawinan->purusa->penduduk->nik,12,6) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($perkawinan->purusa->penduduk->tanggal_lahir)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $perkawinan->purusa->penduduk->jenis_kelamin)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->golongan_darah ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Agama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: @if($perkawinan->purusa->penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $perkawinan->purusa->penduduk->agama)) }} @else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Alamat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->alamat ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->tempekan->nama_tempekan ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->ayah->nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->purusa->penduduk->ibu->nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <img src="@if($perkawinan->purusa->penduduk->foto != NULL) {{ $perkawinan->purusa->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <h5 class="card-title text-primary">Data Pradana</h5>
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Cacah Krama Mipil</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ substr($perkawinan->pradana->nomor_cacah_krama_mipil,0,4) }}-{{ substr($perkawinan->pradana->nomor_cacah_krama_mipil,4,2) }}-{{ substr($perkawinan->pradana->nomor_cacah_krama_mipil,6,2) }}-{{ substr($perkawinan->pradana->nomor_cacah_krama_mipil,8,6) }}-{{ substr($perkawinan->pradana->nomor_cacah_krama_mipil,14,3) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ substr($perkawinan->pradana->penduduk->nik,0,6) }}-{{ substr($perkawinan->pradana->penduduk->nik,6,6) }}-{{ substr($perkawinan->pradana->penduduk->nik,12,6) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($perkawinan->pradana->penduduk->tanggal_lahir)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $perkawinan->pradana->penduduk->jenis_kelamin)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->golongan_darah ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Agama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: @if($perkawinan->pradana->penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $perkawinan->pradana->penduduk->agama)) }} @else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Alamat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->alamat ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Desa Adat Asal</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Banjar Adat Asal</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->tempekan->nama_tempekan ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->ayah->nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-12">
                                            <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-12">
                                            <span class="text-dark font-weight-bold">: {{ $perkawinan->pradana->penduduk->ibu->nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <img src="@if($perkawinan->pradana->penduduk->foto != NULL) {{ $perkawinan->pradana->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4 mb-3" />
                            <div class="float-right">
                                {{-- <button class="btn btn-danger btn-icon-split text-end" onclick="tolak_modal()">
                                    <span class="icon">
                                        <i class="fas fa-file-excel"></i>
                                    </span>
                                    <span class="text">Belum Dapat Dikonfirmasi</span>
                                </button> --}}

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    {{-- Modal Tolak --}}
    <div class="modal fade" id="alasan_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Perkawinan Belum Dapat Dikonfirmasi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form-tolak-perkawinan" method="post" action="{{ route('banjar-perkawinan-keluar-tolak', $perkawinan->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="alasan_penolakan" class="text-dark">Alasan Tidak Mengkonfirmasi Perkawinan<span class="text-danger">*</span></label>
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
    {{-- MODAL --}}
@endsection
@push('js')
    <script>
        $(document).ready( function () {
            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perkawinan').addClass('active');

            //DatePicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
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