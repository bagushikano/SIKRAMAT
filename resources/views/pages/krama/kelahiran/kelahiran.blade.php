@extends('layouts.krama.krama')
@push('css')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
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
                                <div class="page-header-icon">
                                    <i class="fa-solid fa-baby mr-2"></i>
                                </div>
                                Data Kelahiran
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
                            <a class="nav-link active" id="daftar_kelahiran_tab" href="#daftar_kelahiran" data-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Daftar Kelahiran</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ajuan_kelahiran_tab" href="#ajuan_kelahiran" data-toggle="tab" role="tab" aria-controls="example" aria-selected="false">Ajuan Data Kelahiran</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="daftar_kelahiran" role="tabpanel" aria-labelledby="overview-tab">
                            
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-daftar-kelahiran" id="dataTable-daftar-kelahiran" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Akta Kelahiran</th>
                                            <th>Nama</th>
                                            <th>Tempat/Tanggal Lahir</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Status</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelahiran as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->nomor_akta_kelahiran}}</td>
                                                <td>{{ $item->cacah_krama_mipil->penduduk->nama}}</td>
                                                <td>{{ $item->cacah_krama_mipil->penduduk->tempat_lahir}}, {{ date('d M Y', strtotime($item->cacah_krama_mipil->penduduk->tanggal_lahir)) ?? '-' }}</td>
                                                <td>{{ ucwords($item->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                                <td class="text-center"><span class="badge badge-success px-3 py-1"> Sah </span></td>
                                                <td class="text-center">
                                                    <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('Kelahiran Detail', $item->id) }}"><i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ajuan_kelahiran" role="tabpanel" aria-labelledby="example-tab">
                            <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('Kelahiran Create Ajuan') }}">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Ajukan Data Kelahiran</span>
                            </a>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-ajuan-kelahiran" id="dataTable-ajuan-kelahiran" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Akta Kelahiran</th>
                                            <th>Nama</th>
                                            <th>Tempat/Tanggal Lahir</th>
                                            <th>Jenis Kelamin</th>
                                            <th style="width: 8%">Status</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelahiran_ajuan as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->nomor_akta_kelahiran}}</td>
                                                <td>{{ $item->cacah_krama_mipil->penduduk->nama}}</td>
                                                <td>{{ $item->cacah_krama_mipil->penduduk->tempat_lahir}}, {{ date('d M Y', strtotime($item->cacah_krama_mipil->penduduk->tanggal_lahir)) ?? '-' }}</td>
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
                                                    <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('Kelahiran Detail Ajuan', $item->id) }}"><i class="fas fa-eye"></i></a>
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
                    $('#daftar_kelahiran').removeClass('active');
                    $('#daftar_kelahiran_tab').removeClass('active');
                    $('#ajuan_kelahiran_tab').addClass('active');
                    $('#ajuan_kelahiran').tab('show');
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
        @if($errors->has('nama_daftar_kelahiran') || $errors->has('kode_daftar_kelahiran'))
            <script>
                $(document).ready(function(){
                    $('#create_daftar_kelahiran').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_daftar_kelahiran') || $errors->has('edit_kode_daftar_kelahiran'))
        <script>
            $(document).ready(function(){
                $("#body_loading").hide();
                $('#edit_daftar_kelahiran').modal('show');
            });
        </script>
        @elseif($errors->has('nama_ajuan_kelahiran') || $errors->has('kode_ajuan_kelahiran'))
            <script>
                $(document).ready(function(){
                    $('#create_ajuan_kelahiran').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_ajuan_kelahiran') || $errors->has('edit_kode_ajuan_kelahiran'))
        <script>
            $(document).ready(function(){
                $("#body_loading_dinas").hide();
                $('#edit_ajuan_kelahiran').modal('show');
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
            $('#nav-link-ajuan-kelahiran').addClass('active');

            $("#dataTable-daftar-kelahiran").DataTable({
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

            $("#dataTable-ajuan-kelahiran").DataTable({
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