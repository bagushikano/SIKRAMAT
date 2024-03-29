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
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA TAMIU</p>
    <p class="my-auto fw-bold text-center fs-10">DESA ADAT {{ strtoupper(session()->get('desa_adat_nama')) }}</p>
    <table class="table mt-5 fs-8" style="font-size: 0.55rem">
        <thead class="text-center">
            <tr>
                <th>NO</th>
                <th>NOMOR<br> TAMIU</th>
                <th>NAMA<br>LENGKAP</th>
                <th>TEMPAT<br>TANGGAL LAHIR</th>
                <th>JENIS<br>KELAMIN</th>
                <th>BANJAR ADAT</th>
                <th>BANJAR DINAS</th>
                <th>PENDIDIKAN<br>TERTINGGI</th>
                <th>PEKERJAAN<br></th>
                <th>GOLONGAN<br>DARAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['tamiu'] as $tamiu)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $tamiu->nomor_tamiu }}</td>
                    <td>{{ $tamiu->nama }}</td>
                    <td>{{ $tamiu->tempat_lahir }},<br>{{ \Carbon\Carbon::parse($tamiu->tanggal_lahir)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ ucfirst($tamiu->jenis_kelamin) }}</td>
                    <td>{{ $tamiu->nama_banjar_adat ?? '-' }}</td>
                    <td>{{ $tamiu->nama_banjar_dinas ?? '-' }}</td>
                    <td>{{ $tamiu->jenjang_pendidikan }}</td>
                    <td>{{ $tamiu->profesi }}</td>
                    <td class="text-center">{{ $tamiu->golongan_darah }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</body>
</html>