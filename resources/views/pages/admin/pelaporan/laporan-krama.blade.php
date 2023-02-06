@extends('layouts.admin.admin')

@push('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>

    <style>
        .scrolledTable{ 
            overflow-y: auto;
            clear:both; 
        }
    </style>
@endpush

@section('title', 'Laporan Data Krama')

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
                                Laporan Data Krama
                            </h1>
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin-laporan-home') }}" class="text-decoration-none text-dark">Laporan Data Kependudukan</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Laporan Data Krama</li>
                    </ol>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="accordion" id="accordionGrafikBulanLahir">
                <div class="card">
                    <div class="card-header bg-white border-bottom" id="grafik_kelahiran">
                        <button class="btn btn-sm btn-primary shadow-none px-2 text-left" type="button" data-toggle="collapse" data-target="#collapse_grafik_bulan_lahir" aria-expanded="true" aria-controls="collapse_grafik_bulan_lahir">
                            <i class="fa-solid fa-chart-area mr-2"></i>
                            Grafik Data Krama
                        </button>
                    </div>
                    <div id="collapse_grafik_bulan_lahir" class="collapse hide" aria-labelledby="grafik_kelahiran" data-parent="#accordionGrafikBulanLahir">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 pl-1">
                                    <canvas id="krama"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-header-actions mt-4">
                <div class="card-header d-flex justify-content-center">
                    <p class="text-dark my-auto">Laporan Data Krama</p>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover small" id="tb_report">
                        <thead class="align-middle text-primary small">
                            <tr class="text-center">
                                <th class="align-middle" rowspan="2" style="width: 1.0rem">No</th>
                                <th class="align-middle" rowspan="2">Desa Adat</th>
                                <th class="align-middle" colspan="3">Krama Mipil</th>
                                <th class="align-middle" colspan="3">Krama Tamiu</th>
                                <th class="align-middle" colspan="3">Tamiu</th>
                            </tr>
                            <tr class="text-center">
                                <th class="align-middle" style="width:4.0rem">Laki-laki</th>
                                <th class="align-middle" style="width:4.0rem">Perempuan</th>
                                <th class="align-middle" style="width:4.0rem">Jumlah</th>
                                <th class="align-middle" style="width:4.0rem">Laki-laki</th>
                                <th class="align-middle" style="width:4.0rem">Perempuan</th>
                                <th class="align-middle" style="width:4.0rem">Jumlah</th>
                                <th class="align-middle" style="width:4.0rem">Laki-laki</th>
                                <th class="align-middle" style="width:4.0rem">Perempuan</th>
                                <th class="align-middle" style="width:4.0rem">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($data['desa_adat'] as $desa_adat)
                                <tr class="align-middle text-center">
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle text-left">{{ $desa_adat->desadat_nama }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_mipil_laki }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_mipil_perempuan }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_mipil }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_tamiu_laki }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_tamiu_perempuan }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_krama_tamiu }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_tamiu_laki }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_tamiu_perempuan }}</td>
                                    <td class="align-middle">{{ $desa_adat->jumlah_tamiu }}</td>
                                </tr>
                            @endforeach
                            <tr class="text-center">
                                <th colspan="2" class="text-primary">Jumlah Keseluruhan</th>
                                <td style="display: none"></td>
                                <td>{{ $data['total_krama_mipil_laki'] }}</td>
                                <td>{{ $data['total_krama_mipil_perempuan'] }}</td>
                                <td>{{ $data['total_krama_mipil'] }}</td>
                                <td>{{ $data['total_krama_tamiu_laki'] }}</td>
                                <td>{{ $data['total_krama_tamiu_perempuan'] }}</td>
                                <td>{{ $data['total_krama_tamiu'] }}</td>
                                <td>{{ $data['total_tamiu_laki'] }}</td>
                                <td>{{ $data['total_tamiu_perempuan'] }}</td>
                                <td>{{ $data['total_tamiu'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    <div class="row">
                        <div class="col-12 my-auto">
                            <a href="{{ route('admin-laporan-home') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
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
            $('#nav-link-laporan').addClass('active');      

            let tb_report = $("#tb_report").DataTable({
                "aaSorting": [],
                "ordering": false,
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "pageLength": 25,
                "lengthMenu": [[25, 50, 75, 100], [25, 50, 75, 100]],
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sSearchPlaceholder": "Rekap data Krama ...",
                    "sZeroRecords": "Tidak terdapat data rekap data Krama",
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
            const krama_data = {
                labels: ['Krama Mipil', 'Krama Tamiu', 'Tamiu'],
                datasets: [
                    {
                        data: ['{{ $data["total_krama_mipil_laki"] }}', '{{ $data["total_krama_tamiu_laki"] }}', '{{ $data["total_tamiu_laki"] }}'],
                        backgroundColor: 'rgb(247, 212, 134)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Laki-laki',
                    },
                    {
                        data: ['{{ $data["total_krama_mipil_perempuan"] }}', '{{ $data["total_krama_tamiu_perempuan"] }}', '{{ $data["total_tamiu_perempuan"] }}'],
                        backgroundColor: 'rgb(130, 183, 245)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Perempuan',
                    },
                    {
                        data: ['{{ $data["total_krama_mipil"] }}', '{{ $data["total_krama_tamiu"] }}', '{{ $data["total_tamiu"] }}'],
                        backgroundColor: 'rgb(158, 178, 59)',
                        borderColor: 'rgb(255, 255, 255)',
                        tension: 1,
                        maxBarThickness: 90,
                        label: 'Total',
                    },
                ]
            };
            const krama_config = {
                type: 'bar',
                data: krama_data,
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
                                text: "Jenis"
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
            let kramaChart = new Chart(
                document.getElementById('krama'),
                krama_config
            );
        });
    </script>
@endpush