@extends('layouts.admin.admin')
@section('title', 'Dashboard')
@section('content')
    <main>
        <header class="page-header page-header-light bg-light mb-0">
            <div class="container">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon">
                                    <i class="fa-solid fa-house-chimney-user"></i>
                                </div>
                                Dashboard
                            </h1>
                            <div class="page-header-subtitle">Dashboard Sistem Informasi Manajemen Kependudukan Desa Adat Terintegrasi</div>
                        </div>
                        {{-- <div class="col-12 col-xl-auto mt-4">
                            <button class="btn btn-white btn-sm line-height-normal p-3" id="reportrange">
                                <i class="mr-2 text-primary" data-feather="calendar"></i>
                                <span></span>
                                <i class="ml-1" data-feather="chevron-down"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container mt-1">
            <div class="card card-waves mb-4">
                <div class="card-body p-5">
                    <div class="row align-items-center justify-content-between">
                        <div class="col">
                            <h2 class="text-primary">Selamat Datang di Dashboard <span>SIKRAMAT!</span></h2>
                            <p class="text-gray-700">SIKRAMAT merupakan Sistem Informasi Manajemen Kependudukan/Krama Adat yang dapat membantu pihak Desa Adat dalam melakukan pendataan kependudukan.</p>
                            <a class="btn btn-primary btn-sm px-3 py-2" href="#!">
                                Laporan / Rekap
                                <i class="ml-1" data-feather="arrow-right"></i>
                            </a>
                        </div>
                        <div class="col d-none d-lg-block mt-xxl-n4"><img class="img-fluid px-xl-4 mb-xxl-n5" height="60%" src="{{asset('assets/admin/assets/img/population.png')}}" /></div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- KRAMA MIPIL --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 1-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-primary mb-1">Krama Mipil</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_krama_mipil, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_krama_mipil_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-user fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KRAMA TAMIU --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 2-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-secondary mb-1">Krama Tamiu</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_krama_tamiu, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_krama_tamiu_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-user fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAMIU --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 3-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-success mb-1">Tamiu</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_tamiu, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_tamiu_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-user fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CACAH KRAMA MIPIL --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 4-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-info mb-1">Cacah Krama Mipil</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_cacah_krama_mipil, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_cacah_krama_mipil_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-users fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- CACAH KRAMA TAMIU --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 1-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-primary mb-1">Cacah Krama Tamiu</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_cacah_krama_tamiu, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_cacah_krama_tamiu_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-users fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CACAH TAMIU --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 2-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-secondary mb-1">Cacah Tamiu</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_cacah_tamiu, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_cacah_tamiu_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-users fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KELAHIRAN --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 3-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-success mb-1">Kelahiran</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_kelahiran, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_kelahiran_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-baby fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KEMATIAN --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- Dashboard info widget 4-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-info mb-1">Kematian</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_kematian, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_kematian_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-book-dead fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- PERKAWINAN --}}
                <div class="col-xl-4 col-md-6 mb-4">
                    <!-- Dashboard info widget 1-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-primary mb-1">Perkawinan</div>
                                    <div class="h5">Jumlah: {{ number_format($jumlah_perkawinan, 0, '', '.') }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_perkawinan_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-heart fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERCERAIAN --}}
                <div class="col-xl-4 col-md-6 mb-4">
                    <!-- Dashboard info widget 2-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-secondary mb-1">Perceraian</div>
                                    <div class="h5">Jumlah: 30</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + 3
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-heart-broken fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAPERAS --}}
                <div class="col-xl-4 col-md-6 mb-4">
                    <!-- Dashboard info widget 3-->
                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold text-success mb-1">Maperas</div>
                                    <div class="h5">Jumlah: {{ $jumlah_maperas }}</div>
                                    <div class="text-xs font-weight-bold text-success d-inline-flex align-items-center">
                                        <span class="mr-2">Bulan ini</span>
                                        <i class="mr-1" data-feather="trending-up"></i>
                                        + {{ $jumlah_maperas_nambah }}
                                    </div>
                                </div>
                                <div class="ml-2"><i class="fas fa-people-arrows fa-2x text-gray-200"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection