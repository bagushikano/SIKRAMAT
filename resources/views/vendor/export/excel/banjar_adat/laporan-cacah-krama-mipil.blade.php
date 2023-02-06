<table>
    <thead>
        <tr>
            <th colspan="14" height="50" style="border: 0.1 solid black">
                LAPORAN DATA CACAH KRAMA MIPIL
                <br>
                BANJAR ADAT {{ strtoupper(session()->get('banjar_adat_nama')) }}
                <br>
                DESA ADAT {{ strtoupper(session()->get('desa_adat_nama')) }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="14" style="border: 0.1 solid black"></td>
        </tr>
        <tr>
            <th style="text-align:center; border: 0.1px solid black;">NO</th>
            <th style="text-align:center; border: 0.1px solid black;">NOMOR CACAH<br>KRAMA MIPIL</th>
            <th style="text-align:center; border: 0.1px solid black;">NIK</th>
            <th style="text-align:center; border: 0.1px solid black;">NAMA<br>LENGKAP</th>
            <th style="text-align:center; border: 0.1px solid black;">NAMA ALIAS<br>(BHISEKA)</th>
            <th style="text-align:center; border: 0.1px solid black;">TEMPAT<br>LAHIR</th>
            <th style="text-align:center; border: 0.1px solid black;">TANGGAL<br>LAHIR</th>
            <th style="text-align:center; border: 0.1px solid black;">JENIS<br>KELAMIN</th>
            <th style="text-align:center; border: 0.1px solid black;">TEMPEKAN</th>
            <th style="text-align:center; border: 0.1px solid black;">PENDIDIKAN<br>TERTINGGI</th>
            <th style="text-align:center; border: 0.1px solid black;">PEKERJAAN</th>
            <th style="text-align:center; border: 0.1px solid black;">GOLONGAN<br>DARAH</th>
            <th style="text-align:center; border: 0.1px solid black;">ALAMAT</th>
            <th style="text-align:center; border: 0.1px solid black;">TANGGAL<br>REGISTRASI</th>
        </tr>
        @foreach ($data['krama_mipil'] as $krama_mipil)
            <tr>
                <td style="text-align: center; border: 0.1px solid black;">{{ $loop->iteration }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->nomor_cacah_krama_mipil }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->nik }}</td>
                @if ($krama_mipil->gelar_depan == NULL && $krama_mipil->gelar_depan == NULL)
                    @php
                        $nama_lengkap = $krama_mipil->nama;
                    @endphp
                @else
                    @if ($krama_mipil->gelar_depan != NULL && $krama_mipil->gelar_belakang == NULL)
                        @php
                            $nama_lengkap = $krama_mipil->gelar_depan.' '.$krama_mipil->nama;
                        @endphp
                    @endif
                    @if ($krama_mipil->gelar_depan == NULL && $krama_mipil->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $krama_mipil->nama.', '.$krama_mipil->gelar_belakang;
                        @endphp
                    @endif
                    @if ($krama_mipil->gelar_depan != NULL && $krama_mipil->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $krama_mipil->gelar_depan.' '.$krama_mipil->nama.', '.$krama_mipil->gelar_belakang;
                        @endphp
                    @endif
                @endif
                <td style="border: 0.1px solid black;">{{ $nama_lengkap }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->nama_alias ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->tempat_lahir }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($krama_mipil->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                <td style="border: 0.1px solid black;">{{ ucfirst($krama_mipil->jenis_kelamin) }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->nama_tempekan ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->jenjang_pendidikan }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->profesi }}</td>
                <td style="text-align: center; border: 0.1px solid black;">{{ $krama_mipil->golongan_darah }}</td>
                <td style="border: 0.1px solid black;">{{ $krama_mipil->alamat ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($krama_mipil->tanggal_registrasi)->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
