<table>
    <thead>
        <tr>
            <th colspan="13" height="50" style="border: 0.1 solid black">
                LAPORAN DATA TAMIU
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
            <th style="text-align:center; border: 0.1px solid black;">NOMOR<br>TAMIU</th>
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
        @foreach ($data['tamiu'] as $tamiu)
            <tr>
                <td style="text-align: center; border: 0.1px solid black;">{{ $loop->iteration }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->nomor_tamiu }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->nik }}</td>
                @if ($tamiu->gelar_depan == NULL && $tamiu->gelar_depan == NULL)
                    @php
                        $nama_lengkap = $tamiu->nama;
                    @endphp
                @else
                    @if ($tamiu->gelar_depan != NULL && $tamiu->gelar_belakang == NULL)
                        @php
                            $nama_lengkap = $tamiu->gelar_depan.' '.$tamiu->nama;
                        @endphp
                    @endif
                    @if ($tamiu->gelar_depan == NULL && $tamiu->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $tamiu->nama.', '.$tamiu->gelar_belakang;
                        @endphp
                    @endif
                    @if ($tamiu->gelar_depan != NULL && $tamiu->gelar_belakang != NULL)
                        @php
                            $nama_lengkap = $tamiu->gelar_depan.' '.$tamiu->nama.', '.$tamiu->gelar_belakang;
                        @endphp
                    @endif
                @endif
                <td style="border: 0.1px solid black;">{{ $nama_lengkap }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->tempat_lahir }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($tamiu->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                <td style="border: 0.1px solid black;">{{ ucfirst($tamiu->jenis_kelamin) }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->alamat ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->nama_banjar_dinas ?? '-' }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->jenjang_pendidikan }}</td>
                <td style="border: 0.1px solid black;">{{ $tamiu->profesi }}</td>
                <td style="text-align: center; border: 0.1px solid black;">{{ $tamiu->golongan_darah }}</td>
                <td style="border: 0.1px solid black;">{{ \Carbon\Carbon::parse($tamiu->tanggal_registrasi)->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
