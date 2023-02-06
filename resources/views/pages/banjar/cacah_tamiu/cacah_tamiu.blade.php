@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('title', 'Data Cacah Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="users"></i></div>
                                Cacah Tamiu
                            </h1>
                            <div class="page-header-subtitle">Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="cardTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="wni_tab" href="#wni" data-toggle="tab" role="tab" aria-controls="wni" aria-selected="true">Tamiu Warga Negara Indonesia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wna_tab" href="#wna" data-toggle="tab" role="tab" aria-controls="wna" aria-selected="false">Tamiu Warga Negara Asing</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="wni" role="tabpanel" aria-labelledby="overview-tab">
                            <div class="row mb-3">
                                <div class="col-12 col-md-8">
                                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('banjar-cacah-tamiu-wni-create') }}">
                                        <span class="icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <span class="text">Tambah Cacah Tamiu</span>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4 d-flex justify-content-end small">
                                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="wni_filter_modal()">
                                        <span class="icon">
                                            <i class="fas fa-filter"></i>
                                        </span>
                                        <span class="text">Filter Cacah Tamiu</span>
                                    </button>
                                </div>
                            </div>
                            <div class="datatable">
                                <table class="table table-bordered table-hover table-responsive dataTable-cacah-tamiu" id="dataTable-cacah-tamiu-wni" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>No. Cacah Tamiu</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Tanggal Masuk</th>
                                            <th style="width: 14%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wna" role="tabpanel" aria-labelledby="example-tab">
                            <div class="row mb-3">
                                <div class="col-12 col-md-8">
                                    <a class="btn btn-primary btn-icon-split mb-3 text-end" href="{{ route('banjar-cacah-tamiu-wna-create') }}">
                                        <span class="icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <span class="text">Tambah Cacah Tamiu</span>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4 d-flex justify-content-end small">
                                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="wna_filter_modal()">
                                        <span class="icon">
                                            <i class="fas fa-filter"></i>
                                        </span>
                                        <span class="text">Filter Cacah Tamiu</span>
                                    </button>
                                </div>
                            </div>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover  dataTable-cacah-tamiu" id="dataTable-cacah-tamiu-wna" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 2%">No.</th>
                                            <th>No. Cacah Tamiu</th>
                                            <th>Nomor Paspor</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Tanggal Masuk</th>
                                            <th style="width: 14%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="wni_filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Cacah Tamiu WNI</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="status" class="text-dark">Status Cacah Tamiu</label>
                                <select class="select2 custom-select @error ('wni_status') is-invalid @enderror" name="wni_status" id="wni_status" style="width: 100%" aria-placeholder="Pilih Status Cacah Krama Tamiu" required>
                                    <option value="2">Semua Status</option>
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif/Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="status" class="text-dark">Rentang Tanggal Lahir</label>
                                <input type="text" class="form-control" name="wni_rentang_waktu" id="wni_rentang_waktu" placeholder="Pilih Rentang Tanggal Lahir" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin" class="text-dark">Jenis Kelamin</label>
                                <select class="select2 custom-select @error ('wni_jenis_kelamin') is-invalid @enderror" name="wni_jenis_kelamin" id="wni_jenis_kelamin" style="width: 100%" aria-placeholder="Pilih Asal" required>
                                    <option value="">Semua Jenis Kelamin</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="rentang_waktu_masuk" class="text-dark">Rentang Tanggal Masuk</label>
                                <input type="text" class="form-control" name="wni_rentang_waktu_masuk" id="wni_rentang_waktu_masuk" placeholder="Pilih Rentang Tanggal Masuk" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wni_golongan_darah" class="text-dark">Golongan Darah</label>
                                <select class="select2 custom-select @error ('wni_golongan_darah') is-invalid @enderror" name="wni_golongan_darah[]" id="wni_golongan_darah" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                                    <option value="-">-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="wni_pilih_semua_golongan_darah" type="checkbox">
                                    <label class="custom-control-label" for="wni_pilih_semua_golongan_darah">Pilih semua golongan darah</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wni_agama" class="text-dark">Agama</label>
                                <select class="select2 custom-select @error ('wni_agama') is-invalid @enderror" name="wni_agama[]" id="wni_agama" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Jenis Perkawinan" required>
                                    <option value="islam">Islam</option>
                                    <option value="katolik">Katolik</option>
                                    <option value="protestan">Protestan</option>
                                    <option value="hindu">Hindu</option>
                                    <option value="buddha">Buddha</option>
                                    <option value="khonghucu">Khonghucu</option>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="wni_pilih_semua_agama" type="checkbox">
                                    <label class="custom-control-label" for="wni_pilih_semua_agama">Pilih semua golongan darah</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pekerjaan" class="text-dark">Pekerjaan</label>
                        <select class="select2 custom-select @error ('wni_pekerjaan') is-invalid @enderror" name="wni_pekerjaan[]" id="wni_pekerjaan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Pekerjaan" required>
                            @foreach($pekerjaan as $kerja)
                                <option value="{{ $kerja->id }}">{{ $kerja->profesi }}</option>
                            @endforeach
                        </select>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="wni_pilih_semua_pekerjaan" type="checkbox">
                            <label class="custom-control-label" for="wni_pilih_semua_pekerjaan">Pilih semua pekerjaan</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pendidikan" class="text-dark">Pendidikan Tertinggi</label>
                        <select class="select2 custom-select @error ('wni_pendidikan') is-invalid @enderror" name="wni_pendidikan[]" id="wni_pendidikan" multiple="multiple" style="width: 100%" aria-placeholder="Pilih Pekerjaan" required>
                            @foreach($pendidikan as $didik)
                                <option value="{{ $didik->id }}">{{ $didik->jenjang_pendidikan }}</option>
                            @endforeach
                        </select>
                        <div class="custom-control custom-checkbox mb-n2">
                            <input class="custom-control-input" id="wni_pilih_semua_pendidikan" type="checkbox">
                            <label class="custom-control-label" for="wni_pilih_semua_pendidikan">Pilih semua pendidikan</label>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-icon-split mb-3 text-end" type="button" onclick="wni_filter_reset()">
                        <span class="icon">
                            <i class="fas fa-sync"></i>
                        </span>
                        <span class="text">Reset</span>
                    </button>
                    <button class="btn btn-success btn-icon-split mb-3 text-end" onclick="wni_filter_submit()">
                        <span class="icon">
                            <i class="fas fa-filter"></i>
                        </span>
                        <span class="text">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="wna_filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Cacah Tamiu WNA</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wna_status" class="text-dark">Status Cacah Tamiu</label>
                                <select class="select2 custom-select @error ('wna_status') is-invalid @enderror" name="wna_status" id="wna_status" style="width: 100%" aria-placeholder="Pilih Status Cacah Krama Tamiu" required>
                                    <option value="2">Semua Status</option>
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif/Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wna_rentang_waktu" class="text-dark">Rentang Tanggal Lahir</label>
                                <input type="text" class="form-control" name="wna_rentang_waktu" id="wna_rentang_waktu" placeholder="Pilih Rentang Tanggal Lahir" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wna_jenis_kelamin" class="text-dark">Jenis Kelamin</label>
                                <select class="select2 custom-select @error ('wna_jenis_kelamin') is-invalid @enderror" name="wna_jenis_kelamin" id="wna_jenis_kelamin" style="width: 100%" aria-placeholder="Pilih Asal" required>
                                    <option value="">Semua Jenis Kelamin</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="wna_rentang_waktu_masuk" class="text-dark">Rentang Tanggal Masuk</label>
                                <input type="text" class="form-control" name="wna_rentang_waktu_masuk" id="wna_rentang_waktu_masuk" placeholder="Pilih Rentang Tanggal Masuk" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-icon-split mb-3 text-end" type="button" onclick="wna_filter_reset()">
                        <span class="icon">
                            <i class="fas fa-sync"></i>
                        </span>
                        <span class="text">Reset</span>
                    </button>
                    <button class="btn btn-success btn-icon-split mb-3 text-end" onclick="wna_filter_submit()">
                        <span class="icon">
                            <i class="fas fa-filter"></i>
                        </span>
                        <span class="text">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nonaktif_tamiu_wni_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-delete-tamiu-wni" method="post" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Keluarkan Cacah Tamiu</h5>
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

    <div class="modal fade" id="nonaktif_tamiu_wna_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-delete-tamiu-wna" method="post" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Keluarkan Cacah Tamiu</h5>
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

    <form id="form-delete-tamiu-wna" method="post" action="/">
        @csrf
        <input type="text" class="datepicker-here form-control" name="tanggal_keluar" id="tanggal_keluar_wna" placeholder="Masukkan Tanggal Keluar" hidden>
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('success'))
        @if($status = Session::get('tamiu'))
            <script>
                $(document).ready(function(){
                    $('#wni').removeClass('active');
                    $('#wni_tab').removeClass('active');
                    $('#wna_tab').addClass('active');
                    $('#wna').tab('show');
                    alertSuccess('Success', '{{$message}}');
                });
            </script>
        @else
        <script>
            $(document).ready(function(){
                alertSuccess('Success', '{{$message}}');
            });
        </script>
        @endif
    @endif
    {{-- END ALERT --}}
    <script>
        function delete_tamiu_wni(id){
            var url = "{{ route('banjar-cacah-tamiu-wni-delete', ":id") }}";
            url = url.replace(':id', id);
            $('#form-delete-tamiu-wni').attr('action', url);
            $('#nonaktif_tamiu_wni_modal').modal('show');
        }

        function delete_tamiu_wna(id){
            var url = "{{ route('banjar-cacah-tamiu-wna-delete', ":id") }}";
            url = url.replace(':id', id);
            $('#form-delete-tamiu-wna').attr('action', url);
            $('#nonaktif_tamiu_wna_modal').modal('show');
        }
        
        $(document).ready( function () {
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-tamiu').addClass('active');

            //Datepicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            //DATE RANGE PICKER
            $('#wni_rentang_waktu').daterangepicker({
                // minDate: "01 Jan 2022",
                locale: {
                    format: 'DD MMM YYYY',
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                }
            });

            $('#wni_rentang_waktu').val('');

            $('#wni_rentang_waktu_masuk').daterangepicker({
                // minDate: "01 Jan 2022",
                locale: {
                    format: 'DD MMM YYYY',
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                }
            });

            $('#wni_rentang_waktu_masuk').val('');

            $('#wna_rentang_waktu').daterangepicker({
                // minDate: "01 Jan 2022",
                locale: {
                    format: 'DD MMM YYYY',
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                }
            });

            $('#wna_rentang_waktu').val('');

            $('#wna_rentang_waktu_masuk').daterangepicker({
                // minDate: "01 Jan 2022",
                locale: {
                    format: 'DD MMM YYYY',
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                }
            });

            $('#wna_rentang_waktu_masuk').val('');

            //SELECT 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#wni_golongan_darah").select2({
                placeholder: "Pilih Golongan Darah",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#wni_agama").select2({
                placeholder: "Pilih Agama",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#wni_pekerjaan").select2({
                placeholder: "Pilih Pekerjaan",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            $("#wni_pendidikan").select2({
                placeholder: "Pilih Pendidikan Tertinggi",
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
        });

        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //WNI DATATABLE
                var table = $('#dataTable-cacah-tamiu-wni');
                var oTable = table.DataTable({
                    "lengthMenu": [
                    [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"] // change per page values here
                        ],

                    // set the initial value
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "language": {
                        "lengthMenu": " _MENU_ records"
                    },
                    "oLanguage": {
                        "sSearch": "Cari:",
                        "sZeroRecords": "Data tidak ditemukan",
                        "sSearchPlaceholder": "Cari Cacah Tamiu...",
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
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-cacah-tamiu-wni-datatable') }}",
                        data : function(d){
                            d.wni_status = $('#wni_status').val();
                            d.wni_rentang_waktu = $('#wni_rentang_waktu').val();
                            d.wni_rentang_waktu_masuk = $('#wni_rentang_waktu_masuk').val();
                            d.wni_golongan_darah = $('#wni_golongan_darah').val();
                            d.wni_jenis_kelamin = $('#wni_jenis_kelamin').val();
                            d.wni_pekerjaan = $('#wni_pekerjaan').val();
                            d.wni_pendidikan = $('#wni_pendidikan').val();
                            d.wni_agama= $('#wni_agama').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_tamiu', class: "wrap", orderable: false },
                        { data: 'penduduk.nik', class: "wrap", orderable: false },
                        { data: 'penduduk.nama', class: "wrap", orderable: false },
                        { data: 'penduduk.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'tanggal_masuk', class: "wrap", orderable: false },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.penduduk.gelar_depan){
                                    nama = nama + row.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                if(data == 'laki-laki'){
                                    return 'Laki-laki';
                                }else{
                                    return 'Perempuan';
                                }
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                return moment(data).format('DD MMM YYYY');
                            }
                        },
                        {
                            'orderable': true,
                            'targets': [0]
                        }, {
                            "searchable": true,
                            "targets": [0]
                        }
                    ],
                    "order": [
                    [0, "asc"]
                    ] // set first column as a default sort by asc
                });

                filter = () => {
                    oTable.ajax.reload();
                }

                //WNA DATATABLE
                var table_wna = $('#dataTable-cacah-tamiu-wna');
                var oTable_wna = table_wna.DataTable({
                    "lengthMenu": [
                    [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"] // change per page values here
                        ],

                    // set the initial value
                    "pageLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "language": {
                        "lengthMenu": " _MENU_ records"
                    },
                    "oLanguage": {
                        "sSearch": "Cari:",
                        "sZeroRecords": "Data tidak ditemukan",
                        "sSearchPlaceholder": "Cari Cacah Tamiu...",
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
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-cacah-tamiu-wna-datatable') }}",
                        data : function(d){
                            d.wna_status = $('#wna_status').val();
                            d.wna_rentang_waktu = $('#wna_rentang_waktu').val();
                            d.wna_rentang_waktu_masuk = $('#wna_rentang_waktu_masuk').val();
                            d.wna_jenis_kelamin = $('#wna_jenis_kelamin').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_cacah_tamiu', class: "wrap", orderable: false },
                        { data: 'wna.nomor_paspor', class: "wrap", orderable: false },
                        { data: 'wna.nama', class: "wrap", orderable: false },
                        { data: 'wna.jenis_kelamin', class: "wrap", orderable: false },
                        { data: 'tanggal_masuk', class: "wrap", orderable: false },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                if(data == 'laki-laki'){
                                    return 'Laki-laki';
                                }else{
                                    return 'Perempuan';
                                }
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                return moment(data).format('DD MMM YYYY');
                            }
                        },
                        {
                            'orderable': true,
                            'targets': [0]
                        }, {
                            "searchable": true,
                            "targets": [0]
                        }
                    ],
                    "order": [
                    [0, "asc"]
                    ] // set first column as a default sort by asc
                });

                wna_filter = () => {
                    oTable_wna.ajax.reload();
                }
            }

            return {
                //main function to initiate the module
                init: function () {
                    handleTable();
                }

            };

        }();

        jQuery(document).ready(function() {
            TableDatatablesEditable.init();
        });

        //FILTER WNI
        function wni_filter_modal(){
            $('#wni_filter_modal').modal('show');
        }

        function wni_filter_submit(){
            filter();
            $('#wni_filter_modal').modal('hide');
        }

        function wni_filter_reset(){
            $('#wni_rentang_waktu').val('');
            $('#wni_status').val('1').trigger('change');
            $('#wni_golongan_darah').val('').trigger('change');
            $('#wni_jenis_kelamin').val('').trigger('change');
            $('#wni_pekerjaan').val('').trigger('change');
            $('#wni_pendidikan').val('').trigger('change');
            $('#wni_agama').val('').trigger('change');
            $('#wni_rentang_waktu_masuk').val('');

            //UNCHECK
            $("#wni_pilih_semua_golongan_darah").prop('checked', false);
            $("#wni_pilih_semua_pekerjaan").prop('checked', false);
            $("#wni_pilih_semua_pendidikan").prop('checked', false);
            $("#wni_pilih_semua_agama").prop('checked', false);
        }

         //FILTER WNA
         function wna_filter_modal(){
            $('#wna_filter_modal').modal('show');
        }

        function wna_filter_submit(){
            wna_filter();
            $('#wna_filter_modal').modal('hide');
        }

        function wna_filter_reset(){
            $('#wna_rentang_waktu').val('');
            $('#wna_status').val('1').trigger('change');
            $('#wna_jenis_kelamin').val('').trigger('change');
            $('#wna_rentang_waktu_masuk').val('');
        }
    </script>

    {{-- PILIH SEMUA FILTER --}}
    <script>
        $("#wni_pilih_semua_golongan_darah").click(function(){
            if($("#wni_pilih_semua_golongan_darah").is(':checked') ){
                $("#wni_golongan_darah").find('option').prop("selected",true);
                $("#wni_golongan_darah").trigger('change');
            } else {
                $("#wni_golongan_darah").find('option').prop("selected",false);
                $("#wni_golongan_darah").trigger('change');
            }
        });

        $("#wni_pilih_semua_agama").click(function(){
            if($("#wni_pilih_semua_agama").is(':checked') ){
                $("#wni_agama").find('option').prop("selected",true);
                $("#wni_agama").trigger('change');
            } else {
                $("#wni_agama").find('option').prop("selected",false);
                $("#wni_agama").trigger('change');
            }
        });

        $("#wni_pilih_semua_pekerjaan").click(function(){
            if($("#wni_pilih_semua_pekerjaan").is(':checked') ){
                $("#wni_pekerjaan").find('option').prop("selected",true);
                $("#wni_pekerjaan").trigger('change');
            } else {
                $("#wni_pekerjaan").find('option').prop("selected",false);
                $("#wni_pekerjaan").trigger('change');
            }
        });

        $("#wni_pilih_semua_pendidikan").click(function(){
            if($("#wni_pilih_semua_pendidikan").is(':checked') ){
                $("#wni_pendidikan").find('option').prop("selected",true);
                $("#wni_pendidikan").trigger('change');
            } else {
                $("#wni_pendidikan").find('option').prop("selected",false);
                $("#wni_pendidikan").trigger('change');
            }
        });
    </script>
@endpush