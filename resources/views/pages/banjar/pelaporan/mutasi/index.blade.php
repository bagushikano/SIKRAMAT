@extends('layouts.banjar.banjar')

@push('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>
    
    <style>
        .select2-selection__choice{
            border-radius: 0.3rem !important;
            height: 1.8rem !important;
            text-align: justify !important;
        }
        .select2-selection__choice__remove{
            color: rgb(206, 81, 81) !important;
        }
        .select2-selection__choice{
            font-size: 1rem !important;
        }
        .select2-search__field{
            padding-bottom: 1.25rem !important;
        }
        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
            color: #fff !important;
            background-color: #0d6efd !important;
        }
    </style>
@endpush

@section('title', 'Laporan Data Mutasi Kependudukan')

@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fas fa-file mr-2"></i>
                                </div>
                                Laporan Data Mutasi Kependudukan
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Mutasi Kependudukan</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="card my-1">
                <div class="card-header p-2 d-flex justify-content-center justify-content-lg-start justify-content-sm-start">
                    <ul class="nav nav-pills small">
                        <li class="nav-item"><a class="nav-link active" id="tabKelahiran" href="#kelahiran" data-toggle="tab">Filter Data Kelahiran</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabKematian" href="#kematian" data-toggle="tab">Filter Data Kematian</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabPerkawinan" href="#perkawinan" data-toggle="tab">Filter Data Perkawinan</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabPerceraian" href="#perceraian" data-toggle="tab">Filter Data Perceraian</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabMaperas" href="#maperas" data-toggle="tab">Filter Data Maperas</a></li>
                    </ul>
                </div>
                <div class="card-body py-auto">
                    <div class="tab-content">
                        <div class="tab-pane active" id="kelahiran">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_kelahiran" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_awal" class="form-label small mb-1">Rentang Awal Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_awal') is-invalid @enderror" name="tgl_lahir_awal" id="tgl_lahir_awal" placeholder="Pilih rentang awal tanggal lahir" readonly required>
                                            @error('tgl_lahir_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_lahir_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Lahir</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_lahir_akhir') is-invalid @enderror" name="tgl_lahir_akhir" id="tgl_lahir_akhir" placeholder="Pilih rentang akhir tanggal lahir" readonly required>
                                            @error('tgl_lahir_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_kelahiran()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_kelahiran" aria-expanded="false" onclick="download_pdf_kelahiran()">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="kematian">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_kematian" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_kematian_awal" class="form-label small mb-1">Rentang Awal Tanggal Kematian</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_kematian_awal') is-invalid @enderror" name="tgl_kematian_awal" id="tgl_kematian_awal" placeholder="Pilih rentang awal tanggal kematian" readonly>
                                            @error('tgl_kematian_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_kematian_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Kematian</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_kematian_akhir') is-invalid @enderror" name="tgl_kematian_akhir" id="tgl_kematian_akhir" placeholder="Pilih rentang akhir tanggal kematian" readonly>
                                            @error('tgl_kematian_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_kematian()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_kematian" aria-expanded="false" onclick="download_pdf_kematian()">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="perkawinan">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_perkawinan" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_perkawinan_awal" class="form-label small mb-1">Rentang Awal Tanggal Perkawinan</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_perkawinan_awal') is-invalid @enderror" name="tgl_perkawinan_awal" id="tgl_perkawinan_awal" placeholder="Pilih rentang awal tanggal perkawinan" readonly required>
                                            @error('tgl_perkawinan_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_perkawinan_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Perkawinan</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_perkawinan_akhir') is-invalid @enderror" name="tgl_perkawinan_akhir" id="tgl_perkawinan_akhir" placeholder="Pilih rentang akhir tanggal perkawinan" readonly>
                                            @error('tgl_perkawinan_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_perkawinan" class="small">Jenis Perkawinan<span class="small text-danger">*</span></label>
                                    <select class="select2 custom-select @error ('jenis_perkawinan') is-invalid @enderror" name="jenis_perkawinan[]" id="jenis_perkawinan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                                        <option value="satu_banjar_adat">Satu Banjar Adat</option>
                                        <option value="beda_banjar_adat">Beda Banjar Adat</option>
                                        <option value="campuran_masuk">Campuran Masuk</option>
                                        <option value="campuran_keluar">Campuran Keluar</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua_jenis_perkawinan">
                                        <label class="form-check-label small" for="pilih_semua_jenis_perkawinan">
                                            Pilih semua jenis perkawinan
                                        </label>
                                    </div>
                                    @error('jenis_perkawinan[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Jenis perkawinan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_perkawinan()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_perkawinan" aria-expanded="false" onclick="download_pdf_perkawinan()">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="perceraian">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_perceraian" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_perceraian_awal" class="form-label small mb-1">Rentang Awal Tanggal Perceraian</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_perceraian_awal') is-invalid @enderror" name="tgl_perceraian_awal" id="tgl_perceraian_awal" placeholder="Pilih rentang awal tanggal perceraian" readonly>
                                            @error('tgl_perceraian_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_perceraian_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Perceraian</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_perceraian_akhir') is-invalid @enderror" name="tgl_perceraian_akhir" id="tgl_perceraian_akhir" placeholder="Pilih rentang akhir tanggal perceraian" readonly>
                                            @error('tgl_perceraian_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_perceraian()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_perceraian" aria-expanded="false" onclick="download_pdf_perceraian()">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="maperas">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_maperas" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_maperas_awal" class="form-label small mb-1">Rentang Awal Tanggal Maperas</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_maperas_awal') is-invalid @enderror" name="tgl_maperas_awal" id="tgl_maperas_awal" placeholder="Pilih rentang awal tanggal maperas" readonly>
                                            @error('tgl_maperas_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_maperas_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Maperas</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_maperas_akhir') is-invalid @enderror" name="tgl_maperas_akhir" id="tgl_maperas_akhir" placeholder="Pilih rentang akhir tanggal maperas" readonly>
                                            @error('tgl_maperas_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_maperas" class="small">Jenis Maperas<span class="small text-danger">*</span></label>
                                    <select class="select2 custom-select @error ('jenis_maperas') is-invalid @enderror" name="jenis_maperas[]" id="jenis_maperas" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Maperas" required>
                                        <option value="satu_banjar_adat">Satu Banjar Adat</option>
                                        <option value="beda_banjar_adat">Beda Banjar Adat</option>
                                        <option value="campuran_masuk">Campuran Masuk</option>
                                        <option value="campuran_keluar">Campuran Keluar</option>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua_jenis_maperas">
                                        <label class="form-check-label small" for="pilih_semua_jenis_maperas">
                                            Pilih semua jenis maperas
                                        </label>
                                    </div>
                                    @error('jenis_maperas[]')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Jenis maperas wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_maperas()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_maperas" aria-expanded="false" onclick="download_pdf_maperas()">
                                                <span class="icon">
                                                    <i class="fa-solid fa-download"></i>
                                                </span>
                                                <span class="text">Download Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('banjar-dashboard') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
                        <span class="icon">
                            <i class="fa-solid fa-circle-arrow-left"></i>
                        </span>
                        <span class="text">Kembali/Batal</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    @if($tab = Session::get('tab'))
        @if($tab == 'kematian')
        <script>
            $(document).ready(function(){
                $('#tabKelahiran').removeClass('active');
                $('#tabKematian').addClass('active');
                $('#kelahiran').removeClass('active');
                $('#kematian').addClass('active');          
            });
        </script>
        @endif
        @if($tab == 'perkawinan')
        <script>
            $(document).ready(function(){
                $('#tabKelahiran').removeClass('active');
                $('#tabPerkawinan').addClass('active');
                $('#kelahiran').removeClass('active');
                $('#perkawinan').addClass('active');              
            });
        </script>
        @endif
        @if($tab == 'perceraian')
        <script>
            $(document).ready(function(){
                $('#tabKelahiran').removeClass('active');
                $('#tabPerceraian').addClass('active');
                $('#kelahiran').removeClass('active');
                $('#perceraian').addClass('active');              
            });
        </script>
        @endif
        @if($tab == 'maperas')
        <script>
            $(document).ready(function(){
                $('#tabKelahiran').removeClass('active');
                $('#tabMaperas').addClass('active');
                $('#kelahiran').removeClass('active');
                $('#maperas').addClass('active');              
            });
        </script>
        @endif
    @endif
    @if($message = Session::get('success'))
        <script>
            $(document).ready(function(){
                alertSuccess('{{$message}}');
            });
        </script>
    @endif

    @if($message = Session::get('failed'))
        <script>
            $(document).ready(function(){
                alertError('{{$message}}');
            });
        </script>
    @endif

    <script>
        $(document).ready( function () {
            $('#collapseRekap').addClass('show');
            $('#nav-link-laporan-mutasi').addClass('active');      

            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            
            $('#jenis_perkawinan').select2({
                placeholder: "Pilih jenis perkawinan",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            });

            $('#jenis_maperas').select2({
                placeholder: "Pilih jenis maperas",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            });

            $("#pilih_semua_jenis_perkawinan").click(function(){
                if($("#pilih_semua_jenis_perkawinan").is(':checked') ){
                    $("#jenis_perkawinan").find('option').prop("selected",true);
                    $("#jenis_perkawinan").trigger('change');
                } else {
                    $("#jenis_perkawinan").find('option').prop("selected",false);
                    $("#jenis_perkawinan").trigger('change');
                }
            });

            $("#pilih_semua_jenis_maperas").click(function(){
                if($("#pilih_semua_jenis_maperas").is(':checked') ){
                    $("#jenis_maperas").find('option').prop("selected",true);
                    $("#jenis_maperas").trigger('change');
                } else {
                    $("#jenis_maperas").find('option').prop("selected",false);
                    $("#jenis_maperas").trigger('change');
                }
            });
        });
    </script>

    {{-- Script Kelahiran --}}
    <script>
        function filter_kelahiran() {
            $('#form_laporan_kelahiran').removeAttr('target');
            $('#form_laporan_kelahiran').attr('action', "{{ route('Laporan Kelahiran') }}");
            $("#form_laporan_kelahiran").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_pdf_kelahiran() {
            $('#form_laporan_kelahiran').removeAttr('target');
            $('#form_laporan_kelahiran').attr('action', "{{ route('Download PDF Laporan Kelahiran') }}");
            $("#form_laporan_kelahiran").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    {{-- Script Kematian --}}
    <script>
        function filter_kematian() {
            $('#form_laporan_kematian').removeAttr('target');
            $('#form_laporan_kematian').attr('action', "{{ route('Laporan Kematian') }}");
            $("#form_laporan_kematian").submit(function(e) {
                e.stopPropagation();
            });
        }
    
        function download_pdf_kematian() {
            $('#form_laporan_kematian').removeAttr('target');
            $('#form_laporan_kematian').attr('action', "{{ route('Download PDF Laporan Kematian') }}");
            $("#form_laporan_kematian").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    {{-- Script Perkawinan --}}
    <script>
        function filter_perkawinan() {
            $('#form_laporan_perkawinan').removeAttr('target');
            $('#form_laporan_perkawinan').attr('action', "{{ route('Laporan Perkawinan') }}");
            $("#form_laporan_perkawinan").submit(function(e) {
                e.stopPropagation();
            });
        }
    
        function download_pdf_perkawinan() {
            $('#form_laporan_perkawinan').removeAttr('target');
            $('#form_laporan_perkawinan').attr('action', "{{ route('Download PDF Laporan Perkawinan') }}");
            $("#form_laporan_perkawinan").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    {{-- Script Perceraian --}}
    <script>
        function filter_perceraian() {
            $('#form_laporan_perceraian').removeAttr('target');
            $('#form_laporan_perceraian').attr('action', "{{ route('Laporan Perceraian') }}");
            $("#form_laporan_perceraian").submit(function(e) {
                e.stopPropagation();
            });
        }
    
        function download_pdf_perceraian() {
            $('#form_laporan_perceraian').removeAttr('target');
            $('#form_laporan_perceraian').attr('action', "{{ route('Download PDF Laporan Perceraian') }}");
            $("#form_laporan_perceraian").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    {{-- Script Maperas --}}
    <script>
        function filter_maperas() {
            $('#form_laporan_maperas').removeAttr('target');
            $('#form_laporan_maperas').attr('action', "{{ route('Laporan Maperas') }}");
            $("#form_laporan_maperas").submit(function(e) {
                e.stopPropagation();
            });
        }
    
        function download_pdf_maperas() {
            $('#form_laporan_maperas').removeAttr('target');
            $('#form_laporan_maperas').attr('action', "{{ route('Download PDF Laporan Maperas') }}");
            $("#form_laporan_maperas").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>
@endpush