@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Daftar Perkawinan')
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
                        <li class="breadcrumb-item active text-red-pastel">Perkawinan</li>
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
                        Data Perkawinan <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span>
                    </div>
                    <button class="btn btn-sm btn-primary float-right" type="button" onclick="filter_modal()"><i class="fas fa-filter mr-2"></i>Filter Perkawinan</button>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="create_perkawinan()">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Perkawinan</span>
                    </button>
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-perkawinan" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">No.</th>
                                    <th style="width: 11%;">No. Perkawinan</th>
                                    <th style="width: 14%;">Jenis Perkawinan</th>
                                    <th>Nama Purusa</th>
                                    <th>Nama Pradana</th>
                                    <th style="width: 16%;">Tanggal Perkawinan</th>
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
    <div class="modal fade" id="create_perkawinan_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Jenis Perkawinan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jenis_perkawinan" class="small text-dark">Jenis Perkawinan<span class="text-danger small">*</span></label>
                        <select class="select2 custom-select @error ('jenis_perkawinan') is-invalid @enderror" name="jenis_perkawinan" id="jenis_perkawinan"  style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                            <option value="">Pilih Jenis Perkawinan</option>
                            <option value="satu_banjar_adat">Satu Banjar Adat</option>
                            <option value="beda_banjar_adat">Beda Banjar Adat</option>
                            <option value="campuran_masuk">Campuran Masuk</option>
                            <option value="campuran_keluar">Campuran Keluar</option>
                        </select>
                        @error('jenis_perkawinan')
                            <div class="invalid-feedback text-start">
                                {{ $message }}
                            </div>
                        @else
                            <div class="invalid-feedback">
                                Jenis Perkawinan wajib dipilih
                            </div>
                        @enderror 
                    </div>
                    <p class="my-auto mb-2 mt-1" id="keterangan" style="display:none;">
                        <i class="text-info fas fa-info-circle mr-1" style="font-size: 1.15rem"></i>
                        <span class="font-weight-bold text-dark">Keterangan: </span>
                    </p>
                    <p class="text-dark my-auto" id="satu_banjar_adat" style="display:none;">
                        <span class="text-primary">Perkawinan Satu Banjar Adat</span> merupakan perkawinan yang terjadi ketika Purusa dan Pradana berasal dari satu Desa Adat dan Banjar Adat yang sama.
                    </p>
                    <p class="text-dark my-auto" id="beda_banjar_adat" style="display:none;">
                        <span class="text-primary">Perkawinan Beda Banjar Adat</span> merupakan perkawinan yang terjadi ketika Purusa dan Pradana berasal dari Banjar Adat yang berbeda.
                    </p>
                    <p class="text-dark my-auto" id="campuran_masuk" style="display:none;">
                        <span class="text-primary">Perkawinan Campuran Masuk</span> merupakan perkawinan yang terjadi ketika Pradana bukan merupakan warga Desa Adat di Bali.
                    </p>
                    <p class="text-dark my-auto" id="campuran_keluar" style="display:none;">
                        <span class="text-primary">Perkawinan Campuran Keluar</span> merupakan perkawinan yang terjadi ketika Penduduk kawin keluar dengan orang yang bukan merupakan warga Desa Adat di Bali.
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
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Perkawinan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status" class="small text-dark">Status Perkawinan</label>
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
                        <label for="filter_jenis_perkawinan" class="small text-dark">Jenis Perkawinan</label>
                        <select class="select2 custom-select @error ('filter_jenis_perkawinan') is-invalid @enderror" name="filter_jenis_perkawinan[]" id="filter_jenis_perkawinan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                            <option value="satu_banjar_adat">Satu Banjar Adat</option>
                            <option value="beda_banjar_adat">Beda Banjar Adat</option>
                            <option value="campuran_masuk">Campuran Masuk</option>
                            <option value="campuran_keluar">Campuran Keluar</option>
                        </select>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="pilih_semua_jenis_perkawinan" type="checkbox">
                            <label class="custom-control-label" for="pilih_semua_jenis_perkawinan">Pilih semua jenis perkawinan</label>
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
                    <input type="text" class="form-control @error ('id_perkawinan') is-invalid @enderror"  id="id_perkawinan" name="id_perkawinan"  value="" required hidden>
                    <span class="text-dark">Perkawinan dengan nomor <span id="nomor_perkawinan" class="text-primary"></span> tidak dikonfirmasi oleh pihak Pradana dengan alasan <span id="alasan_penolakan" class="text-dark font-weight-bold"></span>. Silahkan pilih tindakan selanjutnya.</span>
                    <div class="form-group mt-4">
                        <label for="tindakan">Tindakan<span class="text-danger small">*</span></label>
                        <select class="select2 custom-select @error ('tindakan') is-invalid @enderror" name="tindakan" id="tindakan"  style="width: 100%" aria-placeholder="Pilih Tindakan" required>
                            <option value="">Pilih Tindakan</option>
                            <option value="edit">Edit Data Perkawinan</option>
                            <option value="hapus">Hapus Data Perkawinan</option>
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
    <form id="form-delete-perkawinan" method="post" action="/">
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
            $('#nav-link-perkawinan').addClass('active');

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

            $("#filter_jenis_perkawinan").select2({
                placeholder: "Pilih Jenis Perkawinan",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //FILTER
            $("#pilih_semua_jenis_perkawinan").click(function(){
                if($("#pilih_semua_jenis_perkawinan").is(':checked') ){
                    $("#filter_jenis_perkawinan").find('option').prop("selected",true);
                    $("#filter_jenis_perkawinan").trigger('change');
                } else {
                    $("#filter_jenis_perkawinan").find('option').prop("selected",false);
                    $("#filter_jenis_perkawinan").trigger('change');
                }
            });

            //JENIS PERKAWINAN ON CHANGE
            $('#jenis_perkawinan').on('change', function(){
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
                var jenis_perkawinan = $('#jenis_perkawinan').val();
                if(jenis_perkawinan){
                    var url = "{{ route('banjar-perkawinan-create', ":jenis") }}";
                    url = url.replace(':jenis', jenis_perkawinan);
                    window.location.href = url;
                }else{
                    $('#jenis_perkawinan').addClass('is-invalid');
                }
            });

            //BTN TINDAKAN ON CLICK
            $('#btnTindakan').on('click', function(){
                var tindakan = $('#tindakan').val();
                var id_perkawinan = $('#id_perkawinan').val();
                if(tindakan){
                    if(tindakan == 'edit'){
                        var url = "{{ route('banjar-perkawinan-edit', ":id") }}";
                        url = url.replace(':id', id_perkawinan);
                        window.location.href = url;
                    }else{
                        $('#tindakan_modal').modal('hide');
                        delete_perkawinan(id_perkawinan);
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
                var table = $('#dataTable-perkawinan');
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
                        "sSearchPlaceholder": "Cari Data Perkawinan...",
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
                        url : "{{ route('banjar-perkawinan-datatable') }}",
                        data : function(d){
                            d.status = $('#status').val();
                            d.rentang_waktu = $('#rentang_waktu').val();
                            d.jenis_perkawinan = $('#filter_jenis_perkawinan').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_perkawinan', class: "wrap" },
                        { data: 'jenis', class: "wrap" },
                        { data: 'purusa.penduduk.nama', class: "wrap" },
                        { data: 'pradana.penduduk.nama', class: "wrap" },
                        { data: 'tanggal_perkawinan', class: "wrap" },
                        { data: 'status', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                if(row.jenis_perkawinan == 'campuran_keluar'){
                                    return row.nama_pasangan;
                                }else{
                                    let nama = '';
                                    if(row.purusa.penduduk.gelar_depan){
                                        nama = nama + row.purusa.penduduk.gelar_depan; 
                                    }
                                    nama = nama + ' ' + data;
                                    if(row.purusa.penduduk.gelar_belakang){
                                        nama = nama + ', ' + row.purusa.penduduk.gelar_belakang;
                                    }
                                    return nama;
                                }
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.pradana.penduduk.gelar_depan){
                                    nama = nama + row.pradana.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.pradana.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.pradana.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 5,
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

        //DELETE DRAFT PERKAWINAN
        function delete_perkawinan(id){
            Swal.fire({
                title: 'Hapus Draft Perkawinan',
                text: "Apakah anda yakin ingin menghapus draft perkawinan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('banjar-perkawinan-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-perkawinan').attr("action", url);
                        $('#form-delete-perkawinan').submit();
                    }
                })
        }

        //CREATE PERKAWINAN
        function create_perkawinan(){
            $('#create_perkawinan_modal').modal('show');
        }

        //FILTER PERKAWINAN
        function filter_modal(){
            $('#filter_modal').modal('show');
        }

        //TINDAKAN PENOLAKAN
        function tindakan(id, nomor, alasan){
            $('#nomor_perkawinan').text(nomor);
            $('#alasan_penolakan').text(alasan);
            $('#id_perkawinan').val(id);
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
            $('#filter_jenis_perkawinan').val('').trigger('change');
        }
    </script>
@endpush