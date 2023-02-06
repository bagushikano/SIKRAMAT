@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Daftar Riwayat Perubahan Data')
@section('content')
    <main>
        {{-- <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-fluid">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-history"></i></div>
                                    Riwayat Perubahan Data
                                    <div class="d-none d-md-inline ml-1 font-weight-500 text-primary">
                                        I Putu Abdi Purnawan, S.Kom
                                    </div>
                                </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header> --}}
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-history mr-1"></i></div>
                                Riwayat Perubahan Data
                            </h1>
                            <div class="page-header-subtitle">
                                Riwayat Perubahan Data
                                <div class="d-none d-md-inline ml-1 font-weight-500 text-primary">
                                    {{ $penduduk->nama }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n2">
            <div class="card">
                <div class="card-header">Riwayat Perubahan</div>
                <div class="card-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover" id="dataTable-riwayat" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th style="width: 20%">Tanggal Perubahan</th>
                                    <th>Perubahan yang Terjadi</th>
                                    <th style="width: 12%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayats as $riwayat)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ date('d M Y', strtotime($riwayat->created_at)) }}</td>
                                        <td>{{ $riwayat->keterangan }}</td>
                                        <td class="text-center">
                                            <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('banjar-cacah-krama-mipil-detail-riwayat', ['id'=>$cacah_krama_mipil->id,'id_riwayat'=>$riwayat->id]) }}"><i class="fas fa-eye"></i></a>
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
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $("#dataTable-riwayat").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari riwayat...",
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
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-krama-mipil').addClass('active');
        });

    </script>
@endpush