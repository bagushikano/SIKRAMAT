@extends('layouts.desa.desa')

@push('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>

    <style>
        .scrolledTable{ 
            overflow-y: auto;
            clear:both; 
        }
    </style>
@endpush

@section('title', 'Laporan Data Maperas')

@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fas fa-file mr-2"></i>
                                </div>
                                Laporan Data Maperas
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-laporan-mutasi-home') }}" class="text-decoration-none text-dark">Laporan Data Mutasi Kependudukan</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Maperas</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="accordion" id="accordionGrafikBulanLahir">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_maperas">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_bulan_lahir" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Maperas Per Bulan
                        </button>
                    </div>
                    <div id="collapse_grafik_bulan_lahir" class="collapse hide" aria-labelledby="grafik_maperas" data-parent="#accordionGrafikBulanLahir">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="maperas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion mt-4" id="accordionGrafikJenisKawin">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_jenis_maperas">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_jenis_maperas" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Maperas Per Jenis Maperas
                        </button>
                    </div>
                    <div id="collapse_grafik_jenis_maperas" class="collapse hide" aria-labelledby="grafik_jenis_maperas" data-parent="#accordionGrafikJenisKawin">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="jenis_maperas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-header-actions mt-4">
                <div class="card-header d-flex justify-content-center">
                    <p class="text-dark my-auto">Laporan Data Maperas</p>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover small" id="tb_report">
                        <thead class="align-middle text-primary small">
                            <tr class="text-center">
                                <th class="align-middle" style="width: 1.0rem">No</th>
                                <th class="align-middle" style="width: 2.0rem">No. <br>Maperas</th>
                                <th class="align-middle" style="width: 5.0rem">Jenis<br>Maperas</th>
                                <th class="align-middle">Nama</th>
                                <th class="align-middle"  style="width: 10.0rem">Orang Tua<br>Lama</th>
                                <th class="align-middle"  style="width: 10.0rem">Orang Tua<br>Baru</th>
                                <th class="align-middle" style="width: 4.0rem">Tanggal<br>Maperas</th>
                                <th class="align-middle" style="width: 8.0rem">Keterangan</th>

                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($data['maperas'] as $maperas)
                                <tr class="align-middle">
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $maperas->nomor_maperas ?? '-' }}</td>
                                    <td class="align-middle">{{ $maperas->jenis }}</td>
                                    @if($maperas->jenis_maperas == 'campuran_keluar')
                                        <td class="align-middle">
                                            @if($maperas->cacah_krama_mipil_lama->penduduk->gelar_depan != '')
                                                {{ $maperas->cacah_krama_mipil_lama->penduduk->gelar_depan }} 
                                            @endif
                                                {{ $maperas->cacah_krama_mipil_lama->penduduk->nama ?? '-' }}@if($maperas->cacah_krama_mipil_lama->penduduk->gelar_belakang != ''), {{ $maperas->cacah_krama_mipil_lama->penduduk->gelar_belakang }}@endif                                    
                                        </td>
                                    @else
                                        <td class="align-middle">
                                            @if($maperas->cacah_krama_mipil_baru->penduduk->gelar_depan != '')
                                                {{ $maperas->cacah_krama_mipil_baru->penduduk->gelar_depan }} 
                                            @endif
                                                {{ $maperas->cacah_krama_mipil_baru->penduduk->nama ?? '-' }}@if($maperas->cacah_krama_mipil_baru->penduduk->gelar_belakang != ''), {{ $maperas->cacah_krama_mipil_baru->penduduk->gelar_belakang }}@endif                                    
                                        </td>
                                    @endif

                                    @if($maperas->jenis_maperas == 'campuran_keluar')
                                        <td class="align-middle text-wrap">
                                            <span>
                                                @if($maperas->ayah_lama->penduduk->gelar_depan != '')
                                                    {{ $maperas->ayah_lama->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ayah_lama->penduduk->nama ?? '-' }}@if($maperas->ayah_lama->penduduk->gelar_belakang != ''), {{ $maperas->ayah_lama->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                            dan 
                                            <span>
                                                @if($maperas->ibu_lama->penduduk->gelar_depan != '')
                                                    {{ $maperas->ibu_lama->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ibu_lama->penduduk->nama ?? '-' }}@if($maperas->ibu_lama->penduduk->gelar_belakang != ''), {{ $maperas->ibu_lama->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                        </td>
                                        <td class="align-middle text-wrap">
                                            <span>
                                                {{ $maperas->nama_ayah_baru }}
                                            </span>
                                            dan 
                                            <span>
                                                {{ $maperas->nama_ibu_baru }}
                                            </span>
                                        </td>
                                    @elseif($maperas->jenis_maperas == 'campuran_masuk')
                                        <td class="align-middle text-wrap">
                                            <span>
                                               {{ $maperas->nama_ayah_lama }}
                                            </span>
                                            dan 
                                            <span>
                                                {{ $maperas->nama_ibu_lama }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-wrap">
                                            <span>
                                                @if($maperas->ayah_baru->penduduk->gelar_depan != '')
                                                    {{ $maperas->ayah_baru->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ayah_baru->penduduk->nama ?? '-' }}@if($maperas->ayah_baru->penduduk->gelar_belakang != ''), {{ $maperas->ayah_baru->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                            dan 
                                            <span>
                                                @if($maperas->ibu_baru->penduduk->gelar_depan != '')
                                                    {{ $maperas->ibu_baru->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ibu_baru->penduduk->nama ?? '-' }}@if($maperas->ibu_baru->penduduk->gelar_belakang != ''), {{ $maperas->ibu_baru->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                        </td>
                                    @else
                                        <td class="align-middle text-wrap">
                                            <span>
                                                @if($maperas->ayah_lama->penduduk->gelar_depan != '')
                                                    {{ $maperas->ayah_lama->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ayah_lama->penduduk->nama ?? '-' }}@if($maperas->ayah_lama->penduduk->gelar_belakang != ''), {{ $maperas->ayah_lama->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                            dan 
                                            <span>
                                                @if($maperas->ibu_lama->penduduk->gelar_depan != '')
                                                    {{ $maperas->ibu_lama->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ibu_lama->penduduk->nama ?? '-' }}@if($maperas->ibu_lama->penduduk->gelar_belakang != ''), {{ $maperas->ibu_lama->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                        </td>
                                        <td class="align-middle text-wrap">
                                            <span>
                                                @if($maperas->ayah_baru->penduduk->gelar_depan != '')
                                                    {{ $maperas->ayah_baru->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ayah_baru->penduduk->nama ?? '-' }}@if($maperas->ayah_baru->penduduk->gelar_belakang != ''), {{ $maperas->ayah_baru->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                            dan 
                                            <span>
                                                @if($maperas->ibu_baru->penduduk->gelar_depan != '')
                                                    {{ $maperas->ibu_baru->penduduk->gelar_depan }} 
                                                @endif
                                                    {{ $maperas->ibu_baru->penduduk->nama ?? '-' }}@if($maperas->ibu_baru->penduduk->gelar_belakang != ''), {{ $maperas->ibu_baru->penduduk->gelar_belakang }}@endif                                    
                                            </span>
                                        </td>
                                    @endif

                                    
                                    <td class="align-middle">{{ Carbon\Carbon::parse($maperas->tanggal_maperas)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="align-middle">{{ $maperas->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    <div class="row">
                        <div class="col-12 my-auto">
                            <a href="{{ route('banjar-laporan-mutasi-home') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
                                <span class="icon">
                                    <i class="fa-solid fa-circle-arrow-left"></i>
                                </span>
                                <span class="text">Kembali/Batal</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready( function () {
            $('#collapseRekap').addClass('show');
            $('#nav-link-laporan-mutasi').addClass('active');   

            let tb_report = $("#tb_report").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "pageLength": 25,
                "lengthMenu": [[25, 50, 75, 100], [25, 50, 75, 100]],
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sSearchPlaceholder": "Rekap data maperas ...",
                    "sZeroRecords": "Tidak terdapat data rekap data maperas",
                    "infoEmpty": "Menampilkan 0 Data",
                    "infoFiltered": "(dari _MAX_ Data)",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                },
                "language": {
                    "paginate": {
                        "previous": 'Sebelumnya',
                        "next": 'Berikutnya'
                    },
                    "info": "Menampilkan _END_ dari _MAX_ data",
                },
            });
            $('#tb_report').wrap("<div class='scrolledTable'></div>");
        });
    </script>

    <script>
        $(document).ready( function () {
            const maperas_data = {
                labels: {!!json_encode($data['daftar_bulan'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_bulan']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Maperas',
                    }
                ]
            };
            const maperas_config = {
                type: 'bar',
                data: maperas_data,
                options: {
                    interaction: {
                        intersect: false,
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        title: {
                            display: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: "Bulan"
                            },
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "Jumlah"
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            };
            let jenismaperasChart = new Chart(
                document.getElementById('maperas'),
                maperas_config
            );
        });
    </script>

    <script>
        $(document).ready( function () {
            const jenis_data = {
                labels: {!!json_encode($data['daftar_jenis'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_jenis']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Maperas',
                    }
                ]
            };
            const jenis_config = {
                type: 'bar',
                data: jenis_data,
                options: {
                    interaction: {
                        intersect: false,
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        title: {
                            display: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: "Jenis Maperas"
                            },
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "Jumlah"
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            };
            let jenismaperasChart = new Chart(
                document.getElementById('jenis_maperas'),
                jenis_config
            );
        });
    </script>
@endpush