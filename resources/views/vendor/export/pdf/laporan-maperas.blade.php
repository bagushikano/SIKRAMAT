<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        html, body {
            font-family: 'Times New Roman', sans-serif;
        }
        .table {
            width: 100%;
            border-spacing: 0px;
            border-collapse: separate;
            border: 0.1px solid black;
        }
        .table td{
            border: 0.1px solid black;
            padding: 0.5rem;
        }
        .table th{
            border: 0.1px solid black;
            padding: 0.5rem;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right
        }
        .my-auto {
            margin: auto;
        }
        .mx-3 {
            margin: 1rem 0rem
        }
        .mt-2 {
            margin-top: 0.5rem;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .mt-5 {
            margin-top: 3rem;
        }
        .fs-8 {
            font-size: 8pt;
        }
        .fs-10 {
            font-size: 10pt;
        }
        .fs-12 {
            font-size: 12pt
        }
        .fs-14 {
            font-size: 14pt
        }
        .fw-bold {
            font-weight: bold;
        }
        .border {
            border: 1rem solid black;
        }
        .table-none {
            width: 100%;
            border-spacing: 0px;
            border-collapse: separate;
            border: none;
        }
        .table-none td{
            border: none;
            padding: 0.5rem;
            font-weight: normal;
        }
        .table-none th{
            border: none;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA MAPERAS</p>
    <p class="my-auto fw-bold text-center fs-10">BANJAR ADAT {{ strtoupper($data['banjar_adat']->nama_banjar_adat) }}</p>
    <p class="my-auto fw-bold text-center fs-10">DESA ADAT {{ strtoupper($data['desa_adat']->desadat_nama) }}</p>
    <table class="table mt-5 fs-8">
        <thead class="text-center">
            <tr class="text-center">
                <th style="width: 1.0rem">NO</th>
                <th style="width: 2.0rem">NO. <br>MAPERAS</th>
                <th style="width: 3.0rem">JENIS<br>MAPERAS</th>
                <th style="width: 7.0rem">Nama</th>
                <th style="width: 6.0rem">ORANG TUA<br>LAMA</th>
                <th style="width: 6.0rem">ORANG TUA<br>BARU</th>
                <th style="width: 2.0rem">TANGGAL<br>MAPERAS</th>
                <th style="width: 5.0rem">KETERANGAN</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($data['maperas'] as $maperas)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $maperas->nomor_maperas ?? '-' }}</td>
                    <td>{{ $maperas->jenis }}</td>
                    @if($maperas->jenis_maperas == 'campuran_keluar')
                        <td>
                            @if($maperas->cacah_krama_mipil_lama->penduduk->gelar_depan != '')
                                {{ $maperas->cacah_krama_mipil_lama->penduduk->gelar_depan }}
                            @endif
                                {{ $maperas->cacah_krama_mipil_lama->penduduk->nama ?? '-' }}@if($maperas->cacah_krama_mipil_lama->penduduk->gelar_belakang != ''), {{ $maperas->cacah_krama_mipil_lama->penduduk->gelar_belakang }}@endif
                        </td>
                    @else
                        <td>
                            @if($maperas->cacah_krama_mipil_baru->penduduk->gelar_depan != '')
                                {{ $maperas->cacah_krama_mipil_baru->penduduk->gelar_depan }}
                            @endif
                                {{ $maperas->cacah_krama_mipil_baru->penduduk->nama ?? '-' }}@if($maperas->cacah_krama_mipil_baru->penduduk->gelar_belakang != ''), {{ $maperas->cacah_krama_mipil_baru->penduduk->gelar_belakang }}@endif
                        </td>
                    @endif

                    @if($maperas->jenis_maperas == 'campuran_keluar')
                        <td>
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
                        <td>
                            <span>
                                {{ $maperas->nama_ayah_baru }}
                            </span>
                            dan
                            <span>
                                {{ $maperas->nama_ibu_baru }}
                            </span>
                        </td>
                    @elseif($maperas->jenis_maperas == 'campuran_masuk')
                        <td>
                            <span>
                                {{ $maperas->nama_ayah_lama }}
                            </span>
                            dan
                            <span>
                                {{ $maperas->nama_ibu_lama }}
                            </span>
                        </td>
                        <td>
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
                        <td>
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
                        <td>
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


                    <td>{{ Carbon\Carbon::parse($maperas->tanggal_maperas)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ $maperas->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</body>
</html>
