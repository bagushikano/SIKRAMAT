<table class="table table-bordered table-hover table-responsive dataTable-prajuru" id="dataTable-prajuru-banjar" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th style="width: 5%">No.</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Banjar Adat</th>
            <th>Email</th>
            <th>Masa Jabatan</th>
            <th style="width: 10%">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prajuru_banjar_adat as $prajuru_banjar)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan }} {{ $prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->nama }}@if($prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang }} @endif</td>
                <td>{{ ucwords(str_replace('_', ' ', $prajuru_banjar->jabatan))}}</td>
                <td>{{ $prajuru_banjar->banjar_adat->nama_banjar_adat}}</td>
                <td>{{ $prajuru_banjar->user->email}}</td>
                <td>{{ date('d M Y', strtotime($prajuru_banjar->tanggal_mulai_menjabat)) }} s.d. {{ date('d M Y', strtotime($prajuru_banjar->tanggal_akhir_menjabat)) }}</td>
                <td class="text-center">
                    <button button type="button" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_prajuru_banjar_adat({{ $prajuru_banjar->id }})"><i class="fas fa-edit"></i></button>
                    <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_prajuru_banjar_adat({{ $prajuru_banjar->id }})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>