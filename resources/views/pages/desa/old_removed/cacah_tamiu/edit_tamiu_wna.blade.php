@extends('layouts.desa.desa')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
@endpush
@section('title', 'Edit Cacah Tamiu')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user mr-2"></i></div>
                                Cacah Tamiu
                            </h1>
                           
                        </div>
                    </div>
                    <ol class="breadcrumb mb-0 mt-4">
                        <li class="breadcrumb-item"><a href="{{ route('desa-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('desa-cacah-tamiu-home') }}" class="text-decoration-none">Cacah Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Edit Cacah Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Masukkan Data Cacah Tamiu</div>
                <div class="card-body">
                    <div id="overlay">
                        <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <form id="form-create-krama-mipil" method="post" action="{{ route('desa-cacah-tamiu-wna-update', $krama->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <div class="form-group">
                                    <label for="title">Nomor Cacah Tamiu<span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control @error ('nomor_cacah_tamiu') is-invalid @enderror" id="nomor_cacah_tamiu" name="nomor_cacah_tamiu" placeholder="Masukkan Nomor Tamiu" value="{{ old('nomor_cacah_tamiu', $krama->nomor_cacah_tamiu) }}" required readonly>
                                    @error('nomor_cacah_tamiu')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Nomor Cacah Tamiu wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <div class="form-group">
                                    <label for="title">Nomor Paspor<span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control @error ('nomor_paspor') is-invalid @enderror" id="nomor_paspor" name="nomor_paspor" placeholder="Masukkan Nomor Paspor" value="{{ old('nomor_paspor', $wna->nomor_paspor) }}" required readonly>
                                    @error('nomor_paspor')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Nomor Paspor wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama', $wna->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Nama wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin" style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    @if(old('jenis_kelamin'))
                                        <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                        <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
                                    @else 
                                        <option value="laki-laki" @if($wna->jenis_kelamin == 'laki-laki') selected @endif>Laki-laki</option>
                                        <option value="perempuan" @if($wna->jenis_kelamin == 'perempuan') selected @endif>Perempuan</option>
                                    @endif
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Jenis kelamin wajib dipilih
                                    </div>
                                @enderror 
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tempat Lahir<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror"  id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan Tempat Lahir" value="{{ old('tempat_lahir', $wna->tempat_lahir) }}" required>
                                @error('tempat_lahir')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tempat lahir wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Lahir<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir',date('d M Y', strtotime($wna->tanggal_lahir))) }}" placeholder="Masukkan Tanggal Lahir" required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal lahir wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Negara Asal<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('negara') is-invalid @enderror" name="negara" id="negara" style="width: 100%" required aria-placeholder="Pilih Negara Asal" required>
                                    <option value="">Pilih Negara Asal</option>
                                    <option value="{{ $negara->id }}" selected>{{ $negara->code }} - {{ $negara->name }}</option>
                                </select>
                                @error('negara')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Negara Asal wajib dipilih
                                    </div>
                                @enderror 
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Alamat<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat', $wna->alamat) }}" required>
                                @error('alamat')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Alamat wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Masuk<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_masuk') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk', date('d M Y', strtotime($krama->tanggal_masuk))) }}" placeholder="Masukkan Tanggal Masuk Krama Mipil" required>
                                @error('tanggal_masuk')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal Masuk wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Keluar</label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_keluar') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_keluar" id="tanggal_keluar" placeholder="Masukkan Keluar Masuk Krama Mipil" value="@if(old('tanggal_keluar')) {{ old('tanggal_keluar') }} @elseif($wna->tanggal_keluar != NULL) {{ date('d M Y', strtotime($wna->tanggal_lahir)) }} @endif">
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Tanggal Keluar wajib diisi
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_dinas_row">
                                <label for="title">Banjar Dinas<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required>
                                    <option value="">Pilih Banjar Dinas</option>
                                    @if(old('banjar_dinas_id'))
                                        @foreach($banjar_dinas as $dinas)
                                            <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($banjar_dinas as $dinas)
                                            <option value="{{ $dinas->id }}" @if($krama->banjar_dinas_id == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('banjar_dinas')
                                    <div class="invalid-feedback text-start">
                                        {{ $errors->first('banjar_dinas') }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Banjar Dinas wajib dipilih
                                    </div>
                                @enderror 
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_adat_row">
                                <label for="title">Banjar Adat<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat"  style="width: 100%" aria-placeholder="Pilih Banjar Adat" required>
                                    <option value="">Pilih Banjar Adat</option>
                                    @if(old('banjar_adat_id'))
                                        @foreach($banjar_adat as $adat)
                                            <option value="{{ $adat->id }}" @if(old('banjar_adat_id') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($banjar_adat as $adat)
                                            <option value="{{ $adat->id }}" @if($krama->banjar_adat_id == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('banjar_adat')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Banjar Adat wajib dipilih
                                    </div>
                                @enderror 
                            </div>
                        </div>

                        <div class="row mx-5">
                        
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="foto">Foto</label>
                                <br>
                                <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                <img src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="rounded img-thumbnail" style="max-width:30%;" id="propic">
                                @error('foto')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Foto wajib diisi
                                    </div>
                                @enderror
                                <div class="custom-file mt-1">
                                    <button type="button" class="btn btn-primary btn-icon-split mt-1" data-target="#crop-image" data-toggle="modal">
                                        <span class="icon">
                                            <i class="fas fa-images"></i>
                                        </span>
                                        <span class="text">Pilih Foto</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <a class="btn btn-danger mr-2" href="{{ route('desa-cacah-tamiu-home') }}">Kembali</a><button class="btn btn-success" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    {{-- CROPPER --}}
<div class="modal fade" id="crop-image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Foto</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row" style="margin: 20px">
                <img  src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="text-center" id="image-preview" width="50%" height="100%" alt="">
                <div class="custom-file" style="margin-top: 20px">
                    <input type="file" class="custom-file-input" id="profile-image" name="foto" accept="images/*" required>
                    <label for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                </div>
                <div id="validasi-foto" class="text-danger small mt-2 text-end" style="display:none;">
                    Ukuran gambar maksimal 2 MB.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="modal-close" class="btn btn-danger" data-dismiss="modal">Kembali</button>
            <button type="button" id="update-foto-profile" class="btn btn-primary" data-dismiss="modal">Pilih</button>
        </div>
        </div>
    </div>
</div>

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
    @if($errors->has('nama') || $errors->has('nomor_paspor') || $errors->has('tempat_lahir') || $errors->has('tanggal_lahir') || $errors->has('agama') || $errors->has('jenis_kelamin') || $errors->has('pendidikan') || $errors->has('pekerjaan') || $errors->has('golongan_darah') || $errors->has('alamat') || $errors->has('provinsi') || $errors->has('kabupaten') || $errors->has('kecamatan') || $errors->has('desa'))
        <script>
            $("input").prop('readonly', false);
            $("select").prop('disabled', false);
        </script>
    @endif
    <script>
        $(document).ready( function () {
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
            //DatePicker
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });

            $("#dataTable-kabupaten").DataTable({
                "responsive": false, "lengthChange": true, "autoWidth": false,
                "oLanguage": {
                    "sSearch": "Cari:",
                    "sZeroRecords": "Data tidak ditemukan",
                    "sSearchPlaceholder": "Cari akun...",
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
            $('#sidebarCacahKrama').removeClass('collapsed');
            $('#collapseCacahKrama').addClass('show');
            $('#collapseCacahKrama').addClass('active');
            $('#nav-link-cacah-tamiu').addClass('active');
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

        //Select 2
        $(".custom-select").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                }
            }
        });

        $("#negara").select2({
            language: {
                noResults: function (params) {
                return "Data tidak ditemukan";
                },
                inputTooShort: function() {
                    return 'Masukkan Kode Negara';
                }
            },
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api-negara-search") }}',
                dataType: 'json',
            },
        });

        //CROPPER
        function changeProfile(){
            $('#profile-image').trigger('click');
        }

        var cropper;
        var image = document.getElementById('image-preview');

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept-Encoding' : 'gzip',
                }
            });
            $('#profile-image').on('change', function(){
                var filedata = this.files[0];
                var imgtype = filedata.type;
                var match = ['image/jpg', 'image/jpeg', 'image/png'];
                if (!(filedata.type==match[0]||filedata.type==match[1]||filedata.type==match[2])) {
                    alert("Format gambar Salah");
                }else if(filedata.size > (2097152)){
                    $('#validasi-foto').show();
                }else{
                    $('#validasi-foto').hide();
                    var reader=new FileReader();
                    reader.onload=function(ev){
                        $('#image-preview').attr('src', ev.target.result);
                        cropper.destroy();
                        cropper = null;
                        cropper = new Cropper(image, {
                            aspectRatio: 3/4,
                            viewMode: 2,
                            preview: '.preview'
                        });
                    }
                    reader.readAsDataURL(this.files[0]);
                    var postData=new FormData();
                    postData.append('file', this.files[0]);
                }
            });
            $('#crop-image').on('shown.bs.modal', function(){
                cropper = new Cropper(image, {
                    aspectRatio: 3/4,
                    viewMode: 2,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function(){
                cropper.destroy();
                cropper = null;
            });

            $('#update-foto-profile').on('click', function(){
                canvas = cropper.getCroppedCanvas({
                    width: 1080,
                    height: 1920,
                });
                canvas.toBlob(function(blob){
                    url = URL.createObjectURL(blob);
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    
                    reader.onloadend = function() {
                        $('#propic').attr('src', reader.result);
                        var base64data = reader.result;
                        $('#foto').val(reader.result);
                        
                    }
                });
            });
        });
    </script>
@endpush