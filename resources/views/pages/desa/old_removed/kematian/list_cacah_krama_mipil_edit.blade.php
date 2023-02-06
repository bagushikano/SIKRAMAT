<div class="datatable table-responsive">
    <table class="table table-bordered table-hover table-responsive" id="dataTable-cacah-krama-mipil" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                {{-- <th>NCKM</th> --}}
                <th>NICK</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Tempat/Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Banjar Adat</th>
                <th style="width: 8%">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kramas as $krama)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    {{-- <td>{{ $krama->nomor_cacah_krama_mipil }}</td> --}}
                    <td>{{ $krama->penduduk->nomor_induk_cacah_krama }}</td>
                    <td>{{ $krama->penduduk->nik }}</td>
                    <td>{{ $krama->penduduk->gelar_depan }} {{ $krama->penduduk->nama }}@if($krama->penduduk->gelar_belakang != ''), {{ $krama->penduduk->gelar_belakang }} @endif</td>
                    <td>{{ ucwords(str_replace('_', ' ', $krama->penduduk->tempat_lahir)) }}, {{ date('d M Y', strtotime($krama->penduduk->tanggal_lahir)) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $krama->penduduk->jenis_kelamin)) }}</td>
                    <td>{{ $krama->banjar_adat->nama_banjar_adat }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_cacah_krama_edit({{ $krama->id }}, '{{ $krama->penduduk->gelar_depan }} {{ $krama->penduduk->nama }}@if($krama->penduduk->gelar_belakang != ''), {{ $krama->penduduk->gelar_belakang }}@endif')"><i class="fas fa-user-check mr-1"></i>Pilih</button>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>