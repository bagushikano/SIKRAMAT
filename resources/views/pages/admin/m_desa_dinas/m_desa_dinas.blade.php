@extends('layouts.admin.admin')
@section('title', 'Data Desa Dinas')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                Data Desa Dinas
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
                <div class="card-header">Master Data Desa Dinas</div>
                <div class="card-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Desa Dinas</th>
                                    <th>Nama Desa Dinas</th>
                                    <th>Nama Kecamatan</th>
                                    <th style="width: 5%">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Desa Dinas</th>
                                    <th>Nama Desa Dinas</th>
                                    <th>Nama Kecamatan</th>
                                    <th style="width: 5%">Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($desas as $desa)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $desa->id }}</td>
                                        <td>{{ $desa->name }}</td>
                                        <td>{{ $desa->kecamatan->name }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark mr-2" data-toggle="modal" data-target="#edit_kecamatan"><i data-feather="eye"></i></button>
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
    <!-- Tambah Kabupaten Modal -->
    <div class="modal fade" id="create_kecamatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-desa-dinas" method="post" action="{{route('admin-kecamatan-home')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Desa Dinas</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="kabupaten_id" id="kabupaten_id"  style="width: 100%" required aria-placeholder="Pilih kabupaten" required>
                                <option value="">Pilih kabupaten</option>
                                <option value="1">Bangli</option>
                                <option value="2">Karangasem</option>
                                <option value="3">Badung</option>
                                <option value="4">Buleleng</option>
                                <option value="5">Tabanan</option>
                            </select>
                            @error('kabupaten_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kabupaten wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kecamatan_id" class="small">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="kecamatan_id" id="kecamatan_id" style="width: 100%" required aria-placeholder="Pilih kecamatan" required>
                                <option value="">Pilih kecamatan</option>
                                <option value="1">Tembuku</option>
                                <option value="2">Susut</option>
                                <option value="3">Kintamani</option>
                            </select>
                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                            @error('kecamatan_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kecamatan wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kode_desa_dinas" class="small">Kode Desa Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control" id="kode_desa_dinas" name="kode_desa_dinas" type="text" placeholder="Masukkan kode desa dinas" required>
                            @error('kode_desa_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode desa dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_desa_dinas" class="small">Nama Desa Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control" id="nama_desa_dinas" name="nama_desa_dinas" type="text" placeholder="Masukkan nama desa dinas" required>
                            @error('nama_desa_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama desa dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Kabupaten Modal -->
    <div class="modal fade" id="edit_kecamatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-kabupaten" method="post" action="{{route('admin-kabupaten-home')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Desa Dinas</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="edit_kabupaten_id" id="edit_kabupaten_id" style="width: 100%" required>
                                <option value="">Pilih kabupaten</option>
                                <option value="1" selected>Bangli</option>
                                <option value="2">Karangasem</option>
                                <option value="3">Badung</option>
                                <option value="4">Buleleng</option>
                                <option value="5">Tabanan</option>
                            </select>
                            @error('edit_kabupaten_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kabupaten wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kecamatan_id" class="small">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="form-control" name="edit_kecamatan_id" id="edit_kecamatan_id" style="width: 100%" required>
                                <option value="">Pilih kecamatan</option>
                                <option value="1" selected>Tembuku</option>
                                <option value="2">Susut</option>
                                <option value="3">Kintamani</option>
                            </select>
                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                            @error('edit_kecamatan_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kecamatan wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_kode_desa_dinas" class="small">Kode Desa Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_kode_desa_dinas" name="edit_kode_desa_dinas" type="text" value="DESDIN-001" placeholder="Masukkan kode desa dinas" required>
                            @error('edit_kode_desa_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode desa dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_desa_dinas" class="small">Nama Desa Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_nama_desa_dinas" name="edit_nama_desa_dinas" type="text" value="Tembuku" placeholder="Masukkan nama desa dinas" required>
                            @error('edit_nama_desa_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama desa dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL --}}
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari desa dinas...",
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

        //Select2
        $("#kabupaten_id").select2({
            placeholder: "Pilih kabupaten",
            language: {
                noResults: function (params) {
                return "Kabupaten tidak ditemukan";
                }
            }
        });
        $("#edit_kabupaten_id").select2({
            placeholder: "Pilih kabupaten",
            language: {
                noResults: function (params) {
                return "Kabupaten tidak ditemukan";
                }
            }
        });
        $("#kecamatan_id").select2({
            placeholder: "Pilih kecamatan",
            language: {
                noResults: function (params) {
                return "Kecamatan tidak ditemukan";
                }
            }
        });
        $("#edit_kecamatan_id").select2({
            placeholder: "Pilih kecamatan",
            language: {
                noResults: function (params) {
                return "Kecamatan tidak ditemukan";
                }
            }
        });
        //Select2
    </script>
@endpush