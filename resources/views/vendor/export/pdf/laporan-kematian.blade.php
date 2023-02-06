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
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA KEMATIAN</p>
    <p class="my-auto fw-bold text-center fs-10">BANJAR ADAT {{ strtoupper($data['banjar_adat']->nama_banjar_adat) }}</p>
    <p class="my-auto fw-bold text-center fs-10">DESA ADAT {{ strtoupper($data['banjar_adat']->nama_banjar_adat) }}</p>
    <table class="table mt-5 fs-8">
        <thead class="text-center">
            <tr>
                <th>NO</th>
                <th style="width: 50px">NO. <br>KEMATIAN</th>
                <th style="width: 150px">NAMA</th>
                <th style="width: 60px">TANGGAL<br>KEMATIAN</th>
                <th style="width: 50px">JENIS<br>KELAMIN</th>
                <th style="width: 50px">TEMPEKAN</th>
                <th style="width: 85px">PENYEBAB<br>KEMATIAN</th>
                <th style="width: 85px">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['kematian'] as $kematian)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kematian->nomor_suket_kematian ?? '-' }}</td>
                    <td>
                    @if($kematian->cacah_krama_mipil->penduduk->gelar_depan != '')
                        {{ $kematian->cacah_krama_mipil->penduduk->gelar_depan }}
                    @endif
                        {{ $kematian->cacah_krama_mipil->penduduk->nama ?? '-' }}@if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $kematian->cacah_krama_mipil->penduduk->gelar_belakang }}@endif
                    </td>
                    <td>{{ Carbon\Carbon::parse($kematian->tanggal_kematian)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ ucfirst($kematian->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                    <td>{{ $kematian->cacah_krama_mipil->tempekan->nama_tempekan ?? '-' }}</td>
                    <td>{{ $kematian->penyebab_kematian ?? '-' }}</td>
                    <td>{{ $kematian->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</body>
</html>
