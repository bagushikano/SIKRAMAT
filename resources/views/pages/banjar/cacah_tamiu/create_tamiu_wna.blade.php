@extends('layouts.banjar.banjar')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/spinner.css')}}" rel="stylesheet" />
    {{-- CROPPER JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
    <style>
        #mapid { height: 500px; }
        #img_container {
            position: relative;
            display: inline-block;
            text-align: center;
        }

        .btn-foto {
            position: absolute;
            bottom: 0rem;
            width: 100%;
        }
        .transparan{
            background-color: black;
            cursor: pointer;
            opacity: 0.6;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endpush
@section('title', 'Tambah Cacah Tamiu')
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
                        <li class="breadcrumb-item"><a href="{{ route('banjar-dashboard') }}" class="text-decoration-none text-dark">Kependudukan Desa Adat</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('banjar-cacah-tamiu-home') }}" class="text-decoration-none text-dark">Cacah Tamiu</a></li>
                        <li class="breadcrumb-item active text-red-pastel">Tambah Cacah Tamiu</li>
                    </ol>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-plus text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Cacah Tamiu</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Penambahan Data Cacah Tamiu Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>                   
                <div class="card-body">
                    <div id="overlay">
                        <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <form id="form-create-tamiu-wna" method="post" action="{{ route('banjar-cacah-tamiu-wna-store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="mx-10 mt-4 justify-content-center">
                            {{-- NOMOR PASPOR - FOTO --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Nomor Paspor<span class="text-danger small">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error ('nomor_paspor') is-invalid @enderror"  id="nomor_paspor" name="nomor_paspor" placeholder="Masukkan Nomor Paspor" value="{{ old('nomor_paspor') }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari WNA"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                        @error('nomor_paspor')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <small class="text-danger" style="display:none;">
                                                nomor_paspor wajib diisi
                                            </small>
                                        @enderror
                                        <small class="text-danger" id="nomor_paspor-validate" style="display:none;">
                                            nomor_paspor harus terdiri dari 16 digit angka
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="title" class="small">Nama<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}" required readonly>
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
                                    
                                    <div class="form-group">
                                        <label for="title" class="small">Negara Asal<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('negara') is-invalid @enderror" name="negara" id="negara" style="width: 100%" required aria-placeholder="Pilih Negara Asal" required disabled>
                                            <option value="">Pilih Negara Asal</option>
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

                                </div>
                                <div class="col-lg-4 col-sm-12 text-center">
                                    <label for="foto" class="text-white">Foto</label>
                                    <br>
                                    <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" placeholder="url" hidden>
                                    <div id="img_container">
                                        <img src="{{asset('assets/admin/assets/img/foto_placeholder.png')}}" class="rounded img-thumbnail" style="max-width:172px;" id="propic">
                                        <div class="transparan btn-foto">
                                            <a data-target="#crop-image" data-toggle="modal" class="text-decoration-none text-white my-2">Pilih Foto</a>
                                        </div>
                                    </div>
                                    @error('foto')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Foto wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- JK - TTL --}}
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Jenis Kelamin<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required disabled>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                            <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
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
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tempat Lahir<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir" required readonly>
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
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tanggal Lahir<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required readonly>
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
                            </div>

                            {{-- ALAMAT - DINAS --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Alamat<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat') }}" required readonly>
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
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Banjar Dinas<span class="text-danger small">*</span></label>
                                        <select class="can-empty select2 custom-select @error ('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required disabled>
                                            <option value="">Pilih Banjar Dinas</option>
                                            @foreach($banjar_dinas as $dinas)
                                                <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                            @endforeach
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
                                </div>
                            </div>

                            {{-- KOOR - TGL REGIS --}}
                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form-group">
                                        <label for="koordinat" class="small">Koordinat Alamat</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder" id="koordinat_alamat_placeholder" placeholder="Pilih Koordinat" value="{{ old('koordinat_alamat_placeholder') }}">
                                            <input type="text" hidden class="form-control" name="koordinat_alamat" id="koordinat_alamat" value="{{ old('koordinat_alamat') }}">
                                            <div class="input-group-append">
                                                <button type="button" class="can-empty btn btn-primary" onclick="map_modal()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari koordinat" disabled><i class="fas fa-map-marker-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="title" class="small">Tanggal Masuk<span class="text-danger small">*</span></label>
                                        <input type="text" class="can-empty datepicker-here form-control @error ('tanggal_masuk') is-invalid @enderror" placeholder="Masukkan Tanggal Masuk" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}" placeholder="Masukkan Tanggal Masuk Krama Mipil" required readonly>
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
                                </div>
                            </div>
                            <hr class="my-4 mt-5" />
                            <div class="float-right">
                                <a class="btn btn-danger mr-2 btn-icon-split text-end" href="{{ route('banjar-cacah-tamiu-home') }}">
                                    <span class="icon">
                                        <i class="fas fa-arrow-left"></i>
                                    </span>
                                    <span class="text">Kembali</span>
                                </a>
                                <button class="btn btn-success btn-icon-split text-end" type="submit">
                                    <span class="icon">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span class="text">Simpan</span>
                                </button>
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
                        <input type="file" class="custom-file-input" id="profile-image" name="foto" accept="image/*" required>
                        <label for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                    </div>
                    <small class="small">(Foto maksimal berukuran 2 MB)</small>
                    <div id="validasi-foto" class="text-danger small text-end" style="display:none;">
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

    {{-- LEAFLET --}}
    <div class="modal fade" id="pick_koordinat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Koordinat Alamat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mx-2" id="mapid"></div>
                <div class="form-group mx-2 mt-3">
                    <input type="text" readonly class="form-control" name="modal_koordinat_alamat_placeholder" id="modal_koordinat_alamat_placeholder" placeholder="Pilih Koordinat">
                    <input type="text" hidden class="form-control" name="modal_koordinat_alamat" id="modal_koordinat_alamat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="modal-close" class="btn btn-danger" data-dismiss="modal">Batal</button>
                <button type="button" onclick="pick_koordinat()" class="btn btn-primary">Simpan</button>
            </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    {{-- LEAFLET --}}
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>
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
            $(".can-empty").prop('readonly', false);
            $(".can-empty").prop('disabled', false);
        </script>
    @endif

    {{-- LEAFLET --}}
    <script>
        //MAP INIT
        var map = L.map('mapid').setView([-8.359888288543399, 115.08508295752111], 10);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
        {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>', 
            maxZoom: 18, 
            id: 'mapbox/streets-v11', 
            tileSize: 512, 
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoiYWJkaXB1cm5hd2FuIiwiYSI6ImNrbGJ6bHBxMDBrOWQydnAwZnFtZGIxZjMifQ.Dskf87XP5VuPW2Srnrz8tw'
        }).addTo(map);

        //ADD CONTROLL
        map.pm.addControls({  
            position: 'topleft',
            drawCircle: false,
            drawMarker: false,
            drawCircleMarker:false,
            drawRectangle: false,
            drawPolyline: false,
            drawPolygon: false,
            drawText: false,
            dragMode:false,
            editMode: false,
            cutPolygon: false,
            removalMode: true,
            rotateMode: false,
        });

        var marker;
        map.on('click', function(e){
            if (marker) { // check
                map.removeLayer(marker); // remove
            }
            marker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map);
            var koor = {lat:e.latlng.lat, lng:e.latlng.lng};
            document.getElementById('modal_koordinat_alamat_placeholder').value=e.latlng.lat+', '+e.latlng.lng;
            document.getElementById('modal_koordinat_alamat').value=JSON.stringify(koor);

            marker.on('dragend', function (e) {
                var koor = marker.getLatLng();
                document.getElementById('modal_koordinat_alamat_placeholder').value = marker.getLatLng().lat+', '+marker.getLatLng().lng;
                document.getElementById('modal_koordinat_alamat').value = JSON.stringify(koor);
            });
        });

        function map_modal(){
            $('#pick_koordinat_modal').on('shown.bs.modal', function() {
                map.invalidateSize();
            });
            $('#pick_koordinat_modal').modal('show');
        }

        function pick_koordinat(){
            var latlng_placeholder = $('#modal_koordinat_alamat_placeholder').val();
            var latlng = $('#modal_koordinat_alamat').val();
            $('#koordinat_alamat_placeholder').val(latlng_placeholder);
            $('#koordinat_alamat').val(latlng);
            $('#pick_koordinat_modal').modal('hide');
        }
    </script>


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

            //Regex nomor_paspor
            $('#nomor_paspor').on('input', function (event) { 
                if($("#nomor_paspor").val().length == 8){
                    $("#nomor_paspor-validate").fadeOut();
                }
            });

            $('#search_button').on('click', function(){
                if($("#nomor_paspor").val().length != 8){
                    $("#nomor_paspor-validate").show();
                }else{
                    var url = "{{ route('banjar-cacah-tamiu-get-wna', ":nomor_paspor") }}";
                    url = url.replace(':nomor_paspor', $("#nomor_paspor").val());
                    $("#nomor_paspor-validate").fadeOut();
                    $("#overlay").css('display', 'flex');
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            if(result.status == 'ditemukan'){
                                $('#nama').val(result.wna.nama); 
                                $('#tempat_lahir').val(result.wna.tempat_lahir); 
                                $('#tanggal_lahir').datepicker("update", result.wna.tanggal_lahir); 
                                $('#jenis_kelamin').val(result.wna.jenis_kelamin).trigger('change');
                                $('#alamat').val(result.wna.alamat);
                                if(result.wna.foto != null){
                                    $('#propic').attr("src", result.penduduk.foto);
                                    $('#image-preview').attr("src", result.penduduk.foto);
                                } 
                                $('#negara').append('<option value="'+result.wna.negara_id+'">'+result.wna.negara.code+' - '+result.wna.negara.name+'</option>');
                                $('#negara').val(result.wna.negara_id).trigger('change');
                                $("input").prop('readonly', true);
                                $("select").prop('disabled', true);
                                $("#banjar_adat").prop('disabled', false);
                                $("#banjar_dinas").prop('disabled', false);
                                $("#tanggal_masuk").prop('readonly', false);
                                $("#overlay").fadeOut();
                                $(".can-empty").prop('readonly', false);
                                $(".can-empty").prop('disabled', false);
                                $("#nomor_paspor").prop('readonly', true);
                                Toast.fire({
                                    icon: 'success',
                                    title: 'WNA ditemukan'
                                })
                            }else if(result.status == 'tidak_ditemukan'){
                                var nomor_paspor = $("#nomor_paspor").val();
                                $(".can-empty").prop('readonly', false);
                                $(".can-empty").prop('disabled', false);
                                $(".can-empty").val('').trigger('change');
                                $("#nomor_paspor").val(nomor_paspor);
                                $("#nomor_paspor").prop('readonly', true);
                                $("#overlay").fadeOut();
                                Toast.fire({
                                    icon: 'warning',
                                    title: 'WNA tidak ditemukan'
                                })
                            }
                            else if(result.status == 'terdaftar'){
                                $("#overlay").fadeOut();
                                Toast.fire({
                                    icon: 'warning',
                                    title: 'WNA telah terdaftar sebagai Cacah Tamiu'
                                })
                            }
                        }
                    });
                }
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
                    return 'Masukkan Kode atau Nama Negara';
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