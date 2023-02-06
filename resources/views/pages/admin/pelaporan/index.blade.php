@extends('layouts.admin.admin')

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

@section('title', 'Laporan Data Kependudukan')

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
                                Laporan Data Kependudukan
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Kependudukan</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="card my-1">
                <div class="card-header p-2 d-flex justify-content-center justify-content-lg-start justify-content-sm-start">
                    <ul class="nav nav-pills small">
                        <li class="nav-item"><a class="nav-link active" id="tabKrama" href="#krama" data-toggle="tab">Laporan Data Krama</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabCacah" href="#cacah" data-toggle="tab">Laporan Data Cacah Krama</a></li>
                        <li class="nav-item"><a class="nav-link" id="tabMutasi" href="#mutasi" data-toggle="tab">Laporan Data Mutasi Kependudukan</a></li>
                    </ul>
                </div>
                <div class="card-body py-auto">
                    <div class="tab-content">
                        <div class="tab-pane active" id="krama">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_krama" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kabupaten" class="small">Kabupaten/Kota<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten" style="width: 100%" required>
                                                <option value="">Pilih Kabupaten/Kota</option>
                                                @foreach($data['kabupaten'] as $kabupaten)
                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kabupaten')
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
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kecamatan" class="small">Kecamatan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan" style="width: 100%" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                            @error('kecamatan')
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

                                <div class="form-group">
                                    <label for="desa_adat" class="small">Desa Adat<span class="small text-danger">*</span></label>
                                    <select class="select2 custom-select select-desa-adat @error ('desa_adat') is-invalid @enderror" name="desa_adat[]" id="desa_adat" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Desa Adat" required>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua_desa_adat" disabled>
                                        <label class="form-check-label small" for="pilih_semua_desa_adat">
                                            Pilih semua Desa Adat
                                        </label>
                                    </div>
                                    @error('desa_adat')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Desa Adat wajib dipilih
                                        </div>
                                    @enderror
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_krama()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_krama" aria-expanded="false" onclick="download_pdf_krama()">
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
                        <div class="tab-pane" id="cacah">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_cacah_krama" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kabupaten_cacah" class="small">Kabupaten/Kota<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kabupaten_cacah') is-invalid @enderror" name="kabupaten_cacah" id="kabupaten_cacah" style="width: 100%" required>
                                                <option value="">Pilih Kabupaten/Kota</option>
                                                @foreach($data['kabupaten'] as $kabupaten)
                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kabupaten_cacah')
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
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kecamatan_cacah" class="small">Kecamatan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kecamatan_cacah') is-invalid @enderror" name="kecamatan_cacah" id="kecamatan_cacah" style="width: 100%" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                            @error('kecamatan_cacah')
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

                                <div class="form-group">
                                    <label for="desa_adat_cacah" class="small">Desa Adat<span class="small text-danger">*</span></label>
                                    <select class="select2 custom-select select-desa-adat @error ('desa_adat_cacah') is-invalid @enderror" name="desa_adat_cacah[]" id="desa_adat_cacah" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Desa Adat" required>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua_desa_adat_cacah" disabled>
                                        <label class="form-check-label small" for="pilih_semua_desa_adat_cacah">
                                            Pilih semua Desa Adat
                                        </label>
                                    </div>
                                    @error('desa_adat_cacah')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Desa Adat wajib dipilih
                                        </div>
                                    @enderror
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_cacah_krama()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_cacah" aria-expanded="false" onclick="download_pdf_cacah_krama()">
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
                        <div class="tab-pane" id="mutasi">
                            <form action="" method="POST" class="form-horizontal needs-validation my-0" id="form_laporan_mutasi" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_mutasi_awal" class="form-label small mb-1">Rentang Awal Tanggal Mutasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_mutasi_awal') is-invalid @enderror" name="tgl_mutasi_awal" id="tgl_mutasi_awal" placeholder="Pilih rentang awal tanggal mutasi kependudukan" readonly>
                                            @error('tgl_mutasi_awal')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_mutasi_akhir" class="form-label small mb-1">Rentang Akhir Tanggal Mutasi</label>
                                            <input type="text" class="datepicker-here bg-white form-control @error ('tgl_mutasi_akhir') is-invalid @enderror" name="tgl_mutasi_akhir" id="tgl_mutasi_akhir" placeholder="Pilih rentang akhir tanggal mutasi kependudukan" readonly>
                                            @error('tgl_mutasi_akhir')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kabupaten_mutasi" class="small">Kabupaten/Kota<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kabupaten_mutasi') is-invalid @enderror" name="kabupaten_mutasi" id="kabupaten_mutasi" style="width: 100%" required>
                                                <option value="">Pilih Kabupaten/Kota</option>
                                                @foreach($data['kabupaten'] as $kabupaten)
                                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kabupaten_mutasi')
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
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="kecamatan_mutasi" class="small">Kecamatan<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error('kecamatan_mutasi') is-invalid @enderror" name="kecamatan_mutasi" id="kecamatan_mutasi" style="width: 100%" required>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                                            @error('kecamatan_mutasi')
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

                                <div class="form-group">
                                    <label for="desa_adat_mutasi" class="small">Desa Adat<span class="small text-danger">*</span></label>
                                    <select class="select2 custom-select select-desa-adat @error ('desa_adat_mutasi') is-invalid @enderror" name="desa_adat_mutasi[]" id="desa_adat_mutasi" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Desa Adat" required>
                                    </select>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilih_semua_desa_adat_mutasi" disabled>
                                        <label class="form-check-label small" for="pilih_semua_desa_adat_mutasi">
                                            Pilih semua Desa Adat
                                        </label>
                                    </div>
                                    @error('desa_adat_mutasi')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Desa Adat wajib dipilih
                                        </div>
                                    @enderror
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm btn-success btn-icon-split text-end p-0" onclick="filter_mutasi()">
                                            <span class="icon">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <span class="text">Tampilkan/Munculkan</span>
                                        </button>
                                        <div class="btn-group btn-sm dropup">
                                            <button class="btn btn-sm btn-primary btn-icon-split shadow-none p-0 pr-2" type="submit" id="download_data_mutasi" aria-expanded="false" onclick="download_pdf_mutasi()">
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
                    <a href="{{ route('admin-dashboard') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
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
        @if($tab == 'cacah')
        <script>
            $(document).ready(function(){
                $('#tabKrama').removeClass('active');
                $('#tabCacah').addClass('active');
                $('#krama').removeClass('active');
                $('#cacah').addClass('active');          
            });
        </script>
        @endif
        @if($tab == 'mutasi')
        <script>
            $(document).ready(function(){
                $('#tabKrama').removeClass('active');
                $('#tabMutasi').addClass('active');
                $('#krama').removeClass('active');
                $('#mutasi').addClass('active');          
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

    {{-- Script Umum --}}
    <script>
        $(document).ready( function () {
            $('#nav-link-laporan').addClass('active');      

            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
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
    </script>

    {{-- Script Krama --}}
    <script>
        $(document).ready( function () {
            $('#desa_adat').select2({
                placeholder: "Pilih Desa Adat",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            });

            $("#pilih_semua_desa_adat").click(function(){
                if($("#pilih_semua_desa_adat").is(':checked') ){
                    $("#desa_adat").find('option').prop("selected",true);
                    $("#desa_adat").trigger('change');
                } else {
                    $("#desa_adat").find('option').prop("selected",false);
                    $("#desa_adat").trigger('change');
                }
            });

            $('#kabupaten').on('change', function(){
                $('#desa_adat').empty();
                $('#pilih_semua_desa_adat').prop('disabled', true);
                $('#pilih_semua_desa_adat').prop('checked', false);
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan').empty();
                            $('#kecamatan').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan').on('change', function(){
                $('#pilih_semua_desa_adat').prop('disabled', true);
                $('#pilih_semua_desa_adat').prop('checked', false);
                if($(this).val() != ""){
                    $('#pilih_semua_desa_adat').prop('disabled', false);
                    $('#desa_adat').empty();
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat').empty();
                            result.desa_adats.forEach(element => {
                                $('#desa_adat').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                     
                        }
                    });
                }else{
                    $('#pilih_semua_desa_adat').prop('disabled', true);
                    $('#pilih_semua_desa_adat').prop('checked', false);
                }
            });
        });

        function filter_krama() {
            $('#form_laporan_krama').removeAttr('target');
            $('#form_laporan_krama').attr('action', "{{ route('admin-laporan-krama') }}");
            $("#form_laporan_krama").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_pdf_krama() {
            $('#form_laporan_krama').removeAttr('target');
            $('#form_laporan_krama').attr('action', "{{ route('admin-laporan-krama-download') }}");
            $("#form_laporan_krama").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>

    {{-- Script Cacah Krama --}}
    <script>
        $(document).ready( function () {
            $('#desa_adat_cacah').select2({
                placeholder: "Pilih Desa Adat",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            });

            $("#pilih_semua_desa_adat_cacah").click(function(){
                if($("#pilih_semua_desa_adat_cacah").is(':checked') ){
                    $("#desa_adat_cacah").find('option').prop("selected",true);
                    $("#desa_adat_cacah").trigger('change');
                } else {
                    $("#desa_adat_cacah").find('option').prop("selected",false);
                    $("#desa_adat_cacah").trigger('change');
                }
            });

            $('#kabupaten_cacah').on('change', function(){
                $('#desa_adat_cacah').empty();
                $('#pilih_semua_desa_adat_cacah').prop('disabled', true);
                $('#pilih_semua_desa_adat_cacah').prop('checked', false);
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_cacah').empty();
                            $('#kecamatan_cacah').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_cacah').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_cacah').on('change', function(){
                $('#pilih_semua_desa_adat_cacah').prop('disabled', true);
                $('#pilih_semua_desa_adat_cacah').prop('checked', false);
                if($(this).val() != ""){
                    $('#pilih_semua_desa_adat_cacah').prop('disabled', false);
                    $('#desa_adat_cacah').empty();
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat_cacah').empty();
                            result.desa_adats.forEach(element => {
                                $('#desa_adat_cacah').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                     
                        }
                    });
                }else{
                    $('#pilih_semua_desa_adat_cacah').prop('disabled', true);
                    $('#pilih_semua_desa_adat_cacah').prop('checked', false);
                }
            });
        });

        function filter_cacah_krama() {
            $('#form_laporan_cacah_krama').removeAttr('target');
            $('#form_laporan_cacah_krama').attr('action', "{{ route('admin-laporan-cacah-krama') }}");
            $("#form_laporan_cacah_krama").submit(function(e) {
                e.stopPropagation();
            });
        }

        function download_pdf_cacah_krama() {
            $('#form_laporan_cacah_krama').removeAttr('target');
            $('#form_laporan_cacah_krama').attr('action', "{{ route('admin-laporan-cacah-krama-download') }}");
            $("#form_laporan_cacah_krama").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>
   
    {{-- Script Mutasi --}}
    <script>
        $(document).ready( function () {
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            
            $('#desa_adat_mutasi').select2({
                placeholder: "Pilih Desa Adat",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            });
    
            $("#pilih_semua_desa_adat_mutasi").click(function(){
                if($("#pilih_semua_desa_adat_mutasi").is(':checked') ){
                    $("#desa_adat_mutasi").find('option').prop("selected",true);
                    $("#desa_adat_mutasi").trigger('change');
                } else {
                    $("#desa_adat_mutasi").find('option').prop("selected",false);
                    $("#desa_adat_mutasi").trigger('change');
                }
            });
    
            $('#kabupaten_mutasi').on('change', function(){
                $('#desa_adat_mutasi').empty();
                $('#pilih_semua_desa_adat_mutasi').prop('disabled', true);
                $('#pilih_semua_desa_adat_mutasi').prop('checked', false);
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_mutasi').empty();
                            $('#kecamatan_mutasi').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_mutasi').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });
    
            $('#kecamatan_mutasi').on('change', function(){
                $('#pilih_semua_desa_adat_mutasi').prop('disabled', true);
                $('#pilih_semua_desa_adat_mutasi').prop('checked', false);
                if($(this).val() != ""){
                    $('#pilih_semua_desa_adat_mutasi').prop('disabled', false);
                    $('#desa_adat_mutasi').empty();
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat_mutasi').empty();
                            result.desa_adats.forEach(element => {
                                $('#desa_adat_mutasi').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                     
                        }
                    });
                }else{
                    $('#pilih_semua_desa_adat_mutasi').prop('disabled', true);
                    $('#pilih_semua_desa_adat_mutasi').prop('checked', false);
                }
            });
        });
    
        function filter_mutasi() {
            $('#form_laporan_mutasi').removeAttr('target');
            $('#form_laporan_mutasi').attr('action', "{{ route('admin-laporan-mutasi') }}");
            $("#form_laporan_mutasi").submit(function(e) {
                e.stopPropagation();
            });
        }
    
        function download_pdf_mutasi() {
            $('#form_laporan_mutasi').removeAttr('target');
            $('#form_laporan_mutasi').attr('action', "{{ route('admin-laporan-mutasi-download') }}");
            $("#form_laporan_mutasi").submit(function(e) {
                e.stopPropagation();
            });
        }
    </script>
@endpush