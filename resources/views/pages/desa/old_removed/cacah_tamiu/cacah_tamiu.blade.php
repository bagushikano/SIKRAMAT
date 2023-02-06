@extends('layouts.desa.desa')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Cacah Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="users"></i></div>
                                Cacah Tamiu
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
                <div class="card-header border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="cardTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="wni_tab" href="#wni" data-toggle="tab" role="tab" aria-controls="wni" aria-selected="true">Tamiu Warga Negara Indonesia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wna_tab" href="#wna" data-toggle="tab" role="tab" aria-controls="wna" aria-selected="false">Tamiu Warga Negara Asing</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="wni" role="tabpanel" aria-labelledby="overview-tab">
                            <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-cacah-tamiu-wni-create') }}">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Cacah Tamiu</span>
                            </a>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-cacah-tamiu" id="dataTable-cacah-tamiu-wni" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Cacah Tamiu</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Banjar Adat</th>
                                            <th>Banjar Dinas</th>
                                            <th style="width: 12%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tamiu_wni as $wni)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $wni->nomor_cacah_tamiu }}</td>
                                                <td>{{ $wni->penduduk->nik }}</td>
                                                <td>{{ $wni->penduduk->gelar_depan }} {{ $wni->penduduk->nama }}@if($wni->penduduk->gelar_belakang != ''), {{ $wni->penduduk->gelar_belakang }} @endif</td>
                                                <td>{{ ucwords(str_replace('_', ' ', $wni->penduduk->jenis_kelamin)) }}</td>
                                                <td>{{ $wni->banjar_adat->nama_banjar_adat }}</td>
                                                <td>{{ $wni->banjar_dinas->nama_banjar_dinas }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('desa-cacah-tamiu-wni-detail', $wni->id) }}"><i class="fas fa-eye"></i></a>
                                                    <a class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('desa-cacah-tamiu-wni-edit', $wni->id) }}"><i class="fas fa-edit"></i></a>
                                                    <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_tamiu_wni({{ $wni->id }})"><i class="fas fa-trash"></i></button>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wna" role="tabpanel" aria-labelledby="example-tab">
                            <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('desa-cacah-tamiu-wna-create') }}">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Cacah Tamiu</span>
                            </a>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover dataTable-cacah-tamiu" id="dataTable-cacah-tamiu-wna" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Cacah Tamiu</th>
                                            <th>Nomor Paspor</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Banjar Adat</th>
                                            <th>Banjar Dinas</th>
                                            <th style="width: 12%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tamiu_wna as $wna)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $wna->nomor_cacah_tamiu }}</td>
                                                <td>{{ $wna->wna->nomor_paspor }}</td>
                                                <td>{{ $wna->wna->nama }}</td>
                                                <td>{{ ucwords(str_replace('_', ' ', $wna->wna->jenis_kelamin)) }}</td>
                                                <td>{{ $wna->banjar_adat->nama_banjar_adat }}</td>
                                                <td>{{ $wna->banjar_dinas->nama_banjar_dinas }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('desa-cacah-tamiu-wna-detail', $wna->id) }}"><i class="fas fa-eye"></i></a>
                                                    <a class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('desa-cacah-tamiu-wna-edit', $wna->id) }}"><i class="fas fa-edit"></i></a>
                                                    <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_tamiu_wna({{ $wna->id }})"><i class="fas fa-trash"></i></button>
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
    <form id="form-delete-tamiu-wni" method="post" action="/">
        @method('delete')
        @csrf
    </form>

    <form id="form-delete-tamiu-wna" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection

@push('js')
    {{-- ALERT --}}
    @if($message = Session::get('success'))
        @if($status = Session::get('tamiu'))
            <script>
                $(document).ready(function(){
                    $('#wni').removeClass('active');
                    $('#wni_tab').removeClass('active');
                    $('#wna_tab').addClass('active');
                    $('#wna').tab('show');
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
    <script>
        function delete_tamiu_wni(id){
            Swal.fire({
                title: 'Hapus Tamiu',
                text: "Apakah anda yakin ingin menghapus Tamiu ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-tamiu-wni-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-tamiu-wni').attr("action", url);
                        $('#form-delete-tamiu-wni').submit();
                    }
                })
        }

        function delete_tamiu_wna(id){
            Swal.fire({
                title: 'Hapus Tamiu',
                text: "Apakah anda yakin ingin menghapus Tamiu ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-cacah-tamiu-wna-delete', ":id") }}";
                        url = url.replace(':id', id)
                        $('#form-delete-tamiu-wna').attr("action", url);
                        $('#form-delete-tamiu-wna').submit();
                    }
                })
        }
        
        $(document).ready( function () {
            $(".dataTable-cacah-tamiu").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari Tamiu...",
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

            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-tamiu').addClass('active');
        });
    </script>
@endpush