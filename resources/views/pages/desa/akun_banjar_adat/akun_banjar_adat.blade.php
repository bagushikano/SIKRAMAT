@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Akun Banjar Adat')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-1"></i></div>
                                Akun Banjar Adat
                            </h1>
                            <div class="page-header-subtitle">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Daftar Akun Banjar Adat</div>
                <div class="card-body">
                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-admin-banjar-create') }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Akun</span>
                    </a>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Nama Banjar Adat</th>
                                    <th>Email</th>
                                    <th>Nama Pengguna</th>
                                    <th style="width: 7%">Status</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tfoot>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->banjar_adat->nama_banjar_adat ?? '-' }}</td>
                                        <td>{{ $user->user->email ?? '-' }}</td>
                                        <td>{{ $user->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            <label class="switch">
                                                <input id="signup-token_{{$user->id}}" name="_token" type="hidden" value="{{csrf_token()}}">
                                              @if($user->status)
                                                <input type="checkbox" id="status_{{$user->id}}" onclick="statusBtn({{$user->id}})" checked>
                                              @else
                                                <input type="checkbox" id="status_{{$user->id}}" onclick="statusBtn({{$user->id}})">
                                              @endif
                                                <span class="slider round"></span>
                                              </label>
                                        </td>
                                        <td class="text-center">
                                            <a button type="button" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('desa-admin-banjar-edit', $user->id) }}"><i class="fas fa-edit"></i></a>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_akun({{ $user->id }})"><i class="fas fa-trash"></i></button>
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
    <!-- Delete Akun Modal -->
    <div class="modal fade" id="delete_akun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Akun</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus akun desa adat ini?</p>
                    <form id="form-delete-akun" method="post" action="/">
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

    {{-- HIDDEN FORM --}}
    <form id="form-nonaktif-akun" method="post" action="/">
        @method('delete')
        @csrf
    </form>
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
        function delete_akun(id){
            Swal.fire({
                title: 'Hapus Akun',
                text: "Apakah anda yakin ingin menghapus akun ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-admin-banjar-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-akun').attr("action", url);
                        $('#form-delete-akun').submit();
                    }
                })
        }

        function statusBtn(id){
            checkBox = document.getElementById("status_"+id);
            if (checkBox.checked == true){
                Swal.fire({
                title: 'Aktifkan Akun',
                text: "Apakah anda yakin ingin mengaktifkan akun ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-admin-banjar-status', [":id", ":status"]) }}";
                        url = url.replace(':id', id);
                        url = url.replace(':status', '1');
                        jQuery.ajax({  
                            url: url,
                            type: "GET",
                            success: function(result){
                                alertSuccess('Success', 'Akun berhasil diaktifkan');
                            }
                        });
                    }else{
                        document.getElementById("status_"+id).checked = false;
                    }
                })
            } else {
                Swal.fire({
                title: 'Nonaktifkan Akun',
                text: "Apakah anda yakin ingin menonaktifkan akun ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-admin-banjar-status', [":id", ":status"]) }}";
                        url = url.replace(':id', id);
                        url = url.replace(':status', '0');
                        jQuery.ajax({  
                            url: url,
                            type: "GET",
                            success: function(result){
                                alertSuccess('Success', 'Akun berhasil dinonaktifkan');
                            }
                        });
                    }else{
                        document.getElementById("status_"+id).checked = true;
                    }
                })
            }
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
            $('#collapsePengguna').addClass('show');
            $('#nav-link-akun').addClass('active');
        });
    </script>
@endpush