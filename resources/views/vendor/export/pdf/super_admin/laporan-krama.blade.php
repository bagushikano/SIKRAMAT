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
    <p class="my-auto fw-bold text-center fs-10">LAPORAN DATA KRAMA</p>
    <p class="my-auto fw-bold text-center fs-10">KECAMATAN {{ strtoupper($data['kecamatan']->name) }}</p>
    @if(str_contains($data['kabupaten']->name, 'Kota'))
        <p class="my-auto fw-bold text-center fs-10">{{ strtoupper($data['kabupaten']->name) }}</p>
    @else
        <p class="my-auto fw-bold text-center fs-10">KABUPATEN {{ strtoupper($data['kabupaten']->name) }}</p>
    @endif
    <table class="table mt-5 fs-8">
        <thead class="text-center">
            <tr class="text-center">
                <th rowspan="2" style="width: 2.0rem">NO</th>
                <th rowspan="2">DESA ADAT</th>
                <th colspan="3">KRAMA MIPIL</th>
                <th colspan="3">KRAMA TAMIU</th>
                <th colspan="3">TAMIU</th>
            </tr>
            <tr class="text-center">
                <th style="width:2.0rem">L</th>
                <th style="width:2.0rem">P</th>
                <th style="width:2.0rem">Jml</th>
                <th style="width:2.0rem">L</th>
                <th style="width:2.0rem">P</th>
                <th style="width:2.0rem">Jml</th>
                <th style="width:2.0rem">L</th>
                <th style="width:2.0rem">P</th>
                <th style="width:2.0rem">Jml</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['desa_adat'] as $desa_adat)
                <tr class="align-middle text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $desa_adat->desadat_nama }}</td>
                    <td>{{ $desa_adat->jumlah_krama_mipil_laki }}</td>
                    <td>{{ $desa_adat->jumlah_krama_mipil_perempuan }}</td>
                    <td>{{ $desa_adat->jumlah_krama_mipil }}</td>
                    <td>{{ $desa_adat->jumlah_krama_tamiu_laki }}</td>
                    <td>{{ $desa_adat->jumlah_krama_tamiu_perempuan }}</td>
                    <td>{{ $desa_adat->jumlah_krama_tamiu }}</td>
                    <td>{{ $desa_adat->jumlah_tamiu_laki }}</td>
                    <td>{{ $desa_adat->jumlah_tamiu_perempuan }}</td>
                    <td>{{ $desa_adat->jumlah_tamiu }}</td>
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
    <p class="fw-bold mt-2" style="font-size: 0.7rem">Dokumen ini dicetak pada {{ Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
    <p class="mt-2" style="font-size: 0.7rem; font-style= italic;"><span class="fw-bold">Keterangan</span>
        <span class="fw-normal">
            <br>L = Laki-laki
            <br>P = Perempuan
            <br>Jml = Jumlah
        </span>
    </p>
</body>
</html>