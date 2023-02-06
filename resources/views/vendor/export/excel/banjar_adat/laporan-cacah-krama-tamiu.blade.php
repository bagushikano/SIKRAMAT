<table>
    <thead>
        <tr>
            <th colspan="13" height="50" style="border: 0.1 solid black">
                LAPORAN DATA CACAH KRAMA TAMIU
                <br>
                BANJAR ADAT {{ strtoupper(session()->get('banjar_adat_nama')) }}
                <br>
                DESA ADAT {{ strtoupper(session()->get('desa_adat_nama')) }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="13" style="border: 0.1 solid black"></td>
        </tr>
        <tr>            
            <th style="text-align:center; border: 0.1px solid black;">NO</th>
            <th style="text-align:center; border: 0.1px solid black;">NOMOR CACAH<br>KRAMA TAMIU</th>
            <th style="text-align:center; border: 0.1px solid black;">NIK</th>
            <th style="text-align:center; border: 0.1px solid black;">NAMA<br>LENGKAP</th>
            <th style="text-align:center; border: 0.1px solid black;">TEMPAT<br>LAHIR</th>
            <th style="text-align:center; border: 0.1px solid black;">TANGGAL<br>LAHIR</th>
            <th style="text-align:center; border: 0.1px solid black;">JENIS<br>KELAMIN</th>
            <th style="text-align:center; border: 0.1px solid black;">ALAMAT</th>
            <th style="text-align:center; border: 0.1px solid black;">BANJAR DINAS</th>
            <th style="text-align:center; border: 0.1px solid black;">PENDIDIKAN<br>TERTINGGI</th>
            <th style="text-align:center; border: 0.1px solid black;">PEKERJAAN</th>
            <th style="text-align:center; border: 0.1px solid black;">GOLONGAN<br>DARAH</th>
            <th style="text-align:center; border: 0.1px solid black;">TANGGAL<br>REGISTRASI</th>
        </tr>
        @foreach ($data['krama_tamiu'] as $krama_tamiu)
            <tr>
                <td style="text-align: center; border: 0.1px solid black;">{{ $loop->iteration }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->nomor_cacah_krama_tamiu }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->nik }}</td>
                @if ($krama_tamiu->gelar_depan == NULL && $krama_tamiu->gelar_depan == NULL)
                    @php
                        $nama_lengkap = $krama_tamiu->nama;
                    @endphp
                @else
                    @if ($krama_tamiu->gelar_depan != NULL && $krama_tamiu->gelar_belakang == NULL)
                        @php
                            $nama_lengkap = $krama_tamiu->gelar_depan.' '.$krama_tamiu->nama;
                        @endphp
                    @endif
                    @if ($krama_tamiu->gelar_depan == NULL && $krama_tamiu->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $krama_tamiu->nama.', '.$krama_tamiu->gelar_belakang;
                        @endphp
                    @endif
                    @if ($krama_tamiu->gelar_depan != NULL && $krama_tamiu->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $krama_tamiu->gelar_depan.' '.$krama_tamiu->nama.', '.$krama_tamiu->gelar_belakang;
                        @endphp
                    @endif
                @endif
                <td style="border: 0.1px solid black;">{{ $nama_lengkap }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->tempat_lahir }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($krama_tamiu->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                <td style="border: 0.1px solid black;">{{ ucfirst($krama_tamiu->jenis_kelamin) }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->alamat ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->nama_banjar_dinas ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->jenjang_pendidikan }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_tamiu->profesi }}</td>
                <td style="text-align: center; border: 0.1px solid black;">{{ $krama_tamiu->golongan_darah }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($krama_tamiu->tanggal_registrasi)->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
