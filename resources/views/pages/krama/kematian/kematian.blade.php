@extends('layouts.krama.krama')
@push('css')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
@endpush
@section('title', 'Daftar Kematian')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fa-solid fa-book-dead mr-2"></i>
                                </div>
                                Data Kematian
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="cardTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="daftar_kematian_tab" href="#daftar_kematian" data-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Daftar Kematian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ajuan_kematian_tab" href="#ajuan_kematian" data-toggle="tab" role="tab" aria-controls="example" aria-selected="false">Ajuan Data Kematian</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="daftar_kematian" role="tabpanel" aria-labelledby="overview-tab">
                            
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-daftar-kematian" id="dataTable-daftar-kematian" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Kematian</th>
                                            <th>Nama</th>
                                            <th>Tanggal Kematian</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Status</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kematian as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->nomor_suket_kematian}}</td>
                                                <td>
                                                    @if($item->cacah_krama_mipil->penduduk->gelar_depan != '')
                                                    {{ $item->cacah_krama_mipil->penduduk->gelar_depan }} 
                                                    @endif
                                                    {{ $item->cacah_krama_mipil->penduduk->nama }}
                                                    @if($item->cacah_krama_mipil->penduduk->gelar_belakang != '')
                                                    , {{ $item->cacah_krama_mipil->penduduk->gelar_belakang }}
                                                    @endif
                                                </td>
                                                <td>{{ date('d M Y', strtotime($item->tanggal_kematian)) }}</td>
                                                <td>{{ ucwords($item->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                                <td class="text-center"><span class="badge badge-success px-3 py-1"> Sah </span></td>
                                                <td class="text-center">
                                                    <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('Kematian Detail', $item->id) }}"><i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ajuan_kematian" role="tabpanel" aria-labelledby="example-tab">
                            <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('Kematian Create Ajuan') }}">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Ajukan Data Kematian</span>
                            </a>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-ajuan-kematian" id="dataTable-ajuan-kematian" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Kematian</th>
                                            <th>Nama</th>
                                            <th>Tanggal Kematian</th>
                                            <th>Jenis Kelamin</th>
                                            <th style="width: 5%">Status</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kematian_ajuan as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->nomor_suket_kematian}}</td>
                                                <td>
                                                    @if($item->cacah_krama_mipil->penduduk->gelar_depan != '')
                                                    {{ $item->cacah_krama_mipil->penduduk->gelar_depan }} 
                                                    @endif
                                                    {{ $item->cacah_krama_mipil->penduduk->nama }}
                                                    @if($item->cacah_krama_mipil->penduduk->gelar_belakang != '')
                                                    , {{ $item->cacah_krama_mipil->penduduk->gelar_belakang }}
                                                    @endif
                                                </td>
                                                <td>{{ date('d M Y', strtotime($item->tanggal_kematian)) }}</td>
                                                <td>{{ ucwords($item->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                                <td class="text-center">
                                                    @if($item->status == '0')
                                                        <span class="badge badge-warning text-wrap px-3 py-1"> Menunggu Konfirmasi </span>
                                                    @elseif($item->status == '1')
                                                        <span class="badge badge-info text-wrap px-3 py-1"> Sedang Diproses </span>
                                                    @elseif($item->status == '2')
                                                        <span class="badge badge-danger px-3 py-1"> Ditolak </span>
                                                    @else
                                                        <span class="badge badge-success px-3 py-1"> Sah </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('Kematian Detail Ajuan', $item->id) }}"><i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    {{-- HIDDEN FORM --}}
    <form id="form-delete-banjar-adat" method="post" action="/">
        @method('delete')
        @csrf
    </form>

    <form id="form-delete-banjar-dinas" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>    {{-- ALERT --}}
    @if($message = Session::get('success'))
        @if($status = Session::get('is_ajuan'))
            <script>
                $(document).ready(function(){
                    $('#daftar_kematian').removeClass('active');
                    $('#daftar_kematian_tab').removeClass('active');
                    $('#ajuan_kematian_tab').addClass('active');
                    $('#ajuan_kematian').tab('show');
                    alertSuccess('Success', '{{$message}}');
                });
            </script>
        @else
        <script>
            $(document).ready(function(){
                alertSuccess('Success', '{{$message}}');
            });
        </script>
        @endif
    @endif
    {{-- END ALERT --}}
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('nama_daftar_kematian') || $errors->has('kode_daftar_kematian'))
            <script>
                $(document).ready(function(){
                    $('#create_daftar_kematian').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_daftar_kematian') || $errors->has('edit_kode_daftar_kematian'))
        <script>
            $(document).ready(function(){
                $("#body_loading").hide();
                $('#edit_daftar_kematian').modal('show');
            });
        </script>
        @elseif($errors->has('nama_ajuan_kematian') || $errors->has('kode_ajuan_kematian'))
            <script>
                $(document).ready(function(){
                    $('#create_ajuan_kematian').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_ajuan_kematian') || $errors->has('edit_kode_ajuan_kematian'))
        <script>
            $(document).ready(function(){
                $("#body_loading_dinas").hide();
                $('#edit_ajuan_kematian').modal('show');
            });
        </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        $(document).ready( function () {
            //SIDE BAR CLASS
            $('#sidebarAjuan').removeClass('collapsed');
            $('#collapseAjuan').addClass('show');
            $('#nav-link-ajuan-kematian').addClass('active');

            $("#dataTable-daftar-kematian").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari data kematian...",
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

            $("#dataTable-ajuan-kematian").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari data ajuan...",
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
        });
    </script>
@endpush