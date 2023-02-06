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
@section('title', 'Detail Maperas')
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
                        <li class="breadcrumb-item active text-red-pastel">Detail Maperas</li>
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
                                    <p class="text-gray-700 mb-0">Detail Data <span class="text-primary font-weight-bold">Maperas</span><br>Banjar Adat <span class="text-primary font-weight-bold">{{ session()->get('banjar_adat_nama') }}</span></p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/population.png')}}" style="max-width: 20rem; max-height: 18rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">Data Maperas</div>
                        <div class="card-body px-5 mt-2">
                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Jenis Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: Beda Banjar Adat (Keluar Banjar Adat)</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $maperas->nomor_maperas }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Akta Pengangkatan Anak</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $maperas->nomor_akta_pengangkatan_anak ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Tanggal Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ date('d M Y', strtotime($maperas->tanggal_maperas)) ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Pemuput Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $maperas->nama_pemuput ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">File Bukti Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: @if($maperas->file_bukti_maperas != NULL)<a class="text-start text-primary" href="{{ $maperas->file_bukti_maperas }}" target="_blank"><i class="fas fa-download"></i> Unduh File Bukti Maperas</a>@endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">File Akta Pengangkatan Anak</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: @if($maperas->file_akta_pengangkatan_anak != NULL)<a class="text-start text-primary" href="{{ $maperas->file_akta_pengangkatan_anak }}" target="_blank"><i class="fas fa-download"></i> Unduh File Akta Pengangkatan Anak</a>@else - @endif</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Status Maperas</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    @if($maperas->status_maperas == '0')
                                        <span>: Draft</span>
                                    @elseif($maperas->status_maperas == '1')
                                        <span>: Terkonfirmasi</span>
                                    @elseif($maperas->status_maperas =='3')
                                        <span>: Sah</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-5 col-sm-4">
                                    <h5 for="title" class="font-weight-bold text-dark">Keterangan</h5>
                                </div>
                                <div class="col-lg-7 col-sm-8">
                                    <span>: {{ $maperas->keterangan ?? '-' }}</span>
                                </div>
                            </div>

                            <hr class="my-4" />
                            <a class="btn btn-danger btn-icon-split my-1 mx-1 text-end" href="{{ route('banjar-maperas-home') }}">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali</span>
                            </a>
                            @if($maperas->status_maperas == '0')
                                <button class="btn btn-warning btn-icon-split my-1 mx-1 text-end" onclick="tolak_modal()">
                                    <span class="icon">
                                        <i class="fas fa-file-excel"></i>
                                    </span>
                                    <span class="text">Belum Dapat Dikonfirmasi</span>
                                </button>

                                <a class="btn btn-success btn-icon-split my-1 mx-1 text-end" href="{{ route('banjar-maperas-keluar-konfirmasi', $maperas->id) }}">
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
                <div class="card-header">Data Anak dan Orang Tua</div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-8">
                            {{-- <h3 class="text-primary">Langkah 1</h3> --}}
                            <h5 class="card-title text-primary mt-3">Data Anak</h5>
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Nama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->penduduk->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Cacah Krama Mipil</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ substr($anak->nomor_cacah_krama_mipil,0,4) }}-{{ substr($anak->nomor_cacah_krama_mipil,4,2) }}-{{ substr($anak->nomor_cacah_krama_mipil,6,2) }}-{{ substr($anak->nomor_cacah_krama_mipil,8,6) }}-{{ substr($anak->nomor_cacah_krama_mipil,14,3) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">No. Induk Kependudukan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ substr($anak->penduduk->nik,0,6) }}-{{ substr($anak->penduduk->nik,6,6) }}-{{ substr($anak->penduduk->nik,12,6) }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Tempat/Tanggal Lahir</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($anak->penduduk->tanggal_lahir)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Jenis Kelamin</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ ucwords(str_replace('_', ' ', $anak->penduduk->jenis_kelamin)) ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Golongan Darah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->penduduk->golongan_darah ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Agama</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: @if($anak->penduduk->agama != ''){{ ucwords(str_replace('_', ' ', $anak->penduduk->agama)) }} @else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Alamat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->penduduk->alamat ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $anak->tempekan->nama_tempekan ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <img src="@if($anak->penduduk->foto != NULL) {{ $anak->penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail float-right" style="max-width:40%;" id="propic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <h5 class="card-title text-primary">Data Orang Tua Lama</h5>
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ayah_lama != NULL) {{ $ayah_lama->penduduk->nama }}@else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ibu_lama != NULL) {{ $ibu_lama->penduduk->nama }}@else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Alamat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ayah_lama != NULL) {{ $ayah_lama->penduduk->alamat ?? '-' }}@elseif($ibu_lama != NULL) {{ $ibu_lama->penduduk->alamat ?? '-' }}@else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ayah_lama != NULL) {{ $ayah_lama->banjar_adat->desa_adat->desadat_nama ?? '-' }}@elseif($ibu_lama != NULL) {{ $ibu_lama->banjar_adat->desa_adat->desadat_nama ?? '-' }}@else - @endif</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ayah_lama != NULL) {{ $ayah_lama->banjar_adat->nama_banjar_adat ?? '-' }}@elseif($ibu_lama != NULL) {{ $ibu_lama->banjar_adat->nama_banjar_adat ?? '-' }}@else - @endif</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">:@if($ayah_lama != NULL) {{ $ayah_lama->tempekan->nama_tempekan ?? '-' }}@elseif($ibu_lama != NULL) {{ $ibu_lama->tempekan->nama_tempekan ?? '-' }}@else - @endif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <h5 class="card-title text-primary">Data Orang Tua Baru</h5>
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Nama Ayah</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ayah_baru->penduduk->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Nama Ibu</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ibu_baru->penduduk->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Alamat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ayah_baru->penduduk->alamat ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Desa Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ayah_baru->banjar_adat->desa_adat->desadat_nama ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Banjar Adat</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ayah_baru->banjar_adat->nama_banjar_adat ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-sm-4">
                                            <label for="nomor_cacah_krama_mipil">Tempekan</label>
                                        </div>
                                        <div class="col-lg-7 col-sm-8">
                                            <span class="text-dark font-weight-bold">: {{ $ayah_baru->tempekan->nama_tempekan ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4 mb-5" />
                            {{-- <div class="float-right mb-2">
                                
                            </div> --}}
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
                    <h5 class="modal-title" id="exampleModalLabel">Maperas Belum Dapat Dikonfirmasi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form-tolak-maperas" method="post" action="{{ route('banjar-maperas-keluar-tolak', $maperas->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="alasan_penolakan" class="text-dark">Alasan Tidak Mengkonfirmasi Maperas<span class="text-danger">*</span></label>
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
            $('#nav-link-maperas').addClass('active');

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

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