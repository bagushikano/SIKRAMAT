@extends('layouts.banjar.banjar')

@push('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>

    <style>
        .scrolledTable{ 
            overflow-y: auto;
            clear:both; 
        }
    </style>
@endpush

@section('title', 'Laporan Data Cacah Krama Mipil')

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
                                Laporan Data Cacah Krama Mipil
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-laporan-cacah-home') }}" class="text-decoration-none text-dark">Laporan Data Cacah Krama</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Cacah Krama Mipil</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="accordion" id="accordionGrafikTempekan">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_tempekan">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_tempekan" aria-expanded="true" aria-controls="collapse_grafik_tempekan">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Tempekan
                        </button>
                    </div>
                    <div id="collapse_grafik_tempekan" class="collapse hide" aria-labelledby="grafik_tempekan" data-parent="#accordionGrafikTempekan">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="tempekan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion mt-4" id="accordionGrafikPekerjaan">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_pekerjaan">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_pekerjaan" aria-expanded="true" aria-controls="collapse_grafik_pekerjaan">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Pekerjaan
                        </button>
                    </div>
                    <div id="collapse_grafik_pekerjaan" class="collapse hide" aria-labelledby="grafik_pekerjaan" data-parent="#accordionGrafikPekerjaan">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="pekerjaan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion mt-4" id="accordionGrafikPendidikan">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_pendidikan">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_pendidikan" aria-expanded="true" aria-controls="collapse_grafik_pendidikan">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Pendidikan Tertinggi
                        </button>
                    </div>
                    <div id="collapse_grafik_pendidikan" class="collapse hide" aria-labelledby="grafik_pendidikan" data-parent="#accordionGrafikPendidikan">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="pendidikan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion mt-4" id="accordionGrafikGoldar">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_goldar">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_goldar" aria-expanded="true" aria-controls="collapse_grafik_goldar">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Golongan Darah
                        </button>
                    </div>
                    <div id="collapse_grafik_goldar" class="collapse hide" aria-labelledby="grafik_goldar" data-parent="#accordionGrafikGoldar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="goldar"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-header-actions mt-4">
                <div class="card-header d-flex justify-content-center">
                    <p class="text-dark my-auto">Laporan Data Cacah Krama Mipil</p>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover small" id="tb_report">
                        <thead class="align-middle text-primary small">
                            <tr class="text-center">
                                <th class="align-middle">No</th>
                                <th class="align-middle">Nomor Cacah<br>Krama Mipil</th>
                                <th class="align-middle">Nama<br>Lengkap</th>
                                <th class="align-middle">Tempat<br>Tanggal Lahir</th>
                                <th class="align-middle">Jenis<br>Kelamin</th>
                                <th class="align-middle">Tempekan</th>
                                <th class="align-middle">Pendidikan<br>Tertinggi</th>
                                <th class="align-middle">Pekerjaan</th>
                                <th class="align-middle">Golongan Darah</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($data['krama_mipil'] as $krama_mipil)
                                <tr class="align-middle">
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $krama_mipil->nomor_cacah_krama_mipil }}</td>
                                    <td class="align-middle">{{ $krama_mipil->nama }}</td>
                                    <td class="align-middle">{{ $krama_mipil->tempat_lahir }},<br>{{ \Carbon\Carbon::parse($krama_mipil->tanggal_lahir)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="align-middle">{{ ucfirst($krama_mipil->jenis_kelamin) }}</td>
                                    <td class="align-middle">{{ $krama_mipil->nama_tempekan ?? '-' }}</td>
                                    <td class="align-middle">{{ $krama_mipil->jenjang_pendidikan }}</td>
                                    <td class="align-middle">{{ $krama_mipil->profesi }}</td>
                                    <td class="align-middle text-center">{{ $krama_mipil->golongan_darah }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    <div class="row">
                        <div class="col-12 my-auto">
                            <a href="{{ route('banjar-laporan-cacah-home') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
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
            $('#nav-link-laporan-cacah').addClass('active');

            let tb_report = $("#tb_report").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "pageLength": 25,
                "lengthMenu": [[25, 50, 75, 100], [25, 50, 75, 100]],
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sSearchPlaceholder": "Rekap cacah krama mipil ...",
                    "sZeroRecords": "Tidak terdapat data rekap cacah krama mipil",
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
            const tempekan_data = {
                labels: {!!json_encode($data['nama_tempekan'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_tempekan_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_tempekan_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                ]
            };
            const tempekan_config = {
                type: 'bar',
                data: tempekan_data,
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
                                text: "Nama Tempekan"
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
            let jenisTempekanChart = new Chart(
                document.getElementById('tempekan'),
                tempekan_config
            );
        });
    </script>

    <script>
        $(document).ready( function () {
            const pekerjaan_data = {
                labels: {!!json_encode($data['nama_profesi'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_profesi_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_profesi_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                ]
            };
            const pekerjaan_config = {
                type: 'bar',
                data: pekerjaan_data,
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
                                text: "Jenis Pekerjaan"
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
            let jenisPekerjaanChart = new Chart(
                document.getElementById('pekerjaan'),
                pekerjaan_config
            );
        });
    </script>

    <script>
        $(document).ready( function () {
            const pendidikan_data = {
                labels: {!!json_encode($data['jenjang_pendidikan'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_pendidikan_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_pendidikan_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                ]
            };
            const pendidikan_config = {
                type: 'bar',
                data: pendidikan_data,
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
                                text: "Jenis Pendidikan"
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
            let jenisPendidikanChart = new Chart(
                document.getElementById('pendidikan'),
                pendidikan_config
            );
        });
    </script>

    <script>
        $(document).ready( function () {
            const goldar_data = {
                labels: {!!json_encode($data['golongan_darah'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_goldar_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_goldar_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                ]
            };
            const goldar_config = {
                type: 'bar',
                data: goldar_data,
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
                                text: "Golongan Darah"
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
            let jenisGoldarChart = new Chart(
                document.getElementById('goldar'),
                goldar_config
            );
        });
    </script>
@endpush