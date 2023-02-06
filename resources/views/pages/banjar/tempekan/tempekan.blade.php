@extends('layouts.banjar.banjar')
@section('title', 'Daftar Tempekan')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-university mr-2"></i></div>
                                Tempekan
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Tempekan</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Tempekan <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span></div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" onclick="tambah_tempekan()">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Tempekan</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-tempekan" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Tempekan</th>
                                    <th>Nama Tempekan</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tempekans as $tempekan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $tempekan->kode_tempekan }}</td>
                                        <td>{{ $tempekan->nama_tempekan }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_tempekan({{ $tempekan->id }})"><i class="fas fa-edit"></i></button>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Nonaktifkan" onclick="delete_tempekan({{ $tempekan->id }})"><i class="fas fa-trash"></i></button>
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
    {{-- Modal Tambah Tempekan --}}
    <div class="modal fade" id="create_tempekan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-tempekan" method="post" action="{{route('banjar-tempekan-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Tempekan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kode_tempekan" class="small">Kode Tempekan<span class="text-danger small">*</span></label>
                            <input class="form-control @error('kode_tempekan') is-invalid @enderror" id="kode_tempekan" name="kode_tempekan" type="text" value="{{ old('kode_tempekan') }}" required readonly>
                            @error('kode_tempekan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Tempekan wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_tempekan" class="small">Nama Tempekan<span class="text-danger small">*</span></label>
                            <input class="form-control @error('nama_tempekan') is-invalid @enderror" id="nama_tempekan" name="nama_tempekan" type="text" value="{{ old('nama_tempekan') }}" placeholder="Masukkan Tempekan" required>
                            @error('nama_tempekan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Tempekan wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Tempekan --}}
    <div class="modal fade" id="edit_tempekan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-edit-tempekan" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Tempekan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_edit">
                        <div class="form-group">
                            <label for="edit_kode_tempekan" class="small">Kode Tempekan<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_kode_tempekan') is-invalid @enderror" id="edit_kode_tempekan" name="edit_kode_tempekan" type="text" value="{{ old('edit_kode_tempekan') }}" required readonly>
                            @error('edit_kode_tempekan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Tempekan wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_tempekan" class="small">Nama Tempekan<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_nama_tempekan') is-invalid @enderror" id="edit_nama_tempekan" name="edit_nama_tempekan" type="text" value="{{ old('edit_nama_tempekan') }}" placeholder="Masukkan Tempekan" required>
                            @error('edit_nama_tempekan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Tempekan wajib diisi
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

    {{-- HIDDEN FORM --}}
    <form id="form-delete-tempekan" method="post" action="/">
        @method('delete')
        @csrf
    </form>
    {{-- HIDDEN FORM --}}
@endsection

@push('js')
    {{-- ALERT --}}
    @if($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('banjar_adat', 'nomor_krama_mipil', 'cacah_krama_mipil'))
            <script>
                $(document).ready(function(){
                    $('#create_krama_mipil').modal('show');
                });
            </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        $(document).ready( function () {
            $("#dataTable-tempekan").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari Tempekan...",
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
            $('#collapseMaster').addClass('show');
            $('#collapseMaster').addClass('active');
            $('#nav-link-tempekan').addClass('active');
        });

        function tambah_tempekan(){
            jQuery.ajax({
                url: "{{ route('banjar-tempekan-get-kode-tempekan') }}",
                method: 'get',
                success: function(result){
                    $("#kode_tempekan").val(result.last_kode);                 
                }
            });
            $('#create_tempekan').modal('show');
        }

        function edit_tempekan(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_tempekan').modal('show');
            var url = "{{ route('banjar-tempekan-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('banjar-tempekan-update', ":id") }}";
                url = url.replace(':id', result.tempekan.id);
                $("#form-edit-tempekan").attr("action", url);
                $('#edit_kode_tempekan').val(result.tempekan.kode_tempekan);
                $('#edit_nama_tempekan').val(result.tempekan.nama_tempekan);
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

        function delete_tempekan(id){
            Swal.fire({
                title: 'Hapus Tempekan',
                text: "Apakah anda yakin ingin menghapus Tempekan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('banjar-tempekan-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-tempekan').attr("action", url);
                        $('#form-delete-tempekan').submit();
                    }
                })
        }

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