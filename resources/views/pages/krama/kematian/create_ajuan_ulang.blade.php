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
@section('title', 'Ajukan Data Kematian')
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
                                Pengajuan Data Kematian
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
                                <div class="wizard-step-text-name text-dark">Data Kematian</div>
                                <div class="wizard-step-text-details text-dark">Lengkapi Formulir Pengajuan Data Kematian Berikut Ini</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('Kematian Store Ajuan') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-xxl-10 col-xl-10">
                                <div class="form-group">
                                    <label class="small"for="title">Anggota Keluarga<span class="text-danger small">*</span></label>
                                    <select class="select2 custom-select @error ('cacah_krama_mipil') is-invalid @enderror" name="cacah_krama_mipil" id="cacah_krama_mipil" style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin">
                                        <option value="" >Pilih Anggota Keluarga</option>
                                        @if(old('cacah_krama_mipil'))
                                            @foreach($kramas as $item)
                                                <option value="{{ $item->id }}" @if(old('cacah_krama_mipil') == $item->id) selected @endif>{{ $item->penduduk->nama }}</option>
                                            @endforeach
                                        @else
                                            @foreach($kramas as $item)
                                                <option value="{{ $item->id }}" @if($kematian->cacah_krama_mipil_id == $item->id) selected @endif>{{ $item->penduduk->nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>                                    
                                    @error('cacah_krama_mipil')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Anggota keluarga wajib dipilih
                                        </div>
                                    @enderror 
                                </div>
                                <div class="form-group">
                                    <label class="small" for="tanggal_kematian">Tanggal Kematian<span class="text-danger small">*</span></label>
                                    <input type="text" class="datepicker-here form-control @error ('tanggal_kematian') is-invalid @enderror" placeholder="Masukkan Tanggal Kematian" name="tanggal_kematian" id="tanggal_kematian" value="{{ old('tanggal_kematian', date('d M Y', strtotime($kematian->tanggal_kematian))) }}" required>
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

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_suket_kematian">No. Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                            <input class="form-control @error('nomor_suket_kematian') is-invalid @enderror" id="nomor_suket_kematian" name="nomor_suket_kematian" type="text" value="{{ old('nomor_suket_kematian', $kematian->nomor_suket_kematian) }}" placeholder="Masukkan Nomor Surat Keterangan Kematian" required>
                                            @error('nomor_suket_kematian')
                                                <div class="invalid-feedback text-start">
                                                    {{ $message }}
                                                </div>
                                            @else
                                                <div class="invalid-feedback">
                                                    Nomor Surat Keterangan Kematian wajib diisi
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="lampiran">File Surat Keterangan Kematian<span class="text-danger small">*</span></label>
                                            <br>    
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input @error('file_suket_kematian') is-invalid @enderror" id="file_suket_kematian" name="file_suket_kematian" accept=".pdf,.jpg" required>
                                                <label for="file_suket_kematian_label" id="file_suket_kematian_label" class="custom-file-label">Pilih File</label>
                                                <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                @error('file_suket_kematian')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        File Surat Keterangan Kematian wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                            <div id="validasi-file-suket-kematian" class="text-danger small text-end" style="display:none;">
                                                Ukuran file maksimal 2 MB.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="nomor_akta_kematian">No. Akta Kematian</label>
                                            <input class="form-control @error('nomor_akta_kematian') is-invalid @enderror" id="nomor_akta_kematian" name="nomor_akta_kematian" type="text" value="{{ old('nomor_akta_kematian', $kematian->nomor_akta_kematian) }}" placeholder="Masukkan Nomor Akta Kematian">
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
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="small" for="lampiran">File Akta Kematian</label>
                                            <br>    
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input @error('file_akta_kematian') is-invalid @enderror" id="file_akta_kematian" name="file_akta_kematian" accept=".pdf,.jpg">
                                                <label for="file_akta_kematian_label" id="file_akta_kematian_label" class="custom-file-label">Pilih File</label>
                                                <small class="small">(Maksimal berukuran 2 MB dengan format PDF/JPG)</small>
                                                @error('file_akta_kematian')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        File Akta Kematian wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                            <div id="validasi-file-akta-kematian" class="text-danger small text-end" style="display:none;">
                                                Ukuran file maksimal 2 MB.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="small" for="penyebab_kematian">Penyebab Kematian<span class="text-danger small">*</span></label>
                                    <textarea type="text" class="form-control @error ('penyebab_kematian') is-invalid @enderror" placeholder="Masukkan Penyebab Kematian" rows="3" name="penyebab_kematian" id="penyebab_kematian" required>{{ $kematian->penyebab_kematian }}</textarea>
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

                                <div class="form-group">
                                    <label class="small" for="keterangan">Keterangan</label>
                                    <textarea type="text" class="form-control @error ('keterangan') is-invalid @enderror" placeholder="Masukkan Keterangan" rows="3" name="keterangan" id="keterangan">{{ $kematian->keterangan }}</textarea>
                                </div>

                                <hr class="my-4" />
                                <div class="float-right my-2">
                                    {{-- <button class="btn btn-light" type="button" id="btn-prev-2">Sebelumnya</button> --}}
                                    <div>
                                        <button class="btn btn-success btn-icon-split text-end" type="submit">
                                            <span class="icon">
                                                <i class="fas fa-save"></i>
                                            </span>
                                            <span class="text">Simpan Ajuan Data Kematian</span>
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
            $("#file_akta_kematian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file-akta-kematian').show();
                    $('#file_akta_kematian').val("");
                }else{
                    document.getElementById('file_akta_kematian_label').innerHTML = document.getElementById('file_akta_kematian').files[0].name;
                    $('#validasi-file-akta-kematian').hide();
                }
            });

            $("#file_suket_kematian").change(function() {
                var filedata = this.files[0];
                if(filedata.size > (2097152)){
                    $('#validasi-file-suket-kematian').show();
                    $('#file_suket_kematian').val("");
                }else{
                    document.getElementById('file_suket_kematian_label').innerHTML = document.getElementById('file_suket_kematian').files[0].name;
                    $('#validasi-file-suket-kematian').hide();
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
        $('#nav-link-ajuan-kematian').addClass('active');
    </script>
@endpush