@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <style>
        .btn-custom { justify-content: flex-start !important;}
    </style>
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
@section('title', 'Edit Kematian')
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
                        <li class="breadcrumb-item"><a href="{{ route('banjar-kematian-home') }}" class="text-decoration-none text-dark">Manajemen Kematian</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Kematian</li>
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
                            <div class="wizard-step-icon"><i class="fas fa-edit text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Kematian</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Perubahan Data Kematian Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-xxl-10 col-xl-10 mt-4">
                            <form id="form-edit-kematian" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                <div class="form-group">
                                    <label class="small" for="title">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error ('cacah_krama_mipil_placeholder') is-invalid @enderror form-custom"  id="cacah_krama_mipil_placeholder" name="cacah_krama_mipil_placeholder" placeholder="Pilih Cacah Krama Mipil" value="{{ old('cacah_krama_mipil_placeholder', $kematian->cacah_krama_mipil->penduduk->nama) }}" required readonly>
                                        <div class="input-group-append">
                                            {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                            <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_cacah_krama_mipil_modal()">
                                                <span class="text">Pilih Cacah Krama</span>
                                                <span class="icon">
                                                    <i class="fas fa-user-plus"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control @error ('cacah_krama_mipil') is-invalid @enderror"  id="cacah_krama_mipil" name="cacah_krama_mipil"  value="{{ old('cacah_krama_mipil', $kematian->cacah_krama_mipil->id) }}" required hidden>
                                    @error('cacah_krama_mipil')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Cacah Krama Mipil wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="small" for="tanggal_kematian">Tanggal Kematian<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('tanggal_kematian') is-invalid @enderror" placeholder="Masukkan Tanggal Kematian" name="tanggal_kematian" id="tanggal_kematian" value="{{ old('tanggal_kematian', date('d M Y', strtotime($kematian->tanggal_kematian))) }}" required>
                                    @error('tanggal_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_suket_kematian">No. Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                            <input class="form-control @error('nomor_suket_kematian') is-invalid @enderror" id="nomor_suket_kematian" name="nomor_suket_kematian" type="text" value="{{ old('nomor_suket_kematian', $kematian->nomor_suket_kematian ?? '-') }}" placeholder="Masukkan Nomor Surat Keterangan Kematian" required>
                                            @error('nomor_suket_kematian')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Nomor Surat Keterangan Kematian wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        @if($kematian->file_suket_kematian == NULL)
                                            <div class="form-group">
                                                <label class="small" for="lampiran">File Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_suket_kematian') is-invalid @enderror" id="file_suket_kematian" name="file_suket_kematian" accept=".pdf,.jpg" required>
                                                    <label for="file_suket_kematian_label" id="file_suket_kematian_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_suket_kematian')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Surat Keterangan Kematian wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file-suket-kematian" class="text-danger small text-end" style="display:none;">
                                                    Ukuran file maksimal 2 MB.
                                                </div>   
                                            </div>
                                        @else
                                        <div class="form-group">
                                            <label class="small" for="lampiran">File Surat Keterangan Kematian</label>
                                            <br>
                                            <div class="row no-gutters">
                                                <div class="col-12 col-md-9">    
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input @error('file_suket_kematian') is-invalid @enderror" id="file_suket_kematian" name="file_suket_kematian"  accept=".pdf,.jpg">
                                                        <label for="file_suket_kematian_label" id="file_suket_kematian_label" class="custom-file-label">Pilih File</label>
                                                        <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                        @error('file_suket_kematian')
                                                            <div class="invalid-feedback text-start">
                                                                {{ $message }}
                                                            </div>
                                                        @else
                                                            <div class="invalid-feedback">
                                                                File Surat Keterangan Kematian wajib diisi
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div id="validasi-file-suket-kematian" class="text-danger small text-end" style="display:none;">
                                                        Ukuran file maksimal 2 MB.
                                                    </div>   
                                                </div>
                                                <div class="col-12 col-md-3 pl-1">
                                                    <a class="btn btn-primary btn-icon-split text-end" href="{{ $kematian->file_suket_kematian }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unduh File Surat Ketarangan Kematian">
                                                        <span class="icon text-white-50 pull-left">
                                                            <i class="fas fa-download"></i>
                                                        </span>
                                                        <span class="text">Unduh</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div> 
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_akta_kematian">No. Akta Kematian</label>
                                            <input class="form-control @error('nomor_akta_kematian') is-invalid @enderror" id="nomor_akta_kematian" name="nomor_akta_kematian" type="text" value="{{ old('nomor_akta_kematian', $kematian->nomor_akta_kematian ?? '-') }}" placeholder="Masukkan Nomor Akta Kematian">
                                            @error('nomor_akta_kematian')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Nomor Akta Kematian wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        @if($kematian->file_suket_kematian == NULL)
                                            <div class="form-group">
                                                <label class="small" for="lampiran">File Akta Kematian</label>
                                                <br>    
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('file_akta_kematian') is-invalid @enderror" id="file_akta_kematian" name="file_akta_kematian" accept=".pdf,.jpg">
                                                    <label for="file_akta_kematian_label" id="file_akta_kematian_label" class="custom-file-label">Pilih File</label>
                                                    <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                    @error('file_akta_kematian')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            File Akta Kematian wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div id="validasi-file-akta-kematian" class="text-danger small text-end" style="display:none;">
                                                    Ukuran file maksimal 2 MB.
                                                </div>
                                            </div>
                                        @else
                                        <div class="form-group">
                                                <label class="small" for="lampiran">File Akta Kematian</label>
                                                <br>    
                                                <div class="row no-gutters">
                                                    <div class="col-12 col-md-9">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input @error('file_akta_kematian') is-invalid @enderror" id="file_akta_kematian" name="file_akta_kematian" accept=".pdf,.jpg">
                                                            <label for="file_akta_kematian_label" id="file_akta_kematian_label" class="custom-file-label">Pilih File</label>
                                                            <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                            @error('file_akta_kematian')
                                                                <div class="invalid-feedback text-start">
                                                                    {{ $message }}
                                                                </div>
                                                            @else
                                                                <div class="invalid-feedback">
                                                                    File Akta Kematian wajib diisi
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div id="validasi-file-akta-kematian" class="text-danger small text-end" style="display:none;">
                                                            Ukuran file maksimal 2 MB.
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 pl-1">
                                                        <a class="btn btn-primary btn-icon-split text-end" href="{{ $kematian->file_akta_kematian }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unduh File Akta Kematian">
                                                            <span class="icon text-white-50 pull-left">
                                                                <i class="fas fa-download"></i>
                                                            </span>
                                                            <span class="text">Unduh</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div> 
                                        @endif
                                        
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="small" for="penyebab_kematian">Penyebab Kematian<span class="text-danger small">*</span></label>
                                    <textarea type="text" class="form-control @error ('penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="penyebab_kematian" id="penyebab_kematian" required>{{ $kematian->penyebab_kematian ?? '-' }}</textarea>
                                    @error('penyebab_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Penyebab Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="small" for="keterangan">Keterangan</label>
                                    <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan">{{ $kematian->keterangan }}</textarea>
                                </div>

                                <hr class="my-4" />
                                <div class="d-flex justify-content-between mb-2">
                                    <a class="btn btn-danger btn-icon-split text-end" href="{{ route('banjar-kematian-home') }}">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span class="text">Kembali</span>
                                    </a>
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_kematian({{ $kematian->id }}, '0')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan sebagai Draft</span>
                                        </button>

                                        <button class="btn btn-success btn-icon-split text-end" onclick="simpan_kematian({{ $kematian->id }}, '1')">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan dan Sahkan</span>
                                        </button>
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
    <!-- Select Cacah Krama Mipil Purusa -->
    <div class="modal fade" id="select_cacah_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Cacah Krama Mipil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-cacah-krama-mipil" cellspacing="0">
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
    <script>
        $(document).ready( function () {
            //DATEPICKER
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SELECT 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
            //SELECT 2

            //SIDE BAR CLASS
            $('#collapsePeristiwa').addClass('show');
            $('#nav-link-kematian').addClass('active');

            //VALIDASI LAMPIRAN
            $("#file_akta_kematian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file-akta-kematian').show();
                    $('#file_akta_kematian').val("");
                }else{
                    document.getElementById('file_akta_kematian_label').innerHTML = document.getElementById('file_akta_kematian').files[0].name;
                    $('#validasi-file-akta-kematian').hide();
                }
            });

            $("#file_suket_kematian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file-suket-kematian').show();
                    $('#file_suket_kematian').val("");
                }else{
                    document.getElementById('file_suket_kematian_label').innerHTML = document.getElementById('file_suket_kematian').files[0].name;
                    $('#validasi-file-suket-kematian').hide();
                }
            });
            //VALIDASI LAMPIRAN
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

        //DATATABLE KRAMA MIPIL
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //CACAH KRAMA MIPIL
                var table_cacah = $('#dataTable-cacah-krama-mipil');
                var oTable_cacah = table_cacah.DataTable({
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
                        url : "{{ route('banjar-kematian-datatable-cacah-krama-mipil') }}",
                        data : function(d){
                            d.tempekan_id = $('#tempekan_id').val();
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

                cacah_filter = () => {
                    oTable_cacah.columns.adjust();
                    oTable_cacah.ajax.reload();
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

        //Swal Init
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
        //Swal init

        //Fungsi Pilih Cacah
        function pilih_cacah_krama_mipil_modal(){
            $('#select_cacah_krama_mipil_modal').on('show.bs.modal', function(e) {
                cacah_filter();
            }).modal('show');
        }

        function pilih_cacah_krama_mipil(id, nama){
            $('#cacah_krama_mipil').val(id);
            $('#cacah_krama_mipil_placeholder').val(nama);
            $('#cacah_krama_mipil_placeholder').prop('readonly', true);
            $('#select_cacah_krama_mipil_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Cacah Krama Mipil Berhasil Dipilih'
            })
        }
        //Fungsi Pilih Cacah

        //Fungsi Simpan Draft/Sah
        function simpan_kematian(id, status){
            var url = "{{ route('banjar-kematian-update', [":id", ":status"]) }}";
            url = url.replace(':id', id);
            url = url.replace(':status', status);
            $("#form-edit-kematian").attr("action", url);
            $('#form-edit-kematian').submit(function (e){
                e.stopPropagation();
            });
        }
    </script>
@endpush