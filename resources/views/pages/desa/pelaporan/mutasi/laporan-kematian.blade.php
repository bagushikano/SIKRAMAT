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

@section('title', 'Laporan Data Kematian')

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
                                Laporan Data Kematian
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('Pelaporan Mutasi') }}" class="text-decoration-none text-dark">Laporan Data Mutasi Kependudukan</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Kematian</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="accordion" id="accordionGrafikBulanLahir">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_kematian">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_bulan_lahir" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Kematian Per Bulan
                        </button>
                    </div>
                    <div id="collapse_grafik_bulan_lahir" class="collapse hide" aria-labelledby="grafik_kematian" data-parent="#accordionGrafikBulanLahir">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="kematian"></canvas>
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
                            Grafik Data Kematian Per Banjar Adat
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
                    <p class="text-dark my-auto">Laporan Data Kematian</p>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover small" id="tb_report">
                        <thead class="align-middle text-primary small">
                            <tr class="text-center">
                                <th class="align-middle">No</th>
                                <th class="align-middle" style="width: 2.0rem">No. <br>Kematian</th>
                                <th class="align-middle">Nama</th>
                                <th class="align-middle" style="width: 2.0rem">Tanggal<br>Kematian</th>
                                <th class="align-middle" style="width: 2.0rem">Jenis<br>Kelamin</th>
                                <th class="align-middle" style="width: 4.0rem">Banjar Adat</th>
                                <th class="align-middle" style="width: 12.0rem">Penyebab<br>Kematian</th>
                                <th class="align-middle" style="width: 12.0rem">Keterangan</th>

                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($data['kematian'] as $kematian)
                                <tr class="align-middle">
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $kematian->nomor_suket_kematian ?? '-' }}</td>
                                    <td class="align-middle">
                                    @if($kematian->cacah_krama_mipil->penduduk->gelar_depan != '')
                                        {{ $kematian->cacah_krama_mipil->penduduk->gelar_depan }} 
                                    @endif
                                        {{ $kematian->cacah_krama_mipil->penduduk->nama ?? '-' }}@if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $kematian->cacah_krama_mipil->penduduk->gelar_belakang }}@endif                                    
                                    </td>
                                    <td class="align-middle">{{ Carbon\Carbon::parse($kematian->tanggal_kematian)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="align-middle">{{ ucfirst($kematian->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                    <td class="align-middle">{{ $kematian->cacah_krama_mipil->banjar_adat->nama_banjar_adat ?? '-' }}</td>
                                    <td class="align-middle">{{ $kematian->penyebab_kematian ?? '-' }}</td>
                                    <td class="align-middle">{{ $kematian->keterangan ?? '-' }}</td>
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
                    "sSearchPlaceholder": "Rekap data kematian ...",
                    "sZeroRecords": "Tidak terdapat data rekap data kematian",
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

    {{-- Grafik Bulan Mati --}}
    <script>
        $(document).ready( function () {
            var haha = {!!json_encode($data['daftar_bulan'])!!};
            const kematian_data = {
                labels: {!!json_encode($data['daftar_bulan'])!!},
                datasets: [
                    {
                        data: {!!json_encode($data['grafik_bulan_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_bulan_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                ]
            };
            const kematian_config = {
                type: 'bar',
                data: kematian_data,
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
            let jeniskematianChart = new Chart(
                document.getElementById('kematian'),
                kematian_config
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
                        data: {!!json_encode($data['grafik_banjar_laki']['jumlah'])!!},
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: {!!json_encode($data['grafik_banjar_perempuan']['jumlah'])!!},
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
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