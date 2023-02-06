@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Daftar Kelahiran')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-baby mr-2"></i></div>
                                Kelahiran Krama
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Kelahiran Krama</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Kelahiran Krama <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-kelahiran-create') }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Kelahiran</span>
                    </a>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kelahiran" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>No. Akta Kelahiran</th>
                                    <th>Nama</th>
                                    <th>Tempat/Tanggal Lahir</th>
                                    <th>Banjar Adat</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelahirans as $kelahiran)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kelahiran->nomor_akta_kelahiran }}</td>
                                        <td>{{ $kelahiran->cacah_krama_mipil->penduduk->nama }}</td>
                                        <td>{{ $kelahiran->cacah_krama_mipil->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($kelahiran->cacah_krama_mipil->penduduk->tanggal_lahir)) }}</td>
                                        <td>{{ $kelahiran->cacah_krama_mipil->banjar_adat->nama_banjar_adat }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('desa-kelahiran-detail', $kelahiran->id) }}"><i class="fas fa-eye"></i></a>
                                            <a class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('desa-kelahiran-edit', $kelahiran->id) }}"><i class="fas fa-edit"></i></a>
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
    <form id="form-delete-kelahiran" method="post" action="/">
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
        $(document).ready( function () {
            $("#dataTable-kelahiran").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari data kelahiran...",
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
            $('#sidebarKeluarga').removeClass('collapsed');
            $('#collapseKeluarga').addClass('show');
            $('#collapseKeluarga').addClass('active');
            $('#nav-link-keluarga-krama').addClass('active');
        });
    </script>
@endpush