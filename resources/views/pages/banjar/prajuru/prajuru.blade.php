@extends('layouts.banjar.banjar')
@section('title', 'Daftar Prajuru Banjar Adat')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .input-group.is-invalid {
            ~ .invalid-feedback {
                display: block;
            }
        }
    </style>
@endpush
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users-cog mr-2"></i></div>
                                Prajuru Banjar Adat
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Prajuru Banjar Adat</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card card-header-actions mb-4">
                <div class="card-header">
                    <div>
                        Prajuru <span class="text-dark">Banjar Adat {{ Session::get('banjar_adat_nama') }}</span>
                    </div>
                    <button class="btn btn-sm btn-primary float-right" type="button" onclick="filter_modal()"><i class="fas fa-filter mr-2"></i>Filter Prajuru</button>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" onclick="tambah_prajuru()">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Prajuru</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-prajuru" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Tempekan</th>
                                    <th>Email</th>
                                    <th>Masa Jabatan</th>
                                    <th style="width: 12%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Tambah Prajuru Banjar Adat Modal -->
    <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-prajuru" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Prajuru Banjar Adat</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small" for="title">Krama Mipil<span class="text-danger small">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error ('krama_mipil_placeholder') is-invalid @enderror"  id="krama_mipil_placeholder" name="krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder') }}" required>
                            <div class="input-group-append">
                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_krama_mipil_modal()">
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
                                <label class="small" for="jabatan">Jabatan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('jabatan') is-invalid @enderror" name="jabatan" id="jabatan" style="width: 100%" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="kelihan_adat" @if(old('jabatan') == 'kelihan_adat') selected @endif>Kelihan Adat</option>
                                    <option value="pangliman_banjar" @if(old('jabatan') == 'pangliman_banjar') selected @endif>Pangliman/Patajuh</option>
                                    <option value="penyarikan_banjar" @if(old('jabatan') == 'penyarikan_banjar') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen_banjar" @if(old('jabatan') == 'patengen_banjar') selected @endif>Patengen/Juru Raksa</option>
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
                                <label class="small" for="email">Email<span class="text-danger small">*</span></label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan Email Prajuru Banjar Adat" required>
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
                                <label class="small" for="tanggal_mulai_menjabat">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
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
                                <label class="small" for="tanggal_akhir_menjabat">Tanggal Akhir Menjabat<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_akhir_menjabat') is-invalid @enderror" name="tanggal_akhir_menjabat" id="tanggal_akhir_menjabat" value="{{ old('tanggal_akhir_menjabat') }}" placeholder="Tahun Akhir Menjabat" required>
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
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="small" for="password">Password<span class="text-danger small">*</span></label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" value="{{ old('password') }}" placeholder="Masukkan Password">
                                @error('password')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Password wajib diisi
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="konfirmasi_pass1" style="display:none">
                                    Password tidak sama!
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="small" for="password">Konfirmasi Password<span class="text-danger small">*</span></label>
                                <input class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" type="password" value="{{ old('confirm_password') }}" placeholder="Masukkan Konfirmasi Password">
                                @error('confirm_password')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Konfirmasi Password wajib diisi
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="konfirmasi_pass2" style="display:none">
                                    Password tidak sama!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" onclick="simpan()">Simpan</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Edit Prajuru Banjar Adat Modal -->
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-edit-prajuru" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Prajuru Banjar Adat</h5>
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
                        <label class="small" for="title">Krama Mipil<span class="text-danger small">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error ('edit_krama_mipil_placeholder') is-invalid @enderror"  id="edit_krama_mipil_placeholder" name="edit_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder') }}" required readonly>
                            <div class="input-group-append">
                                {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                <button class="btn btn-primary btn-icon-split" type="button" onclick="edit_pilih_krama_mipil_modal()">
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
                                <label class="small" for="edit_jabatan">Jabatan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('edit_jabatan') is-invalid @enderror" name="edit_jabatan" id="edit_jabatan" style="width: 100%" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="kelihan_adat" @if(old('edit_jabatan') == 'kelihan_adat') selected @endif>Kelihan Adat</option>
                                    <option value="pangliman_banjar" @if(old('edit_jabatan') == 'pangliman_banjar') selected @endif>Pangliman/Patajuh</option>
                                    <option value="penyarikan_banjar" @if(old('edit_jabatan') == 'penyarikan_banjar') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen_banjar" @if(old('edit_jabatan') == 'patengen_banjar') selected @endif>Patengen/Juru Raksa</option>
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
                                <label class="small" for="edit_email">Email<span class="text-danger small">*</span></label>
                                <input class="form-control @error('edit_email') is-invalid @enderror" id="edit_email" name="edit_email" type="edit_email" value="{{ old('edit_email') }}" placeholder="Masukkan Email Prajuru Banjar Adat" required>
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
                                <label class="small" for="edit_tanggal_mulai_menjabat">Tanggal Mulai Menjabat<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('edit_tanggal_mulai_menjabat') is-invalid @enderror" name="edit_tanggal_mulai_menjabat" id="edit_tanggal_mulai_menjabat" value="{{ old('edit_tanggal_mulai_menjabat') }}" placeholder="Tanggal Mulai Menjabat" required>
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
                                <label class="small" for="edit_tanggal_akhir_menjabat">Tanggal Akhir Menjabat<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('edit_tanggal_akhir_menjabat') is-invalid @enderror" name="edit_tanggal_akhir_menjabat" id="edit_tanggal_akhir_menjabat" value="{{ old('edit_tanggal_akhir_menjabat') }}" placeholder="Tahun Akhir Menjabat" required>
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
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="small" for="password">Password</label>
                                <input class="form-control @error('edit_password') is-invalid @enderror" id="edit_password" name="edit_password" type="password" value="{{ old('edit_password') }}" placeholder="Masukkan Password">
                                @error('edit_password')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Password wajib diisi
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="edit_konfirmasi_pass1" style="display:none">
                                    Password tidak sama!
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="small" for="password">Konfirmasi Password</label>
                                <input class="form-control @error('edit_confirm_password') is-invalid @enderror" id="edit_confirm_password" name="edit_confirm_password" type="password" value="{{ old('confirm_password') }}" placeholder="Masukkan Konfirmasi Password">
                                @error('edit_confirm_password')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Konfirmasi Password wajib diisi
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="edit_konfirmasi_pass2" style="display:none">
                                    Password tidak sama!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" onclick="simpan()">Simpan</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Detail Prajuru Banjar Adat Modal -->
    <div class="modal fade" id="show_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-show-prajuru" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Prajuru Banjar Adat</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="show_loading">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="body_show">
                    <div class="form-group">
                        <label class="small" for="title">Krama Mipil</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error ('show_krama_mipil_placeholder') is-invalid @enderror"  id="show_krama_mipil_placeholder" name="show_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder') }}" required disabled>
                        </div>
                        <input type="text" class="form-control @error ('show_krama_mipil') is-invalid @enderror"  id="show_krama_mipil" name="show_krama_mipil"  value="{{ old('show_krama_mipil') }}" required hidden>
                        @error('show_krama_mipil')
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
                                <label class="small" for="show_jabatan">Jabatan</label>
                                <select class="select2 custom-select @error ('show_jabatan') is-invalid @enderror" name="show_jabatan" id="show_jabatan" style="width: 100%" required disabled>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="kelihan_adat" @if(old('show_jabatan') == 'kelihan_adat') selected @endif>Kelihan Adat</option>
                                    <option value="pangliman_banjar" @if(old('show_jabatan') == 'pangliman_banjar') selected @endif>Pangliman/Patajuh</option>
                                    <option value="penyarikan_banjar" @if(old('show_jabatan') == 'penyarikan_banjar') selected @endif>Penyarikan/Juru Tulis</option>
                                    <option value="patengen_banjar" @if(old('show_jabatan') == 'patengen_banjar') selected @endif>Patengen/Juru Raksa</option>
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
                                <label class="small" for="show_email">Email</label>
                                <input class="form-control @error('show_email') is-invalid @enderror" id="show_email" name="show_email" type="show_email" value="{{ old('show_email') }}" placeholder="Masukkan Email Prajuru Banjar Adat" required disabled>
                                @error('show_email')
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
                                <label class="small" for="show_tanggal_mulai_menjabat">Tanggal Mulai Menjabat</label>
                                <input type="text" class="datepicker-here form-control @error ('show_tanggal_mulai_menjabat') is-invalid @enderror" name="show_tanggal_mulai_menjabat" id="show_tanggal_mulai_menjabat" value="{{ old('show_tanggal_mulai_menjabat') }}" placeholder="Tanggal Mulai Menjabat" required disabled>
                                @error('show_tanggal_mulai_menjabat')
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
                                <label class="small" for="show_tanggal_akhir_menjabat">Tanggal Akhir Menjabat</label>
                                <input type="text" class="datepicker-here form-control @error ('show_tanggal_akhir_menjabat') is-invalid @enderror" name="show_tanggal_akhir_menjabat" id="show_tanggal_akhir_menjabat" value="{{ old('show_tanggal_akhir_menjabat') }}" placeholder="Tahun Akhir Menjabat" required disabled>
                                @error('show_tanggal_akhir_menjabat')
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
                </div>
                <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Select Krama Mipil -->
    <div class="modal fade" id="select_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Krama Mipil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-krama-mipil" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:5%">No.</th>
                                    <th style="width:10%">No Krama Mipil</th>
                                    <th style="width:10%">NIK</th>
                                    <th>Nama</th>
                                    <th style="width: 16%">Tempat/Tanggal Lahir</th>
                                    <th style="width: 12%">Tempekan</th>
                                    <th style="width: 8%">Tindakan</th>
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

    <div class="modal fade" id="edit_select_krama_mipil_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Krama Mipil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="datatable">
                        <table class="table table-bordered table-hover w-100" id="dataTable-krama-mipil-edit" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:5%">No.</th>
                                    <th style="width:10%">No Krama Mipil</th>
                                    <th style="width:10%">NIK</th>
                                    <th>Nama</th>
                                    <th style="width: 16%">Tempat/Tanggal Lahir</th>
                                    <th style="width: 12%">Tempekan</th>
                                    <th style="width: 8%">Tindakan</th>
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray-100">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data Prajuru</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small" for="status" class="text-dark">Status Prajuru</label>
                        <select class="select2 custom-select @error ('status') is-invalid @enderror" name="status" id="status" style="width: 100%" aria-placeholder="Pilih Status Kelahiran" required>
                            <option value="">Semua Status</option>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="small" for="status" class="text-dark">Rentang Waktu Menjabat</label>
                        <input type="text" class="form-control" name="rentang_waktu" id="rentang_waktu" placeholder="Pilih Rentang Waktu" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-icon-split mb-3 text-end" type="button" onclick="filter_reset()">
                        <span class="icon">
                            <i class="fas fa-sync"></i>
                        </span>
                        <span class="text">Reset</span>
                    </button>
                    <button class="btn btn-success btn-icon-split mb-3 text-end" onclick="filter_submit()">
                        <span class="icon">
                            <i class="fas fa-filter"></i>
                        </span>
                        <span class="text">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="form-delete-prajuru" method="post" action="/">
        @method('delete')
        @csrf
    </form>
    {{-- HIDDEN FORM --}}
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    {{-- VALIDATION --}}
    @if (count($errors)>0)
        @if($errors->has('email') || $errors->has('krama_mipil') || $errors->has('jabatan') || $errors->has('tanggal_mulai_menjabat') || $errors->has('tanggal_akhir_menjabat') || $errors->has('password') || $errors->has('confirm_password'))
            <script>
                $(document).ready(function(){
                    $('#create_modal').modal('show');
                });
            </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        $(document).ready( function () {
            //Select 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });

            //DATE RANGE PICKER
            $('#rentang_waktu').daterangepicker({
                minDate: "01 Jan 2022",
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

            $('#rentang_waktu').val('');

            //DATEPICKER
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //Password Confirm
            $("#confirm_password").keyup(function() {
                let pass = $('#password').val();
                let confirm_pass = $('#confirm_password').val();
                if(pass == confirm_pass){
                    $('#konfirmasi_pass1').hide();
                    $('#konfirmasi_pass2').hide();
                }
            });

            $("#password").keyup(function() {
                let pass = $('#password').val();
                let confirm_pass = $('#confirm_password').val();
                if(pass == confirm_pass){
                    $('#konfirmasi_pass1').hide();
                    $('#konfirmasi_pass2').hide();
                }
            });

            $("#edit_confirm_password").keyup(function() {
                let pass = $('#edit_password').val();
                let confirm_pass = $('#edit_confirm_password').val();
                if(pass == confirm_pass){
                    $('#edit_konfirmasi_pass1').hide();
                    $('#edit_konfirmasi_pass2').hide();
                }
            });

            $("#edit_password").keyup(function() {
                let pass = $('#edit_password').val();
                let confirm_pass = $('#edit_confirm_password').val();
                if(pass == confirm_pass){
                    $('#edit_konfirmasi_pass1').hide();
                    $('#edit_konfirmasi_pass2').hide();
                }
            });
        });

        function tambah_prajuru(){
            $('#create_modal').modal('show');
        }

        function edit_prajuru(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_modal').modal('show');
            var url = "{{ route('banjar-prajuru-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('banjar-prajuru-update', ":id") }}";
                url = url.replace(':id', result.prajuru.id);
                $("#form-edit-prajuru").attr("action", url);
                $('#edit_krama_mipil_placeholder').val(result.prajuru.krama_mipil.cacah_krama_mipil.penduduk.nama);
                $('#edit_krama_mipil').val(result.prajuru.krama_mipil.id);
                $('#edit_email').val(result.user.email);
                $('#edit_jabatan').val(result.prajuru.jabatan).trigger('change');
                $('#edit_tanggal_mulai_menjabat').datepicker('update', result.prajuru.tanggal_mulai_menjabat);
                $('#edit_tanggal_akhir_menjabat').datepicker('update', result.prajuru.tanggal_akhir_menjabat);
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

        function detail_prajuru(id){
            $("#body_show").hide();
            $("#show_loading").show();
            $('#show_modal').modal('show');
            var url = "{{ route('banjar-prajuru-detail', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                console.log(result);
                $("#form-edit-prajuru").attr("action", url);
                $('#show_krama_mipil_placeholder').val(result.prajuru.krama_mipil.cacah_krama_mipil.penduduk.nama);
                $('#show_krama_mipil').val(result.prajuru.krama_mipil.id);
                $('#show_email').val(result.user.email);
                $('#show_jabatan').val(result.prajuru.jabatan).trigger('change');
                $('#show_tanggal_mulai_menjabat').datepicker('update', result.prajuru.tanggal_mulai_menjabat);
                $('#show_tanggal_akhir_menjabat').datepicker('update', result.prajuru.tanggal_akhir_menjabat);
                $("#show_loading").hide();
                $("#body_show").show();                 
                }
            });
        }

        function delete_prajuru(id){
            Swal.fire({
                title: 'Nonaktifkan Prajuru',
                text: "Apakah anda yakin ingin menonaktifkan Prajuru ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('banjar-prajuru-delete', ":id") }}";
                    url = url.replace(':id', id);
                    $('#form-delete-prajuru').attr("action", url);
                    $('#form-delete-prajuru').submit();
                }
            })
        }

        function simpan(){
            let pass = $('#password').val();
            let confirm_pass = $('#confirm_password').val();
            if(pass == confirm_pass){
                var url = "{{ route('banjar-prajuru-store') }}";
                $("#form-create-prajuru").attr("action", url);
                $('#form-create-prajuru').submit(function (e){
                    e.stopPropagation();
                });
            }else{
                $('#konfirmasi_pass1').show();
                $('#konfirmasi_pass2').show();
            }
            
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

    {{-- DATATABLE --}}
    <script>
        var TableDatatablesEditable = function () {
            var handleTable = function () {
                //DATATABLE PRAJURU
                var table = $('#dataTable-prajuru');
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
                        "processing": "Sedang diproses",
                    },
                    ajax: {
                        url : "{{ route('banjar-prajuru-datatable') }}",
                        data : function(d){
                            d.status = $('#status').val();
                            d.rentang_waktu = $('#rentang_waktu').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'krama_mipil.cacah_krama_mipil.penduduk.nama', class: "wrap" },
                        { data: 'jabatan', class: "wrap" },
                        { data: 'krama_mipil.cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap" },
                        { data: 'user.email', class: "wrap" },
                        { data: 'tanggal_mulai_menjabat', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 1,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.krama_mipil.cacah_krama_mipil.penduduk.gelar_depan){
                                    nama = nama + row.krama_mipil.cacah_krama_mipil.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.krama_mipil.cacah_krama_mipil.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 2,
                            render: function(data, type, row, meta){
                                if(data == 'kelihan_adat'){
                                    return 'Kelihan Adat';
                                }else if(data == 'pangliman_banjar'){
                                    return 'Pangliman/Patajuh';
                                }else if(data == 'penyarikan_banjar'){
                                    return 'Penyarikan/Juru Tulis';
                                }else if(data == 'patengen_banjar'){
                                    return 'Patengen/Juru Raksa';
                                }else{
                                    return '-';
                                }
                            }
                        },
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                var awal = moment(row.tanggal_mulai_menjabat).format('DD MMM YYYY');
                                var akhir = moment(row.tanggal_akhir_menjabat).format('DD MMM YYYY');
                                return awal+' s.d. '+akhir;
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

                //DATATABLE KRAMA MIPIL
                var table_krama = $('#dataTable-krama-mipil');
                var oTable_krama = table_krama.DataTable({
                    "autoWidth": false,
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
                        "sSearchPlaceholder": "Cari Krama...",
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
                        url : "{{ route('banjar-prajuru-datatable-krama-mipil') }}",
                        data : function(d){
                            d.status = $('#status').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_krama_mipil', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.nik', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.nama', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.tempat_lahir', class: "wrap" },
                        { data: 'cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.cacah_krama_mipil.penduduk.gelar_depan){
                                    nama = nama + row.cacah_krama_mipil.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.cacah_krama_mipil.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.cacah_krama_mipil.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                let tanggal_lahir = moment(row.cacah_krama_mipil.penduduk.tanggal_lahir).format('DD MMM YYYY');

                                return data+', '+tanggal_lahir;
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
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

                krama_filter = () => {
                    oTable_krama.ajax.reload();
                }

                //DATATABLE KRAMA MIPIL EDIT
                var table_krama = $('#dataTable-krama-mipil-edit');
                var oTable_krama = table_krama.DataTable({
                    "autoWidth": false,
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
                        "sSearchPlaceholder": "Cari Krama...",
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
                        url : "{{ route('banjar-prajuru-datatable-krama-mipil-edit') }}",
                        data : function(d){
                            d.status = $('#status').val();
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", class:"text-center", orderable: false, searchable: false, name: 'DT_RowIndex' },
                        { data: 'nomor_krama_mipil', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.nik', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.nama', class: "wrap" },
                        { data: 'cacah_krama_mipil.penduduk.tempat_lahir', class: "wrap" },
                        { data: 'cacah_krama_mipil.tempekan.nama_tempekan', class: "wrap" },
                        { data: 'link', class:"text-center", orderable: false, searchable: false, render: function ( data, type, row ) { return data; } },
                    ],
                    "columnDefs": [
                        {
                            'targets': 3,
                            render: function(data, type, row, meta){
                                let nama = '';
                                if(row.cacah_krama_mipil.penduduk.gelar_depan){
                                    nama = nama + row.cacah_krama_mipil.penduduk.gelar_depan; 
                                }
                                nama = nama + ' ' + data;
                                if(row.cacah_krama_mipil.penduduk.gelar_belakang){
                                    nama = nama + ', ' + row.cacah_krama_mipil.penduduk.gelar_belakang;
                                }
                                return nama;
                            }
                        },
                        {
                            'targets': 4,
                            render: function(data, type, row, meta){
                                let tanggal_lahir = moment(row.cacah_krama_mipil.penduduk.tanggal_lahir).format('DD MMM YYYY');

                                return data+', '+tanggal_lahir;
                            }
                        },
                        {
                            'targets': 5,
                            render: function(data, type, row, meta){
                                if(data){
                                    return data;
                                }else{
                                    return '-';
                                }
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

                edit_krama_filter = () => {
                    oTable_krama.ajax.reload();
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

        //Swal Init
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        //Swal init

        //Fungsi Pilih Cacah
        function pilih_krama_mipil_modal(){
            $('#select_krama_mipil_modal').on('show.bs.modal', function(e) {
                krama_filter();
            }).modal('show');
        }

        function pilih_krama_mipil(id, nama){
            $('#krama_mipil').val(id);
            $('#krama_mipil_placeholder').val(nama);
            $('#krama_mipil_placeholder').prop('readonly', true);
            $('#select_krama_mipil_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Mipil Berhasil Dipilih'
            })
        }

        function edit_pilih_krama_mipil_modal(){
            $('#edit_select_krama_mipil_modal').on('show.bs.modal', function(e) {
                krama_filter();
            }).modal('show');
        }

        function edit_pilih_krama_mipil(id, nama){
            $('#edit_krama_mipil').val(id);
            $('#edit_krama_mipil_placeholder').val(nama);
            $('#edit_krama_mipil_placeholder').prop('readonly', true);
            $('#edit_select_krama_mipil_modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Krama Mipil Berhasil Dipilih'
            })
        }
        //Fungsi Pilih Cacah

        //Filter
        function filter_modal(){
            $('#filter_modal').modal('show');
        }

        function filter_submit(){
            filter();
            $('#filter_modal').modal('hide');
        }

        function filter_reset(){
            $('#rentang_waktu').val('');
            $('#status').val('1').trigger('change');
        }
    </script>
@endpush