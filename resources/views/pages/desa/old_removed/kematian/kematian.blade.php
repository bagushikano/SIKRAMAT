@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Daftar Kematian')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-users mr-2"></i></div>
                                Kematian
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Kematian</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4">
                <div class="card-header">Kematian Cacah Krama <span class="text-dark">Desa Adat {{ Session::get('desa_adat_nama') }}</span></div>
                <div class="card-body">
                    <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" data-toggle="modal" data-target="#create_kematian">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah Kematian</span>
                    </button>
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover table-responsive" id="dataTable-kematian" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th>No. Akta Kematian</th>
                                    <th>No. Induk Cacah Krama</th>
                                    <th>Nama</th>
                                    <th>Tanggal Kematian</th>
                                    <th style="width: 8%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kematians as $kematian)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kematian->nomor_akta_kematian }}</td>
                                        <td>{{ $kematian->cacah_krama_mipil->penduduk->nomor_induk_cacah_krama }}</td>
                                        <td>{{ $kematian->cacah_krama_mipil->penduduk->gelar_depan }} {{ $kematian->cacah_krama_mipil->penduduk->nama }} @if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''), {{ $kematian->cacah_krama_mipil->penduduk->gelar_belakang }} @endif</td>
                                        <td>{{ date('d M Y', strtotime($kematian->tanggal_kematian)) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="detail_kematian({{ $kematian->id }})"><i class="fas fa-eye"></i></a>
                                            <button class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="edit_kematian({{ $kematian->id }})"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL --}}
    <!-- Tambah Kematian Modal -->
    <div class="modal fade" id="create_kematian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-create-kematian" method="post" action="{{route('desa-kematian-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Kematian</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('cacah_krama_mipil_placeholder') is-invalid @enderror"  id="cacah_krama_mipil_placeholder" name="cacah_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('krama_mipil_placeholder') }}" required>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                    <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_cacah_krama_modal()">
                                        <span class="text">Pilih Cacah Krama</span>
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <input type="text" class="form-control @error ('cacah_krama_mipil') is-invalid @enderror"  id="cacah_krama_mipil" name="cacah_krama_mipil"  value="{{ old('cacah_krama_mipil') }}" required hidden>
                            @error('cacah_krama_mipil')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Cacah Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="nomor_akta_kematian">No. Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                            <input class="form-control @error('nomor_akta_kematian') is-invalid @enderror" id="nomor_akta_kematian" name="nomor_akta_kematian" type="text" value="{{ old('nomor_akta_kematian') }}" placeholder="Masukkan Nomor Akta Kematian atau Nomor Surat Kematian" required>
                            @error('nomor_akta_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nomor Akta Kematian wajib diisi
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_kematian">Tanggal Kematian<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('tanggal_kematian') is-invalid @enderror" placeholder="Masukkan Tanggal Kematian" name="tanggal_kematian" id="tanggal_kematian" value="{{ old('tanggal_kematian') }}" required>
                                    @error('tanggal_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="lampiran" class="small">File Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                    <br>    
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" required>
                                        <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                        @error('lampiran')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Lampiran wajib diisi
                                            </div>
                                        @enderror
                                    </div>
                                    <div id="validasi-lampiran" class="text-danger small mt-2 text-end" style="display:none;">
                                        Ukuran lampiran maksimal 2 MB.
                                    </div>
                                    {{-- <input type="file" class="form-control-file" id="lampiran" name="lampiran"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="penyebab_kematian">Penyebab Kematian<span class="text-danger small">*</span></label>
                            <textarea type="text" class="form-control @error ('penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="penyebab_kematian" id="penyebab_kematian" required></textarea>
                            @error('penyebab_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Penyebab Kematian wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Kematian Modal -->
    <div class="modal fade" id="edit_kematian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-edit-kematian" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Kematian</h5>
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
                            <label for="title">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('edit_cacah_krama_mipil_placeholder') is-invalid @enderror"  id="edit_cacah_krama_mipil_placeholder" name="edit_cacah_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('edit_krama_mipil_placeholder') }}" required readonly>
                                <div class="input-group-append">
                                    {{-- <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih Krama Mipil">Pilih Krama</button> --}}
                                    <button class="btn btn-primary btn-icon-split" type="button" onclick="pilih_cacah_krama_modal_edit()">
                                        <span class="text">Pilih Cacah Krama</span>
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <input type="text" class="form-control @error ('edit_cacah_krama_mipil') is-invalid @enderror"  id="edit_cacah_krama_mipil" name="edit_cacah_krama_mipil"  value="{{ old('edit_cacah_krama_mipil') }}" required hidden>
                            @error('edit_cacah_krama_mipil')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Cacah Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="edit_nomor_akta_kematian">No. Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_nomor_akta_kematian') is-invalid @enderror" id="edit_nomor_akta_kematian" name="edit_nomor_akta_kematian" type="text" value="{{ old('edit_nomor_akta_kematian') }}" placeholder="Masukkan Nomor Akta Kematian atau Nomor Surat Kematian" required>
                            @error('edit_nomor_akta_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nomor Akta Kematian wajib diisi
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="edit_tanggal_kematian">Tanggal Kematian<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('edit_tanggal_kematian') is-invalid @enderror" placeholder="dd mmm yyyy" name="edit_tanggal_kematian" id="edit_tanggal_kematian" value="{{ old('edit_tanggal_kematian') }}" required>
                                    @error('tanggal_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                <label for="lampiran" class="small">File Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                <br>    
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('edit_lampiran') is-invalid @enderror" id="edit_lampiran" name="edit_lampiran">
                                    <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                    @error('edit_lampiran')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Lampiran wajib diisi
                                        </div>
                                    @enderror
                                </div>
                                <div id="validasi-lampiran" class="text-danger small mt-2 text-end" style="display:none;">
                                    Ukuran lampiran maksimal 2 MB.
                                </div>
                                {{-- <input type="file" class="form-control-file" id="lampiran" name="lampiran"> --}}
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_penyebab_kematian">Penyebab Kematian<span class="text-danger small">*</span></label>
                            <textarea type="text" class="form-control @error ('edit_penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="edit_penyebab_kematian" id="edit_penyebab_kematian" required></textarea>
                            @error('edit_penyebab_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Penyebab Kematian wajib diisi
                                </div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Kematian Modal -->
    <div class="modal fade" id="detail_kematian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-detail-kematian" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Kematian</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_detail">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_detail">
                        <div class="form-group">
                            <label for="title">Cacah Krama Mipil<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('detail_cacah_krama_mipil_placeholder') is-invalid @enderror"  id="detail_cacah_krama_mipil_placeholder" name="detail_cacah_krama_mipil_placeholder" placeholder="Pilih Krama Mipil" value="{{ old('detail_krama_mipil_placeholder') }}" required readonly>
                            </div>
                            <input type="text" class="form-control @error ('detail_cacah_krama_mipil') is-invalid @enderror"  id="detail_cacah_krama_mipil" name="detail_cacah_krama_mipil"  value="{{ old('detail_cacah_krama_mipil') }}" required hidden>
                            @error('detail_cacah_krama_mipil')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Cacah Krama Mipil wajib dipilih
                                </div>
                            @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="detail_nomor_akta_kematian">No. Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                            <input class="form-control @error('detail_nomor_akta_kematian') is-invalid @enderror" id="detail_nomor_akta_kematian" name="detail_nomor_akta_kematian" type="text" value="{{ old('detail_nomor_akta_kematian') }}" placeholder="Masukkan Nomor Akta Kematian atau Nomor Surat Kematian" required>
                            @error('detail_nomor_akta_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nomor Akta Kematian wajib diisi
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="detail_tanggal_kematian">Tanggal Kematian<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('detail_tanggal_kematian') is-invalid @enderror" placeholder="dd mmm yyyy" name="detail_tanggal_kematian" id="detail_tanggal_kematian" value="{{ old('detail_tanggal_kematian') }}" required>
                                    @error('tanggal_kematian')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Tanggal Kematian wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                <label for="lampiran" class="small">File Akta Kematian / Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                <br>    
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('detail_lampiran') is-invalid @enderror" id="detail_lampiran" name="detail_lampiran">
                                    <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                    @error('detail_lampiran')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Lampiran wajib diisi
                                        </div>
                                    @enderror
                                </div>
                                <div id="validasi-lampiran" class="text-danger small mt-2 text-end" style="display:none;">
                                    Ukuran lampiran maksimal 2 MB.
                                </div>
                                {{-- <input type="file" class="form-control-file" id="lampiran" name="lampiran"> --}}
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="detail_penyebab_kematian">Penyebab Kematian<span class="text-danger small">*</span></label>
                            <textarea type="text" class="form-control @error ('detail_penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="detail_penyebab_kematian" id="detail_penyebab_kematian" required></textarea>
                            @error('detail_penyebab_kematian')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Penyebab Kematian wajib diisi
                                </div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Select Krama Mipil Modal -->
    <div class="modal fade" id="select_cacah_krama_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <form id="form-create-prajuru-desa-adat" method="post" action="{{route('desa-prajuru-desa-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Pilih Cacah Krama</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_select_cacah_krama">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_select_cacah_krama">
                        
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="select_cacah_krama_modal_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-krama" role="document">
            <form id="form-create-prajuru-desa-adat" method="post" action="{{route('desa-prajuru-desa-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Pilih Cacah Krama</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_select_cacah_krama_edit">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_select_cacah_krama_edit">
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL --}}

    {{-- HIDDEN FORM --}}
    <form id="form-delete-kematian" method="post" action="/">
        @method('delete')
        @csrf
    </form>
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
        @if($errors->has('krama') || $errors->has('nomor_akta_kelahiran') || $errors->has('tanggal_kematian') || $errors->has('penyebab_kematian'))
            <script>
                $(document).ready(function(){
                    $('#create_kematian').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nomor_akta_kelahiran') || $errors->has('edit_tanggal_kematian') || $errors->has('edit_penyebab_kematian'))
        <script>
            $(document).ready(function(){
                $("#body_loading").hide();
                $('#edit_kematian').modal('show');
            });
        </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        $(document).ready( function () {
            $("#dataTable-kematian").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari data kematian...",
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
            $('#sidebarKeluarga').removeClass('collapsed');
            $('#collapseKeluarga').addClass('show');
            $('#collapseKeluarga').addClass('active');
            $('#nav-link-keluarga-krama').addClass('active');

            $("#lampiran").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-lampiran').show();
                    $('#lampiran').val("");
                }else{
                    document.getElementById('lampiran_label').innerHTML = document.getElementById('lampiran').files[0].name;
                    $('#validasi-lampiran').hide();
                }
            });

            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            $('#select_cacah_krama_modal').on('hidden.bs.modal', function () {
                $('#create_kematian').modal('show');
            })

            $('#select_cacah_krama_modal_edit').on('hidden.bs.modal', function () {
                $('#edit_kematian').modal('show');
            })
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

        //Pilih Cacah Krama Modal Tambah Kematian
        function pilih_cacah_krama_modal(){
            $('#body_loading_select_cacah_krama').show();
            $('#body_select_cacah_krama').hide();
            $('#select_cacah_krama_modal').modal('show');
            $('#create_kematian').modal('hide');
            var url = "{{ route('desa-kematian-get-cacah-krama-mipil') }}";
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    if ($.fn.DataTable.isDataTable("#dataTable-cacah-krama-mipil")) {
                        $('#dataTable-cacah-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_cacah_krama').html(result.hasil);        
                    $("#dataTable-cacah-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Cacah Krama...",
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
                    $('#body_loading_select_cacah_krama').hide();
                    $('#body_select_cacah_krama').show();
                }
            });
        }

        function pilih_cacah_krama(id, nama){
            $('#cacah_krama_mipil').val(id);
            $('#cacah_krama_mipil_placeholder').val(nama);
            $('#cacah_krama_mipil_placeholder').prop('readonly', true);
            $('#create_kematian').modal('show');
            $('#select_cacah_krama_modal').modal('hide');
        }
        //Pilih Cacah Krama Modal Tambah Kematian

        //Pilih Cacah Krama Modal Edit Kematian
        function pilih_cacah_krama_modal_edit(){
            $('#body_loading_select_cacah_krama_edit').show();
            $('#body_select_cacah_krama_edit').hide();
            $('#select_cacah_krama_modal_edit').modal('show');
            $('#edit_kematian').modal('hide');
            var url = "{{ route('desa-kematian-get-cacah-krama-mipil-edit') }}";
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    if ($.fn.DataTable.isDataTable("#dataTable-cacah-krama-mipil")) {
                        $('#dataTable-cacah-krama-mipil').DataTable().clear().destroy();
                    }
                    $('#body_select_cacah_krama_edit').html(result.hasil);        
                    $("#dataTable-cacah-krama-mipil").DataTable({
                        "responsive": false, "lengthChange": true, "autoWidth": false,
                        "oLanguage": {
                            "sSearch": "Cari:",
                            "sZeroRecords": "Data tidak ditemukan",
                            "sSearchPlaceholder": "Cari Cacah Krama...",
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
                    $('#body_loading_select_cacah_krama_edit').hide();
                    $('#body_select_cacah_krama_edit').show();
                }
            });
        }

        function pilih_cacah_krama_edit(id, nama){
            $('#edit_cacah_krama_mipil').val(id);
            $('#edit_cacah_krama_mipil_placeholder').val(nama);
            $('#edit_cacah_krama_mipil_placeholder').prop('readonly', true);
            $('#edit_kematian').modal('show');
            $('#select_cacah_krama_modal_edit').modal('hide');
        }
        //Pilih Cacah Krama Modal Edit Kematian

        function edit_kematian(id){
            $('#body_loading').show();
            $('#body_edit').hide();
            $('#edit_kematian').modal('show');
            var url = "{{ route('desa-kematian-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    var url = "{{ route('desa-kematian-update', ":id") }}";
                    url = url.replace(':id', result.kematian.id);
                    $("#form-edit-kematian").attr("action", url);
                    $("#edit_tanggal_kematian").datepicker("update", result.kematian.tanggal_kematian);
                    $("#edit_cacah_krama_mipil_placeholder").val(result.kematian.cacah_krama_mipil.penduduk.nama);
                    $("#edit_cacah_krama_mipil").val(result.kematian.cacah_krama_mipil.id);
                    $("#edit_nomor_akta_kematian").val(result.kematian.nomor_akta_kematian);
                    $("#edit_penyebab_kematian").text(result.kematian.penyebab_kematian);
                    $("#body_loading").hide();
                    $("#body_edit").show();                 
                }
            });
        }

        function detail_kematian(id){
            $('#body_loading_detail').show();
            $('#body_detail').hide();
            $('#detail_kematian').modal('show');
            var url = "{{ route('desa-kematian-detail', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
                url: url,
                method: 'get',
                success: function(result){
                    console.log(result);
                    var url = "{{ route('desa-kematian-update', ":id") }}";
                    url = url.replace(':id', result.kematian.id);
                    $("#detail_cacah_krama_mipil_placeholder").val(result.kematian.cacah_krama_mipil.penduduk.nama);
                    $("#detail_cacah_krama_mipil").val(result.kematian.cacah_krama_mipil.id);
                    $("#detail_nomor_akta_kematian").val(result.kematian.nomor_akta_kematian);
                    $("#detail_penyebab_kematian").text(result.kematian.penyebab_kematian);
                    $("#body_loading_detail").hide();
                    $("#body_detail").show();                  
                }
            });
        }
    </script>
@endpush