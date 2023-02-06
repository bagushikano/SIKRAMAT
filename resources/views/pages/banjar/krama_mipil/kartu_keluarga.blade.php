<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Kartu Keluarga Krama Mipil</title>
        <link href="{{ asset('assets/admin/css/styles.css')}}" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}" />
        <style>
            @media print {
                html, body {
                    height:100%; 
                    margin: 0 !important; 
                    padding: 0 !important;
                    overflow: hidden;
                    -webkit-print-color-adjust: exact; 
                }
            }

            @font-face { 
                font-family: 'Roboto', sans-serif;
                src: url("/assets/admin/assets/fonts/roboto/Roboto-Light.tff");
                !important;
            }
            
        </style>
    </head>
    <body>
        <div>
            <main>
                {{-- <div class="container col-lg-8 mt-5"> --}}
                    <!-- Invoice-->
    
                    <div class="card invoice">
                        <div class="card-header border-bottom-0 bg-gradient-light-to-secondary text-white-50">
                            <div class="justify-content-between align-items-center mt-2 ">
                                <div class="text-center text-lg-center">
                                    <img class="invoice-brand-img" src="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}" alt="">
                                    <div class="h2 text-primary">Kartu Keluarga Krama Desa Adat</div>
                                    <span class="text-dark">No. 032501011278001</span>
                                </div>
                            </div>
                        </div>
    
                        <div class="card-body">
                            {{-- KRAMA MIPIL --}}
                            <div class="row mx-5 mb-2 justify-content-between align-items-center">
                                <div class="col-7 text-lg-left" style="line-height: 1rem">
                                    <label class="small font-weight-700">Krama Mipil</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small font-weight-500"> Nama Krama Mipil </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-500">: {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small"> Kedudukan </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: @if($krama_mipil->kedudukan_krama_mipil != NULL){{ ucwords($krama_mipil->kedudukan_krama_mipil) }}@else- @endif</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small"> Alamat </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $krama_mipil->cacah_krama_mipil->penduduk->alamat }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small">Banjar Adat </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $banjar_adat->nama_banjar_adat }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small">Banjar Dinas </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $banjar_dinas->nama_banjar_dinas ?? '-' }} </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5 text-lg-left" style="line-height: 1rem">
                                    <label class="small font-weight-700 text-light"></label>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="d-flex flex-column font-weight-bold">
                                                <label class="small line-height-normal"> Desa Adat </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $desa_adat->desadat_nama }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="font-weight-bold">
                                                <label class="small">Kecamatan </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $kecamatan->name }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="font-weight-bold">
                                                <label class="small "> Kabupaten </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $kabupaten->name }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="font-weight-bold">
                                                <label class="small"> Kode Desa Adat </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="small">: {{ $desa_adat->desadat_kode }} </label> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                            {{-- ANGGOTA KRAMA MIPIL --}}
                            <div class="col-lg-12">
                                <div class="datatable">
                                    <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px">No.</th>
                                                <th>Nama</th>
                                                <th>No. Cacah Krama Mipil</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Tempat/Tanggal Lahir</th>
                                                <th>Status Hubungan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-1 text-center">1</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                                <td class="p-1">{{ ucwords($krama_mipil->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->tempat_lahir }}, {{ $krama_mipil->cacah_krama_mipil->penduduk->tanggal_lahir }}</td>
                                                <td class="p-1">Kepala Keluarga</td>
                                            </tr>
                                            @foreach($anggota_krama_mipil as $item)
                                            <tr>
                                                <td class="p-1 text-center">{{ $loop->iteration+1 }}</td>
                                                <td class="p-1">{{ $item->cacah_krama_mipil->penduduk->nama }}</td>
                                                <td class="p-1">{{ $item->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                                <td class="p-1">{{ ucwords($item->cacah_krama_mipil->penduduk->jenis_kelamin) }}</td>
                                                <td class="p-1">{{ $item->cacah_krama_mipil->penduduk->tempat_lahir }}, {{ $item->cacah_krama_mipil->penduduk->tanggal_lahir }}</td>
                                                <td class="p-1">{{ ucwords($item->status_hubungan) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="datatable">
                                    <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px">No.</th>
                                                <th>Pendidikan</th>
                                                <th>Pekerjaan</th>
                                                <th>Status Perkawinan</th>
                                                <th>Nama Ayah</th>
                                                <th>Nama Ibu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-1 text-center">1</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->pendidikan->jenjang_pendidikan }}</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->pekerjaan->profesi }}</td>
                                                <td class="p-1">{{ ucwords(str_replace('_', ' ', $krama_mipil->cacah_krama_mipil->penduduk->status_perkawinan)) }}</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->ayah->nama ?? '-' }}</td>
                                                <td class="p-1">{{ $krama_mipil->cacah_krama_mipil->penduduk->ibu->nama ?? '-' }}</td>
                                            </tr>
                                            @foreach($anggota_krama_mipil as $item)
                                            <tr>
                                                <td class="p-1 text-center">{{ $loop->iteration+1 }}</td>
                                                <td class="p-1">{{ $item->cacah_krama_mipil->penduduk->pendidikan->jenjang_pendidikan }}</td>
                                                <td class="p-1">{{ $item->cacah_krama_mipil->penduduk->pekerjaan->profesi }}</td>
                                                <td class="p-1">{{ ucwords(str_replace('_', ' ', $item->cacah_krama_mipil->penduduk->status_perkawinan)) }}</td>
                                                @if($item->cacah_krama_mipil->penduduk->ayah_kandung_id != NULL)
                                                    <td class="p-1">@if($item->cacah_krama_mipil->penduduk->ayah->gelar_depan !=NULL){{ $item->cacah_krama_mipil->penduduk->ayah->gelar_depan }} @endif{{ $item->cacah_krama_mipil->penduduk->ayah->nama }}@if($item->cacah_krama_mipil->penduduk->ayah->gelar_belakang !=NULL), {{ $item->cacah_krama_mipil->penduduk->ayah->gelar_belakang }} @endif</td>
                                                @else
                                                    <td class="p-1">-</td>
                                                @endif
                                                @if($item->cacah_krama_mipil->penduduk->ibu_kandung_id != NULL)
                                                    <td class="p-1">@if($item->cacah_krama_mipil->penduduk->ibu->gelar_depan !=NULL){{ $item->cacah_krama_mipil->penduduk->ibu->gelar_depan }} @endif{{ $item->cacah_krama_mipil->penduduk->ibu->nama }}@if($item->cacah_krama_mipil->penduduk->ibu->gelar_belakang !=NULL), {{ $item->cacah_krama_mipil->penduduk->ibu->gelar_belakang }} @endif</td>
                                                @else
                                                    <td class="p-1">-</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <p></p>
                        <div class="card-footer mt-n10 border-top-0 bg-white">
                            <div class="row">
                                <div class="col-6">
                                </div>
                                <div class="col-6">
                                    <div class="mb-4 float-right mr-5">
                                        <!-- Invoice - sent from info-->
                                        <div class="small mt-10">{{ $desa_adat->desadat_nama }}, {{ $tanggal_sekarang }}</div>
                                        <div class="h6 mb-0 text-center">Bendesa</div>
                                        <div class="small mt-10 text-center">{{ $bendesa->krama_mipil->cacah_krama_mipil->penduduk->nama }}</div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
    
                    </div>
                {{-- </div> --}}
            </main>
        </div>
    <script type="text/javascript">
        window.print();
    
    </script>
    </body>
</html>
