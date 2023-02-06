@extends('layouts.admin.admin')
@section('title', 'Data Kabupaten')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                Data Kabupaten
                            </h1>
                            <div class="page-header-subtitle ml-1">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</div>
                        </div>
                    </div>
                    <div class="mr-3 ml-3 mt-3">
                        @if (session()->has('statusInput'))
                        <div class="row">
                            <div class="col-sm-12 alert alert-success alert-dismissible fade show" role="alert">
                                {{ session()->has('statusInput') }}
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        @if (count($errors)>0)
                        <div class="row">
                            <div class="col-sm-12 alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                @foreach ($errors->all() as $item)
                                    <li>{{$item}}</li>
                                @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n5">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Master Data Kabupaten</div>
                    <div class="card-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Kabupaten</th>
                                    <th>Nama Kabupaten</th>
                                    <th>Nama Provinsi</th>
                                    <th style="width: 5%">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Kabupaten</th>
                                    <th>Nama Kabupaten</th>
                                    <th>Nama Provinsi</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($kabupatens as $kabupaten)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kabupaten->id }}</td>
                                        <td>{{ $kabupaten->name }}</td>
                                        <td>{{ $kabupaten->provinsi->name }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark mr-2" onclick="show_kabupaten('{{ $kabupaten->id }}', '{{ $kabupaten->name }}', '{{ $kabupaten->provinsi->id }}', '{{ $kabupaten->provinsi->name }}')"><i data-feather="eye"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}

    <!-- Show Kabupaten Modal -->
    <div class="modal fade" id="show_kabupaten" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Kabupaten</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_kode_kabupaten" class="small">Kode Provinsi</label>
                        <input class="form-control" id="kode_provinsi" name="kode_provinsi" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_kabupaten" class="small">Nama Provinsi</label>
                        <input class="form-control" id="nama_provinsi" name="nama_provinsi" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_kode_kabupaten" class="small">Kode Kabupaten</label>
                        <input class="form-control" id="kode_kabupaten" name="kode_kabupaten" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_kabupaten" class="small">Nama Kabupaten</label>
                        <input class="form-control" id="nama_kabupaten" name="nama_kabupaten" type="text" readonly>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    {{-- MODAL --}}
@endsection

@push('js')
    <script>
        function show_kabupaten(kode_kabupaten, nama_kabupaten, kode_provinsi, nama_provinsi){
            $('#kode_kabupaten').val(kode_kabupaten);
            $('#nama_kabupaten').val(nama_kabupaten);
            $('#kode_provinsi').val(kode_provinsi);
            $('#nama_provinsi').val(nama_provinsi);
            $('#show_kabupaten').modal('show');
        }

        $(document).ready( function () {
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari kabupaten...",
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

            //SIDE BAR CLASS
            $('#sidebarMasterData').removeClass('collapsed');
            $('#collapseMasterData').addClass('show');
            $('#sidebarMasterKabupaten').addClass('active');
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

        //GET ALL DATA
    </script>
@endpush