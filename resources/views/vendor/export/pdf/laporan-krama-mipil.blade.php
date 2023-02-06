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
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA KRAMA MIPIL</p>
    <p class="my-auto fw-bold text-center fs-10">BANJAR ADAT {{ strtoupper($data['banjar_adat']->nama_banjar_adat) }}</p>
    <p class="my-auto fw-bold text-center fs-10">DESA ADAT {{ strtoupper($data['desa_adat']->desadat_nama) }}</p>
    <table class="table mt-5 fs-8">
        <thead class="text-center">
            <tr>
                <th>NO</th>
                <th>NOMOR<br>KRAMA MIPIL</th>
                <th>NAMA<br>LENGKAP</th>
                <th>TEMPAT<br>TANGGAL LAHIR</th>
                <th>JENIS<br>KELAMIN</th>
                <th>TEMPEKAN</th>
                <th>PENDIDIKAN<br>TERTINGGI</th>
                <th>PEKERJAAN<br></th>
                <th>GOLONGAN<br>DARAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['krama_mipil'] as $krama_mipil)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $krama_mipil->nomor_krama_mipil }}</td>
                    <td>{{ $krama_mipil->nama }}</td>
                    <td>{{ $krama_mipil->tempat_lahir }},<br>{{ \Carbon\Carbon::parse($krama_mipil->tanggal_lahir)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ ucfirst($krama_mipil->jenis_kelamin) }}</td>
                    <td>{{ $krama_mipil->nama_tempekan ?? '-' }}</td>
                    <td>{{ $krama_mipil->jenjang_pendidikan }}</td>
                    <td>{{ $krama_mipil->profesi }}</td>
                    <td class="text-center">{{ $krama_mipil->golongan_darah }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</body>
</html>
