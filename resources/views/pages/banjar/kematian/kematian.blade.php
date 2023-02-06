@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Daftar Kematian')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-book-dead mr-2"></i></div>
                                Manajemen Kematian
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Manajemen Kematian</li>
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
                        Manajemen Kematian <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span>
                    </div>
                    <button class="btn btn-sm btn-primary float-right" type="button" onclick="filter_modal()"><i class="fas fa-filter mr-2"></i>Filter Kematian</button>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('banjar-kematian-create') }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Kematian</span>
                    </a>
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kematian" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>No. Kematian</th>
                                    <th>Nama</th>
                                    <th>Tanggal Kematian</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tempekan</th>
                                    <th>Status</th>
                                    <th style="width: 8%">Tindakan</th>
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
    <div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Kematian</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status" class="small" class="text-dark">Status Kematian</label>
                        <select class="select2 custom-select @error ('status') is-invalid @enderror" name="status" id="status" style="width: 100%" aria-placeholder="Pilih Status Kematian" required>
                            <option value="">Semua Status</option>
                            <option value="1">Kematian Sah</option>
                            <option value="0">Kematian Draft</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="small" class="text-dark">Rentang Waktu</label>
                        <input type="text" class="form-control" name="rentang_waktu" id="rentang_waktu" placeholder="Pilih Rentang Waktu" />
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button class="btn btn-danger btn-icon-split mb-3 text-end"  type="button" data-dismiss="modal">
                        <span class="icon">
                            <i class="fas fa-times"></i>
                        </span>
                        <span class="text">Batal</span>
                    </button> --}}
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
    {{-- MODAL --}}

    {{-- HIDDEN FORM --}}
    <form id="form-delete-kematian" method="post" action="/">
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
    {{-- END ALERT --}}
    <script>
        $(document).ready( function () {
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
            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-kematian').addClass('active');
        });

        //DATATABLE
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                var table = $('#dataTable-kematian');
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
                        "sSearchPlaceholder": "Cari Data Kematian...",
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
                        url : "{{ route('banjar-kematian-datatable') }}",
                        data : function(d){
                            d.status = $('#status').val();
                            d.rentang_waktu = $('#rentang_waktu').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_suket_kematian', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.penduduk.nama', class: "wrap", orderable: false },
                        { data: 'tanggal_kematian', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.penduduk.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap", orderable: false },
                        { data: 'status', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 2,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.cacah_krama_mipil.penduduk.gelar_depan){
                                    nama = nama + row.cacah_krama_mipil.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.cacah_krama_mipil.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.cacah_krama_mipil.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                return moment(data).format('DD MMM YYYY');
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

        //DATATABLE INIT
        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
        });

        //Delete Kematian
        function delete_kematian(id){
            Swal.fire({
                title: 'Hapus Draft Kematian',
                text: "Apakah anda yakin ingin menghapus draft kematian ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('banjar-kematian-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-kematian').attr("action", url);
                        $('#form-delete-kematian').submit();
                    }
                })
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
        }
    </script>
@endpush