@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <style>
    .input-group.is-invalid {
        ~ .invalid-feedback {
            display: block;
        }
    }
    </style>
@endpush
@section('title', 'Prajuru Desa dan Banjar Adat')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users-cog mr-1"></i></div>
                                Prajuru
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
                            <a class="nav-link active" id="desa_adat_tab" href="#desa_adat" data-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Prajuru Desa Adat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="banjar_adat_tab" href="#banjar_adat" data-toggle="tab" role="tab" aria-controls="example" aria-selected="false">Prajuru Banjar Adat</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div id="overlay" style="display: none;">
                        <div class="w-100 d-flex justify-content-center align-items-center">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="desa_adat" role="tabpanel" aria-labelledby="overview-tab">
                            <div class="row mb-3">
                                <div class="col-12 col-md-10">
                                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="tambah_prajuru_desa_adat()">
                                        <span class="icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <span class="text">Tambah Prajuru</span>
                                    </button>
                                </div>
                                <div class="col-12 col-md-2 d-flex justify-content-end small">
                                    <select class="select2 custom-select @error ('status_prajuru_desa_adat') is-invalid @enderror" name="status_prajuru_desa_adat" id="status_prajuru_desa_adat" style="width: 100%">
                                        <option value="menjabat">Prajuru Aktif</option>
                                        <option value="purna">Prajuru Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="datatable table-responsive" id="datatable-prajuru-desa-adat">
                                <table class="table table-bordered table-hover table-responsive dataTable-prajuru" id="dataTable-prajuru-adat" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Email</th>
                                            <th>Masa Jabatan</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prajuru_desa_adat as $prajuru_desa)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan }} {{ $prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->nama}}@if($prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang }} @endif</td>
                                                <td>{{ ucwords(str_replace('_', ' ', $prajuru_desa->jabatan))}}</td>
                                                <td>{{ $prajuru_desa->user->email}}</td>
                                                <td>{{ date('d M Y', strtotime($prajuru_desa->tanggal_mulai_menjabat)) }} s.d. {{ date('d M Y', strtotime($prajuru_desa->tanggal_akhir_menjabat)) }}</td>
                                                <td class="text-center">
                                                    <button button type="button" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_prajuru_desa_adat({{ $prajuru_desa->id }})"><i class="fas fa-edit"></i></button>
                                                    <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_prajuru_desa_adat({{ $prajuru_desa->id }})"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="banjar_adat" role="tabpanel" aria-labelledby="example-tab">
                            <div class="row mb-3">
                                <div class="col-12 col-md-10">
                                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="tambah_prajuru_banjar_adat()">
                                        <span class="icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <span class="text">Tambah Prajuru</span>
                                    </button>
                                </div>
                                <div class="col-12 col-md-2 d-flex justify-content-end small">
                                    <select class="select2 custom-select @error ('status_prajuru_banjar_adat') is-invalid @enderror" name="status_prajuru_banjar_adat" id="status_prajuru_banjar_adat" style="width: 100%">
                                        <option value="menjabat">Prajuru Aktif</option>
                                        <option value="purna">Prajuru Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="datatable table-responsive" id="dataTable-prajuru-banjar-adat">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Tambah Prajuru Desa Adat Modal -->
    <div class="modal fade" id="create_prajuru_desa_adat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-prajuru-desa-adat" method="post" action="{{route('desa-prajuru-desa-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Prajuru Desa Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder') }}" required>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                    <button class="btn btn-primary btn-icon-split" type="button" onclick="tambah_prajuru_desa_adat_pilih_krama_modal()">
                                        <span class="text">Pilih Krama</span>
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <input type="text" class="form-control @error ('krama_mipil') is-invalid @enderror"  id="krama_mipil" name="krama_mipil"  value="{{ old('krama_mipil') }}" required hidden>
                            @error('krama_mipil')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="krama" class="small">Jabatan<span class="text-danger small">*</span></label>
                                    <select class="select2 custom-select @error ('jabatan') is-invalid @enderror" name="jabatan" id="jabatan" style="width: 100%" required>
                                        <option value="">Pilih Jabatan</option>
                                        <option value="bendesa" @if(old('jabatan') == 'bendesa') selected @endif>Bendesa</option>
                                        <option value="pangliman" @if(old('jabatan') == 'pangliman') selected @endif>Pangliman</option>
                                        <option value="penyarikan" @if(old('jabatan') == 'penyarikan') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen" @if(old('jabatan') == 'patengen') selected @endif>Patengen/Juru Raksa</option>
                                    </select>
                                    @error('jabatan')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Jabatan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email" class="small">Email<span class="text-danger small">*</span></label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan Email Prajuru Desa Adat" required>
                                    @error('email')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Email wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai_menjabat" class="small">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('tanggal_mulai_menjabat') is-invalid @enderror" name="tanggal_mulai_menjabat" id="tanggal_mulai_menjabat" value="{{ old('tanggal_mulai_menjabat') }}" placeholder="Tanggal Mulai Menjabat" required>
                                    @error('tanggal_mulai_menjabat')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Mulai Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_akhir_menjabat" class="small">Tanggal Akhir Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('tanggal_akhir_menjabat') is-invalid @enderror" name="tanggal_akhir_menjabat" id="tanggal_akhir_menjabat" value="{{ old('tanggal_akhir_menjabat') }}" placeholder="Tanggal Akhir Menjabat" required>
                                    @error('tanggal_akhir_menjabat')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Akhir Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <label for="email" class="small text-warning"><i class="fas fa-lock"></i> (Password dibuat secara otomatis menggunakan Nomor Krama Mipil yang dipilih sebagai Prajuru)</label>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Prajuru Desa Adat Modal -->
    <div class="modal fade" id="edit_prajuru_desa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-edit-prajuru-desa" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Prajuru Desa Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_edit">
                        <div class="form-group">
                            <label for="title">Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('edit_krama_mipil_placeholder') is-invalid @enderror"  id="edit_krama_mipil_placeholder" name="edit_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('edit_krama_mipil_placeholder') }}" required readonly>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                    <button class="btn btn-primary btn-icon-split" type="button" onclick="edit_prajuru_desa_adat_pilih_krama_modal()">
                                        <span class="text">Pilih Krama</span>
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <input type="text" class="form-control @error ('edit_krama_mipil') is-invalid @enderror"  id="edit_krama_mipil" name="edit_krama_mipil"  value="{{ old('edit_krama_mipil') }}" required hidden>
                            @error('edit_krama_mipil')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_jabatan" class="small">Jabatan<span class="text-danger small">*</span></label>
                                    <select class="select2 custom-select @error ('edit_jabatan') is-invalid @enderror" name="edit_jabatan" id="edit_jabatan" style="width: 100%" required>
                                        <option value="">Pilih Jabatan</option>
                                        <option value="bendesa" @if(old('edit_jabatan') == 'bendesa') selected @endif>Bendesa</option>
                                        <option value="pangliman" @if(old('edit_jabatan') == 'pangliman') selected @endif>Pangliman</option>
                                        <option value="penyarikan" @if(old('edit_jabatan') == 'penyarikan') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen" @if(old('edit_jabatan') == 'patengen') selected @endif>Patengen/Juru Raksa</option>
                                    </select>
                                    @error('edit_jabatan')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Jabatan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email" class="small">Email<span class="text-danger small">*</span></label>
                                    <input class="form-control @error('edit_email') is-invalid @enderror" id="edit_email" name="edit_email" type="edit_email" value="{{ old('edit_email') }}" placeholder="Masukkan Email Prajuru Desa Adat" required>
                                    @error('edit_email')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Email wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_tanggal_mulai_menjabat" class="small">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('edit_tanggal_mulai_menjabat') is-invalid @enderror" name="edit_tanggal_mulai_menjabat" id="edit_tanggal_mulai_menjabat" value="{{ old('edit_tanggal_mulai_menjabat') }}" placeholder="Tahun Mulai Menjabat" required>
                                    @error('edit_tanggal_mulai_menjabat')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Mulai Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_tanggal_akhir_menjabat" class="small">Tanggal Akhir Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('edit_tanggal_akhir_menjabat') is-invalid @enderror" name="edit_tanggal_akhir_menjabat" id="edit_tanggal_akhir_menjabat" value="{{ old('edit_tahun_akhir_menjabat') }}" placeholder="Tahun Akhir Menjabat" required>
                                    @error('edit_tanggal_akhir_menjabat')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Akhir Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_status_prajuru" class="small">Status Prajuru<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('edit_status_prajuru') is-invalid @enderror" name="edit_status_prajuru" id="edit_status_prajuru" style="width: 100%" required>
                                <option value="1" @if(old('edit_status_prajuru') == '1') selected @endif>Aktif</option>
                                <option value="0" @if(old('edit_status_prajuru') == '0') selected @endif>Tidak Aktif</option>
                            </select>
                            @error('edit_status_prajuru')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Prajuru wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group ml-2">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="customCheck1" name="reset_password" type="checkbox">
                                <label class="custom-control-label" for="customCheck1">Reset Password</label>
                            </div>
                            <label for="password" class="small text-warning"><i class="fas fa-lock"></i> (Reset password akan secara otomatis menggunakan Nomor Krama Mipil sebagai password)</label>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambah Prajuru Banjar Adat Modal -->
    <div class="modal fade" id="create_prajuru_banjar_adat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-banjar-dinas" method="post" action="{{route('desa-prajuru-banjar-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Prajuru Banjar Adat</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="banjar_adat_id" class="small">Banjar Adat<span class="text-danger small">*</span></label>
                        <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat_id" style="width: 100%" required>
                            <option value="">Pilih Banjar Adat</option>
                            @foreach($banjar_adat as $banjar)
                                <option value="{{ $banjar->id }}" @if(old('banjar_adat_id') == $banjar->id) selected @endif>{{ $banjar->nama_banjar_adat }}</option>
                            @endforeach
                        </select>
                        @error('banjar_adat_id')
                            <div class="invalid-feedback text-start">
                                {{ $message }}
                            </div>
                        @else
                            <div class="invalid-feedback">
                                Banjar Adat wajib dipilih
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Krama Mipil<span class="text-danger small">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error ('krama_mipil_banjar_placeholder') is-invalid @enderror"  id="krama_mipil_banjar_placeholder" name="krama_mipil_banjar_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_banjar_placeholder') }}" required>
                            <div class="input-group-append">
                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                <button class="btn btn-primary btn-icon-split" type="button" onclick="tambah_prajuru_banjar_adat_pilih_krama_modal()">
                                    <span class="text">Pilih Krama</span>
                                    <span class="icon">
                                        <i class="fas fa-user-plus"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <input type="text" class="form-control @error ('krama_mipil_banjar') is-invalid @enderror"  id="krama_mipil_banjar" name="krama_mipil_banjar"  value="{{ old('krama_mipil_banjar') }}" required hidden>
                        @error('krama_mipil_banjar')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @else
                            <div class="invalid-feedback">
                                Krama Mipil wajib dipilih
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="jabatan_banjar" class="small">Jabatan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('jabatan_banjar') is-invalid @enderror" name="jabatan_banjar" id="jabatan_banjar" style="width: 100%" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="kelihan_adat" @if(old('jabatan_banjar') == 'kelihan_adat') selected @endif>Kelihan Adat</option>
                                    <option value="pangliman_banjar" @if(old('jabatan_banjar') == 'pangliman_banjar') selected @endif>Pangliman</option>
                                    <option value="penyarikan_banjar" @if(old('jabatan_banjar') == 'penyarikan_banjar') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen_banjar" @if(old('jabatan_banjar') == 'patengen_banjar') selected @endif>Patengen/Juru Raksa</option>
                                </select>
                                @error('jabatan_banjar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Jabatan wajib dipilih
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="email_banjar" class="small">Email<span class="text-danger small">*</span></label>
                                <input class="form-control @error('email_banjar') is-invalid @enderror" id="email_banjar" name="email_banjar" type="email" value="{{ old('email_banjar') }}" placeholder="Masukkan Email Prajuru Banjar Adat" required>
                                @error('email_banjar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Email wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="tanggal_mulai_menjabat_banjar" class="small">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_mulai_menjabat_banjar') is-invalid @enderror" name="tanggal_mulai_menjabat_banjar" id="tanggal_mulai_menjabat_banjar" value="{{ old('tanggal_mulai_menjabat_banjar') }}" placeholder="Tanggal Mulai Menjabat" required>
                                @error('tanggal_mulai_menjabat_banjar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal Mulai Menjabat wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="tanggal_akhir_menjabat_banjar" class="small">Tanggal Akhir Menjabat<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_akhir_menjabat_banjar') is-invalid @enderror" name="tanggal_akhir_menjabat_banjar" id="tanggal_akhir_menjabat_banjar" value="{{ old('tanggal_akhir_menjabat_banjar') }}" placeholder="Tahun Akhir Menjabat" required>
                                @error('tanggal_akhir_menjabat_banjar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal Akhir Menjabat wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <label for="email" class="small text-warning"><i class="fas fa-lock"></i> (Password dibuat secara otomatis menggunakan Nomor Krama Mipil yang dipilih sebagai Prajuru)</label>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
            </div>
            </form>
        </div>
    </div>

    <!-- Edit Prajuru Banjar Adat Modal -->
    <div class="modal fade" id="edit_prajuru_banjar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-edit-prajuru-banjar" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Prajuru Banjar Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_banjar">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_edit_banjar">
                        <div class="form-group">
                            <label for="edit_banjar_adat_id" class="small">Banjar Adat<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('edit_banjar_adat_id') is-invalid @enderror" name="edit_banjar_adat_id" id="edit_banjar_adat_id" style="width: 100%" required>
                                <option value="">Pilih Banjar Adat</option>
                                @foreach($banjar_adat as $banjar)
                                    <option value="{{ $banjar->id }}" @if(old('edit_banjar_adat_id') == $banjar->id) selected @endif>{{ $banjar->nama_banjar_adat }}</option>
                                @endforeach
                            </select>
                            @error('edit_banjar_adat_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Banjar Adat wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="title">Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('edit_krama_mipil_banjar_placeholder') is-invalid @enderror"  id="edit_krama_mipil_banjar_placeholder" name="edit_krama_mipil_banjar_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('edit_krama_mipil_banjar_placeholder') }}" required readonly>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                    <button class="btn btn-primary btn-icon-split" type="button" onclick="edit_prajuru_banjar_adat_pilih_krama_modal()">
                                        <span class="text">Pilih Krama</span>
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <input type="text" class="form-control @error ('edit_krama_mipil_banjar') is-invalid @enderror"  id="edit_krama_mipil_banjar" name="edit_krama_mipil_banjar"  value="{{ old('edit_krama_mipil_banjar') }}" required hidden>
                            @error('edit_krama_mipil_banjar')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_jabatan_banjar" class="small">Jabatan<span class="text-danger small">*</span></label>
                                    <select class="select2 custom-select @error ('edit_jabatan_banjar') is-invalid @enderror" name="edit_jabatan_banjar" id="edit_jabatan_banjar" style="width: 100%" required>
                                        <option value="">Pilih Jabatan</option>
                                        <option value="kelihan_adat" @if(old('edit_jabatan_banjar') == 'kelihan_adat') selected @endif>Kelihan Adat</option>
                                        <option value="pangliman_banjar" @if(old('edit_jabatan_banjar') == 'pangliman_banjar') selected @endif>Pangliman</option>
                                        <option value="penyarikan_banjar" @if(old('edit_jabatan_banjar') == 'penyarikan_banjar') selected @endif>Penyarikan/Juru Tulis</option>
                                        <option value="patengen_banjar" @if(old('edit_jabatan_banjar') == 'patengen_banjar') selected @endif>Patengen/Juru Raksa</option>
                                    </select>
                                    @error('edit_jabatan_banjar')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Jabatan wajib dipilih
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_email_banjar" class="small">Email<span class="text-danger small">*</span></label>
                                    <input class="form-control @error('edit_email_banjar') is-invalid @enderror" id="edit_email_banjar" name="edit_email_banjar" type="email" value="{{ old('edit_email_banjar') }}" placeholder="Masukkan Email Prajuru Banjar Adat" required>
                                    @error('edit_email_banjar')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Email wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_tanggal_mulai_menjabat_banjar" class="small">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('edit_tanggal_mulai_menjabat_banjar') is-invalid @enderror" name="edit_tanggal_mulai_menjabat_banjar" id="edit_tanggal_mulai_menjabat_banjar" value="{{ old('edit_tanggal_mulai_menjabat_banjar') }}" placeholder="Tahun Mulai Menjabat" required>
                                    @error('edit_tanggal_mulai_menjabat_banjar')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Mulai Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_tanggal_akhir_menjabat_banjar" class="small">Tahun Akhir Menjabat<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('edit_tanggal_akhir_menjabat_banjar') is-invalid @enderror" name="edit_tanggal_akhir_menjabat_banjar" id="edit_tanggal_akhir_menjabat_banjar" value="{{ old('edit_tanggal_akhir_menjabat_banjar') }}" placeholder="Tahun Akhir Menjabat" required>
                                    @error('edit_tanggal_akhir_menjabat_banjar')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Akhir Menjabat wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_status_prajuru_banjar" class="small">Status Prajuru<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error ('edit_status_prajuru') is-invalid @enderror" name="edit_status_prajuru_banjar" id="edit_status_prajuru_banjar" style="width: 100%" required>
                                <option value="1" @if(old('edit_status_prajuru_banjar') == '1') selected @endif>Aktif</option>
                                <option value="0" @if(old('edit_status_prajuru_banjar') == '0') selected @endif>Tidak Aktif</option>
                            </select>
                            @error('edit_status_prajuru_banjar')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Status Prajuru wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group ml-2">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="customCheck1" name="reset_password_banjar" type="checkbox">
                                <label class="custom-control-label" for="customCheck1">Reset Password</label>
                            </div>
                            <label for="password" class="small text-warning"><i class="fas fa-lock"></i> (Reset password akan secara otomatis menggunakan Nomor Krama Mipil sebagai password)</label>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Select Krama Mipil Modal -->
    <div class="modal fade" id="select_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <form id="form-create-prajuru-desa-adat" method="post" action="{{route('desa-prajuru-desa-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Pilih Krama Mipil</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_select_krama_mipil">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_select_krama_mipil">
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL --}}

    {{-- HIDDEN FORM --}}
    <form id="form-delete-prajuru-desa" method="post" action="/">
        @method('delete')
        @csrf
    </form>

    <form id="form-delete-prajuru-banjar" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('success'))
        @if($status = Session::get('prajuru'))
            <script>
                $(document).ready(function(){
                    $('#desa_adat').removeClass('active');
                    $('#desa_adat_tab').removeClass('active');
                    $('#banjar_adat').addClass('active');
                    $('#banjar_adat_tab').tab('show');
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
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('email') || $errors->has('jabatan') || $errors->has('tanggal_mulai_menjabat') || $errors->has('tanggal_akhir_menjabat'))
            <script>
                $(document).ready(function(){
                    $('#create_prajuru_desa_adat').modal('show');
                });
            </script>
        @elseif($errors->has('email_banjar') || $errors->has('jabatan_banjar') || $errors->has('tanggal_mulai_menjabat_banjar') || $errors->has('tanggal_akhir_menjabat_banjar'))
        <script>
            $(document).ready(function(){
                $('#desa_adat').removeClass('active');
                $('#desa_adat_tab').removeClass('active');
                $('#banjar_adat').addClass('active');
                $('#banjar_adat_tab').tab('show');
                $('#create_prajuru_banjar_adat').modal('show');
            });
        </script>
        @elseif($errors->has('edit_email') || $errors->has('edit_jabatan') || $errors->has('edit_tanggal_mulai_menjabat') || $errors->has('edit_tanggal_akhir_menjabat'))
        <script>
            $(document).ready(function(){
                $('#body_loading').hide();
                $('#edit_prajuru_desa').modal('show');
            });
        </script>
        @elseif($errors->has('edit_email_banjar') || $errors->has('edit_jabatan_banjar') || $errors->has('edit_tanggal_mulai_menjabat_banjar') || $errors->has('edit_tanggal_akhir_menjabat_banjar'))
        <script>
            $(document).ready(function(){
                $('#desa_adat').removeClass('active');
                $('#desa_adat_tab').removeClass('active');
                $('#banjar_adat').addClass('active');
                $('#banjar_adat_tab').tab('show');
                $('#body_loading_banjar').hide();
                $('#edit_banjar_desa').modal('show');
            });
        </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        $(document).ready( function () {
            $(".dataTable-prajuru").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari Prajuru...",
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

            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            $('#sidebarPrajuru').removeClass('collapsed');
            $('#collapsePrajuru').addClass('show');
            $('#collapsePrajuru').addClass('active');
            $('#nav-link-akun-prajuru').addClass('active');
        });

        //TAMBAH PRAJURU DESA ADAT
        function tambah_prajuru_desa_adat(){
            $('#create_prajuru_desa_adat').modal('show');
        }

        function tambah_prajuru_desa_adat_pilih_krama_modal(){
            $('#body_loading_select_krama_mipil').show();
            $('#body_select_krama_mipil').hide();
            $('#select_krama_mipil_modal').modal('show');
            $('#create_prajuru_desa_adat').modal('hide');
            var url = "{{ route('desa-prajuru-desa-get-krama-mipil') }}";
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    if ($.fn.DataTable.isDataTable("#dataTable-krama-mipil")) {
                        $('#dataTable-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_krama_mipil').html(result.hasil);        
                    $("#dataTable-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Krama Mipil...",
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
                    $('#body_loading_select_krama_mipil').hide();
                    $('#body_select_krama_mipil').show();
                }
            });
        }

        function tambah_prajuru_desa_adat_pilih_krama(id, nama){
            $('#krama_mipil').val(id);
            $('#krama_mipil_placeholder').val(nama);
            $('#krama_mipil_placeholder').prop('readonly', true);
            $('#create_prajuru_desa_adat').modal('show');
            $('#select_krama_mipil_modal').modal('hide');
        }
        //TAMBAH PRAJURU DESA ADAT

        //EDIT PRAJURU DESA ADAT
        function edit_prajuru_desa_adat(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_prajuru_desa').modal('show');
            var url = "{{ route('desa-prajuru-desa-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('desa-prajuru-desa-update', ":id") }}";
                url = url.replace(':id', result.prajuru_desa.id);
                $("#form-edit-prajuru-desa").attr("action", url);
                $('#edit_jabatan').val(result.prajuru_desa.jabatan).trigger('change');
                $('#edit_status_prajuru').val(result.prajuru_desa.status_prajuru_desa_adat).trigger('change');
                $('#edit_tanggal_mulai_menjabat').datepicker("update", result.prajuru_desa.tanggal_mulai_menjabat);
                $('#edit_tanggal_akhir_menjabat').datepicker("update", result.prajuru_desa.tanggal_akhir_menjabat);
                $('#edit_email').val(result.user.email);
                $('#edit_krama_mipil').val(result.prajuru_desa.krama_mipil.id);
                $('#edit_krama_mipil_placeholder').val(result.prajuru_desa.krama_mipil.cacah_krama_mipil.penduduk.nama)
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

        function edit_prajuru_desa_adat_pilih_krama_modal(){
            $('#body_loading_select_krama_mipil').show();
            $('#body_select_krama_mipil').hide();
            $('#select_krama_mipil_modal').modal('show');
            $('#edit_prajuru_desa').modal('hide');
            var url = "{{ route('desa-prajuru-desa-get-krama-mipil-edit') }}";
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    if ($.fn.DataTable.isDataTable("#dataTable-krama-mipil")) {
                        $('#dataTable-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_krama_mipil').html(result.hasil);        
                    $("#dataTable-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Krama Mipil...",
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
                    $('#body_loading_select_krama_mipil').hide();
                    $('#body_select_krama_mipil').show();
                }
            });
        }

        function edit_prajuru_desa_adat_pilih_krama(id, nama){
            $('#edit_krama_mipil').val(id);
            $('#edit_krama_mipil_placeholder').val(nama);
            $('#edit_krama_mipil_placeholder').prop('readonly', true);
            $('#edit_prajuru_desa').modal('show');
            $('#select_krama_mipil_modal').modal('hide');
        }
        //EDIT PRAJURU DESA ADAT

        //DELETE PRAJURU DESA ADAT
        function delete_prajuru_desa_adat(id){
            Swal.fire({
                title: 'Hapus Prajuru',
                text: "Apakah anda yakin ingin menghapus Prajuru Desa Adat ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-prajuru-desa-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-prajuru-desa').attr("action", url);
                        $('#form-delete-prajuru-desa').submit();
                    }
                })
        }
        //DELETE PRAJURU DESA ADAT

        //TAMBAH PRAJURU BANJAR ADAT
        function tambah_prajuru_banjar_adat(){
            $('#create_prajuru_banjar_adat').modal('show');
        }

        function tambah_prajuru_banjar_adat_pilih_krama_modal(){
            $('#body_loading_select_krama_mipil').show();
            $('#body_select_krama_mipil').hide();
            $('#create_prajuru_banjar_adat').modal('hide');
            $('#select_krama_mipil_modal').modal('show');
            var url = "{{ route('desa-prajuru-banjar-get-krama-mipil', ":banjar_adat_id") }}";
            url = url.replace(':banjar_adat_id', $("#banjar_adat_id").val());
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    if ($.fn.DataTable.isDataTable("#dataTable-krama-mipil")) {
                        $('#dataTable-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_krama_mipil').html(result.hasil);        
                    $("#dataTable-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Krama Mipil...",
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
                    $('#body_loading_select_krama_mipil').hide();
                    $('#body_select_krama_mipil').show();
                }
            });
        }

        function tambah_prajuru_banjar_adat_pilih_krama(id, nama){
            $('#krama_mipil_banjar').val(id);
            $('#krama_mipil_banjar_placeholder').val(nama);
            $('#krama_mipil_banjar_placeholder').prop('readonly', true);
            $('#create_prajuru_banjar_adat').modal('show');
            $('#select_krama_mipil_modal').modal('hide');
        }
        //TAMBAH PRAJURU BANJAR ADAT

        //EDIT PRAJURU BANJAR ADAT
        function edit_prajuru_banjar_adat(id){
            $("#body_edit_banjar").hide();
            $("#body_loading_banjar").show();
            $('#edit_prajuru_banjar').modal('show');
            var url = "{{ route('desa-prajuru-banjar-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('desa-prajuru-banjar-update', ":id") }}";
                url = url.replace(':id', result.prajuru_banjar.id);
                $("#form-edit-prajuru-banjar").attr("action", url);
                $('#edit_jabatan_banjar').val(result.prajuru_banjar.jabatan).trigger('change');
                $('#edit_banjar_adat_id').val(result.prajuru_banjar.banjar_adat_id).trigger('change');
                $('#edit_status_prajuru_banjar').val(result.prajuru_banjar.status_prajuru_banjar_adat).trigger('change');
                $('#edit_tanggal_mulai_menjabat_banjar').datepicker("update", result.prajuru_banjar.tanggal_mulai_menjabat);
                $('#edit_tanggal_akhir_menjabat_banjar').datepicker("update", result.prajuru_banjar.tanggal_akhir_menjabat);
                $('#edit_email_banjar').val(result.user.email);
                $('#edit_krama_mipil_banjar').val(result.prajuru_banjar.krama_mipil.id);
                $('#edit_krama_mipil_banjar_placeholder').val(result.prajuru_banjar.krama_mipil.cacah_krama_mipil.penduduk.nama)

                $("#body_loading_banjar").hide();
                $("#body_edit_banjar").show();                 
                }
            });
        }

        function edit_prajuru_banjar_adat_pilih_krama_modal(){
            $('#body_loading_select_krama_mipil').show();
            $('#body_select_krama_mipil').hide();
            $('#select_krama_mipil_modal').modal('show');
            $('#edit_prajuru_banjar').modal('hide');
            var url = "{{ route('desa-prajuru-banjar-get-krama-mipil-edit', ":banjar_adat_id") }}";
            url = url.replace(':banjar_adat_id', $("#edit_banjar_adat_id").val());
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    if ($.fn.DataTable.isDataTable("#dataTable-krama-mipil")) {
                        $('#dataTable-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_krama_mipil').html(result.hasil);        
                    $("#dataTable-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Krama Mipil...",
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
                    $('#body_loading_select_krama_mipil').hide();
                    $('#body_select_krama_mipil').show();
                }
            });
        }

        function edit_prajuru_banjar_adat_pilih_krama(id, nama){
            $('#edit_krama_mipil_banjar').val(id);
            $('#edit_krama_mipil_banjar_placeholder').val(nama);
            $('#edit_krama_mipil_banjar_placeholder').prop('readonly', true);
            $('#edit_prajuru_banjar').modal('show');
            $('#select_krama_mipil_modal').modal('hide');
        }
        //EDIT PRAJURU BANJAR ADAT

        //DELETE PRAJURU BANJAR ADAT
        function delete_prajuru_banjar_adat(id){
            Swal.fire({
                title: 'Hapus Prajuru',
                text: "Apakah anda yakin ingin menghapus Prajuru Banjar Adat ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-prajuru-banjar-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-prajuru-banjar').attr("action", url);
                        $('#form-delete-prajuru-banjar').submit();
                    }
                })
        }
        //DELETE PRAJURU BANJAR ADAT

         //Select 2
         $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
            }
        });

        $(".select-krama").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                },
                inputTooShort: function() {
                    return 'Masukkan NIK atau Nomor Induk Krama';
                }
            },
            minimumInputLength: 16,
            ajax: {
                url: '{{ route("desa-prajuru-krama-search") }}',
                dataType: 'json',
            },
        });

        $('#banjar_adat_id').on('change', function(){
            if($(this).val() == ''){
                $('#krama_banjar').prop('disabled', true);
            }else if($(this).val() != ''){
                $('#krama_banjar').prop('disabled', false);
            }
        });

        $('#status_prajuru_desa_adat').on('change', function(){
            if($(this).val() != ""){
                $("#overlay").css('display', 'flex');
                var url = "{{ route('desa-prajuru-desa-filter', ":status") }}";
                url = url.replace(':status', $(this).val());
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        console.log(result);
                        if ($.fn.DataTable.isDataTable(".dataTable-prajuru")) {
                            $('.dataTable-prajuru').DataTable().clear().destroy();
                        }
                        $('#dataTable-prajuru-adat').html(result.hasil);  
                        $("#overlay").fadeOut();      
                        $(".dataTable-prajuru").DataTable({
                            "responsive": false, "lengthChange": true, "autoWidth": false,
                            "oLanguage": {
                                "sSearch": "Cari:",
                                "sZeroRecords": "Data tidak ditemukan",
                                "sSearchPlaceholder": "Cari Prajuru...",
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
                    }
                });
            }
        });

        $('#status_prajuru_banjar_adat').on('change', function(){
            if($(this).val() != ""){
                $("#overlay").css('display', 'flex');
                var url = "{{ route('desa-prajuru-banjar-filter', ":status") }}";
                url = url.replace(':status', $(this).val());
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        console.log(result);
                        if ($.fn.DataTable.isDataTable(".dataTable-prajuru")) {
                            $('.dataTable-prajuru').DataTable().clear().destroy();
                        }
                        $('#dataTable-prajuru-banjar-adat').html(result.hasil);  
                        $("#overlay").fadeOut();      
                        $(".dataTable-prajuru").DataTable({
                            "responsive": false, "lengthChange": true, "autoWidth": false,
                            "oLanguage": {
                                "sSearch": "Cari:",
                                "sZeroRecords": "Data tidak ditemukan",
                                "sSearchPlaceholder": "Cari Prajuru...",
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
                    }
                });
            }
        });

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