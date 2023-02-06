@extends('layouts.admin.admin')
@section('title', 'Data Desa Adat')
@section('content')
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-fluid">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                                Tambah Desa Adat
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            <ol class="breadcrumb mb-0 mt-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin-desa-adat-home') }}">Master Data Desa Adat</a></li>
                                <li class="breadcrumb-item active text-red-pastel">Tambah Desa Adat</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Formulir Penambahan Desa Adat</div>
                <div class="card-body">
                    <div class="mx-5 mb-4">
                        <span class="text-primary font-weight-bold">Desa Adat</span>
                        <br>
                        <span class="text-bold">Input Formulir Penambahan Desa Adat</span>
                    </div>
                    <div class="row mx-5">
                        <div class="col-lg-6 col-sm-12">
                            <label for="title">Nama</label>
                            <input type="text" class="form-control @error ('nama') is-invalid @enderror"  id="nama" name="nama" placeholder="Masukkan nama" required>
                            @error('nama')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <label for="title">NIP</label>
                            <input type="text" class="form-control @error ('nip') is-invalid @enderror" name="nip" placeholder="Masukkan NIP" required>
                            @error('nip')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    NIP wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group mx-5 mt-5 float-right">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Tambah Kabupaten Modal -->
    <div class="modal fade" id="create_kecamatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-desa-adat" method="post" action="{{route('admin-kecamatan-home')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Desa Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kabupaten_id">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="form-control" name="kabupaten_id" id="kabupaten_id" required>
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
                            <label for="kecamatan_id">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="form-control" name="kecamatan_id" id="kecamatan_id" required>
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
                            <label for="exampleFormControlInput1">Kode Desa adat<span class="text-danger small">*</span></label>
                            <input class="form-control" id="kode_desa_adat" name="kode_desa_adat" type="text" placeholder="Masukkan kode desa adat" required>
                            @error('kode_desa_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode desa adat wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Nama Desa adat<span class="text-danger small">*</span></label>
                            <input class="form-control" id="nama_desa_adat" name="nama_desa_adat" type="text" placeholder="Masukkan nama desa adat" required>
                            @error('nama_desa_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama desa adat wajib diisi
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Kabupaten</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_kabupaten_id">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="form-control" name="edit_kabupaten_id" id="edit_kabupaten_id" required>
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
                            <label for="kecamatan_id">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="form-control" name="edit_kecamatan_id" id="edit_kecamatan_id" required>
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
                            <label for="edit_kode_desa_adat">Kode Desa adat<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_kode_desa_adat" name="edit_kode_desa_adat" type="text" value="DESDIN-001" placeholder="Masukkan kode desa adat" required>
                            @error('edit_kode_desa_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode desa adat wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_desa_adat">Nama Desa adat<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_nama_desa_adat" name="edit_nama_desa_adat" type="text" value="Tembuku" placeholder="Masukkan nama desa adat" required>
                            @error('edit_nama_desa_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama desa adat wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
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
                    "sSearchPlaceholder": "Cari desa adat...",
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
        $(document).ready(function() {
            $('.select2').select2();
        });
        //Select2
    </script>
@endpush