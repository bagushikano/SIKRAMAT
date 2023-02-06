@extends('layouts.admin.admin')
@push('css')
    <link href="{{ asset('assets/admin/css/toggle_slider.css')}}" rel="stylesheet" />
@endpush
@section('title', 'Data Akun Super Admin')
@section('content')
    <main>
        <header class="page-header page-header-light pb-10">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-user"></i></i></div>
                                Data Akun Super Admin
                            </h1>
                            <div class="page-header-subtitle">Tambah Akun Super Admin</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n15">
            <!-- Example DataTable for Dashboard Demo-->
            <div class="card mb-4 mt-4">
                <div class="card-header">Masukkan Data Super Admin</div>
                <div class="card-body">
                    <div class="row mx-5">
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">NIK<span class="text-danger small">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error ('nik') is-invalid @enderror"  id="nik" name="nik" placeholder="Masukkan NIK" required>
                                @error('nik')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        NIK wajib diisi
                                    </div>
                                @enderror
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="search_button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Nama<span class="text-danger small">*</span></label>
                            <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan nama" required disabled>
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
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Tempat Lahir<span class="text-danger small">*</span></label>
                            <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror"  id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan tempat lahir" required disabled>
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
                            <input type="date" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" placeholder="Masukkan tanggal lahir" required disabled>
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
                            <label for="title">Agama<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih agama" required disabled>
                                <option value="">Pilih agama</option>
                                <option value="islam">Islam</option>
                                <option value="protestan">Protestan</option>
                                <option value="katolik">Katolik</option>
                                <option value="hindu">Hindu</option>
                                <option value="buddha">Buddha</option>
                                <option value="khonghucu">Khonghucu</option>
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
                            <label for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih jenis kelamin" required disabled>
                                <option value="laki-laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>
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
                            <label for="title">Pendidikan Terakhir<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="pendidikan" id="pendidikan"  style="width: 100%" required aria-placeholder="Pilih pendidikan" required disabled>
                                <option value="">Pilih pendidikan</option>
                                @foreach($pendidikans as $pendidikan)
                                    <option value="{{ $pendidikan->id }}">{{ $pendidikan->jenjang_pendidikan }}</option>
                                @endforeach
                            </select>
                            @error('pendidikan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Pendidikan terakhir wajib dipilih
                                </div>
                            @enderror  
                        </div>
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Pekerjaan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="pekerjaan" id="pekerjaan"  style="width: 100%" required aria-placeholder="Pilih pekerjaan" required disabled>
                                <option value="">Pilih pekerjaan</option>
                                @foreach($pekerjaans as $pekerjaan)
                                    <option value="{{ $pekerjaan->id }}">{{ $pekerjaan->profesi }}</option>
                                @endforeach
                            </select>
                            @error('pekerjaan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Pekerjaan wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Golongan Darah<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="golongan_darah" id="golongan_darah"  style="width: 100%" required aria-placeholder="Pilih Golongan Daerah" required disabled>
                                <option value="">Pilih golongan darah</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
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
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Alamat<span class="text-danger small">*</span></label>
                            <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan alamat" required disabled>
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
                            <label for="title">Provinsi<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih provinsi" required disabled>
                                <option value="">Pilih provinsi</option>
                                @foreach($provinsis as $provinsi)
                                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                                @endforeach
                            </select>
                            @error('provinsi')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Provinsi wajib dipilih
                                </div>
                            @enderror  
                        </div>
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Kabupaten<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih kabupaten" required disabled>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <small class="small">(Pilih provinsi terlebih dahulu)</small>
                            @error('kabupaten')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kabupaten wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>

                    <div class="row mx-5">
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Kecamatan<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih kecamatan" required disabled>
                                <option value="">Pilih kecamatan</option>
                            </select>
                            <small class="small">(Pilih kabupaten/kota terlebih dahulu)</small>
                            @error('kecamatan')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Kecamatan wajib dipilih
                                </div>
                            @enderror  
                        </div>
                        <div class="col-lg-6 col-sm-12 py-2">
                            <label for="title">Desa<span class="text-danger small">*</span></label>
                            <select class="select2 custom-select" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih desa" required disabled>
                                <option value="">Pilih desa</option>
                            </select>
                            <small class="small">(Pilih kecamatan terlebih dahulu)</small>
                            @error('desa')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Desa wajib dipilih
                                </div>
                            @enderror 
                        </div>
                    </div>
                    

                    <div class="float-right mr-5 mt-4">
                        <button class="btn btn-danger mr-2" type="button" data-dismiss="modal">Kembali</button><button class="btn btn-success" type="submit">Simpan</button>   
                    </div>
                    
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    
    {{-- ALERT --}}
    @if($message = Session::get('success'))
    <script>
        $(document).ready(function(){
            alertSuccess('Success', '{{$message}}');
        });
    </script>
    @endif
    {{-- END ALERT --}}
    <script>
        function edit_pekerjaan(id){
            $("#body_edit").hide();
            $("#body_loading").show();
            $('#edit_pekerjaan').modal('show');
            jQuery.ajax({
            url: "/admin/master/pekerjaan/"+id,
            method: 'get',
            success: function(result){
                    $("#form-edit-pekerjaan").attr("action", "/admin/master/pekerjaan/"+result.pekerjaan['id']+"/update");
                    $("#edit_profesi").val(result.pekerjaan['profesi']);
                    $("#body_loading").hide();
                    $("#body_edit").show();                 
                }
            });
        }

        function delete_pekerjaan(id){
            $('#form-delete-pekerjaan').attr("action", "/admin/master/pekerjaan/"+id+"/delete");
            $('#delete_pekerjaan').modal('show');  
        }

        $(document).ready( function () {
            //Daerah On Change
            $('#provinsi').on('change', function(){
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kabupaten/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            $('#kabupaten').empty();
                            $('#kabupaten').append('<option value="">Pilih kabupaten</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kabupaten').on('change', function(){
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/kecamatan/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan').empty();
                            $('#kecamatan').append('<option value="">Pilih kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#kecamatan').on('change', function(){
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: "/admin/master/desa-dinas/"+$(this).val(),
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa').empty();
                            $('#desa').append('<option value="">Pilih desa</option>');
                            result['0'].forEach(element => {
                                $('#desa').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });

            $('#search_button').on('click', function(){
                jQuery.ajax({
                    url: "/admin/akun/super-admin/get-penduduk/"+$('#nik').val(),
                    method: 'get',
                    success: function(result){
                        console.log(result);
                        $('#nama').val(result.penduduk.nama); 
                        $('#tempat_lahir').val(result.penduduk.tempat_lahir); 
                        $('#tanggal_lahir').val(result.penduduk.tanggal_lahir); 
                        $('#agama').val(result.penduduk.agama).trigger('change');
                        $('#jenis_kelamin').val(result.penduduk.jenis_kelamin).trigger('change');
                        $('#pendidikan_terakhir').val(result.penduduk.pendidikan_id).trigger('change');
                        $('#pekerjaan').val(result.penduduk.pekerjaan_id).trigger('change');  
                        $('#golongan_darah').val(result.penduduk.golongan_darah).trigger('change');
                        $('#alamat').val(result.penduduk.alamat); 
                        $('#desa').append('<option value="' + result.desa.id + '"' +'>' + result.desa.name + '</option>'); 
                        $('#desa').val(result.desa.id);
                        $('#kecamatan').append('<option value="' + result.kecamatan.id + '"' +'>' + result.kecamatan.name + '</option>'); 
                        $('#kecamatan').val(result.kecamatan.id); 
                        $('#kabupaten').append('<option value="' + result.kabupaten.id + '"' +'>' + result.kabupaten.name + '</option>'); 
                        $('#kabupaten').val(result.kabupaten.id);
                        $('#provinsi').val(result.provinsi.id).trigger('change');
                    }
                });
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
            $('#sidebarAkun').removeClass('collapsed');
            $('#collapseAkun').addClass('show');
            $('#sidebarAkunSuperAdmin').addClass('active');
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
    </script>
@endpush