@extends('layouts.krama.krama')
@push('css')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <style>
        @media (min-width: 768px) {
            .h-md-100 {
                height: 100% !important;
            }
        }
    </style>
@endpush
@section('title', 'Anggota Keluarga')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Anggota Keluarga
                            </h1>
                            <div class="page-header-subtitle">
                                Anggota Keluarga Krama Desa Adat
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            @csrf
            <div class="row">
                <div class="col-xxl-4 col-xl-12 mb-4">
                    <div class="card mh-75">
                        <div class="card-body h-100 d-flex justify-content-center py-5 py-xl-4">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <p class="text-gray-700 mb-0">Data <span class="text-primary font-weight-bold">Anggota Keluarga</span>
                                    </p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/population.png')}}" style="max-width: 25rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12 mb-4">
                    <div class="card">
                        <div class="card-header">Kepala Keluarga</div>
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
                                    <h5 for="title" class="font-weight-bold text-dark">Kedudukan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ ucwords($krama_mipil->kedudukan_krama_mipil) ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Alamat</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->penduduk->alamat }}</span>
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
                            <div class="d-flex justify-content-between mb-n2">
                                <a class="btn btn-danger btn-icon-split my-1 text-end" href="{{ route('Dashboard Krama') }}">
                                    <span class="icon">
                                        <i class="fas fa-arrow-left"></i>
                                    </span>
                                    <span class="text">Kembali</span>
                                </a>
                                <div>
                                    <a class="btn btn-primary btn-icon-split my-1 text-end" href="{{ route('Detail Krama', $krama_mipil->id) }}">
                                        <span class="icon">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                        <span class="text">Detail Krama Mipil</span>
                                    </a>
                                    <a class="btn btn-info btn-icon-split my-1 text-end" target="_blank" href="{{ route('Kartu Keluarga Krama', $krama_mipil->id) }}">
                                        <span class="icon">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <span class="text">Kartu Keluarga Krama Mipil</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Anggota Keluarga</div>
                <div class="card-body px-5 mt-3 mb-4">
                    <form id="form-create-krama-mipil" method="post" action="{{ route('banjar-cacah-krama-mipil-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                        <div class="datatable">
                        <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th style="width: 18%">No. Cacah Krama Mipil</th>
                                    <th>Nama</th>
                                    <th style="width: 15%">Status Hubungan</th>
                                    <th style="width: 15%">Tanggal Registrasi</th>
                                    <th style="width: 10%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($krama_mipil->anggota as $anggota_krama)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $anggota_krama->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                        <td>{{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $anggota_krama->status_hubungan)) }}</td>
                                        <td>{{ date('d M Y', strtotime($anggota_krama->tanggal_registrasi)) }}</td>
                                        <td class="text-center">
                                            <a button type="button" class="btn btn-primary btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('Detail Anggota', $anggota_krama->cacah_krama_mipil_id) }}"><i class="fas fa-eye"></i></a>
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
@endsection
@push('js')
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
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
            $('#link-anggota-keluarga').addClass('active');

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //DatePicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
        });
    </script>
@endpush