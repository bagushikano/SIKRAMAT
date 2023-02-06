@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Daftar Cacah Krama Mipil')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Cacah Krama Mipil
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Cacah Krama Mipil</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card card-header-actions mb-4">
                <div class="card-header">
                    <div>
                        Cacah Krama Mipil <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span>
                    </div>
                    <button class="btn btn-sm btn-primary float-right" type="button" onclick="filter_modal()"><i class="fas fa-filter mr-2"></i>Filter Cacah Krama</button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 col-md-10">
                            <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('banjar-cacah-krama-mipil-create') }}">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Cacah Krama Mipil</span>
                            </a>
                        </div>
                        {{-- <div class="col-12 col-md-2 d-flex justify-content-end small">
                            <select class="select2 custom-select @error ('tempekan_id') is-invalid @enderror" name="tempekan_id" id="tempekan_id" onchange="filter()" style="width: 100%">
                                <option value="">Semua Tempekan</option>
                                @foreach($tempekan as $tempek)
                                    <option value="{{ $tempek->id }}">{{ $tempek->nama_tempekan }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                    <div class="datatable">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-cacah-krama-mipil" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    {{-- <th>NCKM</th> --}}
                                    <th>NIC Krama Mipil</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tempekan</th>
                                    <th style="width: 14%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Cacah Krama Mipil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="status" class="small">Status Cacah Krama Mipil</label>
                                <select class="select2 custom-select @error ('status') is-invalid @enderror" name="status" id="status" style="width: 100%" aria-placeholder="Pilih Status Kelahiran" required>
                                    <option value="2">Semua Status</option>
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif/Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="status" class="small">Rentang Tanggal Lahir</label>
                                <input type="text" class="form-control" name="rentang_waktu" id="rentang_waktu" placeholder="Pilih Rentang Waktu" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="golongan_darah" class="small">Golongan Darah</label>
                                <select class="select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah[]" id="golongan_darah" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                                    <option value="-">-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="pilih_semua_golongan_darah" type="checkbox">
                                    <label class="custom-control-label" for="pilih_semua_golongan_darah">Pilih semua golongan darah</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin" class="small">Jenis Kelamin</label>
                                <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin" style="width: 100%" aria-placeholder="Pilih Status Kelahiran" required>
                                    <option value="">Semua Jenis Kelamin</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tempekan" class="small">Tempekan</label>
                        <select class="select2 custom-select @error ('tempekan') is-invalid @enderror" name="tempekan[]" id="tempekan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                            @foreach($tempekan as $tempek)
                                <option value="{{ $tempek->id }}">{{ $tempek->nama_tempekan }}</option>
                            @endforeach
                        </select>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="pilih_semua_tempekan" type="checkbox">
                            <label class="custom-control-label" for="pilih_semua_tempekan">Pilih semua tempekan</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pekerjaan" class="small">Pekerjaan</label>
                        <select class="select2 custom-select @error ('pekerjaan') is-invalid @enderror" name="pekerjaan[]" id="pekerjaan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Pekerjaan" required>
                            @foreach($pekerjaan as $kerja)
                                <option value="{{ $kerja->id }}">{{ $kerja->profesi }}</option>
                            @endforeach
                        </select>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="pilih_semua_pekerjaan" type="checkbox">
                            <label class="custom-control-label" for="pilih_semua_pekerjaan">Pilih semua pekerjaan</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pendidikan" class="small">Pendidikan Tertinggi</label>
                        <select class="select2 custom-select @error ('pendidikan') is-invalid @enderror" name="pendidikan[]" id="pendidikan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Pekerjaan" required>
                            @foreach($pendidikan as $didik)
                                <option value="{{ $didik->id }}">{{ $didik->jenjang_pendidikan }}</option>
                            @endforeach
                        </select>
                        <div class="custom-control custom-checkbox mb-n2">
                            <input class="custom-control-input" id="pilih_semua_pendidikan" type="checkbox">
                            <label class="custom-control-label" for="pilih_semua_pendidikan">Pilih semua pendidikan</label>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-icon-split mb-3 text-end" type="button" onclick="filter_reset()">
                        <span class="icon">
                            <i class="fas fa-sync"></i>
                        </span>
                        <span class="text">Reset</span>
                    </button>
                    <button class="btn btn-success btn-icon-split mb-3 text-end" onclick="filter_submit()">
                        <span class="icon">
                            <i class="fas fa-filter"></i>
                        </span>
                        <span class="text">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Nonaktifkan Cacah Krama Mipil -->
     <div class="modal fade" id="nonaktif_cacah_krama_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-delete-anggota" method="post" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Keluarkan Cacah Krama Mipil</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title" class="small">Tanggal Keluar<span class="text-danger small">*</span></label>
                            <input type="text" class="datepicker-here form-control @error ('tanggal_keluar') is-invalid @enderror" name="tanggal_keluar" id="tanggal_keluar" value="{{ old('tanggal_keluar') }}" placeholder="Masukkan Tanggal Keluar" required>
                            @error('tanggal_tanggal_keluarlahir')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Tanggal keluar wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alasan_keluar" class="small">Alasan Keluar<span class="text-danger small">*</span></label>
                            <textarea type="text" class="form-control @error ('alasan_keluar') is-invalid @enderror" placeholder="Masukkan Alasan Keluar" rows="3" name="alasan_keluar" id="alasan_keluar" required></textarea>
                            @error('alasan_keluar')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Alasan keluar wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="form-delete-krama" method="post" action="/">
        @csrf
        <input type="text" class="datepicker-here form-control" name="tanggal_keluar" id="tanggal_keluar" placeholder="Masukkan Tanggal Keluar" hidden>
    </form>
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
        function delete_krama(id){
            var url = "{{ route('banjar-cacah-krama-mipil-delete', ":id") }}";
            url = url.replace(':id', id);
            $('#form-delete-anggota').attr('action', url);
            $('#nonaktif_cacah_krama_modal').modal('show');
        }

        $(document).ready( function () {
            //SIDE BAR CLASS
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');

            //DATEPICKER
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            //DATE RANGE PICKER
            $('#rentang_waktu').daterangepicker({
                // minDate: "01 Jan 2022",
                locale: {
                    format: 'DD MMM YYYY',
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                }
            });

            $('#rentang_waktu').val('');

            //SELECT 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#golongan_darah").select2({
                placeholder: "Pilih Golongan Darah",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#tempekan").select2({
                placeholder: "Pilih Tempekan",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#pekerjaan").select2({
                placeholder: "Pilih Pekerjaan",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#pendidikan").select2({
                placeholder: "Pilih Pendidikan Tertinggi",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
        });

        var TableDatatablesEditable = function () {
            var handleTable = function () {
                var table = $('#dataTable-cacah-krama-mipil');
                var oTable = table.DataTable({
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
                        "sSearchPlaceholder": "Cari Cacah Krama...",
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
                        url : "{{ route('banjar-cacah-krama-mipil-datatable') }}",
                        data : function(d){
                            d.status = $('#status').val();
                            d.tempekan = $('#tempekan').val();
                            d.rentang_waktu = $('#rentang_waktu').val();
                            d.golongan_darah = $('#golongan_darah').val();
                            d.jenis_kelamin = $('#jenis_kelamin').val();
                            d.pekerjaan = $('#pekerjaan').val();
                            d.pendidikan = $('#pendidikan').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_krama_mipil', class: "wrap", orderable: false },
                        { data: 'penduduk.nik', class: "wrap", orderable: false },
                        { data: 'penduduk.nama', class: "wrap", orderable: false },
                        { data: 'penduduk.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'tempekan.nama_tempekan', class: "wrap", orderable: false },
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

                filter = () => {
                    oTable.ajax.reload();
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

        //Filter
        function filter_modal(){
            $('#filter_modal').modal('show');
        }

        function filter_submit(){
            filter();
            $('#filter_modal').modal('hide');
        }

        function filter_reset(){
            $('#rentang_waktu').val('');
            $('#status').val('1').trigger('change');
            $('#golongan_darah').val('').trigger('change');
            $('#jenis_kelamin').val('').trigger('change');
            $('#tempekan').val('').trigger('change');
            $('#pekerjaan').val('').trigger('change');
            $('#pendidikan').val('').trigger('change');

            //UNCHECK
            $("#pilih_semua_golongan_darah").prop('checked', false);
            $("#pilih_semua_tempekan").prop('checked', false);
            $("#pilih_semua_pekerjaan").prop('checked', false);
            $("#pilih_semua_pendidikan").prop('checked', false);
        }
    </script>

    {{-- PILIH SEMUA FILTER --}}
    <script>
        $("#pilih_semua_golongan_darah").click(function(){
            if($("#pilih_semua_golongan_darah").is(':checked') ){
                $("#golongan_darah").find('option').prop("selected",true);
                $("#golongan_darah").trigger('change');
            } else {
                $("#golongan_darah").find('option').prop("selected",false);
                $("#golongan_darah").trigger('change');
            }
        });

        $("#pilih_semua_tempekan").click(function(){
            if($("#pilih_semua_tempekan").is(':checked') ){
                $("#tempekan").find('option').prop("selected",true);
                $("#tempekan").trigger('change');
            } else {
                $("#tempekan").find('option').prop("selected",false);
                $("#tempekan").trigger('change');
            }
        });

        $("#pilih_semua_pekerjaan").click(function(){
            if($("#pilih_semua_pekerjaan").is(':checked') ){
                $("#pekerjaan").find('option').prop("selected",true);
                $("#pekerjaan").trigger('change');
            } else {
                $("#pekerjaan").find('option').prop("selected",false);
                $("#pekerjaan").trigger('change');
            }
        });

        $("#pilih_semua_pendidikan").click(function(){
            if($("#pilih_semua_pendidikan").is(':checked') ){
                $("#pendidikan").find('option').prop("selected",true);
                $("#pendidikan").trigger('change');
            } else {
                $("#pendidikan").find('option').prop("selected",false);
                $("#pendidikan").trigger('change');
            }
        });
    </script>
@endpush