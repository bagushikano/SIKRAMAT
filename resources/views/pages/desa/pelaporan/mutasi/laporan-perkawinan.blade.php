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

@section('title', 'Laporan Data Perkawinan')

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
                                Laporan Data Perkawinan
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('Pelaporan Mutasi') }}" class="text-decoration-none text-dark">Laporan Data Mutasi Kependudukan</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Perkawinan</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="accordion" id="accordionGrafikBulanLahir">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_perkawinan">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_bulan_lahir" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Perkawinan Per Bulan
                        </button>
                    </div>
                    <div id="collapse_grafik_bulan_lahir" class="collapse hide" aria-labelledby="grafik_perkawinan" data-parent="#accordionGrafikBulanLahir">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="perkawinan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion mt-4" id="accordionGrafikJenisKawin">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_jenis_perkawinan">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_jenis_perkawinan" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Perkawinan Per Jenis Perkawinan
                        </button>
                    </div>
                    <div id="collapse_grafik_jenis_perkawinan" class="collapse hide" aria-labelledby="grafik_jenis_perkawinan" data-parent="#accordionGrafikJenisKawin">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="jenis_perkawinan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion mt-4" id="accordionGrafikBanjar">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_banjar">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_banjar" aria-expanded="true" aria-controls="collapse_grafik_banjar">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Perkawinan Per Banjar Adat
                        </button>
                    </div>
                    <div id="collapse_grafik_banjar" class="collapse hide" aria-labelledby="grafik_banjar" data-parent="#accordionGrafikBanjar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="banjar"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-header-actions mt-4">
                <div class="card-header d-flex justify-content-center">
                    <p class="text-dark my-auto">Laporan Data Perkawinan</p>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover small" id="tb_report">
                        <thead class="align-middle text-primary small">
                            <tr class="text-center">
                                <th class="align-middle">No</th>
                                <th class="align-middle" style="width: 2.0rem">No. <br>Perkawinan</th>
                                <th class="align-middle" style="width: 4.0rem">Jenis<br>Perkawinan</th>
                                <th class="align-middle">Nama Purusa</th>
                                <th class="align-middle">Nama Pradana</th>
                                <th class="align-middle" style="width: 2.0rem">Tanggal<br>Perkawinan</th>
                                <th class="align-middle" style="width: 4.0rem">Banjar<br>Adat</th>
                                <th class="align-middle" style="width: 12.0rem">Keterangan</th>

                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($data['perkawinan'] as $perkawinan)
                                <tr class="align-middle">
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $perkawinan->nomor_perkawinan ?? '-' }}</td>
                                    <td class="align-middle">{{ $perkawinan->jenis }}</td>
                                    @if($perkawinan->jenis_perkawinan == 'campuran_keluar')
                                        <td class="align-middle">
                                            {{ $perkawinan->nama_pasangan }}
                                        </td>
                                    @else
                                        <td class="align-middle">
                                            @if($perkawinan->purusa->penduduk->gelar_depan != '')
                                                {{ $perkawinan->purusa->penduduk->gelar_depan }} 
                                            @endif
                                                {{ $perkawinan->purusa->penduduk->nama ?? '-' }}@if($perkawinan->purusa->penduduk->gelar_belakang != ''), {{ $perkawinan->purusa->penduduk->gelar_belakang }}@endif                                    
                                        </td>
                                    @endif
                                    <td class="align-middle">
                                        @if($perkawinan->pradana->penduduk->gelar_depan != '')
                                            {{ $perkawinan->pradana->penduduk->gelar_depan }} 
                                        @endif
                                            {{ $perkawinan->pradana->penduduk->nama ?? '-' }}@if($perkawinan->pradana->penduduk->gelar_belakang != ''), {{ $perkawinan->pradana->penduduk->gelar_belakang }}@endif                                    
                                    </td>
                                    <td class="align-middle">{{ Carbon\Carbon::parse($perkawinan->tanggal_perkawinan)->locale('id')->translatedFormat('d M Y') }}</td>
                                    @if($perkawinan->jenis == 'Beda Banjar Adat (Keluar)' || $perkawinan->jenis == 'Campuran Keluar')
                                        <td class="align-middle">{{ $perkawinan->banjar_adat_pradana->nama_banjar_adat ?? '-' }}</td>
                                    @else 
                                        <td class="align-middle">{{ $perkawinan->banjar_adat_purusa->nama_banjar_adat ?? '-' }}</td>
                                    @endif
                                    <td class="align-middle">{{ $perkawinan->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    <div class="row">
                        <div class="col-12 my-auto">
                            <a href="{{ route('Pelaporan Mutasi') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
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
                    "sSearchPlaceholder": "Rekap data perkawinan ...",
                    "sZeroRecords": "Tidak terdapat data rekap data perkawinan",
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

    {{-- Grafik Bulan --}}
    <script>
        $(document).ready( function () {
            const perkawinan_data = {
                labels: {!!json_encode($data['daftar_bulan'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_bulan']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perkawinan',
                    }
                ]
            };
            const perkawinan_config = {
                type: 'bar',
                data: perkawinan_data,
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
            let jenisperkawinanChart = new Chart(
                document.getElementById('perkawinan'),
                perkawinan_config
            );
        });
    </script>

    {{-- Grafik Jenis --}}
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
                        label: 'Perkawinan',
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
                                text: "Jenis Perkawinan"
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
            let jenisperkawinanChart = new Chart(
                document.getElementById('jenis_perkawinan'),
                jenis_config
            );
        });
    </script>

    {{-- Grafik Banjar Adat --}}
    <script>
        $(document).ready( function () {
            const banjar_data = {
                labels: {!!json_encode($data['daftar_banjar'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_banjar']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perkawinan',
                    }
                ]
            };
            const banjar_config = {
                type: 'bar',
                data: banjar_data,
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
                                text: "Banjar Adat"
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
            let banjarChart = new Chart(
                document.getElementById('banjar'),
                banjar_config
            );
        });
    </script>
@endpush