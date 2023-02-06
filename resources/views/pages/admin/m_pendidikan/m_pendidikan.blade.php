@extends('layouts.admin.admin')
@section('title', 'Data Jenjang Pendidikan')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                Data Jenjang Pendidikan
                            </h1>
                            <div class="page-header-subtitle ml-1">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</div>
                        </div>
                    </div>
                    <div class="mr-3 ml-3 mt-3">
                        @if (count($errors)>0)
                        <div class="row">
                            <div class="col-sm-12 alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $item)
                                    <li>{{$item}}</li>
                                @endforeach
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
                <div class="card-header">Master Data Jenjang Pendidikan</div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-icon-split mb-3" data-toggle="modal" data-target="#create_kabupaten">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Jenjang Pendidikan</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Jenjang Pendidikan</th>
                                    <th style="width: 8%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendidikans as $pendidikan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pendidikan->jenjang_pendidikan }}</td>
                                        <td class="text-center">
                                            <button button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="edit_pendidikan({{ $pendidikan->id }})"><i class="fas fa-eye"></i></button>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_pendidikan({{ $pendidikan->id }})"><i class="fas fa-trash"></i></button>
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
    <div class="modal fade" id="create_kabupaten" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-kabupaten" method="post" action="{{route('admin-pendidikan-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Jenjang Pendidikan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="jenjang_pendidikan" class="small">Jenjang Pendidikan<span class="text-danger small">*</span></label>
                            <input class="form-control" id="jenjang_pendidikan" name="jenjang_pendidikan" type="text" value="{{ old('jenjang_pendidikan') }}" placeholder="Masukkan jenjang pendidikan" required>
                            @error('jenjang_pendidikan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Jenjang pendidikan wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Pendidikan Modal -->
    <div class="modal fade" id="edit_pendidikan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-edit-pendidikan" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Jenjang Pendidikan</h5>
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
                            <label for="edit_jenjang_pendidikan" class="small">Jenjang Pendidikan<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_jenjang_pendidikan" name="jenjang_pendidikan" type="text" placeholder="Masukkan jenjang pendidikan" required>
                            @error('edit_jenjang_pendidikan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Jenjang pendidikan wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Pendidikan Modal -->
    <div class="modal fade" id="delete_pendidikan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Jenjang Pendidikan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus jenjang pendidikan ini?</p>
                    <form id="form-delete-pendidikan" method="post" action="/wkwk">
                        @method('delete')
                        @csrf
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL --}}
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
    <script>
        function edit_pendidikan(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_pendidikan').modal('show');
            jQuery.ajax({
            url: "/admin/master/jenjang-pendidikan/"+id,
            method: 'get',
            success: function(result){
                    $("#form-edit-pendidikan").attr("action", "/admin/master/jenjang-pendidikan/"+result.pendidikan['id']+"/update");
                    $("#edit_jenjang_pendidikan").val(result.pendidikan['jenjang_pendidikan']);
                    $("#body_loading").hide();
                    $("#body_edit").show();                 
                }
            });
        }

        function delete_pendidikan(id){
            $('#form-delete-pendidikan').attr("action", "/admin/master/jenjang-pendidikan/"+id+"/delete");
            $('#delete_pendidikan').modal('show');  
        }

        $(document).ready( function () {
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari jenjang pendidikan...",
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

            //ALERT
            
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