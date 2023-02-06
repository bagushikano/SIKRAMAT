@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
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
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Perkawinan Keluar Desa Adat
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-perkawinan-masuk-desa-adat-home') }}" class="text-decoration-none">Perkawinan Keluar Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Detail Perkawinan</li>
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
                            <div class="wizard-step-icon"><i class="fas fa-info text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Perkawinan</div>
                                <div class="wizard-step-text-details text-dark">Detail Perkawinan Keluar Desa Adat</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="overlay">
                        <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-xxl-8 col-xl-8 mt-4">
                            <form id="form-create-perkawinan" method="post" action="{{route('desa-perkawinan-masuk-desa-adat-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf 
                                <div id="perkawinan_dalam_desa_adat">
                                    <div class="form-group">
                                        <label for="title">No. Bukti Serah Terima Perkawinan</label>
                                        <input type="text" class="form-control @error ('nomor_bukti_serah_terima_perkawinan') is-invalid @enderror"  id="nomor_bukti_serah_terima_perkawinan" name="nomor_bukti_serah_terima_perkawinan" placeholder="Masukkan No. Bukti Serah Terima Perkawinan" value="{{ old('nomor_bukti_serah_terima_perkawinan', $perkawinan->nomor_perkawinan) }}" required disabled>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="kabupaten_purusa">Kabupaten Asal Purusa</label>
                                                <select class="select2 custom-select @error ('kabupaten_purusa') is-invalid @enderror" name="kabupaten_purusa" id="kabupaten_purusa"  style="width: 100%" required disabled>
                                                    <option value="{{ $kabupaten_purusa->id }}">{{ $kabupaten_purusa->name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="kecamatan_purusa">Kecamatan Asal Purusa</label>
                                                <select class="select2 custom-select @error ('kecamatan_purusa') is-invalid @enderror" name="kecamatan_purusa" id="kecamatan_purusa"  style="width: 100%" required disabled>
                                                    <option value="{{ $kecamatan_purusa->id }}">{{ $kecamatan_purusa->name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row mb-3">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group mb-n1">
                                                <label for="desa_adat_purusa">Desa Adat Asal Purusa</label>
                                                <select class="select2 custom-select @error ('desa_adat_purusa') is-invalid @enderror" name="desa_adat_purusa" id="desa_adat_purusa"  style="width: 100%" required disabled>
                                                    <option value="{{ $desa_adat_purusa->id }}">{{ $desa_adat_purusa->desadat_nama }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group mb-n1">
                                                <label for="banjar_adat_purusa">Banjar Adat Asal Purusa</label>
                                                <select class="select2 custom-select @error ('banjar_adat_purusa') is-invalid @enderror" name="banjar_adat_purusa" id="banjar_adat_purusa"  style="width: 100%" required disabled>
                                                    <option value="{{ $banjar_adat_purusa->id }}">{{ $banjar_adat_purusa->nama_banjar_adat }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Cacah Krama Purusa</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('purusa_placeholder') is-invalid @enderror"  id="purusa_placeholder" name="purusa_placeholder" placeholder="Pilih Cacah Krama Purusa" value="{{ old('purusa_placeholder', $perkawinan->purusa->penduduk->nama) }}" required disabled>
                                        </div>
                                    </div>

                                    
                                    <div class="form-group">
                                        <label for="banjar_adat_pradana">Banjar Adat Pradana</label>
                                        <select class="select2 custom-select @error ('banjar_adat_pradana') is-invalid @enderror" name="banjar_adat_pradana" id="banjar_adat_pradana"  style="width: 100%" required disabled required disabled>
                                            <option value="{{ $banjar_adat_pradana->id }}">{{ $banjar_adat_pradana->nama_banjar_adat }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Cacah Krama Pradana</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('pradana_placeholder') is-invalid @enderror"  id="pradana_placeholder" name="pradana_placeholder" placeholder="Pilih Cacah Krama Pradana" value="{{ old('pradana_placeholder', $perkawinan->purusa->penduduk->nama) }}" required disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal_perkawinan">Tanggal Perkawinan</label>
                                        <input type="text" class="datepicker-here form-control @error ('tanggal_perkawinan') is-invalid @enderror" placeholder="Masukkan Tanggal Perkawinan" name="tanggal_perkawinan" id="tanggal_perkawinan" value="{{ old('tanggal_perkawinan', $perkawinan->tanggal_perkawinan) }}" required disabled>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="lampiran">Bukti Serah Terima Perkawinan</label>
                                        <br>    
                                        <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ $perkawinan->lampiran }}">
                                            <span class="icon">
                                                <i class="fas fa-download"></i>
                                            </span>
                                            <span class="text">Unduh Bukti Serah Terima Perkawinan</span>
                                        </a>
                                    </div>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    {{-- <button class="btn btn-light" type="button">Previous</button> --}}
                                    <a class="btn btn-light mr-2" href="{{ route('desa-perkawinan-keluar-desa-adat-home') }}">Kembali</a> <div><button class="btn btn-danger mr-1" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Tolak" onclick="tolak_perkawinan({{ $perkawinan->id }})">Tolak</button><button class="btn btn-success" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Setujui" onclick="setuju_perkawinan({{ $perkawinan->id }})">Setujui</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Select Cacah Krama Mipil Purusa -->
    <div class="modal fade" id="select_purusa_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Purusa</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="body_loading_select_purusa">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="body_select_purusa">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Select Cacah Krama Mipil Pradana -->
    <div class="modal fade" id="select_pradana_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Pradana</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="body_loading_select_pradana">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="body_select_pradana">
                    
                </div>
            </div>
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
            $("#tanggal_perkawinan").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SIDE BAR CLASS
            $('#sidebarPerkawinan').removeClass('collapsed');
            $('#collapsePerkawinan').addClass('show');
            $('#collapsePerkawinan').addClass('active');
            $('#nav-link-perkawinan-keluar-desa').addClass('active');
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

        //Approval Button
        function setuju_perkawinan(id){
            Swal.fire({
                title: 'Setujui Perkawinan',
                text: "Apakah anda yakin ingin menyetujui perkawinan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-krama-mipil-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-krama').attr("action", url);
                        $('#form-delete-krama').submit();
                    }
                })
        }

        function tolak_perkawinan(id){
            Swal.fire({
                title: 'Tolak Perkawinan',
                text: "Masukkan alasan penolakan pada kotak dibawah ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                input: 'textarea'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-krama-mipil-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-krama').attr("action", url);
                        $('#form-delete-krama').submit();
                    }
                })
        }
        //Approval Button

        //Select 2
        $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
            }
        });

   
    </script>
@endpush