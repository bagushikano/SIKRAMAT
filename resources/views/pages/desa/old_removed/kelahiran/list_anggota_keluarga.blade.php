<div class="datatable table-responsive">
    <table class="table table-bordered table-hover table-responsive" id="dataTable-anggota-keluarga" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th>No. Induk Cacah Krama</th>
                <th>Nama</th>
                <th>Status Hubungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($anggota_keluargas as $anggota_keluarga)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $anggota_keluarga->cacah_krama_mipil->penduduk->nomor_induk_cacah_krama }}</td>
                    <td>{{ $anggota_keluarga->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_keluarga->cacah_krama_mipil->penduduk->nama }}@if($anggota_keluarga->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $anggota_keluarga->cacah_krama_mipil->penduduk->gelar_belakang }} @endif</td>
                    <td>{{ ucwords(str_replace('_', ' ', $anggota_keluarga->status_hubungan)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer mt-2 mb-n3"><button class="btn btn-danger" type="button" data-dismiss="modal" id="btnTutup">Tutup</button><button class="btn btn-success ml-2" type="button" onclick="pilih_krama_mipil({{ $krama_mipil->id }}, '{{ $krama_mipil->cacah_krama_mipil->penduduk->gelar_depan }} {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}@if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang }}@endif')" id="btnPilih">Pilih Krama Mipil</button></div>