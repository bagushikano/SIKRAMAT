@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Edit Krama Mipil')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Detail Krama Mipil
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-krama-mipil-home') }}" class="text-decoration-none">Krama Mipil</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Detail Krama Mipil</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            @csrf
            <div class="row">
                <div class="col-xxl-4 col-xl-12 mb-4">
                    <div class="card h-100">
                        <div class="card-body h-100 d-flex justify-content-center py-5 py-xl-4">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <p class="text-gray-700 mb-0">Data <span class="text-primary font-weight-bold">Krama Mipil</span> beserta anggota keluarganya</p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/family.png')}}" style="max-width: 15rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12 mb-4">
                    <div class="card mb-4">
                        <div class="card-header">Krama Mipil (Kepala Keluarga)</div>
                        <div class="card-body px-5 mt-2 mb-2">
                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Krama Mipil</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->nomor_krama_mipil }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nama Krama Mipil</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Banjar Adat</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->banjar_adat->nama_banjar_adat }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Tanggal Registrasi</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ date('d M Y', strtotime($krama_mipil->tanggal_registrasi)) }}</span>
                                </div>
                            </div>

                            <hr class="my-4" />
                            <button class="btn btn-warning btn-icon-split text-end float-right" type="button" data-toggle="modal" data-target="#ganti_krama_mipil">
                                <span class="icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                                <span class="text">Ganti Krama Mipil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4 mt-4">
                <div class="card-header">Anggota Keluarga Krama Mipil</div>
                <div class="card-body px-5 mt-3 mb-4">
                    <form id="form-create-krama-mipil" method="post" action="{{ route('desa-cacah-krama-mipil-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <button class="btn btn-primary btn-icon-split mb-4 text-end" type="button"   data-toggle="modal" data-target="#create_anggota_krama_mipil">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Anggota Keluarga</span>
                    </button>
                        <div class="datatable">
                        <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Cacah Krama Mipil</th>
                                    <th style="width: 30%">Status Hubungan</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anggota_krama_mipil as $anggota_krama)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_depan != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $anggota_krama->status_hubungan)) }}</td>
                                        <td class="text-center">
                                            <button button type="button" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_anggota({{ $anggota_krama->id }})"><i class="fas fa-edit"></i></button>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_anggota({{ $anggota_krama->id }})"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <div class="modal fade" id="create_anggota_krama_mipil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-krama-mipil" method="post" action="{{route('desa-anggota-krama-mipil-store', $krama_mipil->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Anggota Krama Mipil</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cacah_krama_mipil">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select select-krama @error ('cacah_krama_mipil') is-invalid @enderror" name="cacah_krama_mipil" id="cacah_krama_mipil"  style="width: 100%">
                                <option value="">Pilih Cacah Krama</option>
                            </select>
                            @error('cacah_krama_mipil')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="status_hubungan">Status Hubungan dengan Krama Mipil<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('status_hubungan') is-invalid @enderror" name="status_hubungan" id="status_hubungan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                <option value="">Pilih Status Hubungan</option>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="anak">Anak</option>
                                <option value="cucu">Cucu</option>
                                <option value="menantu">Menantu</option>
                                <option value="mertua">Mertua</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="famili_lain">Famili Lain</option>
                            </select>
                            @error('status_hubungan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Hubungan wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="edit_anggota_krama_mipil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-edit-anggota-krama-mipil" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Anggota Krama Mipil</h5>
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
                            <label for="edit_cacah_krama_mipil">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select select-krama @error ('edit_cacah_krama_mipil') is-invalid @enderror" name="edit_cacah_krama_mipil" id="edit_cacah_krama_mipil"  style="width: 100%">
                                <option value="">Pilih Cacah Krama</option>
                            </select>
                            @error('cacah_krama_mipil')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_status_hubungan">Status Hubungan dengan Krama Mipil<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('edit_status_hubungan') is-invalid @enderror" name="edit_status_hubungan" id="edit_status_hubungan"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="anak">Anak</option>
                                <option value="cucu">Cucu</option>
                                <option value="menantu">Menantu</option>
                                <option value="mertua">Mertua</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="famili_lain">Famili Lain</option>
                            </select>
                            @error('edit_status_hubungan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Hubungan wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="ganti_krama_mipil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-krama-mipil" method="post" action="{{route('desa-krama-mipil-update', $krama_mipil->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Ganti Krama Mipil</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="krama_mipil_lama">Krama Mipil Lama</label>
                            <select class="select2 custom-select select-krama @error ('krama_mipil_lama') is-invalid @enderror" name="krama_mipil_lama" id="krama_mipil_lama"  style="width: 100%" disabled>
                                <option value="">{{ $krama_mipil->cacah_krama_mipil->penduduk->nomor_induk_cacah_krama }} - {{ $krama_mipil->cacah_krama_mipil->penduduk->gelar_depan }} {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}@if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</option>
                            </select>
                            @error('krama_mipil_lama')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="krama_mipil_baru">Krama Mipil Baru<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('krama_mipil_baru') is-invalid @enderror" name="krama_mipil_baru" id="krama_mipil_baru"  style="width: 100%">
                                <option value="" hidden>Pilih Krama Mipil Baru</option>
                                @foreach($anggota_krama_mipil as $anggota_krama)
                                    <option value="{{ $anggota_krama->cacah_krama_mipil_id }}">{{ $anggota_krama->cacah_krama_mipil->penduduk->nomor_induk_cacah_krama }} - {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_depan != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</option>
                                @endforeach
                            </select>
                            @error('cacah_krama_mipil')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alasan_penggantian">Alasan Penggantian<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('alasan_penggantian') is-invalid @enderror" name="alasan_penggantian" id="alasan_penggantian"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                <option value="">Pilih Alasan Penggantian</option>
                                <option value="krama_mipil_meninggal_dunia">Krama Mipil Meninggal Dunia</option>
                                <option value="krama_mipil_sakit">Krama Mipil Sakit</option>
                                <option value="krama_mipil_lansia">Krama Mipil Lansia</option>
                                <option value="krama_mipil_baru">Krama Mipil Baru</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('status_hubungan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Hubungan wajib dipilih
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
    <form id="form-delete-anggota" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('error'))
    <script>
        $(document).ready(function(){
            alertError('Gagal', '{{$message}}');
        });
    </script>
    @elseif($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    <script>
        $(document).ready( function () {
            $("#anggota-keluarga").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari anggota...",
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
            $('#sidebarKrama').removeClass('collapsed');
            $('#collapseKrama').addClass('show');
            $('#collapseKrama').addClass('active');
            $('#nav-link-krama-mipil').addClass('active');

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
            $(".select-krama").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    },
                    inputTooShort: function() {
                        return 'Masukkan Nama atau Nomor Induk Cacah Krama';
                    }
                },
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("desa-anggota-krama-mipil-search") }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            krama_mipil_id: {{ $krama_mipil->id }}
                        };
                    }
                },
            });
        });

        function edit_anggota(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_anggota_krama_mipil').modal('show');
            var url = "{{ route('desa-anggota-krama-mipil-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                console.log(result);
                var url = "{{ route('desa-anggota-krama-mipil-update', ":id") }}";
                url = url.replace(':id', result.anggota_krama_mipil.id);
                $("#form-edit-anggota-krama-mipil").attr("action", url);
                $("#edit_cacah_krama_mipil").empty();
                if(result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan != null && result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang != null){
                    var nama = result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan+' '+result.anggota_krama_mipil.cacah_krama_mipil.penduduk.nama+', '+result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang; 
                }else if(result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan == null && result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang != null){
                    var nama = result.anggota_krama_mipil.cacah_krama_mipil.penduduk.nama+', '+result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang; 
                }else if(result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan != null && result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang == null){
                    var nama = result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan+' '+result.anggota_krama_mipil.cacah_krama_mipil.penduduk.nama; 
                }else if(result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_depan == null && result.anggota_krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang == null){
                    var nama = result.anggota_krama_mipil.cacah_krama_mipil.penduduk.nama; 
                }
                $("#edit_cacah_krama_mipil").append('<option value="'+result.anggota_krama_mipil.cacah_krama_mipil_id+'">'+nama+'</option>')
                $('#edit_status_hubungan').val(result.anggota_krama_mipil.status_hubungan).trigger('change');
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

        function delete_anggota(id){
            Swal.fire({
                title: 'Hapus Krama',
                text: "Apakah anda yakin ingin menghapus Anggota Krama Mipil ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-anggota-krama-mipil-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-anggota').attr("action", url);
                        $('#form-delete-anggota').submit();
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