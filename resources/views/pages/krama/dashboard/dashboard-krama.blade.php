@extends('layouts.krama.krama')

@section('title', 'Dashboard Krama')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-selection {
            border: 1px solid black !important;
        }
        .select2-selection{
            background-color: white !important;
        }
        .select2-search--dropdown{
            border-top: 1px solid black !important;
            border-left: 1px solid black !important;
            border-right: 1px solid black !important;
        }
        .select2-results { 
            border-left: 1px solid black !important;
            border-bottom: 1px solid black !important;
            border-right: 1px solid black !important;
            border-bottom-left-radius: 0.3rem;
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
                                    <i class="fa-solid fa-house-chimney-user mr-2"></i>
                                </div>
                                Dashboard
                            </h1>
                            <div class="page-header-subtitle">
                                Dashboard Krama Desa Adat
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-n10">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-7">
                    <div class="card card-waves mb-4">
                        <div class="card-body p-4">
                            <h2 class="text-primary">Selamat Datang di Dashboard <span>SIKRAMAT!</span></h2>
                            <p class="text-gray-700">SIKRAMAT merupakan Sistem Informasi Manajemen Kependudukan/Krama Adat yang dapat membantu pihak Desa Adat dalam melakukan pendataan kependudukan.</p>
                            <a class="btn btn-primary btn-sm px-3 py-2" href="{{ route('Keluarga Krama') }}">
                                Lebih Lanjut
                                <i class="ml-1" data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card card-waves mb-4">
                        <div class="card-body p-4">
                            <h2 class="text-primary">Lihat Profile</h2>
                            <p class="text-gray-700">Status kependudukan, data diri, dan data akun dapat anda lihat pada Menu Profile. Lihat dan perbaharui profile anda sekarang.</p>
                            <a class="btn btn-primary btn-sm px-3 py-2" href="{{ route('Profile Krama') }}">
                                Lihat Profile
                                <i class="ml-1" data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#link-dashboard').addClass('active');

            $('.select-role').select2({
                placeholder: "Pilih hak akses",
                closeOnSelect: true,
                language: {
                    noResults: function () {
                        return 'Tidak terdapat data yang sesuai';
                    }
                }
            })
        });
    </script>
@endpush