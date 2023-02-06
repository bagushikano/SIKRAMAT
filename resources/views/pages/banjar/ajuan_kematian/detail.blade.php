@extends('layouts.banjar.banjar')
@section('title', 'Detail Ajuan Data Kematian')
@section('content')
<main>
    <header class="page-header page-header-light pb-10">
        <div class="container">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-book-dead mr-2"></i></div>
                            Detail Ajuan Data Kematian
                        </h1>
                       
                    </div>
                </div>
                <ol class="breadcrumb mb-0 mt-4">
                    <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('banjar-ajuan-kematian-home') }}" class="text-decoration-none text-dark">Daftar Ajuan Data Kematian</a></li>
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
                        <div class="wizard-step-icon"><i class="fas fa-baby text-dark"></i></div>
                        <div class="wizard-step-text">
                            <div class="wizard-step-text-name text-dark">Data Kematian</div>
                            <div class="wizard-step-text-details text-dark">Data Data Diri Penduduk yang Telah Meninggal</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-xxl-10 col-xl-10 mt-2">
                        <h5 class="card-title text-primary">Data Diri</h5>
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Cacah Krama Mipil</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,0,4) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,4,2) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,6,2) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,8,6) }}-{{ substr($cacah_krama_mipil->nomor_cacah_krama_mipil,14,3) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ substr($penduduk->nik,0,6) }}-{{ substr($penduduk->nik,6,6) }}-{{ substr($penduduk->nik,12,6) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->nama }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($penduduk->tanggal_lahir)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $penduduk->jenis_kelamin)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->golongan_darah ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Agama</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: @if($penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $penduduk->agama)) }} @else - @endif</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Alamat</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->alamat ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Jenis Kependudukan</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $cacah_krama_mipil->jenis_kependudukan)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->tempekan->nama_tempekan ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Banjar Dinas</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $cacah_krama_mipil->banjar_dinas->nama_banjar_dinas ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->ayah->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $penduduk->ibu->nama }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="row">
                                    <div class="col-12">
                                        <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4" />
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <h5 class="card-title text-primary">Data Kematian</h5>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Kematian</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kematian->nomor_suket_kematian }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">No. Akta Kematian</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kematian->nomor_akta_kematian }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">File Akta Kematian</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: @if($kematian->file_akta_kematian != NULL)<a class="text-start text-primary small" href="{{ $kematian->file_akta_kematian }}" target="_blank"><i class="fas fa-download"></i> Unduh File Akta Kematian</a>@else - @endif</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">File Surat Keterangan Kematian</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: @if($kematian->file_suket_kematian != NULL)<a class="text-start text-primary small" href="{{ $kematian->file_suket_kematian }}" target="_blank"><i class="fas fa-download"></i> Unduh File Surat Keterangan Kematian</a>@else - @endif</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Keterangan</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kematian->keterangan ?? '-' }}</span>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Diajukan oleh</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kematian->user->user->penduduk->nama ?? '-' }}</span>
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Diajukan pada</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($kematian->created_at)) ?? '-' }}</span>
                                    </div>
                                </div>
                                @if($kematian->status == '2')
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Ditolak pada</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($kematian->tanggal_tolak)) ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Alasan Penolakan</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ $kematian->alasan_tolak_ajuan ?? '-' }}</span>
                                    </div>
                                </div>
                                @endif
                                @if($kematian->status == '3')
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label for="nomor_cacah_krama_mipil">Disahkan pada</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <span class="text-dark font-weight-bold">: {{ date('d M Y', strtotime($kematian->tanggal_sah)) ?? '-' }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-4 col-sm-12">
                            </div>
                        </div>
                        <hr class="my-4" />
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-danger btn-icon-split mb-3 text-end" href="{{ route('banjar-ajuan-kematian-home') }}">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali</span>
                            </a>
                            <div>
                                @if($kematian->status == '0')
                                    <a class="btn btn-success btn-icon-split mb-3 text-end" href="{{ route('banjar-ajuan-kematian-proses', $kematian->id) }}">
                                        <span class="icon">
                                            <i class="fas fa-clipboard-check"></i>
                                        </span>
                                        <span class="text">Proses Data Ajuan</span>
                                    </a>
                                @elseif($kematian->status == '1')
                                    <button class="btn btn-warning btn-icon-split mb-3 text-end" type="button" onclick="tolak_modal()">
                                        <span class="icon">
                                            <i class="fas fa-file-excel"></i>
                                        </span>
                                        <span class="text">Tolak Ajuan</span>
                                    </button>
                                    <a class="btn btn-success btn-icon-split mb-3 text-end" href="{{ route('banjar-ajuan-kematian-sahkan', $kematian->id) }}">
                                        <span class="icon">
                                            <i class="fas fa-user-check"></i>
                                        </span>
                                        <span class="text">Sahkan Ajuan</span>
                                    </a>
                                @endif
                            </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">Tolak Ajuan Data Kematian</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form-tolak-perkawinan" method="post" action="{{ route('banjar-ajuan-kematian-tolak', $kematian->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="alasan_penolakan" class="text-dark">Alasan Penolakan<span class="text-danger">*</span></label>
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
@if($message = Session::get('alert'))
    <script>
        $(document).ready(function(){
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

            Toast.fire({
                icon: 'success',
                title: '{{ $message }}'
            });
        });
    </script>
@endif
<script>
    $(document).ready(function() {
        $('#collapseAjuan').addClass('show');
        $('#nav-link-ajuan-kematian').addClass('active');

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
    });

    function tolak_modal(){
        $('#alasan_modal').modal('show');
    }
</script>
@endpush