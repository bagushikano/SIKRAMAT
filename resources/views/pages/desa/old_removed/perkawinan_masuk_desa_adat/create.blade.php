@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
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
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Perkawinan Masuk Desa Desa Adat
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-perkawinan-masuk-desa-adat-home') }}" class="text-decoration-none">Perkawinan Masuk Desa Adat</a></li>
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
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perkawinan Masuk Desa Adat Berikut Ini</div>
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
                                        <label for="title">No. Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
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
                                    <div class="form-group">
                                        <label for="banjar_adat_purusa">Banjar Adat Purusa<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('banjar_adat_purusa') is-invalid @enderror" name="banjar_adat_purusa" id="banjar_adat_purusa"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                            <option value="">Pilih Banjar Adat</option>
                                            @foreach($banjar_adat as $banjar)
                                            <option value="{{ $banjar->id }}" @if(old('banjar_adat_purusa') == $banjar->id) selected @endif>{{ $banjar->nama_banjar_adat }}</option>
                                            @endforeach
                                        </select>
                                        @error('banjar_adat_purusa')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Banjar Adat Purusa wajib dipilih
                                            </div>
                                        @enderror 
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Pilih Cacah Krama Purusa<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('purusa_placeholder') is-invalid @enderror"  id="purusa_placeholder" name="purusa_placeholder" placeholder="Pilih Cacah Krama Purusa" value="{{ old('purusa_placeholder') }}" required>
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
                                        <input type="text" class="form-control @error ('purusa') is-invalid @enderror"  id="purusa" name="purusa"  value="{{ old('purusa') }}" required hidden>
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
                                                <label for="kabupaten_pradana">Kabupaten Asal Pradana<span class="text-danger small">*</span></label>
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
                                                <label for="kecamatan_pradana">Kecamatan Asal Pradana<span class="text-danger small">*</span></label>
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
                                                <label for="desa_adat_pradana">Desa Adat Asal Pradana<span class="text-danger small">*</span></label>
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
                                                <label for="banjar_adat_pradana">Banjar Adat Asal Pradana<span class="text-danger small">*</span></label>
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
                                        <label for="title">Pilih Cacah Krama Pradana<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('pradana_placeholder') is-invalid @enderror"  id="pradana_placeholder" name="pradana_placeholder" placeholder="Pilih Cacah Krama Pradana" value="{{ old('pradana_placeholder') }}" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_pradana_modal()">
                                                    <span class="text">Pilih Pradana</span>
                                                    <span class="icon">
                                                        <i class="fas fa-user-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control @error ('pradana') is-invalid @enderror"  id="pradana" name="pradana"  value="{{ old('pradana') }}" required hidden>
                                        @error('pradana')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Pradana wajib dipilih
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal_perkawinan">Tanggal Perkawinan<span class="text-danger small">*</span></label>
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
        
                                    <div class="form-group">
                                        <label for="lampiran">Bukti Serah Terima Perkawinan<span class="text-danger small">*</span></label>
                                        <br>    
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" accept=".pdf,.doc" required>
                                            <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                            @error('lampiran')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Bukti Serah Terima Perkawinan wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                        <div id="validasi-lampiran" class="text-danger small mt-2 text-end" style="display:none;">
                                            Ukuran Bukti Serah Terima Perkawinan maksimal 2 MB.
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    {{-- <button class="btn btn-light" type="button">Previous</button> --}}
                                    <a class="btn btn-danger mr-2" href="{{ route('desa-perkawinan-masuk-desa-adat-home') }}">Kembali</a><button class="btn btn-success" type="submit">Simpan</button>
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
            $('#nav-link-perkawinan-masuk-desa').addClass('active');

            //VALIDASI LAMPIRAN
            $("#lampiran").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-lampiran').show();
                    $('#lampiran').val("");
                }else{
                    document.getElementById('lampiran_label').innerHTML = document.getElementById('lampiran').files[0].name;
                    $('#validasi-lampiran').hide();
                }
            });
            //VALIDASI LAMPIRAN

            //PERKAWINAN MASUK DESA ADAT LISTENER
            $('#kabupaten_pradana').on('change', function(){
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
                            $('#desa_adat_pradana').empty();
                            $('#banjar_adat_pradana').empty(); 
                            $('#desa_adat_pradana').append('<option value="">Pilih Desa Adat</option>');
                            $('#banjar_adat_pradana').append('<option value="" selected>Pilih Banjar Adat</option>');
                            $('#pradana_masuk_desa_adat').prop("disabled", true);                   
                        }
                    });
                }
            });

            $('#kecamatan_pradana').on('change', function(){
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
                            $('#banjar_adat_pradana').empty(); 
                            $('#banjar_adat_pradana').append('<option value="" selected>Pilih Banjar Adat</option>');
                            $('#pradana_masuk_desa_adat').prop("disabled", true);                                      
                        }
                    });
                }
            });

            $('#desa_adat_pradana').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('desa-banjar-adat-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#banjar_adat_pradana').empty();
                            $('#banjar_adat_pradana').append('<option value="" selected>Pilih Banjar Adat</option>');
                            result.banjar_adats.forEach(element => {
                                $('#banjar_adat_pradana').append('<option value="' + element['id'] + '"' +'>' + element['nama_banjar_adat'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#banjar_adat_pradana').on('change', function(){
                if($(this).val() == ''){
                    $('#pradana_masuk_desa_adat').prop("disabled", true);
                }else if($(this).val() != ''){
                    $('#pradana_masuk_desa_adat').prop("disabled", false);
                }
            });
            //AKHIR PERKAWINAN MASUK DESA ADAT LISTENER
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
            var banjar_adat_id = $('#banjar_adat_purusa').val();
            if(banjar_adat_id != ''){
                $('#body_loading_select_purusa').show();
                $('#body_select_purusa').hide();
                $('#select_purusa_modal').modal('show');
                var url = "{{ route('desa-perkawinan-dalam-desa-adat-get-purusa', ":banjar_adat_id") }}";
                url = url.replace(':banjar_adat_id', banjar_adat_id);
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        console.log(result);
                        if ($.fn.DataTable.isDataTable("#dataTable-purusa")) {
                            $('#dataTable-purusa').DataTable().clear().destroy();
                        }
                        $('#body_select_purusa').html(result.hasil);        
                        $("#dataTable-purusa").DataTable({
                            "responsive": false, "lengthChange": true, "autoWidth": false,
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
                            },
                        });
                        $('#body_loading_select_purusa').hide();
                        $('#body_select_purusa').show();
                    }
                });
            }else if(banjar_adat_id == ''){
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Banjar Adat Purusa Terlebih Dahulu'
                })
            }
        }

        function pilih_purusa(id, nama){
            $('#purusa').val(id);
            $('#purusa_placeholder').val(nama);
            $('#purusa_placeholder').prop('readonly', true);
            $('#select_purusa_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Purusa Berhasil Dipilih'
            })
        }
        //Pilih Purusa

        //Pilih Pradana
        function pilih_pradana_modal(){
            var banjar_adat_id = $('#banjar_adat_pradana').val();
            if(banjar_adat_id != ''){
                $('#body_loading_select_pradana').show();
                $('#body_select_pradana').hide();
                $('#select_pradana_modal').modal('show');
                var url = "{{ route('desa-perkawinan-dalam-desa-adat-get-pradana', ":banjar_adat_id") }}";
                url = url.replace(':banjar_adat_id', banjar_adat_id);
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        console.log(result);
                        if ($.fn.DataTable.isDataTable("#dataTable-pradana")) {
                            $('#dataTable-pradana').DataTable().clear().destroy();
                        }
                        $('#body_select_pradana').html(result.hasil);        
                        $("#dataTable-pradana").DataTable({
                            "responsive": false, "lengthChange": true, "autoWidth": false,
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
                            },
                        });
                        $('#body_loading_select_pradana').hide();
                        $('#body_select_pradana').show();
                    }
                });
            }else if(banjar_adat_id == ''){
                Toast.fire({
                    icon: 'warning',
                    title: 'Pilih Banjar Adat Pradana Terlebih Dahulu'
                })
            }
        }

        function pilih_pradana(id, nama){
            $('#pradana').val(id);
            $('#pradana_placeholder').val(nama);
            $('#pradana_placeholder').prop('readonly', true);
            $('#select_pradana_modal').modal('hide');
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