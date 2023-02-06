@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Daftar Cacah Krama Tamiu')
@section('content')
    <main>
        {{-- <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="layout"></i></div>
                                Krama Mipil
                            </h1>
                            <div class="page-header-subtitle">Daftar Krama Mipil Desa Adat <span class="text-light">{{ Session::get('desa_adat_nama') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </header> --}}
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Cacah Krama Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Cacah Krama Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Cacah Krama Tamiu <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-cacah-krama-tamiu-create') }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Cacah Krama Tamiu</span>
                    </a>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-cacah-krama-tamiu" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    {{-- <th>No. Krama Tamiu</th> --}}
                                    <th>NICK</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Banjar Adat</th>
                                    <th>Banjar Dinas</th>
                                    <th style="width: 12%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kramas as $krama)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td>{{ $krama->nomor_krama_tamiu }}</td> --}}
                                        <td>{{ $krama->penduduk->nomor_induk_cacah_krama }}</td>
                                        <td>{{ $krama->penduduk->nik }}</td>
                                        <td>{{ $krama->penduduk->gelar_depan }} {{ $krama->penduduk->nama }}@if($krama->penduduk->gelar_belakang != ''), {{ $krama->penduduk->gelar_belakang }} @endif</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $krama->penduduk->jenis_kelamin)) }}</td>
                                        <td>{{ $krama->banjar_adat->nama_banjar_adat }}</td>
                                        <td>{{ $krama->banjar_dinas->nama_banjar_dinas }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('desa-cacah-krama-tamiu-detail', $krama->id) }}"><i class="fas fa-eye"></i></a>
                                            <a class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('desa-cacah-krama-tamiu-edit', $krama->id) }}"><i class="fas fa-edit"></i></a>
                                            <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama({{ $krama->id }})"><i class="fas fa-trash"></i></button>
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

    {{-- HIDDEN FORM --}}
    <form id="form-delete-krama" method="post" action="/">
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

        function delete_krama(id){
            Swal.fire({
                title: 'Hapus Krama',
                text: "Apakah anda yakin ingin menghapus Krama Tamiu ini?",
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
            $("#dataTable-cacah-krama-tamiu").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari cacah krama...",
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
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active')
        });
    </script>
@endpush