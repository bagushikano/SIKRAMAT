@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Daftar Krama Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Krama Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Krama Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Krama Tamiu <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end"  data-toggle="modal" data-target="#create_krama_tamiu">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Krama Tamiu</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-krama-tamiu" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Nomor Krama Tamiu</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Banjar Adat</th>
                                    <th style="width: 12%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($krama_tamiu as $krama)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $krama->nomor_krama_tamiu }}</td>
                                        <td>{{ $krama->cacah_krama_tamiu->penduduk->gelar_depan }} {{ $krama->cacah_krama_tamiu->penduduk->nama }}@if($krama->cacah_krama_tamiu->penduduk->gelar_belakang != ''), {{ $krama->cacah_krama_tamiu->penduduk->gelar_belakang }} @endif</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $krama->cacah_krama_tamiu->penduduk->jenis_kelamin)) }}</td>
                                        <td>{{ $krama->banjar_adat->nama_banjar_adat }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="/"><i class="fas fa-eye"></i></a>
                                            <a class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="/"><i class="fas fa-edit"></i></a>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama({{ $krama->id }})"><i class="fas fa-trash"></i></button>
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
    <div class="modal fade" id="create_krama_tamiu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-krama-tamiu" method="post" action="{{route('desa-krama-tamiu-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Krama Tamiu</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="banjar_adat">Banjar Adat<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('banjar_adat') is-invalid @enderror" name="banjar_adat" id="banjar_adat"  style="width: 100%" aria-placeholder="Pilih Banjar Adat" required>
                                <option value="">Pilih Banjar Adat</option>
                                @foreach($banjar_adat as $adat)
                                    <option value="{{ $adat->id }}" @if(old('banjar_adat') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                @endforeach
                            </select>
                            @error('banjar_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Banjar Adat wajib dipilih
                                </div>
                            @enderror 
                        </div>
                        <div class="form-group">
                            <label for="nomor_krama_tamiu">Nomor Krama Tamiu<span class="text-danger small">*</span></label>
                            <input class="form-control" id="nomor_krama_tamiu" name="nomor_krama_tamiu" type="text" placeholder="Nomor Krama Tamiu" value="{{ old('nomor_krama_tamiu') }}" required readonly>
                            <small class="small">(Pilih Banjar Adat terlebih dahulu)</small>
                            @error('nomor_krama_tamiu')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nomor Krama Tamiu
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="cacah_krama_tamiu">Cacah Krama Tamiu<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select select-krama @error ('cacah_krama_tamiu') is-invalid @enderror" name="cacah_krama_tamiu" id="cacah_krama_tamiu"  style="width: 100%">
                                <option value="">Cari Cacah Krama</option>
                            </select>
                            @error('cacah_krama_tamiu')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
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
        @if($errors->has('banjar_adat', 'nomor_krama_tamiu', 'cacah_krama_tamiu'))
            <script>
                $(document).ready(function(){
                    $('#create_krama_tamiu').modal('show');
                });
            </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        function delete_krama(id){
            Swal.fire({
                title: 'Hapus Krama',
                text: "Apakah anda yakin ingin menghapus Krama tamiu ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-krama-tamiu-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-krama').attr("action", url);
                        $('#form-delete-krama').submit();
                    }
                })
        }

        $(document).ready( function () {
            $("#dataTable-krama-tamiu").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari krama...",
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
            $('#nav-link-krama-tamiu').addClass('active');

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
                    url: '{{ route("desa-krama-tamiu-search") }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            banjar_adat_id: $('#banjar_adat').val()
                        };
                    }
                },
            });

            //On Change Event
            $('#banjar_adat').on('change', function(){
                if($(this).val() == ''){
                    $('#nomor_krama_tamiu').val('');
                }else if($(this).val() != ''){
                    var url = "{{ route('desa-krama-tamiu-generate-nomor-krama-tamiu', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            $('#nomor_krama_tamiu').val(result.nomor_krama_tamiu);                
                        }
                    });
                }
            });
        });
    
    </script>
@endpush