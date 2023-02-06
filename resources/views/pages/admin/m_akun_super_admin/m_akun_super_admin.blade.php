@extends('layouts.admin.admin')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Akun Super Admin')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-1">
                            <h1 class="page-header-title">
                                Data Akun Super Admin
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
                <div class="card-header">Daftar Akun Super Admin</div>
                <div class="card-body">
                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('admin-super-admin-create') }}">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Akun</span>
                    </a>
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th style="width: 7%">Status</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->penduduk->nik }}</td>
                                        <td>{{ $user->penduduk->nama }}</td>
                                        <td>{{ $user->user->email }}</td>
                                        <td class="text-center">
                                            <label class="switch">
                                                <input id="signup-token_{{$user->id}}" name="_token" type="hidden" value="{{csrf_token()}}">
                                              @if($user->status == "aktif")
                                                <input type="checkbox" id="status_{{$user->id}}" onclick="statusBtn({{$user->id}})" checked>
                                              @else
                                                <input type="checkbox" id="status_{{$user->id}}" onclick="statusBtn({{$user->id}})">
                                              @endif
                                                <span class="slider round"></span>
                                              </label>
                                        </td>
                                        <td class="text-center">
                                            <a style="margin-right:7px" href=""><button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail admin"><i class="fas fa-eye"></i></button></a>
                                            <a style="margin-right:7px" href=""><button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus admin"><i class="fas fa-trash"></i></button></a>
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
    <!-- Delete Pekerjaan Modal -->
    <div class="modal fade" id="delete_pekerjaan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Pekerjaan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus pekerjaan ini?</p>
                    <form id="form-delete-pekerjaan" method="post" action="/wkwk">
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
        function edit_pekerjaan(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_pekerjaan').modal('show');
            jQuery.ajax({
            url: "/admin/master/pekerjaan/"+id,
            method: 'get',
            success: function(result){
                    $("#form-edit-pekerjaan").attr("action", "/admin/master/pekerjaan/"+result.pekerjaan['id']+"/update");
                    $("#edit_profesi").val(result.pekerjaan['profesi']);
                    $("#body_loading").hide();
                    $("#body_edit").show();                 
                }
            });
        }

        function delete_pekerjaan(id){
            $('#form-delete-pekerjaan').attr("action", "/admin/master/pekerjaan/"+id+"/delete");
            $('#delete_pekerjaan').modal('show');  
        }

        $(document).ready( function () {
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari akun...",
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
            $('#sidebarAkun').removeClass('collapsed');
            $('#collapseAkun').addClass('show');
            $('#sidebarAkunSuperAdmin').addClass('active');
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