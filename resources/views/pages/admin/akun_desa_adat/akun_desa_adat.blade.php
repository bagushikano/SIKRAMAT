@extends('layouts.admin.admin')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Akun Desa Adat')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="user"></i></div>
                                Akun Desa Adat
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
                <div class="card-header">Daftar Akun Desa Adat</div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" data-toggle="modal" data-target="#create_akun">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Akun</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Kode Desa Adat</th>
                                    <th>Nama Desa Adat</th>
                                    <th>Email</th>
                                    <th style="width: 7%">Status</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tfoot>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->desa_adat->desadat_kode }}</td>
                                        <td>{{ $user->desa_adat->desadat_nama }}</td>
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
                                            <button button type="button" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_akun({{ $user->id }})"><i class="fas fa-edit"></i></button>
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
    <!-- Tambah Akun Admin Modal -->
    <div class="modal fade" id="create_akun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-akun-admin-desa" method="post" action="{{route('admin-admin-desa-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Akun Desa Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('kabupaten_id') is-invalid @enderror" name="kabupaten_id" id="kabupaten_id" style="width: 100%" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                @endforeach
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
                            <select class="select2 custom-select @error('kecamatan_id') is-invalid @enderror" name="kecamatan_id" id="kecamatan_id" style="width: 100%" required>
                                <option value="">Pilih Kecamatan</option>
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
                            <label for="desa_adat_id" class="small">Desa Adat<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('desa_adat_id') is-invalid @enderror" name="desa_adat_id" id="desa_adat_id" style="width: 100%" required>
                                <option value="">Pilih Desa Adat</option>
                            </select>
                            <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                            @error('desa_adat_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Desa Adat wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email_desa_adat" class="small">Email Desa Adat<span class="text-danger small">*</span></label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="text" value="{{ old('email') }}" placeholder="Masukkan Email Desa Adat" required>
                            @error('email')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Email Desa Adat wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Akun Admin Modal -->
    <div class="modal fade" id="edit_akun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-detail-akun-admin-desa" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Akun Desa Adat</h5>
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
                            <label for="kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_kabupaten_id') is-invalid @enderror" name="edit_kabupaten_id" id="edit_kabupaten_id" style="width: 100%" required disabled>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                @endforeach
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
                            <select class="select2 custom-select @error('edit_kecamatan_id') is-invalid @enderror" name="edit_kecamatan_id" id="edit_kecamatan_id" style="width: 100%" required disabled>
                                <option value="">Pilih Kecamatan</option>
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
                            <label for="desa_adat_id" class="small">Desa Adat<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_desa_adat_id') is-invalid @enderror" name="edit_desa_adat_id" id="edit_desa_adat_id" style="width: 100%" readonly disabled>
                                <option value="">Pilih Desa Adat</option>
                            </select>
                            <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                            @error('edit_desa_adat_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Desa Adat wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email_desa_adat" class="small">Email Desa Adat<span class="text-danger small">*</span></label>
                            <input class="form-control" id="edit_email" name="edit_email" type="text" placeholder="Masukkan Email Desa Adat" required>
                            @error('edit_email')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Email Desa Adat wajib diisi
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
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('edit_email'))
            <script>
                $(document).ready(function(){
                    alertError('Error', '{{ $errors->first('edit_email') }}');
                });
            </script>
        @else
            <script>
                $(document).ready(function(){
                    $('#create_akun').modal('show');
                });
            </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        function edit_akun(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_akun').modal('show');
            jQuery.ajax({
            url: "/admin/akun/desa-adat/"+id+"/edit",
            method: 'get',
            success: function(result){
                console.log(result);
                $("#form-detail-akun-admin-desa").attr("action", "/admin/akun/desa-adat/"+result.admin_desa_adat['id']+"/update");
                $('#edit_kabupaten_id').val(result.kabupaten.id).trigger('change');
                $('#edit_kecamatan_id').append('<option value="' + result.kecamatan.id + '"' +'>' + result.kecamatan.name + '</option>'); 
                $('#edit_kecamatan_id').val(result.kecamatan.id);
                $('#edit_desa_adat_id').append('<option value="' + result.desa_adat.id + '"' +'>' + result.desa_adat.desadat_nama + '</option>'); 
                $('#edit_desa_adat_id').val(result.desa_adat.id);
                $('#edit_email').val(result.user.email);
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

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
                        $('#form-delete-akun').attr("action", "/admin/akun/desa-adat/"+id+"/delete");
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
                            jQuery.ajax({  
                                url: "/admin/akun/desa-adat/"+id+"/status/aktif",
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
                            jQuery.ajax({  
                                url: "/admin/akun/desa-adat/"+id+"/status/tidak_aktif",
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

            //Get Data
            $('#kabupaten_id').on('change', function(){
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_id').empty();
                            $('#kecamatan_id').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_id').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_id').on('change', function(){
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/desa-adat/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_adat_id').empty();
                            $('#desa_adat_id').append('<option value="">Pilih Desa Adat</option>');
                            result.desa_adats.forEach(element => {
                                $('#desa_adat_id').append('<option value="' + element['id'] + '"' +'>' + element['desadat_nama'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            //SIDE BAR CLASS
            $('#sidebarAkun').removeClass('collapsed');
            $('#collapseAkun').addClass('show');
            $('#sidebarAkunSuperAdmin').addClass('active');
        });

        //Select 2
        $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
            }
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