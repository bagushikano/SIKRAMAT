@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Daftar Maperas')
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
                        <li class="breadcrumb-item active text-red-pastel">Maperas</li>
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
                        Data Maperas <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span>
                    </div>
                    <button class="btn btn-sm btn-primary float-right" type="button" onclick="filter_modal()"><i class="fas fa-filter mr-2"></i>Filter Maperas</button>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="create_maperas()">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Maperas</span>
                    </button>
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-maperas" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">No.</th>
                                    <th style="width: 11%;">No. Maperas</th>
                                    <th style="width: 14%;">Jenis Maperas</th>
                                    <th>Nama Anak</th>
                                    <th style="width: 16%;">Tanggal Maperas</th>
                                    <th style="width: 5%;">Status</th>
                                    <th style="width: 10%">Tindakan</th>
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

    {{-- MODAL --}}
    <!-- Select Jenis Perkawinan Modal -->
    <div class="modal fade" id="create_maperas_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Jenis Maperas</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jenis_maperas" class="small text-dark">Jenis Maperas<span class="text-danger small">*</span></label>
                        <select class="select2 custom-select @error ('jenis_maperas') is-invalid @enderror" name="jenis_maperas" id="jenis_maperas"  style="width: 100%" aria-placeholder="Pilih Jenis Maperas" required>
                            <option value="">Pilih Jenis Maperas</option>
                            <option value="satu_banjar_adat">Satu Banjar Adat</option>
                            <option value="beda_banjar_adat">Beda Banjar Adat</option>
                            <option value="campuran_masuk">Campuran Masuk</option>
                            <option value="campuran_keluar">Campuran Keluar</option>
                        </select>
                        @error('jenis_maperas')
                            <div class="invalid-feedback text-start">
                                {{ $message }}
                            </div>
                        @else
                            <div class="invalid-feedback">
                                Jenis Maperas wajib dipilih
                            </div>
                        @enderror 
                    </div>
                    <p class="my-auto mb-2 mt-1" id="keterangan" style="display:none;">
                        <i class="text-info fas fa-info-circle mr-1" style="font-size: 1.15rem"></i>
                        <span class="font-weight-bold text-dark">Keterangan: </span>
                    </p>
                    <p class="text-dark my-auto" id="satu_banjar_adat" style="display:none;">
                        <span class="text-primary">Maperas Satu Banjar Adat</span> merupakan maperas yang terjadi ketika orang tua lama dan orang tau baru berasal dari satu Desa Adat dan Banjar Adat yang sama.
                    </p>
                    <p class="text-dark my-auto" id="beda_banjar_adat" style="display:none;">
                        <span class="text-primary">Maperas Beda Banjar Adat</span> merupakan maperas yang terjadi ketika orang tua lama dan orang tua baru berasal dari Banjar Adat yang berbeda.
                    </p>
                    <p class="text-dark my-auto" id="campuran_masuk" style="display:none;">
                        <span class="text-primary">Maperas Campuran Masuk</span> merupakan maperas yang terjadi ketika calon anak bukan merupakan warga Desa Adat di Bali.
                    </p>
                    <p class="text-dark my-auto" id="campuran_keluar" style="display:none;">
                        <span class="text-primary">Maperas Campuran Keluar</span> merupakan maperas yang terjadi ketika seorang anak diangkat oleh orang yang bukan merupakan warga Desa Adat di Bali.
                    </p>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="button" id="btnNext">Selanjutnya</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Maperas</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status" class="small text-dark">Status Maperas</label>
                        <select class="select2 custom-select @error ('status') is-invalid @enderror" name="status" id="status" style="width: 100%" aria-placeholder="Pilih Status Kelahiran" required>
                            <option value="">Semua Status</option>
                            <option value="3">Sah</option>
                            <option value="0">Draft</option>
                            <option value="1">Terkonfirmasi</option>
                            <option value="2">Tidak Terkonfirmasi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status" class="small text-dark">Rentang Waktu</label>
                        <input type="text" class="form-control" name="rentang_waktu" id="rentang_waktu" placeholder="Pilih Rentang Waktu" />
                    </div>
                    <div class="form-group">
                        <label for="filter_jenis_maperas" class="small text-dark">Jenis Maperas</label>
                        <select class="select2 custom-select @error ('filter_jenis_maperas') is-invalid @enderror" name="filter_jenis_maperas[]" id="filter_jenis_maperas" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Maperas" required>
                            <option value="satu_banjar_adat">Satu Banjar Adat</option>
                            <option value="beda_banjar_adat">Beda Banjar Adat</option>
                            <option value="campuran_masuk">Campuran Masuk</option>
                            <option value="campuran_keluar">Campuran Keluar</option>
                        </select>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="pilih_semua_jenis_maperas" type="checkbox">
                            <label class="custom-control-label" for="pilih_semua_jenis_maperas">Pilih semua jenis maperas</label>
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

    <!-- Select Tindakan Modal -->
    <div class="modal fade" id="tindakan_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Tindakan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control @error ('id_maperas') is-invalid @enderror"  id="id_maperas" name="id_maperas"  value="" required hidden>
                    <span class="text-dark">Maperas dengan nomor <span id="nomor_perkawinan" class="text-primary"></span> tidak dikonfirmasi oleh pihak asal dengan alasan <span id="alasan_penolakan" class="text-dark font-weight-bold"></span>. Silahkan pilih tindakan selanjutnya.</span>
                    <div class="form-group mt-4">
                        <label for="tindakan">Tindakan<span class="text-danger small">*</span></label>
                        <select class="select2 custom-select @error ('tindakan') is-invalid @enderror" name="tindakan" id="tindakan"  style="width: 100%" aria-placeholder="Pilih Tindakan" required>
                            <option value="">Pilih Tindakan</option>
                            <option value="edit">Edit Data Maperas</option>
                            <option value="hapus">Hapus Data Maperas</option>
                        </select>
                        @error('tindakan')
                            <div class="invalid-feedback text-start">
                                {{ $message }}
                            </div>
                        @else
                            <div class="invalid-feedback">
                                Tindakan wajib dipilih
                            </div>
                        @enderror 
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="button" id="btnTindakan">Selanjutnya</button></div>
            </div>
        </div>
    </div>
    {{-- MODAL --}}

    {{-- HIDDEN FORM --}}
    <form id="form-delete-maperas" method="post" action="/">
        @method('delete')
        @csrf
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
    @if($message = Session::get('error'))
    <script>
        $(document).ready(function(){
            alertError('Error', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    <script>
        $(document).ready( function () {
            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-maperas').addClass('active');

           //DATE RANGE PICKER
           $('#rentang_waktu').daterangepicker({
                minDate: "01 Jan 2022",
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

            $("#filter_jenis_maperas").select2({
                placeholder: "Pilih Jenis Maperas",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //FILTER
            $("#pilih_semua_jenis_maperas").click(function(){
                if($("#pilih_semua_jenis_maperas").is(':checked') ){
                    $("#filter_jenis_maperas").find('option').prop("selected",true);
                    $("#filter_jenis_maperas").trigger('change');
                } else {
                    $("#filter_jenis_maperas").find('option').prop("selected",false);
                    $("#filter_jenis_maperas").trigger('change');
                }
            });

            //JENIS PERKAWINAN ON CHANGE
            $('#jenis_maperas').on('change', function(){
                if($(this).val() == ''){
                    $('#keterangan').hide();
                    $('#satu_banjar_adat').hide();
                    $('#beda_banjar_adat').hide();
                    $('#campuran_masuk').hide();
                }else{
                    $('#keterangan').show();
                    if($(this).val() == 'satu_banjar_adat'){
                        $('#beda_banjar_adat').hide();
                        $('#campuran_masuk').hide();
                        $('#campuran_keluar').hide();
                        $('#satu_banjar_adat').show();
                    }else if($(this).val() == 'beda_banjar_adat'){
                        $('#satu_banjar_adat').hide();
                        $('#campuran_masuk').hide();
                        $('#campuran_keluar').hide();
                        $('#beda_banjar_adat').show();
                    }else if($(this).val() == 'campuran_masuk'){
                        $('#satu_banjar_adat').hide();
                        $('#beda_banjar_adat').hide();
                        $('#campuran_keluar').hide();
                        $('#campuran_masuk').show();
                    }else if($(this).val() == 'campuran_keluar'){
                        $('#satu_banjar_adat').hide();
                        $('#beda_banjar_adat').hide();
                        $('#campuran_masuk').hide();
                        $('#campuran_keluar').show();
                    }
                }
            });

            //BTN NEXT ON CLICK
            $('#btnNext').on('click', function(){
                var jenis_maperas = $('#jenis_maperas').val();
                if(jenis_maperas){
                    var url = "{{ route('banjar-maperas-create', ":jenis") }}";
                    url = url.replace(':jenis', jenis_maperas);
                    window.location.href = url;
                }else{
                    $('#jenis_maperas').addClass('is-invalid');
                }
            });

            //BTN TINDAKAN ON CLICK
            $('#btnTindakan').on('click', function(){
                var tindakan = $('#tindakan').val();
                var id_maperas = $('#id_maperas').val();
                if(tindakan){
                    if(tindakan == 'edit'){
                        var url = "{{ route('banjar-maperas-edit', ":id") }}";
                        url = url.replace(':id', id_maperas);
                        window.location.href = url;
                    }else{
                        $('#tindakan_modal').modal('hide');
                        delete_maperas(id_maperas);
                    }
                }else{
                    $('#tindakan').addClass('is-invalid');
                }
            });

            $('#tindakan').on('change', function(){
                if($(this).val()){
                    $(this).removeClass('is-invalid');
                }
            })
            
        });

        //DATATABLE INIT
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                var table = $('#dataTable-maperas');
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
                        "sSearchPlaceholder": "Cari Data Maperas...",
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
                        url : "{{ route('banjar-maperas-datatable') }}",
                        data : function(d){
                            d.status = $('#status').val();
                            d.rentang_waktu = $('#rentang_waktu').val();
                            d.jenis_maperas = $('#filter_jenis_maperas').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_maperas', class: "wrap" },
                        { data: 'jenis', class: "wrap" },
                        { data: 'cacah_krama_mipil_baru.penduduk.nama', class: "wrap" },
                        { data: 'tanggal_maperas', class: "wrap" },
                        { data: 'status', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                if(row.jenis_maperas == 'campuran_keluar'){
                                    let nama = '';
                                    if(row.cacah_krama_mipil_lama.penduduk.gelar_depan){
                                        nama = nama + row.cacah_krama_mipil_lama.penduduk.gelar_depan + ' ' ; 
                                    }
                                    nama = nama + row.cacah_krama_mipil_lama.penduduk.nama;
                                    if(row.cacah_krama_mipil_lama.penduduk.gelar_belakang){
                                        nama = nama + ', ' + row.cacah_krama_mipil_lama.penduduk.gelar_belakang;
                                    }
                                    return nama;
                                }else{
                                    let nama = '';
                                    if(row.cacah_krama_mipil_baru.penduduk.gelar_depan){
                                        nama = nama + row.cacah_krama_mipil_baru.penduduk.gelar_depan + ' ' ; 
                                    }
                                    nama = nama + data;
                                    if(row.cacah_krama_mipil_baru.penduduk.gelar_belakang){
                                        nama = nama + ', ' + row.cacah_krama_mipil_baru.penduduk.gelar_belakang;
                                    }
                                    return nama;
                                }
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                return moment(data).format('DD MMM YYYY');
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

        //DATATABLE INIT
        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
        });

        //DELETE DRAFT MAPERAS
        function delete_maperas(id){
            Swal.fire({
                title: 'Hapus Draft Maperas',
                text: "Apakah anda yakin ingin menghapus draft maperas ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('banjar-maperas-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-maperas').attr("action", url);
                        $('#form-delete-maperas').submit();
                    }
                })
        }

        //CREATE MAPERAS
        function create_maperas(){
            $('#create_maperas_modal').modal('show');
        }

        //FILTER PERKAWINAN
        function filter_modal(){
            $('#filter_modal').modal('show');
        }

        //TINDAKAN PENOLAKAN
        function tindakan(id, nomor, alasan){
            $('#nomor_maperas').text(nomor);
            $('#alasan_penolakan').text(alasan);
            $('#id_maperas').val(id);
            $('#tindakan_modal').modal('show');     
        }

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
            $('#status').val('').trigger('change');
            $('#filter_jenis_maperas').val('').trigger('change');
        }
    </script>
@endpush