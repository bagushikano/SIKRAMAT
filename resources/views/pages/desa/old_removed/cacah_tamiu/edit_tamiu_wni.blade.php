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
                <div class="card-header">Data Cacah Tamiu</div>
                <div class="card-body">
                    <div id="overlay">
                        <div class="w-100 d-flex justify-content-center mt-5 pt-5">
                          <div class="spinner"></div>
                        </div>
                    </div>
                    <form id="form-create-krama-mipil" method="post" action="{{ route('desa-cacah-tamiu-wni-update', $krama->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nomor Cacah Tamiu<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('nomor_cacah_tamiu') is-invalid @enderror" id="nomor_cacah_tamiu" name="nomor_cacah_tamiu" value="{{ $krama->nomor_cacah_tamiu }}" required disabled>
                                @error('nomor_tamiu')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Nomor Cacah Tamiu wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <div class="form-group">
                                    <label for="title">NIK<span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control @error ('nik') is-invalid @enderror" id="nik" name="nik" placeholder="Masukkan NIK" value="{{ $penduduk->nik }}" required disabled>
                                    @error('nik')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            NIK wajib diisi
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama Tercetak<span class="font-italic small"> (*dengan gelar jika ada)</span></label>
                                <input type="text" class="form-control @error ('nama_tercetak') is-invalid @enderror" id="nama_tercetak" name="nama_tercetak" placeholder="Nama Tercetak" value="{{ $penduduk->gelar_depan }} {{ $penduduk->nama }}@if($penduduk->gelar_belakang != ''), {{ $penduduk->gelar_belakang }} @endif" readonly>
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Gelar Depan</label>
                                <input type="text" class="form-control @error ('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" placeholder="Masukkan Gelar Depan" value="{{ old('gelar_depan', $penduduk->gelar_depan) }}">
                                @error('gelar_depan')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama<span class="text-danger small">*</span><span class="font-italic small"> (*tanpa gelar)</span></label>
                                <input type="text" class="form-control @error ('nama') is-invalid @enderror" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ old('nama',$penduduk->nama) }}" required>
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
                                <label for="title">Gelar Belakang</label>
                                <input type="text" class="form-control @error ('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" value="{{ old('gelar_belakang', $penduduk->gelar_belakang) }}">
                                @error('gelar_belakang')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Nama Alias (Bhiseka)</label>
                                <input type="text" class="form-control @error ('nama_alias') is-invalid @enderror" id="nama_alias" name="nama_alias" placeholder="Masukkan Nama Alias" value="{{ old('nama_alias', $penduduk->nama_alias) }}">
                                @error('nama_alias')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Nama Alias wajib diisi
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Jenis Kelamin<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin"  style="width: 100%" required aria-placeholder="Pilih Jenis Kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    @if(old('jenis_kelamin'))
                                        <option value="laki-laki" @if(old('jenis_kelamin') == 'laki-laki') selected @endif>Laki-laki</option>
                                        <option value="perempuan" @if(old('jenis_kelamin') == 'perempuan') selected @endif>Perempuan</option>
                                    @else
                                        <option value="laki-laki" @if($penduduk->jenis_kelamin == 'laki-laki') selected @endif>Laki-laki</option>
                                        <option value="perempuan" @if($penduduk->jenis_kelamin == 'perempuan') selected @endif>Perempuan</option>
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
                                <input type="text" class="form-control @error ('tempat_lahir') is-invalid @enderror"  id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan Tempat Lahir"value="{{ old('tempat_lahir',$penduduk->tempat_lahir) }}" required>
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
                                <input type="text" class="datepicker-here form-control @error ('tanggal_lahir') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_lahir" id="tanggal_lahir" placeholder="Masukkan Tanggal Lahir" value="{{ old('tanggal_lahir',date('d M Y', strtotime($penduduk->tanggal_lahir))) }}" required>
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
                                <select class="select2 custom-select @error('agama') is-invalid @enderror" name="agama" id="agama" style="width: 100%" required aria-placeholder="Pilih Agama" required>
                                    <option value="">Pilih Agama</option>
                                    @if(old('agama'))
                                        <option value="islam" @if(old('agama') == 'islam') selected @endif>Islam</option>
                                        <option value="protestan" @if(old('agama') == 'protestan') selected @endif>Protestan</option>
                                        <option value="katolik" @if(old('agama') == 'katolik') selected @endif>Katolik</option>
                                        <option value="hindu" @if(old('agama') == 'hindu') selected @endif>Hindu</option>
                                        <option value="buddha" @if(old('agama') == 'buddha') selected @endif>Buddha</option>
                                        <option value="khonghucu" @if(old('agama') == 'khonghucu') selected @endif>Khonghucu</option>
                                    @else
                                        <option value="islam" @if($penduduk->agama == 'islam') selected @endif>Islam</option>
                                        <option value="protestan" @if($penduduk->agama == 'protestan') selected @endif>Protestan</option>
                                        <option value="katolik" @if($penduduk->agama == 'katolik') selected @endif>Katolik</option>
                                        <option value="hindu" @if($penduduk->agama == 'hindu') selected @endif>Hindu</option>
                                        <option value="buddha" @if($penduduk->agama == 'buddha') selected @endif>Buddha</option>
                                        <option value="khonghucu" @if($penduduk->agama == 'khonghucu') selected @endif>Khonghucu</option> 
                                    @endif
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
                                <label for="title">Pendidikan Tertinggi<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('pendidikan') is-invalid @enderror" name="pendidikan" id="pendidikan"  style="width: 100%" required aria-placeholder="Pilih Pendidikan" required>
                                    <option value="">Pilih Pendidikan</option>
                                    @if(old('pendidikan'))
                                        @foreach($pendidikans as $pendidikan)
                                            <option value="{{ $pendidikan->id }}" @if(old('pendidikan') == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($pendidikans as $pendidikan)
                                            <option value="{{ $pendidikan->id }}" @if($penduduk->pendidikan_id == $pendidikan->id) selected @endif>{{ $pendidikan->jenjang_pendidikan }}</option>
                                        @endforeach
                                    @endif
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
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Pekerjaan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('pekerjaan') is-invalid @enderror" name="pekerjaan" id="pekerjaan"  style="width: 100%" required aria-placeholder="Pilih Pekerjaan" required>
                                    <option value="">Pilih Pekerjaan</option>
                                    @if(old('pekerjaan'))
                                        @foreach($pekerjaans as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}"  @if(old('pekerjaan') == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                        @endforeach
                                    @else
                                        @foreach($pekerjaans as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}"  @if($penduduk->profesi_id == $pekerjaan->id) selected @endif>{{ $pekerjaan->profesi }}</option>
                                        @endforeach 
                                    @endif
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Golongan Darah<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('golongan_darah') is-invalid @enderror" name="golongan_darah" id="golongan_darah"  style="width: 100%" aria-placeholder="Pilih Golongan Darah" required>
                                    @if(old('golongan_darah'))
                                        <option value="" @if(old('golongan_darah') == '') selected @endif>Pilih Golongan Darah</option>
                                        <option value="A" @if(old('golongan_darah') == 'A') selected @endif>A</option>
                                        <option value="B" @if(old('golongan_darah') == 'B') selected @endif>B</option>
                                        <option value="AB" @if(old('golongan_darah') == 'AB') selected @endif>AB</option>
                                        <option value="O" @if(old('golongan_darah') == 'O') selected @endif>O</option>
                                    @else 
                                        <option value="">Pilih Golongan Darah</option>
                                        <option value="A" @if($penduduk->golongan_darah == 'A') selected @endif>A</option>
                                        <option value="B" @if($penduduk->golongan_darah == 'B') selected @endif>B</option>
                                        <option value="AB" @if($penduduk->golongan_darah == 'AB') selected @endif>AB</option>
                                        <option value="O" @if($penduduk->golongan_darah == 'O') selected @endif>O</option>
                                    @endif
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">No. Telepon</label>
                                <input type="text" class="form-control @error ('telepon') is-invalid @enderror"  id="telepon" name="telepon" placeholder="Masukkan Nomor Telepon" value="{{ old('telepon',$penduduk->telepon) }}">
                                @error('telepon')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Status Perkawinan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error ('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="status_perkawinan"  style="width: 100%" required aria-placeholder="Pilih Golongan Darah">
                                    <option value="">Pilih Status Perkawinan</option>
                                    @if(old('status_perkawinan'))
                                        <option value="belum_kawin" @if(old('status_perkawinan') == 'belum_kawin') selected @endif>Belum Kawin</option>
                                        <option value="kawin" @if(old('status_perkawinan') == 'kawin') selected @endif>Kawin</option>
                                        <option value="cerai_hidup" @if(old('status_perkawinan') == 'cerai_hidup') selected @endif>Cerai Hidup</option>
                                        <option value="cerai_mati"  @if(old('status_perkawinan') == 'cerai_mati') selected @endif>Cerai Mati</option>
                                    @else 
                                        <option value="belum_kawin" @if($penduduk->status_perkawinan == 'belum_kawin') selected @endif>Belum Kawin</option>
                                        <option value="kawin" @if($penduduk->status_perkawinan == 'kawin') selected @endif>Kawin</option>
                                        <option value="cerai_hidup" @if($penduduk->status_perkawinan == 'cerai_hidup') selected @endif>Cerai Hidup</option>
                                        <option value="cerai_mati"  @if($penduduk->status_perkawinan == 'cerai_mati') selected @endif>Cerai Mati</option>
                                    @endif
                                </select>
                                @error('status_perkawinan')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Status perkawinan wajib dipilih
                                    </div>
                                @enderror  
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Ayah Kandung</label>
                                <select class="select2 custom-select select-krama @error ('ayah_kandung') is-invalid @enderror" name="ayah_kandung" id="ayah_kandung"  style="width: 100%">
                                    @if($penduduk->ayah != '')
                                        <option value="{{ $penduduk->ayah->id }}">{{ $penduduk->ayah->nama }}</option>
                                    @else 
                                        <option value="">Cari Ayah Kandung</option>
                                    @endif
                                </select>
                                @error('ayah_kandung')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Ibu Kandung</label>
                                <select class="select2 custom-select select-krama @error ('ibu_kandung') is-invalid @enderror" name="ibu_kandung" id="ibu_kandung"  style="width: 100%">
                                    @if($penduduk->ibu != '')
                                        <option value="{{ $penduduk->ibu->id }}">{{ $penduduk->ibu->nama }}</option>
                                    @else 
                                        <option value="">Cari Ibu Kandung</option>
                                        @endif
                                </select>                                
                                @error('ibu_kandung')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Alamat<span class="text-danger small">*</span></label>
                                <input type="text" class="form-control @error ('alamat') is-invalid @enderror"  id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ old('alamat',$penduduk->alamat) }}" required>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Provinsi<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('provinsi') is-invalid @enderror" name="provinsi" id="provinsi"  style="width: 100%" required aria-placeholder="Pilih Provinsi" required>
                                    <option value="">Pilih Provinsi</option>
                                    @if(old('provinsi'))
                                        @foreach($provinsis as $prov)
                                            <option value="{{ $prov->id }}" @if(old('provinsi') == $prov->id) selected @endif>{{ $prov->name }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($provinsis as $prov)
                                            <option value="{{ $prov->id }}" @if($prov->id == $provinsi->id) selected @endif>{{ $prov->name }}</option>
                                        @endforeach
                                    @endif
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
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Kabupaten<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('kabupaten') is-invalid @enderror" name="kabupaten" id="kabupaten"  style="width: 100%" required aria-placeholder="Pilih Kabupaten" required>
                                    <option value="">Pilih Kabupaten</option>
                                    @if(old('kabupaten'))
                                        @foreach($kabupatens as $kab)
                                            <option value="{{ $kab->id }}" @if(old('kabupaten') == $kab->id) selected @endif>{{ $kab->name }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($kabupatens as $kab)
                                            <option value="{{ $kab->id }}" @if($kab->id == $kabupaten->id) selected @endif>{{ $kab->name }}</option>
                                        @endforeach
                                    @endif
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Kecamatan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('kecamatan') is-invalid @enderror" name="kecamatan" id="kecamatan"  style="width: 100%" required aria-placeholder="Pilih Kecamatan" required>
                                    <option value="">Pilih Kecamatan</option>
                                    @if(old('kecamatan'))
                                        @foreach($kecamatans as $kec)
                                            <option value="{{ $kec->id }}" @if(old('kecamatan') == $kec->id) selected @endif>{{ $kec->name }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($kecamatans as $kec)
                                            <option value="{{ $kec->id }}" @if($kec->id == $kecamatan->id) selected @endif>{{ $kec->name }}</option>
                                        @endforeach
                                    @endif
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
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Desa/Kelurahan<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('desa') is-invalid @enderror" name="desa" id="desa"  style="width: 100%" required aria-placeholder="Pilih Desa" required>
                                    <option value="">Pilih Desa/Kelurahan</option>
                                    @if(old('desa'))
                                        @foreach($desas as $des)
                                            <option value="{{ $des->id }}" @if($des->id == $desa->id || old('desa') == $desa->id) selected @endif>{{ $des->name }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($desas as $des)
                                            <option value="{{ $des->id }}" @if($des->id == $desa->id || old('desa') == $desa->id) selected @endif>{{ $des->name }}</option>
                                        @endforeach
                                    @endif
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Masuk<span class="text-danger small">*</span></label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_masuk') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_masuk" id="tanggal_masuk" placeholder="Masukkan Tanggal Masuk Krama Mipil"  value="{{ old('tanggal_lahir',date('d M Y', strtotime($krama->tanggal_masuk))) }}" required>
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

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="title">Tanggal Keluar</label>
                                <input type="text" class="datepicker-here form-control @error ('tanggal_keluar') is-invalid @enderror" placeholder="dd mmm yyyy" name="tanggal_keluar" id="tanggal_keluar" placeholder="Masukkan Keluar Masuk Krama Mipil"  value="@if($krama->tanggal_keluar != '') {{ old('tanggal_lahir',date('d M Y', strtotime($krama->tanggal_keluar))) }} @endif">
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
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_dinas_row">
                                <label for="title">Banjar Dinas<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('banjar_dinas_id') is-invalid @enderror" name="banjar_dinas_id" id="banjar_dinas"  style="width: 100%" aria-placeholder="Pilih Banjar Dinas" required>
                                    <option value="">Pilih Banjar Dinas</option>
                                    @if(old('banjar_dinas_id'))
                                        @foreach($banjar_dinas as $dinas)
                                            <option value="{{ $dinas->id }}" @if(old('banjar_dinas_id') == $dinas->id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                        @endforeach
                                    @else
                                        @foreach($banjar_dinas as $dinas)
                                            <option value="{{ $dinas->id }}" @if($dinas->id == $krama->banjar_dinas_id) selected @endif>{{ $dinas->nama_banjar_dinas }}</option>
                                        @endforeach 
                                    @endif
                                </select>
                                @error('banjar_dinas_id')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Banjar Dinas wajib dipilih
                                    </div>
                                @enderror 
                            </div>
                        </div>

                        <div class="row mx-5">
                            <div class="col-lg-6 col-sm-12 py-2" id="banjar_adat_row">
                                <label for="title">Banjar Adat<span class="text-danger small">*</span></label>
                                <select class="select2 custom-select @error('banjar_adat_id') is-invalid @enderror" name="banjar_adat_id" id="banjar_adat"  style="width: 100%" aria-placeholder="Pilih Banjar Adat" required>
                                    <option value="">Pilih Banjar Adat</option>
                                    @if(old('banjar_adat_id'))
                                        @foreach($banjar_adat as $adat)
                                            <option value="{{ $adat->id }}" @if(old('banjar_adat_id') == $adat->id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
                                        @endforeach
                                    @else 
                                        @foreach($banjar_adat as $adat)
                                            <option value="{{ $adat->id }}" @if($adat->id == $krama->banjar_adat_id) selected @endif>{{ $adat->nama_banjar_adat }}</option>
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
                            <div class="col-lg-6 col-sm-12 py-2">
                                <label for="foto">Foto</label>
                                <br>
                                <input type="text" class="form-control @error ('foto') is-invalid @enderror" name="foto" id="foto" value="{{ old('foto') }}" placeholder="url" hidden>
                                <img src="@if(old('foto')) {{ old('foto') }} @elseif($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="rounded img-thumbnail" style="max-width:30%;" id="propic">
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
                <img  src="@if($penduduk->foto !='') {{ $penduduk->foto }} @else {{asset('assets/admin/assets/img/foto_placeholder.png')}} @endif" class="text-center" id="image-preview" width="50%" height="100%" alt="">
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
    @if (count($errors)>0)
    @if($errors->has('golongan_darah'))
        <script>
            $("#golongan_darah").addClass('is-invalid');
        </script>
    @endif
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
            //Nama On Change
            $('#gelar_depan,#nama,#gelar_belakang').on('keyup', function() {
                nama_tercetak();
            });
            //Jenis Kependudukan On Change
            $("#jenis_kependudukan").on('change', function(){
                if($(this).val() == 'dinas'){
                    $("#banjar_dinas").prop('required', true);
                    $("#banjar_adat").prop('required', false);
                    $("#banjar_dinas_row").fadeIn();
                    $("#banjar_adat_row").fadeOut();
                }else if($(this).val() == ''){
                    $("#banjar_adat_row").fadeOut();
                    $("#banjar_dinas_row").fadeOut();
                    $("#banjar_adat").prop('required', false);
                    $("#banjar_dinas").prop('required', false);
                }else{
                    $("#banjar_adat").prop('required', true);
                    $("#banjar_dinas").prop('required', true);
                    $("#banjar_adat_row").fadeIn();
                    $("#banjar_dinas_row").fadeIn();
                }
            });
            //Regex NIK
            $('#nik').on('input', function (event) { 
                this.value = this.value.replace(/[^0-9]/g, '');
                if($("#nik").val().length == 16){
                    $("#nik-validate").fadeOut();
                }
            });
            //Daerah On Change
            $('#provinsi').on('change', function(){
                var url = "{{ route('admin-kabupaten-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                if($(this).val() != ""){
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kabupaten').empty();
                            $('#kabupaten').append('<option value="">Pilih Kabupaten</option>');
                            result['0'].forEach(element => {
                                $('#kabupaten').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });
            $('#kabupaten').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-kecamatan-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#kecamatan').empty();
                            $('#kecamatan').append('<option value="">Pilih Kecamatan</option>');
                            result['0'].forEach(element => {
                                $('#kecamatan').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
                        }
                    });
                }
            });
            $('#kecamatan').on('change', function(){
                if($(this).val() != ""){
                    var url = "{{ route('admin-desa-dinas-get', ":id") }}";
                    url = url.replace(':id', $(this).val());
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#desa').empty();
                            $('#desa').append('<option value="">Pilih Desa/Kelurahan</option>');
                            result['0'].forEach(element => {
                                $('#desa').append('<option value="' + element['id'] + '"' +'>' + element['name'] + '</option>');
                            });                     
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
            $('#nav-link-cacah-tamiu').addClass('active');;
        });
        function nama_tercetak(){
            var gelar_depan = "";
            var nama = $("#nama").val();
            var gelar_belakang = "";
            if ($("#gelar_depan").val()!="") {
                gelar_depan += $("#gelar_depan").val() + " ";
            }
            if ($("#gelar_belakang").val()!="") {
                gelar_belakang += ", " + $("#gelar_belakang").val();
            }
            $("#nama_tercetak").val(gelar_depan+nama+gelar_belakang);
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
                        form.classList.remove('is-invalid')
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
                url: '{{ route("api-cacah-tamiu-wni-ortu-search") }}',
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