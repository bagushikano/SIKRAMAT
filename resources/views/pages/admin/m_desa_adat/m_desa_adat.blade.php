@extends('layouts.admin.admin')
@section('title', 'Data Desa Adat')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                Data Desa Adat
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
            <!-- Illustration dashboard card example-->
            {{-- <div class="card card-waves mb-4 mt-5">
                <div class="card-body p-5">
                    <div class="row align-items-center justify-content-between">
                        <div class="col">
                            <h2 class="text-primary">Welcome back, your dashboard is ready!</h2>
                            <p class="text-gray-700">Great job, your affiliate dashboard is ready to go! You can view sales, generate links, prepare coupons, and download affiliate reports using this dashboard.</p>
                            <a class="btn btn-primary btn-sm px-3 py-2" href="#!">
                                Get Started
                                <i class="ml-1" data-feather="arrow-right"></i>
                            </a>
                        </div>
                        <div class="col d-none d-lg-block mt-xxl-n4"><img class="img-fluid px-xl-4 mt-xxl-n5" src="assets/img/freepik/statistics-pana.svg" /></div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="row mt-5">
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 1-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-primary mb-1">Earnings (monthly)</div>
                                    <div class="h5">$4,390</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        12%
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-dollar-sign fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 2-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-secondary mb-1">Average sale price</div>
                                    <div class="h5">$27.00</div>
                                    <div class="text-xs font-weight-bold text-danger d-inline-flex align-items-center">
                                        <i class="mr-1" data-feather="trending-down"></i>
                                        3%
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-tag fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 3-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-success mb-1">Clicks</div>
                                    <div class="h5">11,291</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        12%
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-mouse-pointer fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 4-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-info mb-1">Conversion rate</div>
                                    <div class="h5">1.23%</div>
                                    <div class="text-xs font-weight-bold text-danger d-inline-flex align-items-center">
                                        <i class="mr-1" data-feather="trending-down"></i>
                                        1%
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-percentage fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}


            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Master Data Desa Adat</div>
                <div class="card-body">
                    <a href="{{ route('admin-desa-adat-create') }}" type="button" class="btn btn-primary btn-icon-split mb-3">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Desa Adat</span>
                    </a>
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Desa Adat</th>
                                    <th>Nama Desa Adat</th>
                                    <th>Kecamatan</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Desa Adat</th>
                                    <th>Nama Desa Adat</th>
                                    <th>Kecamatan</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>DESDIN-001</td>
                                    <td>Tembuku</td>
                                    <td>Tembuku</td>
                                    <td>
                                        <button class="btn btn-datatable btn-icon btn-transparent-dark mr-2" data-toggle="modal" data-target="#edit_kecamatan"><i data-feather="eye"></i></button>
                                        <button class="btn btn-datatable btn-icon btn-transparent-dark"><i data-feather="trash-2"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>DESDIN2-002</td>
                                    <td>Tegalasah</td>
                                    <td>Tembuku</td>
                                    <td>
                                        <button class="btn btn-datatable btn-icon btn-transparent-dark mr-2"  data-toggle="modal" data-target="#edit_kecamatan"><i data-feather="eye"></i></button>
                                        <button class="btn btn-datatable btn-icon btn-transparent-dark"><i data-feather="trash-2"></i></button>
                                    </td>
                                </tr>
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