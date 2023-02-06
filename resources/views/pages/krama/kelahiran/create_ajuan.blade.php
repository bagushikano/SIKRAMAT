@extends('layouts.krama.krama')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .form-custom[readonly] {
            display: block;
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #687281;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #c5ccd6;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
@endpush
@section('title', 'Ajukan Data Kelahiran')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fa-solid fa-baby mr-2"></i>
                                </div>
                                Pengajuan Data Kelahiran
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <div class="card mb-4 mt-5">
                <div class="card-header border-bottom">
                    <div class="nav nav-pills nav-justified flex-column flex-xl-row nav-wizard" id="cardTab" role="tablist">
                        <a class="nav-item nav-link active bg-gray-200" id="wizard1-tab" href="#wizard1" data-toggle="tab" role="tab" aria-controls="wizard1" aria-selected="true">
                            <div class="wizard-step-icon"><i class="fas fa-plus text-dark"></i></div>
                            <div class="wizard-step-text">
                                <div class="wizard-step-text-name text-dark">Data Kelahiran</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Pengajuan Data Kelahiran Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('Kelahiran Store Ajuan') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-xxl-10 col-xl-10">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <div class="form-group">
                                            <label class="small"for="title">NIK</label>
                                            <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" value="{{ old('nik') }}">
                                            @error('nik')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-danger" id="nik-validate" style="display:none;">
                                                NIK harus terdiri dari 16 digit angka
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <div class="form-group">
                                            <label class="small"for="title">No. Akta Kelahiran</label>
                                            <input type="text" class="form-control @error ('nomor_akta_kelahiran') is-invalid @enderror"  id="nomor_akta_kelahiran" name="nomor_akta_kelahiran" placeholder="Masukkan Nomor Akta Kelahiran" value="{{ old('nomor_akta_kelahiran') }}">
                                            @error('nomor_akta_kelahiran')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="small"for="title">Nama<span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}" required>
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
        
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="title">Tempat Lahir<span class="text-danger small">*</span></label>
                                        <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir" required>
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
                                        <label class="small"for="title">Tanggal Lahir<span class="text-danger small">*</span></label>
                                        <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="Masukkan Tanggal Lahir" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Masukkan Tanggal Lahir" required>
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
        
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
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
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="title">Golongan Darah<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah" required>
                                            <option value="-"  @if(old('golongan_darah') == '-') selected @endif>-</option>
                                            <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                            <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                            <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                            <option value="O"  @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                        </select>
                                        @error('golongan_darah')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Golongan darah wajib dipilih
                                            </div>
                                        @enderror  
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="title">Agama<span class="text-danger small">*</span></label>
                                        <select class="select2 custom-select @error ('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required>
                                            {{-- <option value="">Pilih Agama</option>
                                            <option value="islam">Islam</option>
                                            <option value="protestan">Protestan</option>
                                            <option value="katolik">Katolik</option> --}}
                                            <option value="hindu" selected>Hindu</option>
                                            {{-- <option value="buddha">Buddha</option>
                                            <option value="khonghucu">Khonghucu</option> --}}
                                        </select>
                                        @error('agama')
                                            <div class="invalid-feedback text-start">
                                                {{ $message }}
                                            </div>
                                        @else
                                            <div class="invalid-feedback">
                                                Agama wajib dipilih
                                            </div>
                                        @enderror  
                                    </div>
                                    <div class="col-lg-6 col-sm-12 py-2">
                                        <label class="small"for="lampiran">File Akta Kelahiran</label>
                                        <br>    
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('file_akta_kelahiran') is-invalid @enderror" id="file_akta_kelahiran" name="file_akta_kelahiran" accept=".pdf,.doc,.jpg,.png">
                                            <label for="lampiran_label" id="lampiran_label" class="custom-file-label">Pilih File</label>
                                        </div>
                                        <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                        <div id="validasi-lampiran" class="text-danger small text-end" style="display:none;">
                                            Ukuran File Akta Kelahiran maksimal 2 MB.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-10 col-xl-11">  
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group" id="ayah_kandung_div">
                                            <label class="small"for="ayah_kandung">Ayah<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%" required>
                                                <option value="">Pilih Ayah</option>
                                                @if($krama_mipil->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki')
                                                    <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}" @if(old('ayah_kandung') == $krama_mipil->cacah_krama_mipil->penduduk->id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endif
                                                @foreach($krama_mipil->anggota as $item)
                                                    @if($item->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki')
                                                        <option value="{{ $item->cacah_krama_mipil->penduduk->id }}" @if(old('ayah_kandung') == $item->cacah_krama_mipil->penduduk->id) selected @endif>{{ $item->cacah_krama_mipil->penduduk->nama }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            {{-- <div class="custom-control custom-checkbox mb-n2">
                                                <input class="custom-control-input" id="tampilkan_semua_ayah" type="checkbox">
                                                <label class="custom-control-label" for="tampilkan_semua_ayah">Tampilkan semua</label>
                                            </div> --}}
                                            @error('ayah_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Ayah wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group" id="ibu_kandung_div">
                                            <label class="small"for="ibu_kandung">Ibu<span class="text-danger small">*</span></label>
                                            <select class="select2 custom-select @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%" required>
                                                <option value="">Pilih Ibu</option>
                                                @if($krama_mipil->cacah_krama_mipil->penduduk->jenis_kelamin == 'perempuan')
                                                    <option value="{{ $krama_mipil->cacah_krama_mipil->penduduk->id }}" @if(old('ibu_kandung') == $krama_mipil->cacah_krama_mipil->penduduk->id) selected @endif>{{ $krama_mipil->cacah_krama_mipil->penduduk->nama }}</option>
                                                @endif
                                                @foreach($krama_mipil->anggota as $item)
                                                    @if($item->cacah_krama_mipil->penduduk->jenis_kelamin == 'perempuan')
                                                        <option value="{{ $item->cacah_krama_mipil->penduduk->id }}" @if(old('ibu_kandung') == $item->cacah_krama_mipil->penduduk->id) selected @endif>{{ $item->cacah_krama_mipil->penduduk->nama }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            {{-- <div class="custom-control custom-checkbox mb-n2">
                                                <input class="custom-control-input" id="tampilkan_semua_ibu" type="checkbox">
                                                <label class="custom-control-label" for="tampilkan_semua_ibu">Tampilkan semua</label>
                                            </div> --}}
                                            @error('ibu_kandung')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Ibu wajib dipilih
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="small" for="keterangan">Keterangan</label>
                                    <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan"></textarea>
                                </div>
                                <hr class="my-4" />
                                <div class="float-right my-2">
                                    {{-- <button class="btn btn-light" type="button" id="btn-prev-2">Sebelumnya</button> --}}
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" type="submit">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan Ajuan Data Kelahiran</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready( function () {
            //DATEPICKER
            $(".datepicker-here").datepicker({
                format: 'd M yyyy',
                language: 'id',
                autoclose: true,
            });
            //DATEPICKER

            //SELECT 2
            $(".custom-select").select2({
                language: {
                    noResults: function (params) {
                    return "Data tidak ditemukan";
                    }
                }
            });
            //SELECT 2

            //VALIDASI LAMPIRAN
            $("#file_akta_kelahiran").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-lampiran').show();
                    $('#file_akta_kelahiran').val("");
                }else{
                    document.getElementById('lampiran_label').innerHTML = document.getElementById('file_akta_kelahiran').files[0].name;
                    $('#validasi-lampiran').hide();
                }
            });
            //VALIDASI LAMPIRAN

            //Regex NIK
            $('#nik').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik").val().length == 16 || $("#nik").val() == ''){
                    $("#nik-validate").hide();
                }else{
                    $("#nik-validate").show();
                }
            });
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

        $('#sidebarAjuan').removeClass('collapsed');
        $('#collapseAjuan').addClass('show');
        $('#nav-link-ajuan-kelahiran').addClass('active');
    </script>
@endpush