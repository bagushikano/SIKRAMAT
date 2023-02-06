@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <style>
        .form-custom[readonly] {
            display: block;
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #687281;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #c5ccd6;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
@endpush
@section('title', 'Tambah Perkawinan')
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
                        <li class="breadcrumb-item active text-red-pastel">Tambah Perkawinan</li>
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
                            <div class="wizard-step-icon"><i class="fas fa-plus text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Perkawinan</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Penambahan Perkawinan Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-9 col-xl-10 mt-4">
                            <form id="form-create-perkawinan" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf 
                                <div id="perkawinan_dalam_desa_adat">
                                    <div class="form-group">
                                        <label class="small" for="title">Jenis Perkawinan</label>
                                        <input type="text" class="form-control"  id="jenis_perkawinan" name="jenis_perkawinan" value="Perkawinan Beda Banjar Adat" required readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="title">Pilih Mempelai Purusa<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('purusa_placeholder') is-invalid @enderror form-custom"  id="purusa_placeholder" name="purusa_placeholder" placeholder="Pilih Mempelai Purusa" value="" required readonly>
                                            <div class="input-group-append">
                                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_purusa_modal()">
                                                    <span class="text">Pilih Purusa</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('purusa') is-invalid @enderror"  id="purusa" name="purusa"  value="" required hidden>
                                        @error('purusa')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Purusa wajib dipilih
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="kabupaten_pradana">Kabupaten Asal Pradana<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kabupaten_pradana') is-invalid @enderror" name="kabupaten_pradana" id="kabupaten_pradana"  style="width: 100%" required>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @foreach($kabupatens as $kabupaten)
                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kabupaten_pradana')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Kabupaten wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small" for="kecamatan_pradana">Kecamatan Asal Pradana<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('kecamatan_pradana') is-invalid @enderror" name="kecamatan_pradana" id="kecamatan_pradana"  style="width: 100%" required>
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                                <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                                @error('kecamatan_pradana')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Kecamatan wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row mb-3">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group mb-n1">
                                                <label class="small" for="desa_adat_pradana">Desa Adat Asal Pradana<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('desa_adat_pradana') is-invalid @enderror" name="desa_adat_pradana" id="desa_adat_pradana"  style="width: 100%" required>
                                                    <option value="">Pilih Desa Adat</option>
                                                </select>
                                                <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                                                @error('desa_adat_pradana')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Desa Adat wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group mb-n1">
                                                <label class="small" for="banjar_adat_pradana">Banjar Adat Asal Pradana<span class="text-danger small">*</span></label>
                                                <select class="select2 custom-select @error ('banjar_adat_pradana') is-invalid @enderror" name="banjar_adat_pradana" id="banjar_adat_pradana"  style="width: 100%" required>
                                                    <option value="">Pilih Banjar Adat</option>
                                                </select>
                                                <span class="small">(Pilih desa adat terlebih dahulu)</span>
                                                @error('banjar_adat_pradana')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Banjar Adat wajib dipilih
                                                    </div>
                                                @enderror 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="title">Pilih Mempelai Pradana<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('pradana_placeholder') is-invalid @enderror form-custom"  id="pradana_placeholder" name="pradana_placeholder" placeholder="Pilih Mempelai Pradana" value="" required readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_pradana_modal()">
                                                    <span class="text">Pilih Pradana</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('pradana') is-invalid @enderror"  id="pradana" name="pradana"  value="" required hidden>
                                        @error('pradana')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Pradana wajib dipilih
                                            </div>
                                        @enderror
                                        <small class="small">(Pilih Purusa terlebih dahulu)</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="status_kekeluargaan">Status Kekeluargaan<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('status_kekeluargaan') is-invalid @enderror" name="status_kekeluargaan" id="status_kekeluargaan"  style="width: 100%" required disabled>
                                            <option value="">Pilih Status Kekeluargaan</option>
                                            <option value="tetap">Tetap di Krama Mipil (Kepala Keluarga) Lama</option>
                                            <option value="baru">Pembentukan Krama Mipil (Kepala Keluarga) Baru</option>
                                        </select>
                                        @error('status_kekeluargaan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Status Kekeluargaan wajib dipilih
                                            </div>
                                        @enderror 
                                        <small class="small">(Pilih Purusa dan Pradana terlebih dahulu)</small>
                                    </div>

                                    <div class="form-group" id="calon_kepala_keluarga_div" style="display: none;">
                                        <label for="status_kekeluargaan" class="small">Kepala Keluarga<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('calon_kepala_keluarga') is-invalid @enderror" name="calon_kepala_keluarga" id="calon_kepala_keluarga"  style="width: 100%">
                                            <option value="">Pilih Kepala Keluarga</option>
                                        </select>
                                        @error('status_kekeluargaan')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Kepala keluarga wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>

                                    {{-- TANGGAL PEMUPUT --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="tanggal_perkawinan" class="small">Tanggal Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="datepicker-here form-control @error ('tanggal_perkawinan') is-invalid @enderror" placeholder="Masukkan Tanggal Perkawinan" name="tanggal_perkawinan" id="tanggal_perkawinan" value="{{ old('tanggal_perkawinan') }}" required>
                                                @error('tanggal_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Tanggal Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">Nama Pemuput Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nama_pemuput') is-invalid @enderror"  id="nama_pemuput" name="nama_pemuput" placeholder="Masukkan Nama Pemuput Perkawinan" value="{{ old('nama_pemuput') }}" required>
                                                @error('nama_pemuput')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nama Pemuput Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- BUKTI PERKAWINAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
                                                <input type="text" class="form-control @error ('nomor_bukti_serah_terima_perkawinan') is-invalid @enderror"  id="nomor_bukti_serah_terima_perkawinan" name="nomor_bukti_serah_terima_perkawinan" placeholder="Masukkan No. Bukti Serah Terima Perkawinan" value="{{ old('nomor_bukti_serah_terima_perkawinan') }}" required>
                                                @error('nomor_bukti_serah_terima_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        No. Bukti Serah Terima Perkawinan wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="lampiran" class="small">File Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_bukti_serah_terima_perkawinan') is-invalid @enderror" id="file_bukti_serah_terima_perkawinan" name="file_bukti_serah_terima_perkawinan" accept=".pdf,.jpg" required>
                                                    <label for="file_bukti_serah_terima_perkawinan_label" id="file_bukti_serah_terima_perkawinan_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_bukti_serah_terima_perkawinan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Bukti Serah Terima Perkawinan wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file_bukti_serah_terima_perkawinan" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Bukti Serah Terima Perkawinan maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AKTA PERKAWINAN --}}
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="title" class="small">No. Akta Perkawinan</label>
                                                <input type="text" class="form-control @error ('nomor_akta_perkawinan') is-invalid @enderror"  id="nomor_akta_perkawinan" name="nomor_akta_perkawinan" placeholder="Masukkan No. Akta Perkawinan" value="{{ old('nomor_akta_perkawinan') }}">
                                                @error('nomor_akta_perkawinan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="file_akta_perkawinan" class="small">File Akta Perkawinan</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_akta_perkawinan') is-invalid @enderror" id="file_akta_perkawinan" name="file_akta_perkawinan" accept=".pdf,.jpg">
                                                    <label for="file_akta_perkawinan_label" id="file_akta_perkawinan_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_akta_perkawinan')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file_akta_perkawinan" class="text-danger small text-end" style="display:none;">
                                                    Ukuran File Akta Perkawinan maksimal 2 MB.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small" for="keterangan">Keterangan</label>
                                        <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan"></textarea>
                                    </div>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-perkawinan-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perkawinan('0')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan</span>
                                        </button>

                                        {{-- <button class="btn btn-success btn-icon-split text-end" onclick="simpan_perkawinan('1')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan dan Sahkan</span>
                                        </button> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

     {{-- MODAL --}}
    <!-- Select Mempelai Mipil Purusa -->
    <div class="modal fade" id="select_purusa_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="pilih_cacah_title">Pilih Mempelai Purusa</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-purusa" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:5%">No.</th>
                                    <th>NIC Krama Mipil</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th style="width: 13%">Jenis Kelamin</th>
                                    <th style="width: 13%">Tempekan</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Mempelai Mipil Pradana -->
    <div class="modal fade" id="select_pradana_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="pilih_cacah_title">Pilih Mempelai Pradana</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-pradana" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:5%">No.</th>
                                    <th>NIC Krama Mipil</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th style="width: 13%">Jenis Kelamin</th>
                                    <th style="width: 13%">Tempekan</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-perkawinan').addClass('active');

            //VALIDASI LAMPIRAN
                $("#file_bukti_serah_terima_perkawinan").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_bukti_serah_terima_perkawinan').show();
                        $('#file_bukti_serah_terima_perkawinan').val("");
                    }else{
                        document.getElementById('file_bukti_serah_terima_perkawinan_label').innerHTML = document.getElementById('file_bukti_serah_terima_perkawinan').files[0].name;
                        $('#validasi-file_bukti_serah_terima_perkawinan').hide();
                    }
                });
                $("#file_akta_perkawinan").change(function() {
                    var filedata = this.files[0];
                    if(filedata.size > (2097152)){
                        $('#validasi-file_akta_perkawinan').show();
                        $('#file_akta_perkawinan').val("");
                    }else{
                        document.getElementById('file_akta_perkawinan_label').innerHTML = document.getElementById('file_akta_perkawinan').files[0].name;
                        $('#validasi-file_akta_perkawinan').hide();
                    }
                });
            //VALIDASI LAMPIRAN

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $('#kabupaten_pradana').on('change', function(){
                //EMPTY CHILD
                $('#kecamatan_pradana').empty();
                $('#desa_adat_pradana').empty();
                $('#banjar_adat_pradana').empty();
                $('#pradana').val('');
                $('#pradana_placeholder').val('');
                $('#pradana_placeholder').prop('false', true);
                $('#status_kekeluargaan').val('').trigger('change');
                $('#status_kekeluargaan').prop('disabled', true);
                $('#calon_kepala_keluarga_div').fadeOut();
                $('#calon_kepala_keluarga').prop('required', false);

                //SET CHILD PLACEHOLDER
                $('#kecamatan_pradana').append('<option value="">Pilih Kecamatan</option>');
                $('#desa_adat_pradana').append('<option value="">Pilih Desa Adat</option>');
                $('#banjar_adat_pradana').append('<option value="">Pilih Banjar Adat</option>');

                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            $('#kecamatan_pradana').empty();
                            $('#kecamatan_pradana').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => { 
                                $('#kecamatan_pradana').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });            
                        }
                    });
                }
            });

            $('#kecamatan_pradana').on('change', function(){
                //EMPTY CHILD
                $('#desa_adat_pradana').empty();
                $('#banjar_adat_pradana').empty();
                $('#pradana').val('');
                $('#pradana_placeholder').val('');
                $('#status_kekeluargaan').val('').trigger('change');
                $('#status_kekeluargaan').prop('disabled', true);
                $('#calon_kepala_keluarga_div').fadeOut();
                $('#calon_kepala_keluarga').prop('required', false);

                //SET CHILD PLACEHOLDER
                $('#desa_adat_pradana').append('<option value="">Pilih Desa Adat</option>');
                $('#banjar_adat_pradana').append('<option value="">Pilih Banjar Adat</option>');

                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat_pradana').empty();
                            $('#desa_adat_pradana').append('<option value="">Pilih Desa Adat</option>');
                            result.desa_adats.forEach(element => {
                                $('#desa_adat_pradana').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                                  
                        }
                    });
                }
            });

            $('#desa_adat_pradana').on('change', function(){
                //GET CURR BANJAR ADAT ID
                var banjar_adat_id = {{ Session::get('banjar_adat_id')}};

                //EMPTY CHILD
                $('#banjar_adat_pradana').empty();
                $('#pradana').val('');
                $('#pradana_placeholder').val('');
                $('#pradana_placeholder').prop('false', true);
                $('#status_kekeluargaan').val('').trigger('change');
                $('#status_kekeluargaan').prop('disabled', true);
                $('#calon_kepala_keluarga_div').fadeOut();
                $('#calon_kepala_keluarga').prop('required', false);

                //SET CHILD PLACEHOLDER
                $('#banjar_adat_pradana').append('<option value="">Pilih Banjar Adat</option>');

                if($(this).val() != ""){
                    var url = "{{ route('admin-banjar-adat-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#banjar_adat_pradana').empty();
                            $('#banjar_adat_pradana').append('<option value="" selected>Pilih Banjar Adat</option>');
                            result.banjar_adats.forEach(element => {
                                if(element['id'] != banjar_adat_id){
                                    $('#banjar_adat_pradana').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                                }
                            });                     
                        }
                    });
                }
            });

            $('#banjar_adat_pradana').on('change', function(){
                //EMPTY CHILD
                $('#pradana').val('');
                $('#pradana_placeholder').val('');
                $('#pradana_placeholder').prop('false', true);
                $('#status_kekeluargaan').val('').trigger('change');
                $('#status_kekeluargaan').prop('disabled', true);
                $('#calon_kepala_keluarga_div').fadeOut();
                $('#calon_kepala_keluarga').prop('required', false);
            });

            //Status Kekeluargaan On Change
            $('#status_kekeluargaan').on('change', function(){
                if($(this).val() == 'baru'){
                    get_calon_kepala_keluarga();
                    $('#calon_kepala_keluarga_div').fadeIn();
                    $('#calon_kepala_keluarga').prop('required', true);
                }else{
                    $('#calon_kepala_keluarga_div').fadeOut();
                    $('#calon_kepala_keluarga').prop('required', false);
                }
            });
        });

        //DATATABLE KRAMA MIPIL
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //Mempelai MIPIL PURUSA
                var table_purusa = $('#dataTable-purusa');
                var oTable_purusa = table_purusa.DataTable({
                    "autoWidth": false,
                    "lengthMenu": [
                    [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"] // change per page values here
                        ],

                    // set the initial value
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "language": {
                        "lengthMenu": " _MENU_ records"
                    },
                    "oLanguage": {
                        "sSearch": "Cari:",
                        "sZeroRecords": "Data tidak ditemukan",
                        "sSearchPlaceholder": "Cari Purusa...",
                        "infoEmpty": "Menampilkan 0 data",
                        "infoFiltered": "(dari _MAX_ data)",
                        "sLengthMenu": "Tampilkan _MENU_ data",
                    },
                    "language": {
                        "paginate": {
                            "previous": 'Sebelumnya',
                            "next": 'Berikutnya'
                        },
                        "info": "Menampilkan _START_ s/d _END_ dari _MAX_ data",
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-perkawinan-datatable-purusa') }}",
                        data : function(d){
                            d.pradana = $('#pradana').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_krama_mipil', class: "wrap" },
                        { data: 'penduduk.nik', class: "wrap" },
                        { data: 'penduduk.nama', class: "wrap", width:2000 },
                        { data: 'penduduk.jenis_kelamin', class: "wrap" },
                        { data: 'tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.penduduk.gelar_depan){
                                    nama = nama + row.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                if(data == 'laki-laki'){
                                    return 'Laki-laki';
                                }else{
                                    return 'Perempuan';
                                }
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
                            }
                        },
                        {
                            'orderable': true,
                            'targets': [0]
                        }, {
                            "searchable": true,
                            "targets": [0]
                        }
                    ],
                    "order": [
                    [0, "asc"]
                    ] // set first column as a default sort by asc
                });

                purusa_filter = () => {
                    oTable_purusa.columns.adjust();
                    oTable_purusa.ajax.reload();
                }

                //Mempelai MIPIL PURUSA
                var table_pradana = $('#dataTable-pradana');
                var oTable_pradana = table_pradana.DataTable({
                    "autoWidth": false,
                    "lengthMenu": [
                    [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"] // change per page values here
                        ],

                    // set the initial value
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "language": {
                        "lengthMenu": " _MENU_ records"
                    },
                    "oLanguage": {
                        "sSearch": "Cari:",
                        "sZeroRecords": "Data tidak ditemukan",
                        "sSearchPlaceholder": "Cari Pradana...",
                        "infoEmpty": "Menampilkan 0 data",
                        "infoFiltered": "(dari _MAX_ data)",
                        "sLengthMenu": "Tampilkan _MENU_ data",
                    },
                    "language": {
                        "paginate": {
                            "previous": 'Sebelumnya',
                            "next": 'Berikutnya'
                        },
                        "info": "Menampilkan _START_ s/d _END_ dari _MAX_ data",
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-perkawinan-datatable-pradana') }}",
                        data : function(d){
                            d.banjar_adat_id = $('#banjar_adat_pradana').val();
                            d.purusa = $('#purusa').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_krama_mipil', class: "wrap" },
                        { data: 'penduduk.nik', class: "wrap" },
                        { data: 'penduduk.nama', class: "wrap", width:2000 },
                        { data: 'penduduk.jenis_kelamin', class: "wrap" },
                        { data: 'tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.penduduk.gelar_depan){
                                    nama = nama + row.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                if(data == 'laki-laki'){
                                    return 'Laki-laki';
                                }else{
                                    return 'Perempuan';
                                }
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
                            }
                        },
                        {
                            'orderable': true,
                            'targets': [0]
                        }, {
                            "searchable": true,
                            "targets": [0]
                        }
                    ],
                    "order": [
                    [0, "asc"]
                    ] // set first column as a default sort by asc
                });

                pradana_filter = () => {
                    oTable_pradana.columns.adjust();
                    oTable_pradana.ajax.reload();
                }
            }

            return {
                //main function to initiate the module
                init: function () {
                    handleTable();
                }

            };

        }();

        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
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

        //Pilih Purusa
        function pilih_purusa_modal(){
            $('#select_purusa_modal').on('show.bs.modal', function(e) {
                purusa_filter();
            }).modal('show');
        }

        function pilih_purusa(id, nama){
            $('#purusa').val(id);
            $('#purusa_placeholder').val(nama);
            $('#purusa_placeholder').prop('readonly', true);
            $('#select_purusa_modal').modal('hide');
            get_calon_kepala_keluarga();
            Toast.fire({
                icon: 'success',
                title: 'Purusa Berhasil Dipilih'
            })
        }
        //Pilih Purusa

        //Pilih Pradana
        function pilih_pradana_modal(){
            var banjar_adat_id = $('#banjar_adat_pradana').val();
            var purusa = $('#purusa').val();
            if(purusa){
                if(banjar_adat_id != ''){
                    $('#select_pradana_modal').on('show.bs.modal', function(e) {
                        pradana_filter();
                    }).modal('show');
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Banjar Adat Pradana Terlebih Dahulu'
                    });
                }
            }else if(banjar_adat_id){
                if(purusa != ''){
                    $('#select_pradana_modal').on('show.bs.modal', function(e) {
                        pradana_filter();
                    }).modal('show');
                }else{
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih Purusa Terlebih Dahulu'
                    });
                }
            }else{
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Purusa dan Banjar Adat Asal Terlebih Dahulu'
                });
            }
        }

        function pilih_pradana(id, nama){
            $('#pradana').val(id);
            $('#pradana_placeholder').val(nama);
            $('#pradana_placeholder').prop('readonly', true);
            $('#select_pradana_modal').modal('hide');
            $('#status_kekeluargaan').prop('disabled', false);
            get_calon_kepala_keluarga();
            Toast.fire({
                icon: 'success',
                title: 'Pradana Berhasil Dipilih'
            })
        }
        //Pilih Pradana

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

        //Fungsi Get Calon Kepala Keluarga
        function get_calon_kepala_keluarga(){
            var purusa = $('#purusa').val();
            var pradana = $('#pradana').val();
            var url = "{{ route('banjar-perkawinan-get-calon-kk', ['purusa_id'=>":purusa_id", 'pradana_id'=>":pradana_id"]) }}";
            url = url.replace(':purusa_id', purusa);
            url = url.replace(':pradana_id', pradana);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    $('#calon_kepala_keluarga').empty();
                    $('#calon_kepala_keluarga').append('<option value="">Pilih Kepala Keluarga</option>');
                    result['0'].forEach(element=>{
                        $('#calon_kepala_keluarga').append('<option value="'+element.id+'">'+element.penduduk.nama+'</option>');
                    })
                }
            });
        }
        //Fungsi Get Calon Kepala Keluarga

        //Fungsi Simpan Draft/Sah
        function simpan_perkawinan(status){
            var url = "{{ route('banjar-perkawinan-beda-banjar-adat-store', ":status") }}";
            url = url.replace(':status', status);
            $("#form-create-perkawinan").attr("action", url);
            $('#form-create-perkawinan').submit(function (e){
                e.stopPropagation();
            });
        }

    </script>
@endpush