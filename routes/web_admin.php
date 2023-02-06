<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route untuk halaman banjar
| Setting route dapat diakses pada RouteServiceProvider.php
|
*/

// ADMIN ROUTES
    Route::group(['prefix' => 'admin'], function () {
        //ADMIN DASHBOARD ROUTES
        Route::get('/', 'AdminController\DashboardController@index')->name('admin-dashboard')->middleware('role:super_admin, verified');
        //AKHIR ADMIN DASHBOARD ROUTES

        //MASTER DATA ROUTE
        Route::group(['prefix' => 'master'], function () {
            //NEGARA ROUTES
            Route::get('/negara/search', 'AdminController\NegaraController@search')->name('api-negara-search');
            //AKHIR NEGARA ROUTES

            //PROVINSI ROUTES
            Route::get('/provinsi/{id}', 'AdminController\ProvinsiController@get')->name('admin-provinsi-get');
            //AKHIR PROVINSI ROUTES

            //KABUPATEN ROUTES
            Route::get('/kabupaten/{id}', 'AdminController\KabupatenController@get')->name('admin-kabupaten-get');
            //AKHIR KABUPATEN ROUTES

            //KECAMATAN ROUTES
            Route::get('/kecamatan/{id}', 'AdminController\KecamatanController@get')->name('admin-kecamatan-get');
            //AKHIR KECAMATAN ROUTES

            //DESA DINAS ROUTES
            Route::get('/desa-dinas/{id}', 'AdminController\DesaDinasController@get')->name('admin-desa-dinas-get');
            //AKHIR DESA DINAS ROUTES

            //DESA ADAT ROUTES
            Route::get('/desa-adat/{id}', 'AdminController\DesaAdatController@get')->name('admin-desa-adat-get');
            //AKHIR DESA ADAT ROUTES

            //BANJAR ADAT ROUTES
            Route::get('/banjar-adat/{id}', 'AdminController\DesaAdatController@get_banjar')->name('admin-banjar-adat-get');
            //AKHIR BANJAR ADAT ROUTES

            //PENDUDUK ROUTES
            Route::get('/get-penduduk/{nik}', 'AdminController\PendudukController@get')->name('get-penduduk');
            //AKHIR PENDUDUK ROUTES

            Route::group(['middleware' => ['role:super_admin', 'verified']],function () {
                //PENDIDIKAN ROUTES
                Route::get('/jenjang-pendidikan', 'AdminController\PendidikanController@index')->name('admin-pendidikan-home');
                Route::post('/jenjang-pendidikan/store', 'AdminController\PendidikanController@store')->name('admin-pendidikan-store');
                Route::get('/jenjang-pendidikan/{id}', 'AdminController\PendidikanController@edit')->name('admin-pendidikan-edit');
                Route::post('/jenjang-pendidikan/{id}/update', 'AdminController\PendidikanController@update')->name('admin-pendidikan-update');
                Route::delete('/jenjang-pendidikan/{id}/delete', 'AdminController\PendidikanController@destroy')->name('admin-pendidikan-delete');
                //AKHIR PENDIDIKAN ROUTES

                //PEKERJAAN ROUTES
                Route::get('/pekerjaan', 'AdminController\PekerjaanController@index')->name('admin-pekerjaan-home');
                Route::post('/pekerjaan/store', 'AdminController\PekerjaanController@store')->name('admin-pekerjaan-store');
                Route::get('/pekerjaan/{id}', 'AdminController\PekerjaanController@edit')->name('admin-pekerjaan-edit');
                Route::post('/pekerjaan/{id}/update', 'AdminController\PekerjaanController@update')->name('admin-pekerjaan-update');
                Route::delete('/pekerjaan/{id}/delete', 'AdminController\PekerjaanController@destroy')->name('admin-pekerjaan-delete');
                //AKHIR PEKERJAAN ROUTES
            });
        });
        //AKHIR MASTER DATA ROUTE

        //MANAJEMEN AKUN ROUTE
        Route::group(['prefix' => 'akun'], function () {
            // Route::get('/super-admin', 'AdminController\SuperAdminController@index')->name('admin-super-admin-home');
            // Route::get('/super-admin/create', 'AdminController\SuperAdminController@create')->name('admin-super-admin-create');
            // Route::get('/super-admin/get-penduduk/{nik}', 'AdminController\SuperAdminController@get_penduduk')->name('admin-super-admin-get-penduduk');

            Route::get('/desa-adat', 'AdminController\AdminDesaAdatController@index')->name('admin-admin-desa-home');
            Route::post('/desa-adat/store', 'AdminController\AdminDesaAdatController@store')->name('admin-admin-desa-store');
            Route::get('/desa-adat/{id}/edit', 'AdminController\AdminDesaAdatController@edit')->name('admin-admin-desa-edit');
            Route::post('/desa-adat/{id}/update', 'AdminController\AdminDesaAdatController@update')->name('admin-admin-desa-update');
            Route::delete('/desa-adat/{id}/delete', 'AdminController\AdminDesaAdatController@destroy')->name('admin-admin-desa-delete');
            Route::get('/desa-adat/{id}/status/{status}', 'AdminController\AdminDesaAdatController@status')->name('admin-admin-desa-status');
            Route::get('/desa-adat/get-desa-adat/{kode}', 'AdminController\AdminDesaAdatController@get_desa_adat')->name('admin-admin-desa-get-desa-adat');
        });
        //AKHIR MANAJEMEN AKUN ROUTE

        //MANAJEMEN PELAPORAN ROUTES
        Route::group(['prefix' => 'laporan'], function () {
            Route::get('/', 'AdminController\PelaporanController@index')->name('admin-laporan-home');

            //LAPORAN KRAMA ROUTES
            Route::post('/laporan/krama', 'AdminController\PelaporanController@lapKrama')->name('admin-laporan-krama');
            Route::get('/laporan/krama', function () {
                return redirect()->route('admin-laporan-krama');
            });
            Route::post('/laporan/krama/download', 'Exports\ExportPdfController@lapKramaSuperAdmin')->name('admin-laporan-krama-download');
            //AKHIR LAPORAN KRAMA ROUTES

            //LAPORAN CACAH KRAMA ROUTES
            Route::post('/laporan/cacah-krama', 'AdminController\PelaporanController@lapCacahKrama')->name('admin-laporan-cacah-krama');
            Route::get('/laporan/cacah-krama', function () {
                return redirect()->route('admin-laporan-cacah-krama');
            });
            Route::post('/laporan/cacah-krama/download', 'Exports\ExportPdfController@lapCacahKramaSuperAdmin')->name('admin-laporan-cacah-krama-download');
            //AKHIR LAPORAN CACAH KRAMA ROUTES

            //LAPORAN MUTASI ROUTES
            Route::post('/laporan/mutasi', 'AdminController\PelaporanController@lapMutasi')->name('admin-laporan-mutasi');
            Route::get('/laporan/mutasi', function () {
                return redirect()->route('admin-laporan-mutasi');
            });
            Route::post('/laporan/mutasi/download', 'Exports\ExportPdfController@lapMutasiSuperAdmin')->name('admin-laporan-mutasi-download');
            //AKHIR LAPORAN MUTASI ROUTES
        });
        //AKHIR MANAJEMEN PELAPORAN ROUTES
    });
// AKHIR ADMIN ROUTES