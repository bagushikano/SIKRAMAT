@extends('layouts.admin.admin')
@section('title', 'Data Kecamatan')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                Data Kecamatan
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
                <div class="card-header">Master Data Kecamatan</div>
                <div class="card-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Kecamatan</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Nama Kabupaten</th>
                                    <th style="width: 5%">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Kecamatan</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Nama Kabupaten</th>
                                    <th style="width: 5%">Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($kecamatans as $kecamatan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kecamatan->id }}</td>
                                        <td>{{ $kecamatan->name }}</td>
                                        <td>{{ $kecamatan->kabupaten->name }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark mr-2" onclick="show_kecamatan('{{ $kecamatan->id }}')"><i data-feather="eye"></i></button>
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
    <!-- Show Kecamatan Modal -->
    <div class="modal fade" id="show_kecamatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Kecamatan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="body_loading">
                    <div class="d-flex justify-content-center">
                      <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                      </div>
                    </div>
                  </div>
                <div class="modal-body" id="body_show">
                    <div class="form-group">
                        <label for="kode_kecamatan" class="small">Kode Provinsi</label>
                        <input class="form-control" id="kode_provinsi" name="kode_provinsi" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_kecamatan" class="small">Nama Provinsin</label>
                        <input class="form-control" id="nama_provinsi" name="nama_provinsi" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="kode_kecamatan" class="small">Kode Kabupaten</label>
                        <input class="form-control" id="kode_kabupaten" name="kode_kabupaten" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_kecamatan" class="small">Nama Kabupaten</label>
                        <input class="form-control" id="nama_kabupaten" name="nama_kabupaten" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="kode_kecamatan" class="small">Kode Kecamatan</label>
                        <input class="form-control" id="kode_kecamatan" name="kode_kecamatan" type="text" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_kecamatan" class="small">Nama Kecamatan</label>
                        <input class="form-control" id="nama_kecamatan" name="nama_kecamatan" type="text" readonly>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    {{-- MODAL --}}
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function show_kecamatan(id){
            $("#body_show").hide();
            $("#body_loading").show();
            $('#show_kecamatan').modal('show');
            jQuery.ajax({
            url: "/admin/master/kecamatan/"+id,
            method: 'get',
            success: function(result){
                    $("#kode_provinsi").val(result.kecamatan.kabupaten.provinsi['id']);
                    $("#nama_provinsi").val(result.kecamatan.kabupaten.provinsi['name']);
                    $("#kode_kabupaten").val(result.kecamatan.kabupaten['id']);
                    $("#nama_kabupaten").val(result.kecamatan.kabupaten['name']);
                    $("#kode_kecamatan").val(result.kecamatan['id']);
                    $("#nama_kecamatan").val(result.kecamatan['name']);
                    $("#body_loading").hide();
                    $("#body_show").show();                 
                }
            });
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

            // $('.select2').select2({});
            $(".select2").select2({
                placeholder: "Pilih kabupaten",
                language: {
                    noResults: function (params) {
                    return "Kabupaten tidak ditemukan";
                    }
                }
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
    </script>
@endpush