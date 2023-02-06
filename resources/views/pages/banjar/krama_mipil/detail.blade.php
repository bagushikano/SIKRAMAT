@extends('layouts.banjar.banjar')
@push('css')
    <style>
        @media (min-width: 768px) {
            .h-md-100 {
                height: 100% !important;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Detail Krama Mipil')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Krama Mipil
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-krama-mipil-home') }}" class="text-decoration-none text-dark">Krama Mipil</a></li>
                        <li class="breadcrumb-item active text-red-pastel">{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            @csrf
            <div class="row">
                <div class="col-xxl-4 col-xl-12 mb-4">
                    <div class="card mh-75">
                        <div class="card-body h-100 d-flex justify-content-center py-5 py-xl-4">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <p class="text-gray-700 mb-0">Data <span class="text-primary font-weight-bold">Krama Mipil</span>
                                        <br>
                                        beserta anggota keluarganya
                                    </p>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <img class="" src="{{asset('assets/admin/assets/img/population.png')}}" style="max-width: 25rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">Krama Mipil (Kepala Keluarga)</div>
                        <div class="card-body px-5 mt-2 mb-2">
                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nomor Krama Mipil</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->nomor_krama_mipil }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Nama Krama Mipil</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Banjar Adat</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->banjar_adat->nama_banjar_adat }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Tempekan</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ $krama_mipil->cacah_krama_mipil->tempekan->nama_tempekan ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-sm-3">
                                    <h5 for="title" class="font-weight-bold text-dark">Tanggal Registrasi</h5>
                                </div>
                                <div class="col-lg-8 col-sm-9">
                                    <span>: {{ date('d M Y', strtotime($krama_mipil->tanggal_registrasi)) }}</span>
                                </div>
                            </div>

                            <hr class="my-4" />
                            <div class="float-right mb-n2">
                                <a class="btn btn-primary btn-icon-split my-1 text-end" href="{{ route('banjar-krama-mipil-detail-krama-mipil', $krama_mipil->id) }}">
                                    <span class="icon">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <span class="text">Detail Krama Mipil</span>
                                </a>
                                <a class="btn btn-warning btn-icon-split my-1 text-end" href="{{ route('banjar-krama-mipil-edit', $krama_mipil->id) }}">
                                    <span class="icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                    <span class="text">Edit Krama Mipil</span>
                                </a>
                                {{-- <div class="dropdown"> --}}
                                    <button class="btn btn-info btn-icon-split my-1 dropdown-toggle" id="dropdownMenuButton" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="icon">
                                            <i class="fas fa-list"></i>
                                        </span>
                                        <span class="text">Tindakan Lainnya</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#ganti_krama_mipil"><i class="fas fa-exchange-alt mr-2"></i>Ganti Krama Mipil</a>
                                        <a class="dropdown-item" href="{{ route('banjar-krama-mipil-kartu-keluarga', $krama_mipil->id) }}" target="_blank"><i class="fas fa-print mr-2"></i>Kartu Keluarga Krama Mipil</a>
                                        <a class="dropdown-item" href="{{ route('banjar-krama-mipil-riwayat-keluarga', $krama_mipil->id) }}"><i class="fas fa-history mr-2"></i>Riwayat Perubahan Data Keluarga</a>
                                    </div>
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Anggota Keluarga Krama Mipil</div>
                <div class="card-body px-5 mt-3 mb-4">
                    <form id="form-create-krama-mipil" method="post" action="{{ route('banjar-cacah-krama-mipil-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <a class="btn btn-primary btn-icon-split mb-4 text-end" href="{{ route('banjar-anggota-krama-mipil-create', $krama_mipil->id) }}">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Anggota Keluarga</span>
                    </a>
                        <div class="datatable">
                        <table class="table table-bordered table-hover" id="anggota-keluarga" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th style="width: 18%">No. Cacah Krama Mipil</th>
                                    <th>Nama</th>
                                    <th style="width: 15%">Status Hubungan</th>
                                    <th style="width: 15%">Tanggal Registrasi</th>
                                    <th style="width: 15%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <td class="text-center">1</td>
                                    <td>{{ $krama_mipil->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                    <td>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</td>
                                    <td>Kepala Keluarga</td>
                                    <td>{{ date('d M Y', strtotime($krama_mipil->tanggal_registrasi)) }}</td>
                                    <td class="text-left">
                                        <a button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('banjar-cacah-krama-mipil-detail', $krama_mipil->cacah_krama_mipil_id) }}"><i class="fas fa-eye"></i></a>
                                            <a button class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('banjar-anggota-krama-mipil-edit', $krama_mipil->id) }}"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr> --}}
                                @foreach($anggota_krama_mipil as $anggota_krama)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $anggota_krama->cacah_krama_mipil->nomor_cacah_krama_mipil }}</td>
                                        <td>{{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $anggota_krama->status_hubungan)) }}</td>
                                        <td>{{ date('d M Y', strtotime($anggota_krama->tanggal_registrasi)) }}</td>
                                        <td class="text-center">
                                            <a button type="button" class="btn btn-primary btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="{{ route('banjar-cacah-krama-mipil-detail', $anggota_krama->cacah_krama_mipil_id) }}"><i class="fas fa-eye"></i></a>
                                            <a button class="btn btn-warning btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="{{ route('banjar-anggota-krama-mipil-edit', $anggota_krama->id) }}"><i class="fas fa-edit"></i></a>
                                            <button button type="button" class="btn btn-danger btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_anggota({{ $anggota_krama->id }})"><i class="fas fa-user-alt-slash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Modal Nonaktifkan Cacah Krama Mipil -->
    <div class="modal fade" id="nonaktif_cacah_krama_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-delete-anggota" method="post" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Keluarkan Anggota Keluarga</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title" class="small">Tanggal Keluar<span class="text-danger small">*</span></label>
                            <input type="text" class="datepicker-here form-control @error ('tanggal_keluar') is-invalid @enderror" name="tanggal_keluar" id="tanggal_keluar" value="{{ old('tanggal_keluar') }}" placeholder="Masukkan Tanggal Keluar" required>
                            @error('tanggal_tanggal_keluarlahir')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Tanggal keluar wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alasan_keluar" class="small">Alasan Keluar<span class="text-danger small">*</span></label>
                            <textarea type="text" class="form-control @error ('alasan_keluar') is-invalid @enderror" placeholder="Masukkan Alasan Keluar" rows="3" name="alasan_keluar" id="alasan_keluar" required></textarea>
                            @error('alasan_keluar')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Alasan keluar wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ganti Krama Mipil -->
    <div class="modal fade" id="ganti_krama_mipil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-krama-mipil" method="post" action="{{route('banjar-krama-mipil-ganti', $krama_mipil->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Ganti Krama Mipil</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="krama_mipil_lama">Krama Mipil Lama</label>
                            <select class="select2 custom-select select-krama @error ('krama_mipil_lama') is-invalid @enderror" name="krama_mipil_lama" id="krama_mipil_lama"  style="width: 100%" disabled>
                                <option value="">{{ $krama_mipil->nomor_krama_mipil }} - {{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                            </select>
                            @error('krama_mipil_lama')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="krama_mipil_baru">Krama Mipil Baru<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('krama_mipil_baru') is-invalid @enderror" name="krama_mipil_baru" id="krama_mipil_baru"  style="width: 100%">
                                <option value="" hidden>Pilih Krama Mipil Baru</option>
                                @foreach($anggota_krama_mipil as $anggota_krama)
                                    <option value="{{ $anggota_krama->cacah_krama_mipil_id }}">{{ $anggota_krama->cacah_krama_mipil->nomor_cacah_krama_mipil }} - {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_depan }} {{ $anggota_krama->cacah_krama_mipil->penduduk->nama }}@if($anggota_krama->cacah_krama_mipil->penduduk->gelar_depan != ''), {{ $anggota_krama->cacah_krama_mipil->penduduk->gelar_belakang }}@endif</option>
                                @endforeach
                            </select>
                            @error('cacah_krama_mipil')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alasan_penggantian">Alasan Penggantian<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('alasan_penggantian') is-invalid @enderror" name="alasan_penggantian" id="alasan_penggantian"  style="width: 100%" aria-placeholder="Pilih Status Hubungan" required>
                                <option value="">Pilih Alasan Penggantian</option>
                                <option value="krama_mipil_meninggal_dunia">Krama Mipil Meninggal Dunia</option>
                                <option value="krama_mipil_sakit">Krama Mipil Sakit</option>
                                <option value="krama_mipil_lansia">Krama Mipil Lansia</option>
                                <option value="krama_mipil_baru">Krama Mipil Baru</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('status_hubungan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Hubungan wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL --}}
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('error'))
    <script>
        $(document).ready(function(){
            alertError('Gagal', '{{$message}}');
        });
    </script>
    @elseif($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    <script>
        $(document).ready( function () {
            $("#anggota-keluarga").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari anggota...",
                    "infoEmpty": "Menampilkan 0 data",
                    "infoFiltered": "(dari _MAX_ data)",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                },
                "language": {
                    "paginate": {
                        "previous": 'Sebelumnya',
                        "next": 'Berikutnya'
                    },
                    "info": "Menampilkan _START_ s/d _END_ dari _MAX_ data",
                },
            });
            //SIDE BAR CLASS
            $('#sidebarKrama').removeClass('collapsed');
            $('#collapseKrama').addClass('show');
            $('#collapseKrama').addClass('active');
            $('#nav-link-krama-mipil').addClass('active');

            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //DatePicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
        });

        function delete_anggota(id){
            var url = "{{ route('banjar-anggota-krama-mipil-delete', ":id") }}";
            url = url.replace(':id', id);
            $('#form-delete-anggota').attr('action', url);
            $('#nonaktif_cacah_krama_modal').modal('show');
        }

        // Validasi Form
        (function () {
            'use strict'
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        //Validasi Form
    </script>
@endpush