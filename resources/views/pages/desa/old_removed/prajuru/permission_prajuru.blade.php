@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet"/>
@endpush
@section('title', 'Hak Akses Prajuru')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user-tag mr-1"></i></div>
                                Hak Akses Prajuru
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Hak Akses Prajuru</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Hak Akses Prajuru <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kabupaten" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Role / Jabatan</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $permission->role)) }}</td>
                                        <td class="text-center">
                                            <button button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="edit_permission({{ $permission->id }})"><i class="fas fa-eye"></i></button>
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
    <!-- Edit Banjar Dinas Modal -->
    <div class="modal fade" id="edit_permission" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-edit-permission" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Hak Akses Prajuru</h5>
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
                            <label for="role" class="small">Role / Jabatan</label>
                            <input class="form-control @error('role') is-invalid @enderror" id="role" name="role" type="text" value="Bendesa" disabled>
                        </div>
                        <div class="form-group">
                            <label for="permission" class="small">Hak Akses<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('permission') is-invalid @enderror" name="permission[]" id="permission" multiple="multiple" style="width: 100%" required>
                                <option value="manajemen_banjar">Manajemen Banjar</option>
                                <option value="manajemen_cacah_krama">Manajemen Cacah Krama</option>
                                <option value="manajemen_krama">Manajemen Krama</option>
                                <option value="manajemen_mutasi">Manajemen Mutasi Krama</option>
                                <option value="report">Statistik dan Laporan Kependudukan</option>
                            </select>
                            @error('permission')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Hak Akses wajib dipilih
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
    <form id="form-delete-krama" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        @if($errors->has('permission'))
            <script>
                $(document).ready(function(){
                    $('#body_loading').hide();
                    $('#edit_permission').modal('show');
                });
            </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        function edit_permission(id){
            $('#body_loading').show();
            $('#body_edit').hide();
            $('#edit_permission').modal('show');
            var url = "{{ route('desa-prajuru-permission-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    var url = "{{ route('desa-prajuru-permission-update', ":id") }}";
                    url = url.replace(':id', id);
                    $("#form-edit-permission").attr("action", url);
                    $('#role').val(result.prajuru_permission.role);
                    if(result.prajuru_permission.role == 'kelihan_adat' || result.prajuru_permission.role == 'pangliman_banjar' || result.prajuru_permission.role == 'penyarikan_banjar' || result.prajuru_permission.role == 'patengen_banjar'){
                        $('#permission').empty();
                        $('#permission').append('<option value="manajemen_cacah_krama">Manajemen Cacah Krama</option>');
                        $('#permission').append('<option value="manajemen_krama">Manajemen Krama</option>');
                        $('#permission').append('<option value="manajemen_mutasi">Manajemen Mutasi Krama</option>');
                        $('#permission').append('<option value="report">Statistik dan Laporan Kependudukan</option>');
                    }
                    $('#permission').val(result.prajuru_permission.permission).trigger('change');
                    $("#body_loading").hide();
                    $("#body_edit").show();                 
                }
            });
        }

        $(document).ready( function () {
            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari jabatan...",
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
            $('#sidebarPrajuru').removeClass('collapsed');
            $('#collapsePrajuru').addClass('show');
            $('#collapsePrajuru').addClass('active');
            $('#nav-link-permission-prajuru').addClass('active');
        });

        //SELECT2
        $(".custom-select").select2({
            placeholder: "Tidak ada hak akses yang dipilih",
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