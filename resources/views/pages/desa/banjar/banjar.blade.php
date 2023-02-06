@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Banjar Adat dan Dinas')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-university mr-1"></i></div>
                                Banjar
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
                            <a class="nav-link active" id="banjar_adat_tab" href="#banjar_adat" data-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Banjar Adat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="banjar_dinas_tab" href="#banjar_dinas" data-toggle="tab" role="tab" aria-controls="example" aria-selected="false">Banjar Dinas</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        <div class="tab-pane fade show active" id="banjar_adat" role="tabpanel" aria-labelledby="overview-tab">
                            <button class="btn btn-primary btn-icon-split mb-3 text-end" type="button" onclick="tambah_banjar_adat()">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Banjar Adat</span>
                            </button>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-banjar" id="dataTable-banjar-adat" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>Kode Banjar Adat</th>
                                            <th>Nama Banjar Adat</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($banjar_adat as $adat)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $adat->kode_banjar_adat}}</td>
                                                <td>{{ $adat->nama_banjar_adat}}</td>
                                                <td class="text-center">
                                                    <button button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="edit_banjar_adat({{ $adat->id }})"><i class="fas fa-eye"></i></button>
                                                    <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_banjar_adat({{ $adat->id }})"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="banjar_dinas" role="tabpanel" aria-labelledby="example-tab">
                            <button class="btn btn-primary btn-icon-split mb-3 text-end"  data-toggle="modal" data-target="#create_banjar_dinas">
                                <span class="icon">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Banjar Dinas</span>
                            </button>
                            <div class="datatable table-responsive">
                                <table class="table table-bordered table-hover table-responsive dataTable-banjar" id="dataTable-banjar-dinas" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>Kode Banjar Dinas</th>
                                            <th>Nama Banjar Dinas</th>
                                            <th>Jenis Banjar Dinas</th>
                                            <th>Desa/Kelurahan</th>
                                            <th style="width: 10%">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($banjar_dinas as $dinas)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $dinas->kode_banjar_dinas }}</td>
                                            <td>{{ $dinas->nama_banjar_dinas }}</td>
                                            <td>{{ ucwords(str_replace('_', ' ', $dinas->jenis_banjar_dinas)) }}</td>
                                            <td>{{ ucwords(strtolower($dinas->desa_dinas->name)) }}</td>
                                            <td class="text-center">
                                                <button button type="button" class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="edit_banjar_dinas({{ $dinas->id }})"><i class="fas fa-eye"></i></button>
                                                <button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_banjar_dinas({{ $dinas->id }})"><i class="fas fa-trash"></i></button>
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
    <!-- Tambah Banjar Adat Modal -->
    <div class="modal fade" id="create_banjar_adat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-banjar-adat" method="post" action="{{route('desa-banjar-adat-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Banjar Adat</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kode_banjar_adat" class="small">Kode Banjar Adat<span class="text-danger small">*</span></label>
                            <input class="form-control @error('kode_banjar_adat') is-invalid @enderror" id="kode_banjar_adat" name="kode_banjar_adat" type="text" value="{{ old('kode_banjar_adat') }}" required readonly>
                            @error('kode_banjar_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Banjar Adat wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_banjar_adat" class="small">Nama Banjar Adat<span class="text-danger small">*</span></label>
                            <input class="form-control @error('nama_banjar_adat') is-invalid @enderror" id="nama_banjar_adat" name="nama_banjar_adat" type="text" value="{{ old('nama_banjar_adat') }}" placeholder="Masukkan Nama Banjar Adat" required>
                            @error('nama_banjar_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Banjar Adat wajib diisi
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Banjar Adat Modal -->
    <div class="modal fade" id="edit_banjar_adat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-edit-banjar-adat" method="post" action="{{route('desa-banjar-adat-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Banjar Adat</h5>
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
                            <label for="kode_banjar_adat" class="small">Kode Banjar Adat<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_kode_banjar_adat') is-invalid @enderror" id="edit_kode_banjar_adat" name="edit_kode_banjar_adat" type="text" value="{{ old('edit_kode_banjar_adat') }}" required readonly>
                            @error('edit_kode_banjar_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Banjar Adat wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_banjar_adat" class="small">Nama Banjar Adat<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_nama_banjar_adat') is-invalid @enderror" id="edit_nama_banjar_adat" name="edit_nama_banjar_adat" type="text" value="{{ old('edit_nama_banjar_adat') }}" placeholder="Masukkan Nama Banjar Adat" required>
                            @error('edit_nama_banjar_adat')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Banjar Adat wajib diisi
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

    <!-- Tambah Banjar Dinas Modal -->
    <div class="modal fade" id="create_banjar_dinas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-create-banjar-dinas" method="post" action="{{route('desa-banjar-dinas-store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Banjar Dinas</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('kabupaten_id') is-invalid @enderror" name="kabupaten_id" id="kabupaten_id" style="width: 100%" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                @endforeach
                            </select>
                            @error('kabupaten_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kabupaten wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kecamatan_id" class="small">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('kecamatan_id') is-invalid @enderror" name="kecamatan_id" id="kecamatan_id" style="width: 100%" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                            @error('kecamatan_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kecamatan wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="desa_dinas_id" class="small">Desa/Kelurahan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('desa_dinas_id') is-invalid @enderror" name="desa_dinas_id" id="desa_dinas_id" style="width: 100%" required>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                            <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                            @error('desa_dinas_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Desa Dinas wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kode_banjar_dinas" class="small">Kode Banjar Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control @error('kode_banjar_dinas') is-invalid @enderror" id="kode_banjar_dinas" name="kode_banjar_dinas" type="text" value="{{ old('kode_banjar_dinas') }}"  placeholder="Kode Banjar Dinas Terisi Otomatis" required readonly>
                            @error('kode_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Banjar Dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_banjar_dinas" class="small">Nama Banjar Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control @error('nama_banjar_dinas') is-invalid @enderror" id="nama_banjar_dinas" name="nama_banjar_dinas" type="text" value="{{ old('nama_banjar_dinas') }}" placeholder="Masukkan Nama Banjar Dinas" required>
                            @error('nama_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Banjar Dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jenis_banjar_dinas" class="small">Jenis Banjar Dinas<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('jenis_banjar_dinas') is-invalid @enderror" name="jenis_banjar_dinas" id="jenis_banjar_dinas" style="width: 100%" required>
                                <option value="">Pilih Jenis Banjar Dinas</option>
                                <option value="dusun">Dusun</option>
                                <option value="lingkungan">Lingkungan</option>
                            </select>
                            @error('jenis_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Jenis Banjar Dinas wajib dipilih
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Banjar Dinas Modal -->
    <div class="modal fade" id="edit_banjar_dinas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-edit-banjar-dinas" method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gray-100">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Banjar Dinas</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" id="body_loading_dinas">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="body_edit_dinas">
                        <div class="form-group">
                            <label for="edit_kabupaten_id" class="small">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_kabupaten_id') is-invalid @enderror" name="edit_kabupaten_id" id="edit_kabupaten_id" style="width: 100%" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_kabupaten_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kabupaten wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_kecamatan_id" class="small">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_kecamatan_id') is-invalid @enderror" name="edit_kecamatan_id" id="edit_kecamatan_id" style="width: 100%" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <span class="small">(Pilih kabupaten terlebih dahulu)</span>
                            @error('edit_kecamatan_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kecamatan wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_desa_dinas_id" class="small">Desa/Kelurahan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_desa_dinas_id') is-invalid @enderror" name="edit_desa_dinas_id" id="edit_desa_dinas_id" style="width: 100%" required>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                            <span class="small">(Pilih kecamatan terlebih dahulu)</span>
                            @error('edit_desa_dinas_id')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Desa Dinas wajib dipilih
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kode_banjar_dinas" class="small">Kode Banjar Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_kode_banjar_dinas') is-invalid @enderror" id="edit_kode_banjar_dinas" name="edit_kode_banjar_dinas" type="text" value="{{ old('edit_kode_banjar_dinas') }}" required readonly>
                            @error('edit_kode_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kode Banjar Dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_banjar_dinas" class="small">Nama Banjar Dinas<span class="text-danger small">*</span></label>
                            <input class="form-control @error('edit_nama_banjar_dinas') is-invalid @enderror" id="edit_nama_banjar_dinas" name="edit_nama_banjar_dinas" type="text" value="{{ old('edit_nama_banjar_dinas') }}" placeholder="Masukkan Nama Banjar Dinas" required>
                            @error('edit_nama_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Nama Banjar Dinas wajib diisi
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit_jenis_banjar_dinas" class="small">Jenis Banjar Dinas<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select @error('edit_jenis_banjar_dinas') is-invalid @enderror" name="edit_jenis_banjar_dinas" id="edit_jenis_banjar_dinas" style="width: 100%" required>
                                <option value="">Pilih Jenis Banjar Dinas</option>
                                <option value="dusun">Dusun</option>
                                <option value="lingkungan">Lingkungan</option>
                            </select>
                            @error('edit_jenis_banjar_dinas')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Jenis Banjar Dinas wajib dipilih
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="submit">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="form-delete-banjar-adat" method="post" action="/">
        @method('delete')
        @csrf
    </form>

    <form id="form-delete-banjar-dinas" method="post" action="/">
        @method('delete')
        @csrf
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- ALERT --}}
    @if($message = Session::get('success'))
        @if($status = Session::get('banjar'))
            <script>
                $(document).ready(function(){
                    $('#banjar_adat').removeClass('active');
                    $('#banjar_adat_tab').removeClass('active');
                    $('#banjar_dinas_tab').addClass('active');
                    $('#banjar_dinas').tab('show');
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
        @if($errors->has('nama_banjar_adat') || $errors->has('kode_banjar_adat'))
            <script>
                $(document).ready(function(){
                    $('#create_banjar_adat').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_banjar_adat') || $errors->has('edit_kode_banjar_adat'))
        <script>
            $(document).ready(function(){
                $("#body_loading").hide();
                $('#edit_banjar_adat').modal('show');
            });
        </script>
        @elseif($errors->has('nama_banjar_dinas') || $errors->has('kode_banjar_dinas'))
            <script>
                $(document).ready(function(){
                    $('#create_banjar_dinas').modal('show');
                });
            </script>
        @elseif($errors->has('edit_nama_banjar_dinas') || $errors->has('edit_kode_banjar_dinas'))
        <script>
            $(document).ready(function(){
                $("#body_loading_dinas").hide();
                $('#edit_banjar_dinas').modal('show');
            });
        </script>
        @endif
    @endif
    {{-- END VALIDATION --}}
    <script>
        // BANJAR ADAT
        function tambah_banjar_adat(){
            jQuery.ajax({
                url: "{{ route('desa-banjar-get-kode-banjar-adat') }}",
                method: 'get',
                success: function(result){
                    $("#kode_banjar_adat").val(result.last_kode);                 
                }
            });
            $('#create_banjar_adat').modal('show');
        }

        function edit_banjar_adat(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_banjar_adat').modal('show');
            var url = "{{ route('desa-banjar-adat-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('desa-banjar-adat-update', ":id") }}";
                url = url.replace(':id', result.banjar_adat['id']);
                $("#form-edit-banjar-adat").attr("action", url);
                $('#edit_kode_banjar_adat').val(result.banjar_adat.kode_banjar_adat);
                $('#edit_nama_banjar_adat').val(result.banjar_adat.nama_banjar_adat);
                $("#body_loading").hide();
                $("#body_edit").show();                 
                }
            });
        }

        function delete_banjar_adat(id){
            Swal.fire({
                title: 'Hapus Banjar Adat',
                text: "Apakah anda yakin ingin menghapus banjar adat ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-banjar-adat-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-banjar-adat').attr("action", url);
                        $('#form-delete-banjar-adat').submit();
                    }
                })
        }
        //BANJAR ADAT

        //BANJAR DINAS
        function edit_banjar_dinas(id){
            $("#body_edit_dinas").hide();
            $("#body_loading_dinas").show();
            $('#edit_banjar_dinas').modal('show');
            var url = "{{ route('desa-banjar-dinas-edit', ":id") }}";
            url = url.replace(':id', id);
            jQuery.ajax({
            url: url,
            method: 'get',
            success: function(result){
                var url = "{{ route('desa-banjar-dinas-update', ":id") }}";
                url = url.replace(':id', result.banjar_dinas['id']);
                $("#form-edit-banjar-dinas").attr("action", url);
                $('#edit_kode_banjar_dinas').val(result.banjar_dinas.kode_banjar_dinas);
                $('#edit_nama_banjar_dinas').val(result.banjar_dinas.nama_banjar_dinas);
                $('#edit_jenis_banjar_dinas').val(result.banjar_dinas.jenis_banjar_dinas).trigger('change');

                $('#edit_kabupaten_id').empty();
                result.kabupatens.forEach(element => {
                    var kab = '<option value="' + element['id'] + '"';
                    if(element['id'] == result.kabupaten.id){
                        kab = kab + ' selected'
                    }
                    kab = kab + '>' + element['name'] + '</option>'
                    $('#edit_kabupaten_id').append(kab);
                });

                $('#edit_kecamatan_id').empty();
                result.kecamatans.forEach(element => {
                    var kec = '<option value="' + element['id'] + '"';
                    if(element['id'] == result.kecamatan.id){
                        kec = kec + ' selected'
                    }
                    kec = kec + '>' + element['name'] + '</option>'
                    $('#edit_kecamatan_id').append(kec);
                });

                $('#edit_desa_dinas_id').empty();
                result.desas.forEach(element => {
                    var des = '<option value="' + element['id'] + '"';
                    if(element['id'] == result.desa.id){
                        des = des + ' selected'
                    }
                    des = des + '>' + element['name'] + '</option>'
                    $('#edit_desa_dinas_id').append(des);
                });

                $("#body_loading_dinas").hide();
                $("#body_edit_dinas").show();                 
                }
            });
        }

        function delete_banjar_dinas(id){
            Swal.fire({
                title: 'Hapus Banjar Dinas',
                text: "Apakah anda yakin ingin menghapus banjar dinas ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('desa-banjar-dinas-delete', ":id") }}";
                        url = url.replace(':id', id);
                        $('#form-delete-banjar-dinas').attr("action", url);
                        $('#form-delete-banjar-dinas').submit();
                    }
                })
        }
        //BANJAR DINAS

        $(document).ready( function () {
            $(".dataTable-banjar").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari banjar...",
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

            //Get Data
            $('#kabupaten_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan_id').empty();
                            $('#kecamatan_id').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan_id').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa_dinas_id').empty();
                            $('#desa_dinas_id').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa_dinas_id').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#desa_dinas_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('desa-banjar-get-kode-banjar-dinas', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $("#kode_banjar_dinas").val(result.last_kode);                    
                        }
                    });
                }
            });

            $('#edit_kabupaten_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#edit_kecamatan_id').empty();
                            $('#edit_kecamatan_id').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#edit_kecamatan_id').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#edit_kecamatan_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#edit_desa_dinas_id').empty();
                            $('#edit_desa_dinas_id').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#edit_desa_dinas_id').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#edit_desa_dinas_id').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('desa-banjar-get-kode-banjar-dinas', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $("#edit_kode_banjar_dinas").val(result.last_kode);                    
                        }
                    });
                }
            });
        });

         //Select 2
         $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
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