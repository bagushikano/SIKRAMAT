<div class="datatable table-responsive">
    <table class="table table-bordered table-hover table-responsive" id="dataTable-krama-mipil" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th>Nomor Krama Mipil</th>
                <th>Nama</th>
                <th>Tempat/Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Banjar Adat</th>
                <th style="width: 12%">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($krama_mipil as $krama)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $krama->nomor_krama_mipil }}</td>
                    <td>{{ $krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $krama->cacah_krama_mipil->penduduk->nama }}@if($krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $krama->cacah_krama_mipil->penduduk->gelar_belakang }} @endif</td>
                    <td>{{ $krama->cacah_krama_mipil->penduduk->tempat_lahir }}, {{ date('d M Y', strtotime($krama->cacah_krama_mipil->penduduk->tanggal_lahir)) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $krama->cacah_krama_mipil->penduduk->jenis_kelamin)) }}</td>
                    <td>{{ $krama->banjar_adat->nama_banjar_adat }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm my"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Anggota" onclick="lihat_anggota_krama_mipil({{ $krama->id }}, '{{ $krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $krama->cacah_krama_mipil->penduduk->nama }}@if($krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif')"><i class="fas fa-eye mr-1 mb-1"></i>Anggota</button>
                        <button type="button" class="btn btn-success btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil({{ $krama->id }}, '{{ $krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $krama->cacah_krama_mipil->penduduk->nama }}@if($krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif')"><i class="fas fa-user-check mr-1"></i>Pilih</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>