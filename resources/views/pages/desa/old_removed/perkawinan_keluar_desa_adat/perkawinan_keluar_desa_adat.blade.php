@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Daftar Perkawinan Keluar Desa Adat')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Perkawinan Keluar Desa Adat
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Perkawinan Keluar Desa Adat</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Perkawinan Keluar <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    {{-- <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-perkawinan-masuk-desa-adat-create') }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Perkawinan</span>
                    </a> --}}
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-perkawinan" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>No. Perkawinan</th>
                                    <th>Nama Purusa</th>
                                    <th>Nama Pradana</th>
                                    <th>Tanggal Perkawinan</th>
                                    <th style="width: 12%">Status Approval</th>
                                    <th style="width: 12%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perkawinans as $perkawinan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $perkawinan->nomor_perkawinan }}</td>
                                        <td>{{ $perkawinan->purusa->penduduk->nama }}</td>
                                        <td>{{ $perkawinan->pradana->penduduk->nama }}</td>
                                        <td>{{ $perkawinan->tanggal_perkawinan }}</td>
                                        <td class="text-center">
                                            @if($perkawinan->approval_pradana == 1)
                                                <span class="badge badge-warning">Disetujui</span> 
                                            @elseif($perkawinan->approval_pradana == 0)
                                                <span class="badge badge-warning">Pending</span> 
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('desa-perkawinan-keluar-desa-adat-detail', $perkawinan->id) }}"><i class="fas fa-eye"></i></a>
                                            <button class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Setujui" onclick="setuju_perkawinan()"><i class="fas fa-check"></i></button>
                                            <button class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Tolak" onclick="tolak_perkawinan()"><i class="fas fa-ban"></i></button>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
        $(document).ready( function () {
            $("#dataTable-perkawinan").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari data perkawinan...",
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
            $('#sidebarPerkawinan').removeClass('collapsed');
            $('#collapsePerkawinan').addClass('show');
            $('#collapsePerkawinan').addClass('active');
            $('#nav-link-perkawinan-satu-desa').addClass('active');
        });

        function setuju_perkawinan(id){
            Swal.fire({
                title: 'Setujui Perkawinan',
                text: "Apakah anda yakin ingin menyetujui perkawinan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-krama-mipil-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-krama').attr("action", url);
                        $('#form-delete-krama').submit();
                    }
                })
        }

        function tolak_perkawinan(id){
            Swal.fire({
                title: 'Tolak Perkawinan',
                text: "Masukkan alasan penolakan pada kotak dibawah ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                input: 'textarea'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-krama-mipil-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-krama').attr("action", url);
                        $('#form-delete-krama').submit();
                    }
                })
        }
    </script>
@endpush