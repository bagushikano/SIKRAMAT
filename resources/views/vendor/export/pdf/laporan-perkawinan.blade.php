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
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA PERKAWINAN</p>
    <p class="my-auto fw-bold text-center fs-10">BANJAR ADAT {{ strtoupper($data['banjar_adat']->nama_banjar_adat) }}</p>
    <p class="my-auto fw-bold text-center fs-10">DESA ADAT {{ strtoupper($data['desa_adat']->desadat_nama) }}</p>
    <table class="table mt-5 fs-8">
        <thead class="text-center">
            <tr>
                <th>NO</th>
                <th style="width: 50px">NO. <br>PERKAWINAN</th>
                <th style="width: 50px">JENIS<br>PERKAWINAN</th>
                <th style="width: 125px">NAMA<br>PURUSA</th>
                <th style="width: 125px">NAMA<br>PRADANA</th>
                <th style="width: 60px">TANGGAL<br>PERKAWINAN</th>
                <th style="width: 100px">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['perkawinan'] as $perkawinan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $perkawinan->nomor_perkawinan ?? '-' }}</td>
                    <td>{{ $perkawinan->jenis }}</td>
                    @if($perkawinan->jenis_perkawinan == 'campuran_keluar')
                        <td>
                            {{ $perkawinan->nama_pasangan }}
                        </td>
                    @else
                        <td>
                            @if($perkawinan->purusa->penduduk->gelar_depan != '')
                                {{ $perkawinan->purusa->penduduk->gelar_depan }}
                            @endif
                                {{ $perkawinan->purusa->penduduk->nama ?? '-' }}@if($perkawinan->purusa->penduduk->gelar_belakang != ''), {{ $perkawinan->purusa->penduduk->gelar_belakang }}@endif
                        </td>
                    @endif
                    <td>
                        @if($perkawinan->pradana->penduduk->gelar_depan != '')
                            {{ $perkawinan->pradana->penduduk->gelar_depan }}
                        @endif
                            {{ $perkawinan->pradana->penduduk->nama ?? '-' }}@if($perkawinan->pradana->penduduk->gelar_belakang != ''), {{ $perkawinan->pradana->penduduk->gelar_belakang }}@endif
                    </td>
                    <td>{{ Carbon\Carbon::parse($perkawinan->tanggal_perkawinan)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ $perkawinan->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</body>
</html>
