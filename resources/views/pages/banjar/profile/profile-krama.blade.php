@extends('layouts.banjar.banjar')

@section('title', 'Profile Prajuru Banjar Adat')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" integrity="sha512-0SPWAwpC/17yYyZ/4HSllgaK7/gg9OlVozq8K7rf3J8LvCjYEEIfzzpnA2/SSjpGIunCSD18r3UhvDcu/xncWA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
    <style>
        #mapid { height: 500px; }
        .nav-pills .nav-link.active,
        .nav-pills .show > .nav-link {
        color: #fff;
        background-color: #0061f2;
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
                                <div class="page-header-icon">
                                    <i class="fas fa-user mr-2"></i>
                                </div>
                                Halaman Profile
                            </h1>
                            <div class="page-header-subtitle">
                                Profile Prajuru Banjar Adat
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-n10">
            <div class="row">
                <div class="col-12 col-md-5 col-lg-4 col-xl-5">
                    <div class="card my-1">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="image mx-auto d-block rounded">
                                    @if($data['penduduk']->foto != '')
                                        <img class="profile-user-img img-fluid img-circle mx-auto d-block" style="width: 50%" src="{{ $data['penduduk']->foto }}?{{date('YmdHis')}}" alt="profile_pribadi">
                                    @else
                                        <img class="profile-user-img img-fluid img-circle mx-auto d-block" style="width: 50%" src="{{ asset('assets/admin/assets/img/foto_placeholder.png') }}?{{date('YmdHis')}}" alt="profile_pribadi">
                                    @endif                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-block btn-primary" data-target="#crop-image" data-toggle="modal">
                                <i class="fas fa-image mr-2"></i>
                                Ganti Foto Profil
                            </button>
                        </div>
                    </div>
                    <div class="card my-1">
                        <div class="card-body">
                            <div class="row">
                                @if ($data['penduduk']->gelar_depan == NULL && $data['penduduk']->gelar_depan == NULL)
                                    @php
                                        $nama_lengkap = $data['penduduk']->nama;
                                    @endphp
                                @else
                                    @if ($data['penduduk']->gelar_depan != NULL && $data['penduduk']->gelar_belakang == NULL)
                                        @php
                                            $nama_lengkap = $data['penduduk']->gelar_depan.' '.$data['penduduk']->nama;
                                        @endphp
                                    @endif
                                    @if ($data['penduduk']->gelar_depan == NULL && $data['penduduk']->gelar_belakang != NULL)
                                        @php
                                            $nama_lengkap = $data['penduduk']->nama.', '.$data['penduduk']->gelar_belakang;
                                        @endphp
                                    @endif
                                    @if ($data['penduduk']->gelar_depan != NULL && $data['penduduk']->gelar_belakang != NULL)
                                        @php
                                            $nama_lengkap = $data['penduduk']->gelar_depan.' '.$data['penduduk']->nama.', '.$data['penduduk']->gelar_belakang;
                                        @endphp
                                    @endif
                                @endif

                                @if($data['penduduk']->ayah_kandung_id != NULL)
                                    @if ($data['penduduk']->ayah->gelar_depan == NULL && $data['penduduk']->ayah->gelar_depan == NULL)
                                        @php
                                            $nama_lengkap = $data['penduduk']->nama;
                                            $nama_ayah = $data['penduduk']->ayah->nama ?? 'Belum ditambahkan';
                                            $nama_ibu = $data['penduduk']->ibu->nama ?? 'Belum ditambahkan';
                                        @endphp
                                    @else
                                        @if ($data['penduduk']->ayah->gelar_depan != NULL && $data['penduduk']->ayah->gelar_belakang == NULL)
                                            @php
                                                $nama_ayah = $data['penduduk']->ayah->gelar_depan.' '.$data['penduduk']->ayah->nama;
                                            @endphp
                                        @endif
                                        @if ($data['penduduk']->ayah->gelar_depan == NULL && $data['penduduk']->ayah->gelar_belakang != NULL)
                                            @php
                                                $nama_ayah = $data['penduduk']->ayah->nama.', '.$data['penduduk']->ayah->gelar_belakang;
                                            @endphp
                                        @endif
                                        @if ($data['penduduk']->ayah->gelar_depan != NULL && $data['penduduk']->ayah->gelar_belakang != NULL)
                                            @php
                                                $nama_ayah = $data['penduduk']->ayah->gelar_depan.' '.$data['penduduk']->ayah->nama.', '.$data['penduduk']->ayah->gelar_belakang;
                                            @endphp
                                        @endif
                                    @endif
                                @else
                                    @php
                                        $nama_ayah = 'Belum ditambahkan';
                                    @endphp
                                @endif

                                @if($data['penduduk']->ibu_kandung_id != NULL)
                                    @if ($data['penduduk']->ibu->gelar_depan == NULL && $data['penduduk']->ibu->gelar_depan == NULL)
                                        @php
                                            $nama_lengkap = $data['penduduk']->nama;
                                            $nama_ibu = $data['penduduk']->ibu->nama ?? 'Belum ditambahkan';
                                            $nama_ibu = $data['penduduk']->ibu->nama ?? 'Belum ditambahkan';
                                        @endphp
                                    @else
                                        @if ($data['penduduk']->ibu->gelar_depan != NULL && $data['penduduk']->ibu->gelar_belakang == NULL)
                                            @php
                                                $nama_ibu = $data['penduduk']->ibu->gelar_depan.' '.$data['penduduk']->ibu->nama;
                                            @endphp
                                        @endif
                                        @if ($data['penduduk']->ibu->gelar_depan == NULL && $data['penduduk']->ibu->gelar_belakang != NULL)
                                            @php
                                                $nama_ibu = $data['penduduk']->ibu->nama.', '.$data['penduduk']->ibu->gelar_belakang;
                                            @endphp
                                        @endif
                                        @if ($data['penduduk']->ibu->gelar_depan != NULL && $data['penduduk']->ibu->gelar_belakang != NULL)
                                            @php
                                                $nama_ibu = $data['penduduk']->ibu->gelar_depan.' '.$data['penduduk']->ibu->nama.', '.$data['penduduk']->ibu->gelar_belakang;
                                            @endphp
                                        @endif
                                    @endif
                                @else
                                    @php
                                        $nama_ibu = 'Belum ditambahkan';
                                    @endphp
                                @endif

                                
                                <div class="col-12 mb-3">
                                    <label for="nama_lengkap" class="form-label text-dark my-0">Nama dengan Gelar</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark p-2" id="nama_lengkap" value="{{ $nama_lengkap ?? '-' }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="nik" class="form-label text-dark my-0">NIK</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark p-2" id="nik" value="{{ $data['penduduk']->nik ?? '-' }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="ttl" class="form-label text-dark my-0">Tempat, Tanggal lahir</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="ttl" value="{{ $data['penduduk']->tempat_lahir }}, {{ \Carbon\Carbon::parse($data['penduduk']->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="gender" class="form-label text-dark my-0">Jenis Kelamin</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="gender" value="{{ ucfirst($data['penduduk']->jenis_kelamin) }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="perkawinan" class="form-label text-dark my-0">Status Perkawinan</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="perkawinan" value="{{ ucwords(str_replace("_", " ", $data['penduduk']->status_perkawinan)) }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="agama" class="form-label text-dark my-0">Agama</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="agama" value="{{ ucfirst($data['penduduk']->agama) }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="nama_ayah" class="form-label text-dark my-0">Nama Ayah</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="nama_ayah" value="{{ $nama_ayah ?? 'Belum ditambahkan' }}" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="nama_ibu" class="form-label text-dark my-0">Nama Ibu</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-dark" id="nama_ibu" value="{{ $nama_ibu ?? 'Belum ditambahkan' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-8 col-xl-7">
                    <div class="card my-1">
                        <div class="card-header p-2 d-flex justify-content-center justify-content-lg-start justify-content-sm-start">
                            <ul class="nav nav-pills small">
                                <li class="nav-item"><a class="nav-link active" id="tabAccount" href="#personal-account" data-toggle="tab">Akun</a></li>
                                <li class="nav-item"><a class="nav-link" id="tabPassword" href="#password" data-toggle="tab">Password</a></li>
                                <li class="nav-item"><a class="nav-link" id="tabPersonal" href="#kependudukan" data-toggle="tab">Kependudukan</a></li>
                            </ul>
                        </div>
                        <div class="card-body py-auto">
                            <div class="tab-content">
                                <div class="tab-pane active" id="personal-account">
                                    <form action="{{ route('Change Profile Krama', $data['penduduk']->id) }}" method="POST" class="form-horizontal needs-validation my-0" novalidate>
                                        @csrf
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Email<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Masukan email" value="{{ $data['user']->email }}" autocomplete="off" required>
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
                                        <div class="form-group row mb-5">
                                            <label for="telepon" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">No.Telp/Hp</label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="telepon" type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" placeholder="Masukan nomor telepon" value="{{ $data['penduduk']->telepon }}" autocomplete="off">
                                                @error('telepon')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Nomor telepon wajib diisi
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="gelar_depan" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Gelar Depan</label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="gelar_depan" type="text" class="form-control @error('gelar_depan') is-invalid @enderror" id="gelar_depan" placeholder="Masukan gelar depan" value="{{ $data['penduduk']->gelar_depan }}" autocomplete="off">
                                                @error('gelar_depan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="nama" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Nama<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="nama" type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" placeholder="Masukan nama" value="{{ $data['penduduk']->nama }}" autocomplete="off" required>
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
                                        <div class="form-group row">
                                            <label for="gelar_belakang" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Gelar Belakang</label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="gelar_belakang" type="text" class="form-control @error('gelar_belakang') is-invalid @enderror" id="gelar_belakang" placeholder="Masukan gelar belakang" value="{{ $data['penduduk']->gelar_belakang }}" autocomplete="off">
                                                @error('gelar_belakang')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row mb-5">
                                            <label for="nama_alias" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Nama Alias (Bhiseka)</label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <input name="nama_alias" type="text" class="form-control @error('nama_alias') is-invalid @enderror" id="nama_alias" placeholder="Masukan nama alias (Bhiseka)" value="{{ $data['penduduk']->nama_alias }}" autocomplete="off">
                                                @error('nama_alias')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="pekerjaan" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Pekerjaan<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <select class="select2 select-pekerjaan form-select @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan" required style="width: 100%">
                                                    <option></option>
                                                    @foreach ($data['pekerjaan'] as $pekerjaan)
                                                        <option @if($pekerjaan->id == $data['penduduk']->profesi_id) selected @endif value="{{ $pekerjaan->id }}">{{ $pekerjaan->profesi }}</option>
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
                                        <div class="form-group row">
                                            <label for="pendidikan" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Pendidikan Tertinggi<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <select class="select2 select-pendidikan form-select @error('pendidikan') is-invalid @enderror" id="pendidikan" name="pendidikan" required style="width: 100%">
                                                    <option></option>
                                                    @foreach ($data['pendidikan'] as $pendidikan)
                                                        <option @if($pendidikan->id == $data['penduduk']->pendidikan_id) selected @endif value="{{ $pendidikan->id }}">{{ $pendidikan->jenjang_pendidikan }}</option>
                                                    @endforeach
                                                </select>
                                                @error('pendidikan')
                                                    <div class="invalid-feedback text-start">
                                                        {{ $message }}
                                                    </div>
                                                @else
                                                    <div class="invalid-feedback">
                                                        Pendidikan wajib dipilih
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="alamat" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Alamat<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <textarea name="alamat" type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" placeholder="Masukan alamat krama wajib" value="" autocomplete="off" required>{{ $data['penduduk']->alamat }}</textarea>
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
                                        <div class="form-group row mb-5">
                                            @if($data['penduduk']->koordinat_alamat)
                                                @php 
                                                    $koor = $data['penduduk']->koordinat_alamat;
                                                    $koor_placeholder = json_decode($koor);
                                                    $koor_placeholder = $koor_placeholder->lat.', '.$koor_placeholder->lng;
                                                @endphp
                                            @else 
                                                @php 
                                                    $koor = '';
                                                    $koor_placeholder = '';
                                                    $koor_placeholder = '';
                                                @endphp
                                            @endif
                                            <label for="alamat" class="col-sm-12 col-md-5 col-lg-4 col-xl-3 col-form-label mt-0 pt-0">Koordinat Alamat</label>
                                            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9 my-auto">
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <input type="text" readonly class="form-control" name="koordinat_alamat_placeholder" id="koordinat_alamat_placeholder" placeholder="Pilih Koordinat" value="{{ old('koordinat_alamat_placeholder', $koor_placeholder) }}">
                                                    <input type="text" hidden class="form-control" name="koordinat_alamat" id="koordinat_alamat" value="{{ old('koordinat_alamat', $koor) }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" onclick="map_modal()" data-toggle="tooltip" id="btn_pick_koordinat" data-placement="top" title="" data-original-title="Cari koordinat"><i class="fas fa-map-marker-alt"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-0">
                                            <div class="col-sm-12 text-right">
                                                <button class="btn btn-sm btn-success btn-icon-split text-end p-0">
                                                    <span class="icon">
                                                        <i class="fas fa-save"></i>
                                                    </span>
                                                    <span class="text">Simpan</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="password">
                                    <form action="{{ route('Change Password', $data['user']->id) }}" class="form-horizontal needs-validation" method="POST" novalidate>
                                        @csrf
                                        <div class="form-group row">
                                            <label for="inputPasswordLama" class="col-sm-12 col-md-3 col-form-label">Password Lama<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-9">
                                                <div class="input-group">
                                                    <input type="password" name="password_lama" autocomplete="off" class="form-control @error('password_lama') is-invalid @enderror" id="inputPasswordLama" placeholder="Masukan password lama" autocomplete="off" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text rounded-right bg-light" style="cursor: pointer" onclick="oldPasswordVisibility()"><i class="fas fa-eye-slash" id="oldPassIcon"></i></span>
                                                    </div>
                                                    @error('password_lama')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Password lama wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-12 col-md-3 col-form-label">Password Baru<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-9">
                                                <div class="input-group">
                                                    <input type="password" name="password" autocomplete="off" class="form-control @error('password') is-invalid @enderror" id="inputPassword" placeholder="Masukan password baru" autocomplete="off" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-light rounded-right" style="cursor: pointer" onclick="newPasswordVisibility()"><i class="fas fa-eye-slash" id="newPassIcon"></i></span>
                                                    </div>
                                                    @error('password')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Password baru wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPasswordConfirmation" class="col-sm-12 col-md-3 col-form-label">Konfirmasi Password<small class="text-danger">*</small></label>
                                            <div class="col-sm-12 col-md-9">
                                                <div class="input-group">
                                                    <input type="password" name="password_confirmation" autocomplete="off" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{ old('password_confirmation') }}" id="inputPasswordConfirmation" placeholder="Masukan konfirmasi password baru" autocomplete="off" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-light rounded-right" style="cursor: pointer" onclick="confirmPasswordVisibility()"><i class="fas fa-eye-slash" id="confirmPassIcon"></i></span>
                                                    </div>
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback text-start">
                                                            {{ $message }}
                                                        </div>
                                                    @else
                                                        <div class="invalid-feedback">
                                                            Konfirmasi password baru wajib diisi
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mt-4">
                                            <div class="col-12 text-right">
                                                <button class="btn btn-sm btn-success btn-icon-split text-end p-0">
                                                    <span class="icon">
                                                        <i class="fas fa-save"></i>
                                                    </span>
                                                    <span class="text">Simpan</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="kependudukan">
                                    {{-- MIPIL --}}
                                    <div class="list-group">
                                        <div class="list-group-item list-group-item-action flex-column align-items-start active">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex w-100 justify-content-center" style="font-weight: bold">
                                                        <h5 class="mb-1 text-white">Cacah Krama Wed/Mipil</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="row">
                                                <div class="col-12 col-md-5 col-lg-4">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">Desa Adat</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-7 col-lg-8">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <p class="mb-1">{{ $data['cacah_krama_mipil']->banjar_adat->desa_adat->desadat_nama }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="row">
                                                <div class="col-12 col-md-5 col-lg-4">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">Banjar Adat</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-7 col-lg-8">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <p class="mb-1">{{ $data['cacah_krama_mipil']->banjar_adat->nama_banjar_adat }}</hp>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="row">
                                                <div class="col-12 col-md-5 col-lg-4">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">Tempekan</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-7 col-lg-8">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <p class="mb-1">{{ $data['cacah_krama_mipil']->tempekan->nama_tempekan }}</hp>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="row">
                                                <div class="col-12 col-md-5 col-lg-4">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">Nomor Cacah Krama Mipil</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-7 col-lg-8">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <p class="mb-1">{{ $data['cacah_krama_mipil']->nomor_cacah_krama_mipil }}</hp>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TAMIU --}}
                                    @if($data['cacah_krama_tamiu'])
                                        <div class="list-group">
                                            <div class="list-group-item list-group-item-action flex-column align-items-start active">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="d-flex w-100 justify-content-center" style="font-weight: bold">
                                                            <h5 class="mb-1 text-white">Cacah Krama Wed/Mipil</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="row">
                                                    <div class="col-12 col-md-5 col-lg-4">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1">Desa Adat</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-lg-8">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1">{{ $data['cacah_krama_tamiu']->banjar_adat->desa_adat->desadat_nama }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="row">
                                                    <div class="col-12 col-md-5 col-lg-4">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1">Banjar Adat</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-lg-8">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1">{{ $data['cacah_krama_tamiu']->banjar_adat->nama_banjar_adat }}</hp>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="row">
                                                    <div class="col-12 col-md-5 col-lg-4">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1">Tanggal Masuk</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-lg-8">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1">{{ $data['cacah_krama_tamiu']->tanggal_masuk }}</hp>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="row">
                                                    <div class="col-12 col-md-5 col-lg-4">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1">Nomor Cacah Krama Tamiu</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-lg-8">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1">{{ date('d M Y', strtotime($data['cacah_krama_tamiu']->nomor_cacah_krama_tamiu)) }}</hp>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('banjar-dashboard') }}" class="btn btn-sm btn-danger btn-icon-split text-end p-0">
                                <span class="icon">
                                    <i class="fas fa-arrow-left"></i>
                                </span>
                                <span class="text">Kembali/Batal</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <form id="form_update_profile_img" action="{{ route('Change Profile Image Krama', $data['penduduk']->id) }}" method="POST" hidden>
        @csrf
        <input type="text" id="profile_img_raw" value="aaa" name="profile_img">
    </form>
    
    {{-- Change Profile Modal --}}
    <div class="modal fade" id="crop-image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Foto Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-danger" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row p-3">
                        <div class="col-12 d-flex justify-content-center">
                            <img src="" id="preview" style="height: 100%; width:auto; max-height: 50vh;" class="text-center" alt="">
                        </div>
                        <div class="custom-file my-2">
                            <input type="file" name="profile_img" class="custom-file-input" id="profile_img" accept=".jpg,.jpeg,.png" required>
                            <label for="foto_label" id="foto_labell" class="custom-file-label">Pilih Foto</label>
                        </div>
                        <small class="small mr-2">(Foto maksimal berukuran 2 MB)</small>
                        <div id="validasi-foto" class="text-danger small text-end" style="display:none;">
                            Ukuran gambar maksimal 2 MB.
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button id="modal-close" class="btn btn-sm btn-danger btn-icon-split text-end p-0" data-dismiss="modal">
                        <span class="icon">
                            <i class="fa-solid fa-circle-arrow-left"></i>
                        </span>
                        <span class="text">Kembali</span>
                    </button>
                    <button onclick="updateProfileImg()" class="btn btn-sm btn-success btn-icon-split text-end p-0">
                        <span class="icon">
                            <i class="fas fa-save"></i>
                        </span>
                        <span class="text">Simpan Foto Profile</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" integrity="sha512-ooSWpxJsiXe6t4+PPjCgYmVfr1NS5QXJACcR/FPpsdm6kqG1FmQ2SVyg2RXeVuCRBLr0lWHnWJP6Zs1Efvxzww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

    @if($message = Session::get('success'))
        <script>
            $(document).ready(function(){
                alertSuccess('{{$message}}');
            });
        </script>
    @endif

    @if($message = Session::get('failed'))
        <script>
            $(document).ready(function(){
                alertError('{{$message}}');
            });
        </script>
    @endif

    @if ($errors->has('password_lama') || $errors->has('password'))
        <script>
            $('#tabAccount').removeClass('active');
            $('#personal-account').removeClass('active');
            $('#tabPassword').addClass('active');
            $('#password').addClass('active');
        </script>
    @endif

    {{-- LEAFLET --}}
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

        var penduduk = {!! json_encode($data['penduduk']) !!}
        if(penduduk.koordinat_alamat){
            let koor = JSON.parse(penduduk.koordinat_alamat);
            let koor_placeholder = koor.lat+', '+koor.lng;
            $("#koordinat_alamat_placeholder").val(koor_placeholder);
            $("#koordinat_alamat").val(JSON.stringify(koor));
            $("#modal_koordinat_alamat_placeholder").val(koor_placeholder);
            $("#modal_koordinat_alamat").val(JSON.stringify(koor));

            var marker;
            if (marker) { // check
                map.removeLayer(marker); // remove
            }
            marker = L.marker([koor.lat, koor.lng], {
                draggable: true
            }).addTo(map);
            document.getElementById('modal_koordinat_alamat_placeholder').value=koor.lat+', '+koor.lng;
            document.getElementById('modal_koordinat_alamat').value=JSON.stringify(koor);

            marker.on('dragend', function (e) {
                var koor = marker.getLatLng();
                document.getElementById('modal_koordinat_alamat_placeholder').value = marker.getLatLng().lat+', '+marker.getLatLng().lng;
                document.getElementById('modal_koordinat_alamat').value = JSON.stringify(koor);
            });
        }

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
        $(document).ready(function () {
            $('#link-profile').addClass('active');
            $('#profile_img').val("");

            $('.select-role').select2({
                placeholder: "Pilih hak akses",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-pekerjaan').select2({
                placeholder: "Pilih pekerjaan saat ini",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })

            $('.select-pendidikan').select2({
                placeholder: "Pilih pendidikan tertinggi",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })
        });

        function oldPasswordVisibility() {
            let toggle = document.getElementById('inputPasswordLama');
            let icon = document.getElementById('oldPassIcon');

            if (toggle.type == 'password') {
                toggle.type = 'text';
                icon.classList.remove('fa-eye-slash')
                icon.classList.add('fa-eye')
            } else {
                toggle.type = 'password';
                icon.classList.remove('fa-eye')
                icon.classList.add('fa-eye-slash')
            }
        }

        function newPasswordVisibility() {
            let toggle = document.getElementById('inputPassword');
            let icon = document.getElementById('newPassIcon');

            if (toggle.type == 'password') {
                toggle.type = 'text';
                icon.classList.remove('fa-eye-slash')
                icon.classList.add('fa-eye')
            } else {
                toggle.type = 'password';
                icon.classList.remove('fa-eye')
                icon.classList.add('fa-eye-slash')
            }
        }

        function confirmPasswordVisibility() {
            let toggle = document.getElementById('inputPasswordConfirmation');
            let icon = document.getElementById('confirmPassIcon');

            if (toggle.type == 'password') {
                toggle.type = 'text';
                icon.classList.remove('fa-eye-slash')
                icon.classList.add('fa-eye')
            } else {
                toggle.type = 'password';
                icon.classList.remove('fa-eye')
                icon.classList.add('fa-eye-slash')
            }
        }

        let cropper;
        let image = document.getElementById('preview');
        image.hidden;
        $('#profile_img').on('change', function(){
            let filedata = this.files[0];
            let match = ['image/jpg', 'image/jpeg', 'image/png'];
            if (!(filedata.type==match[0] || filedata.type==match[1] || filedata.type==match[2])) {
                alert("Invalied image type. Allows .jpg, .jpeg, .png only");
            }else if(filedata.size > (2097152)){
                $('#validasi-foto').show();
            }else{ 
                $('#validasi-foto').hide();
                let reader = new FileReader();
                reader.onload=function(ev){
                    $('#preview').attr('src', ev.target.result);
                    cropper.destroy();
                    cropper = new Cropper(image, {
                        aspectRatio: 3 / 4,
                        cropBoxResizable: false,
                        viewMode: 2,
                        preview: '.preview'
                    });
                    image.show;
                }
                reader.readAsDataURL(this.files[0]);
                let postData = new FormData();
                postData.append('file', this.files[0]);
            }
        });
        $('#crop-image').on('shown.bs.modal', function(){
            cropper = new Cropper(image, {
                aspectRatio: 14 / 9,
                cropBoxResizable: false,
                preview: '.preview'
            });            
        });

        function updateProfileImg() {
            var img_val;
            canvas = cropper.getCroppedCanvas();
            canvas.toBlob(function(blob){
                let reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    img_val = (reader.result);
                    $('#profile_img_raw').val(img_val);
                    console.log(img_val);
                    $('#form_update_profile_img').submit();
                    $('#crop-image').modal('toggle');
                }
            });
            cropper.destroy();
        }
    </script>

    
@endpush