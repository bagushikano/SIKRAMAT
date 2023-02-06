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

// DESA ROUTES
    Route::group(['middleware' => ['role:admin_desa_adat,bendesa,pangliman,penyarikan,patengen', 'verified'], 'prefix' => 'desa-adat'], function () {
        Route::get('/', 'DesaAdatController\DashboardController@index')->name('desa-dashboard');

        //PENDUDUK GET ROUTES
        Route::get('/get-penduduk/{nik}', 'DesaAdatController\PendudukController@get')->name('desa-get-penduduk');
        //AKHIR PENDUDUK ROUTES

        //MANAJEMEN BANJAR ROUTES
        Route::group(['prefix' => 'banjar'], function () {
            Route::get('/', 'DesaAdatController\BanjarController@index')->name('desa-banjar-home');
            //BANJAR ADAT ROUTES
                Route::post('/banjar-adat/store', 'DesaAdatController\BanjarController@store_banjar_adat')->name('desa-banjar-adat-store');
                Route::get('/banjar-adat/{id}/edit', 'DesaAdatController\BanjarController@edit_banjar_adat')->name('desa-banjar-adat-edit');
                Route::post('/banjar-adat/{id}/update', 'DesaAdatController\BanjarController@update_banjar_adat')->name('desa-banjar-adat-update');
                Route::delete('/banjar-adat/{id}/delete', 'DesaAdatController\BanjarController@delete_banjar_adat')->name('desa-banjar-adat-delete');
                Route::get('/get-kode-banjar-adat', 'DesaAdatController\BanjarController@get_kode_banjar_adat')->name('desa-banjar-get-kode-banjar-adat');
                Route::get('/banjar-adat/{id}', 'DesaAdatController\BanjarController@get_banjar_adat')->name('desa-banjar-adat-get');
            //AKHIR BANJAR ADAT ROUTES

            //BANJAR DINAS ROUTES
                Route::post('/banjar-dinas/store', 'DesaAdatController\BanjarController@store_banjar_dinas')->name('desa-banjar-dinas-store');
                Route::get('/banjar-dinas/{id}/edit', 'DesaAdatController\BanjarController@edit_banjar_dinas')->name('desa-banjar-dinas-edit');
                Route::post('/banjar-dinas/{id}/update', 'DesaAdatController\BanjarController@update_banjar_dinas')->name('desa-banjar-dinas-update');
                Route::delete('/banjar-dinas/{id}/delete', 'DesaAdatController\BanjarController@delete_banjar_dinas')->name('desa-banjar-dinas-delete');
                Route::get('/get-kode-banjar-dinas/{id}', 'DesaAdatController\BanjarController@get_kode_banjar_dinas')->name('desa-banjar-get-kode-banjar-dinas');
            //AKHIR BANJAR DINAS ROUTES
        });
        //END MANAJEMEN BANJAR ROUTES

        //MANAJEMEN AKUN ADMIN BANJAR ADAT ROUTES
        Route::group(['prefix' => 'akun-banjar-adat'], function () {
            Route::get('/', 'DesaAdatController\AdminBanjarAdatController@index')->name('desa-admin-banjar-home');
            Route::get('/create', 'DesaAdatController\AdminBanjarAdatController@create')->name('desa-admin-banjar-create');
            Route::post('/store', 'DesaAdatController\AdminBanjarAdatController@store')->name('desa-admin-banjar-store');
            Route::get('/edit/{id}', 'DesaAdatController\AdminBanjarAdatController@edit')->name('desa-admin-banjar-edit');
            Route::post('/update/{id}', 'DesaAdatController\AdminBanjarAdatController@update')->name('desa-admin-banjar-update');
            Route::delete('/delete/{id}', 'DesaAdatController\AdminBanjarAdatController@destroy')->name('desa-admin-banjar-delete');
            Route::get('/status/{id}/{status}', 'DesaAdatController\AdminBanjarAdatController@status')->name('desa-admin-banjar-status');
        });
        //AKHIR MANAJEMEN AKUN ADMIN BANJAR ADAT ROUTES

        //PRAJURU DESA ADAT ROUTES
        Route::group(['middleware' => ['role:admin_desa_adat'], 'prefix' => 'prajuru'], function () {
            Route::get('/datatable', 'DesaAdatController\PrajuruDesaAdatController@datatable')->name('desa-prajuru-datatable');
            Route::get('/datatable-krama-mipil', 'DesaAdatController\PrajuruDesaAdatController@datatable_krama_mipil')->name('desa-prajuru-datatable-krama-mipil');
            Route::get('/datatable-krama-mipil-edit', 'DesaAdatController\PrajuruDesaAdatController@datatable_krama_mipil_edit')->name('desa-prajuru-datatable-krama-mipil-edit');
            Route::get('/', 'DesaAdatController\PrajuruDesaAdatController@index')->name('desa-prajuru-home');
            Route::post('/store', 'DesaAdatController\PrajuruDesaAdatController@store')->name('desa-prajuru-store');
            Route::get('/edit/{id}', 'DesaAdatController\PrajuruDesaAdatController@edit')->name('desa-prajuru-edit');
            Route::post('/update/{id}', 'DesaAdatController\PrajuruDesaAdatController@update')->name('desa-prajuru-update');
            Route::get('/detail/{id}', 'DesaAdatController\PrajuruDesaAdatController@detail')->name('desa-prajuru-detail');
            Route::delete('/delete/{id}', 'DesaAdatController\PrajuruDesaAdatController@destroy')->name('desa-prajuru-delete');
        });
        //AKHIR PRAJURU DESA ADAT ROUTES

        //MANAJEMEN CACAH KRAMA ROUTES
        Route::group(['middleware' => ['role:no_role', 'permission:manajemen_krama'], 'prefix' => 'cacah-krama'], function () {
            //CACAH KRAMA MIPIL ROUTES
            Route::get('/cacah-krama-mipil', 'DesaAdatController\CacahKramaMipilController@index')->name('desa-cacah-krama-mipil-home');
            Route::get('/cacah-krama-mipil/get-penduduk/{nik}', 'DesaAdatController\CacahKramaMipilController@get_penduduk')->name('desa-cacah-krama-mipil-get-penduduk');
            Route::get('/cacah-krama-mipil/create', 'DesaAdatController\CacahKramaMipilController@create')->name('desa-cacah-krama-mipil-create');
            Route::post('/cacah-krama-mipil/store', 'DesaAdatController\CacahKramaMipilController@store')->name('desa-cacah-krama-mipil-store');
            Route::get('/cacah-krama-mipil/edit/{id}', 'DesaAdatController\CacahKramaMipilController@edit')->name('desa-cacah-krama-mipil-edit');
            Route::post('/cacah-krama-mipil/update/{id}', 'DesaAdatController\CacahKramaMipilController@update')->name('desa-cacah-krama-mipil-update');
            Route::delete('/cacah-krama-mipil/delete/{id}', 'DesaAdatController\CacahKramaMipilController@destroy')->name('desa-cacah-krama-mipil-delete');
            Route::get('/cacah-krama-mipil/detail/{id}', 'DesaAdatController\CacahKramaMipilController@detail')->name('desa-cacah-krama-mipil-detail');
            Route::get('/cacah-krama-mipil/orang-tua/search', 'DesaAdatController\CacahKramaMipilController@search_orang_tua')->name('api-cacah-krama-mipil-ortu-search');
            //AKHIR CACAH KRAMA MIPIL ROUTES

            //CACAH KRAMA TAMIU ROUTES
            Route::get('/cacah-krama-tamiu', 'DesaAdatController\CacahKramaTamiuController@index')->name('desa-cacah-krama-tamiu-home');
            Route::get('/cacah-krama-tamiu/get-penduduk/{nik}', 'DesaAdatController\CacahKramaTamiuController@get_penduduk')->name('desa-cacah-krama-tamiu-get-penduduk');
            Route::get('/cacah-krama-tamiu/create', 'DesaAdatController\CacahKramaTamiuController@create')->name('desa-cacah-krama-tamiu-create');
            Route::post('/cacah-krama-tamiu/store', 'DesaAdatController\CacahKramaTamiuController@store')->name('desa-cacah-krama-tamiu-store');
            Route::get('/cacah-krama-tamiu/edit/{id}', 'DesaAdatController\CacahKramaTamiuController@edit')->name('desa-cacah-krama-tamiu-edit');
            Route::post('/cacah-krama-tamiu/update/{nomor_krama_mipil}', 'DesaAdatController\CacahKramaTamiuController@update')->name('desa-cacah-krama-tamiu-update');
            Route::delete('/cacah-krama-tamiu/delete/{id}', 'DesaAdatController\CacahKramaTamiuController@destroy')->name('desa-cacah-krama-tamiu-delete');
            Route::get('/cacah-krama-tamiu/detail/{id}', 'DesaAdatController\CacahKramaTamiuController@detail')->name('desa-cacah-krama-tamiu-detail');
            Route::get('/cacah-krama-tamiu/orang-tua/search', 'DesaAdatController\CacahKramaTamiuController@search_orang_tua')->name('api-cacah-krama-tamiu-ortu-search');
            //AKHIR CACAH KRAMA TAMIU ROUTES

            //CACAH TAMIU ROUTES
            Route::get('/cacah-tamiu', 'DesaAdatController\CacahTamiuController@index')->name('desa-cacah-tamiu-home');
            Route::get('/cacah-tamiu/get-penduduk/{nik}', 'DesaAdatController\CacahTamiuController@get_penduduk')->name('desa-cacah-tamiu-get-penduduk');
            Route::get('/cacah-tamiu/wni/create', 'DesaAdatController\CacahTamiuController@create_tamiu_wni')->name('desa-cacah-tamiu-wni-create');
            Route::post('/cacah-tamiu/wni/store', 'DesaAdatController\CacahTamiuController@store_tamiu_wni')->name('desa-cacah-tamiu-wni-store');
            Route::get('/cacah-tamiu/wni/edit/{id}', 'DesaAdatController\CacahTamiuController@edit_tamiu_wni')->name('desa-cacah-tamiu-wni-edit');
            Route::post('/cacah-tamiu/wni/update/{nomor_krama_mipil}', 'DesaAdatController\CacahTamiuController@update_tamiu_wni')->name('desa-cacah-tamiu-wni-update');
            Route::delete('/cacah-tamiu/wni/delete/{id}', 'DesaAdatController\CacahTamiuController@delete_tamiu_wni')->name('desa-cacah-tamiu-wni-delete');
            Route::get('/cacah-tamiu/wni/detail/{id}', 'DesaAdatController\CacahTamiuController@detail_wni')->name('desa-cacah-tamiu-wni-detail');
            Route::get('/cacah-tamiu-wni/orang-tua/search', 'DesaAdatController\CacahTamiuController@search_orang_tua')->name('api-cacah-tamiu-wni-ortu-search');

            Route::get('/cacah-tamiu/wna/create', 'DesaAdatController\CacahTamiuController@create_tamiu_wna')->name('desa-cacah-tamiu-wna-create');
            Route::get('/cacah-tamiu/get-wna/{nomor_paspor}', 'DesaAdatController\CacahTamiuController@get_wna')->name('desa-cacah-tamiu-get-wna');
            Route::post('/cacah-tamiu/wna/store', 'DesaAdatController\CacahTamiuController@store_tamiu_wna')->name('desa-cacah-tamiu-wna-store');
            Route::get('/cacah-tamiu/wna/edit/{id}', 'DesaAdatController\CacahTamiuController@edit_tamiu_wna')->name('desa-cacah-tamiu-wna-edit');
            Route::post('/cacah-tamiu/wna/update/{id}', 'DesaAdatController\CacahTamiuController@update_tamiu_wna')->name('desa-cacah-tamiu-wna-update');
            Route::delete('/cacah-tamiu/wna/delete/{id}', 'DesaAdatController\CacahTamiuController@delete_tamiu_wna')->name('desa-cacah-tamiu-wna-delete');
            Route::get('/cacah-tamiu/wna/detail/{id}', 'DesaAdatController\CacahTamiuController@detail_wna')->name('desa-cacah-tamiu-wna-detail');
            //AKHIR CACAH TAMIU ROUTES
        });
        //AKHIR MANAJEMEN CACAH KRAMA ROUTES

        //MANAJEMEN KRAMA ROUTES
        Route::group(['middleware' => ['role:no_role', 'permission:manajemen_krama'], 'prefix' => 'krama'], function () {
            //MANAJEMEN KRAMA MIPIL ROUTES
                Route::get('/krama-mipil', 'DesaAdatController\KramaMipilController@index')->name('desa-krama-mipil-home');
                Route::get('/generate-nomor-krama-mipil/{banjar_adat_id}', 'DesaAdatController\KramaMipilController@generate_nomor_krama_mipil')->name('desa-krama-mipil-generate-nomor-krama-mipil');
                Route::get('/krama-mipil-search', 'DesaAdatController\KramaMipilController@search_krama')->name('desa-krama-mipil-search');
                Route::post('/krama-mipil/store', 'DesaAdatController\KramaMipilController@store')->name('desa-krama-mipil-store');
                Route::get('/krama-mipil/edit/{id}', 'DesaAdatController\KramaMipilController@edit')->name('desa-krama-mipil-edit');
                Route::post('/krama-mipil/update/{id}', 'DesaAdatController\KramaMipilController@update')->name('desa-krama-mipil-update');
                Route::delete('/krama-mipil/delete/{id}', 'DesaAdatController\KramaMipilController@delete')->name('desa-krama-mipil-delete');

                Route::get('/anggota-krama-mipil-search', 'DesaAdatController\AnggotaKramaMipilController@search_krama')->name('desa-anggota-krama-mipil-search');
                Route::post('/anggota-krama-mipil/store/{krama_mipil_id}', 'DesaAdatController\AnggotaKramaMipilController@store')->name('desa-anggota-krama-mipil-store');
                Route::get('/anggota-krama-mipil/edit/{id}', 'DesaAdatController\AnggotaKramaMipilController@edit')->name('desa-anggota-krama-mipil-edit');
                Route::post('/anggota-krama-mipil/update/{id}', 'DesaAdatController\AnggotaKramaMipilController@update')->name('desa-anggota-krama-mipil-update');
                Route::delete('/anggota-krama-mipil/delete/{id}', 'DesaAdatController\AnggotaKramaMipilController@destroy')->name('desa-anggota-krama-mipil-delete');
            //AKHIR MANAJEMEN KRAMA MIPIL ROUTES

            //MANAJEMEN KRAMA TAMIU ROUTES
                Route::get('/krama-tamiu', 'DesaAdatController\KramaTamiuController@index')->name('desa-krama-tamiu-home');
                Route::get('/generate-nomor-krama-tamiu/{banjar_adat_id}', 'DesaAdatController\KramaTamiuController@generate_nomor_krama_tamiu')->name('desa-krama-tamiu-generate-nomor-krama-tamiu');
                Route::get('/krama-tamiu-search', 'DesaAdatController\KramaTamiuController@search_krama')->name('desa-krama-tamiu-search');
                Route::post('/krama-tamiu/store', 'DesaAdatController\KramaTamiuController@store')->name('desa-krama-tamiu-store');
            //AKHIR MANAJEMEN KRAMA TAMIU ROUTES

            //MANAJEMEN TAMIU ROUTES
                Route::get('/tamiu', 'DesaAdatController\TamiuController@index')->name('desa-tamiu-home');
                Route::get('/generate-nomor-tamiu/{banjar_adat_id}', 'DesaAdatController\TamiuController@generate_nomor_tamiu')->name('desa-tamiu-generate-nomor-tamiu');
                Route::get('/tamiu-search', 'DesaAdatController\TamiuController@search_krama')->name('desa-tamiu-search');
                Route::post('/tamiu/store', 'DesaAdatController\TamiuController@store')->name('desa-tamiu-store');
            //AKHIR MANAJEMEN TAMIU ROUTES
        });
        //AKHIR MANAJEMEN KRAMA ROUTES

        //MANAJEMEN HAK AKSES PRAJURU ROUTES
            Route::group(['middleware' => ['role:no_role', 'permission:manajemen_prajuru'], 'prefix' => 'hak-akses-prajuru'], function () {
                Route::get('/', 'DesaAdatController\PrajuruPermissionController@index')->name('desa-prajuru-permission-home');
                Route::get('/edit/{id}', 'DesaAdatController\PrajuruPermissionController@edit')->name('desa-prajuru-permission-edit');
                Route::post('/update/{id}', 'DesaAdatController\PrajuruPermissionController@update')->name('desa-prajuru-permission-update');
            });
        //AKHIR MANAJEMEN HAK AKSES PRAJURU ROUTES

        //MANAJEMEN KELAHIRAN ROUTES
        Route::group(['middleware' => ['role:no_role'], 'prefix' => 'kelahiran'], function () {
            Route::get('/', 'DesaAdatController\KelahiranController@index')->name('desa-kelahiran-home');
            Route::get('/get-krama-mipil', 'DesaAdatController\KelahiranController@get_krama_mipil')->name('desa-kelahiran-get-krama-mipil');
            Route::get('/get-anggota-krama-mipil/{id}', 'DesaAdatController\KelahiranController@get_anggota_krama_mipil')->name('desa-kelahiran-get-anggota-krama-mipil');
            Route::get('/get-anggota-keluarga/{id}', 'DesaAdatController\KelahiranController@get_anggota_keluarga')->name('desa-kelahiran-get-anggota-keluarga');
            Route::get('/create', 'DesaAdatController\KelahiranController@create')->name('desa-kelahiran-create');
            Route::post('/store', 'DesaAdatController\KelahiranController@store')->name('desa-kelahiran-store');
            Route::get('/edit/{id}', 'DesaAdatController\KelahiranController@edit')->name('desa-kelahiran-edit');
            Route::post('/update/{id}', 'DesaAdatController\KelahiranController@update')->name('desa-kelahiran-update');
            Route::get('/detail/{id}', 'DesaAdatController\KelahiranController@detail')->name('desa-kelahiran-detail');
        });
        //AKHIR MANAJEMEN KELAHIRAN ROUTES

        //MANAJEMEN KEMATIAN ROUTES
        Route::group(['middleware' => ['role:no_role'], 'prefix' => 'kematian'], function () {
            Route::get('/', 'DesaAdatController\KematianController@index')->name('desa-kematian-home');
            Route::get('/get-cacah-krama-mipil', 'DesaAdatController\KematianController@get_cacah_krama_mipil')->name('desa-kematian-get-cacah-krama-mipil');
            Route::get('/get-cacah-krama-mipil-edit', 'DesaAdatController\KematianController@get_cacah_krama_mipil_edit')->name('desa-kematian-get-cacah-krama-mipil-edit');
            Route::get('/create', 'DesaAdatController\KematianController@create')->name('desa-kematian-create');
            Route::post('/store', 'DesaAdatController\KematianController@store')->name('desa-kematian-store');
            Route::get('/edit/{id}', 'DesaAdatController\KematianController@edit')->name('desa-kematian-edit');
            Route::post('/update/{id}', 'DesaAdatController\KematianController@update')->name('desa-kematian-update');
            Route::get('/detail/{id}', 'DesaAdatController\KematianController@detail')->name('desa-kematian-detail');
        });
        //AKHIR MANAJEMEN KEMATIAN ROUTES

        //MANAJEMEN PERKAWINAN ROUTES
        Route::group(['middleware' => ['role:no_role'], 'prefix' => 'perkawinan'], function () {
            //PERKAWINAN DALAM DESA ADAT ROUTES
                Route::get('/perkawinan-dalam-desa-adat', 'DesaAdatController\PerkawinanDalamDesaAdatController@index')->name('desa-perkawinan-dalam-desa-adat-home');
                Route::get('/perkawinan-dalam-desa-adat/create', 'DesaAdatController\PerkawinanDalamDesaAdatController@create')->name('desa-perkawinan-dalam-desa-adat-create');
                Route::post('/perkawinan-dalam-desa-adat/store', 'DesaAdatController\PerkawinanDalamDesaAdatController@store')->name('desa-perkawinan-dalam-desa-adat-store');

                Route::get('/perkawinan-dalam-desa-adat/get-purusa/{banjar_adat_id}', 'DesaAdatController\PerkawinanDalamDesaAdatController@get_purusa')->name('desa-perkawinan-dalam-desa-adat-get-purusa');
                Route::get('/perkawinan-dalam-desa-adat/get-pradana/{banjar_adat_id}', 'DesaAdatController\PerkawinanDalamDesaAdatController@get_pradana')->name('desa-perkawinan-dalam-desa-adat-get-pradana');
            //AKHIR PERKAWINAN DALAM DESA ADAT ROUTES

            //PERKAWINAN MASUK DESA ADAT ROUTES
                Route::get('/perkawinan-masuk-desa-adat', 'DesaAdatController\PerkawinanMasukDesaAdatController@index')->name('desa-perkawinan-masuk-desa-adat-home');
                Route::get('/perkawinan-masuk-desa-adat/create', 'DesaAdatController\PerkawinanMasukDesaAdatController@create')->name('desa-perkawinan-masuk-desa-adat-create');
                Route::post('/perkawinan-masuk-desa-adat/store', 'DesaAdatController\PerkawinanMasukDesaAdatController@store')->name('desa-perkawinan-masuk-desa-adat-store');
            //AKHIR PERKAWINAN MASUK DESA ADAT ROUTES

            //PERKAWINAN KELUAR DESA ADAT ROUTES
                Route::get('/perkawinan-keluar-desa-adat', 'DesaAdatController\PerkawinanKeluarDesaAdatController@index')->name('desa-perkawinan-keluar-desa-adat-home');
                Route::get('/perkawinan-keluar-desa-adat/detail/{id}', 'DesaAdatController\PerkawinanKeluarDesaAdatController@detail')->name('desa-perkawinan-keluar-desa-adat-detail');
            //AKHIR PERKAWINAN KELUAR DESA ADAT ROUTES
        });
        //AKHIR MANAJEMEN PERKAWINAN ROUTES

        //PROFILE ROUTES
            Route::get('/profile', 'DesaAdatController\ProfileKramaController@profile')->name('desa-profile-prajuru');
        //AKHIR PROFILE ROUTES

        # | Start Pelaporan Routes
        Route::prefix('pelaporan')->group( function(){  
            
            # Pelaporan Krama
            Route::prefix('krama')->group( function(){
                Route::get('/', 'DesaAdatController\PelaporanKramaController@index')->name('Pelaporan Krama Desa Adat');

                Route::post('/laporan/krama-mipil', 'DesaAdatController\PelaporanKramaController@lapKramaMipil')->name('Laporan Krama Mipil Desa Adat');
                Route::get('/laporan/krama-mipil', function () {
                    return redirect()->route('Pelaporan Krama Desa Adat');
                });

                Route::post('/laporan/krama-tamiu', 'DesaAdatController\PelaporanKramaController@lapKramaTamiu')->name('Laporan Krama Tamiu Desa Adat');
                Route::get('/laporan/krama-mipil', function () {
                    return redirect()->route('Pelaporan Krama Desa Adat');
                });

                Route::post('/laporan/tamiu', 'DesaAdatController\PelaporanKramaController@lapTamiu')->name('Laporan Tamiu Desa Adat');
                Route::get('/laporan/krama-mipil', function () {
                    return redirect()->route('Pelaporan Krama Desa Adat');
                });
            });

            # Pelaporan Cacah Krama
            Route::prefix('cacah-krama')->group( function(){
                Route::get('/', 'DesaAdatController\PelaporanCacahController@index')->name('Pelaporan Cacah Krama Desa Adat');

                Route::post('/laporan/cacah-krama-mipil', 'DesaAdatController\PelaporanCacahController@lapKramaMipil')->name('Laporan Cacah Krama Mipil Desa Adat');
                Route::get('/laporan/cacah-krama-mipil', function () {
                    return redirect()->route('Pelaporan Cacah Krama Desa Adat');
                });

                Route::post('/laporan/cacah-krama-tamiu', 'DesaAdatController\PelaporanCacahController@lapKramaTamiu')->name('Laporan Cacah Krama Tamiu Desa Adat');
                Route::get('/laporan/cacah-krama-tamiu', function () {
                    return redirect()->route('Pelaporan Cacah Krama Desa Adat');
                });

                Route::post('/laporan/cacah-tamiu', 'DesaAdatController\PelaporanCacahController@lapTamiu')->name('Laporan Cacah Tamiu Desa Adat');
                Route::get('/laporan/cacah-tamiu', function () {
                    return redirect()->route('Pelaporan Cacah Krama Desa Adat');
                });
            });

            # Download Laporan PDF
            Route::prefix('download/pdf')->group( function(){
                Route::post('/laporan/krama-mipil', 'Exports\ExportPdfController@lapKramaMipil')->name('Download PDF Laporan Krama Mipil Desa Adat');
                Route::post('/laporan/krama-tamiu', 'Exports\ExportPdfController@lapKramaTamiu')->name('Download PDF Laporan Krama Tamiu Desa Adat');
                Route::post('/laporan/tamiu', 'Exports\ExportPdfController@lapTamiu')->name('Download PDF Laporan Tamiu Desa Adat');
                
                Route::post('/laporan/cacah-krama-mipil', 'Exports\ExportPdfController@lapCacahKramaMipil')->name('Download PDF Laporan Cacah Krama Mipil Desa Adat');
                Route::post('/laporan/cacah-krama-tamiu', 'Exports\ExportPdfController@lapCacahKramaTamiu')->name('Download PDF Laporan Cacah Krama Tamiu Desa Adat');
                Route::post('/laporan/cacah-tamiu', 'Exports\ExportPdfController@lapCacahTamiu')->name('Download PDF Laporan Cacah Tamiu Desa Adat');
            });

            # Download Laporan Excel
            Route::prefix('download/excel')->group( function(){
                Route::post('/laporan/krama-mipil', 'Exports\ExportExcelController@lapKramaMipil')->name('Download Excel Laporan Krama Mipil Desa Adat');
                Route::post('/laporan/krama-tamiu', 'Exports\ExportExcelController@lapKramaTamiu')->name('Download Excel Laporan Krama Tamiu Desa Adat');
                Route::post('/laporan/tamiu', 'Exports\ExportExcelController@lapTamiu')->name('Download Excel Laporan Tamiu Desa Adat');

                Route::post('/laporan/cacah-krama-mipil', 'Exports\ExportExcelController@lapCacahKramaMipil')->name('Download Excel Laporan Cacah Krama Mipil Desa Adat');
                Route::post('/laporan/cacah-krama-tamiu', 'Exports\ExportExcelController@lapCacahKramaTamiu')->name('Download Excel Laporan Cacah Krama Tamiu Desa Adat');
                Route::post('/laporan/cacah-tamiu', 'Exports\ExportExcelController@lapCacahTamiu')->name('Download Excel Laporan Cacah Tamiu Desa Adat');
            });


            //PELAPORAN MUTASI ROUTES
            Route::group(['prefix' => 'mutasi'], function () {
                Route::get('/', 'DesaAdatController\PelaporanMutasiController@index')->name('Pelaporan Mutasi');

                //PELAPORAN KELAHIRAN ROUTES
                    Route::post('/laporan/kelahiran', 'DesaAdatController\PelaporanMutasiController@lapKelahiran')->name('Laporan Kelahiran Desa Adat');
                    Route::get('/laporan/kelahiran', function () {
                        return redirect()->route('Laporan Kelahiran Desa Adat');
                    });
                    Route::post('/laporan/kelahiran/download', 'Exports\ExportPdfController@lapKelahiranDesaAdat')->name('Download PDF Laporan Kelahiran Desa Adat');
                //AKHIR PELAPORAN KELAHIRAN ROUTES

                //PELAPORAN KEMATIAN ROUTES
                    Route::post('/laporan/kematian', 'DesaAdatController\PelaporanMutasiController@lapKematian')->name('Laporan Kematian Desa Adat');
                    Route::get('/laporan/kematian', function () {
                        return redirect()->route('Laporan Kematian Desa Adat');
                    });
                    Route::post('/laporan/kematian/download', 'Exports\ExportPdfController@lapKematianDesaAdat')->name('Download PDF Laporan Kematian Desa Adat');
                //AKHIR PELAPORAN KEMATIAN ROUTES

                //PELAPORAN PERKAWINAN ROUTES
                    Route::post('/laporan/perkawinan', 'DesaAdatController\PelaporanMutasiController@lapPerkawinan')->name('Laporan Perkawinan Desa Adat');
                    Route::get('/laporan/perkawinan', function () {
                        return redirect()->route('Laporan Perkawinan Desa Adat');
                    });
                    Route::post('/laporan/perkawinan/download', 'Exports\ExportPdfController@lapPerkawinanDesaAdat')->name('Download PDF Laporan Perkawinan Desa Adat');
                //AKHIR PELAPORAN PERKAWINAN ROUTES

                //PELAPORAN PERCERAIAN ROUTES
                    Route::post('/laporan/perceraian', 'DesaAdatController\PelaporanMutasiController@lapPerceraian')->name('Laporan Perceraian Desa Adat');
                    Route::get('/laporan/perceraian', function () {
                        return redirect()->route('Laporan Perceraian Desa Adat');
                    });
                    Route::post('/laporan/perceraian/download', 'Exports\ExportPdfController@lapPerceraianDesaAdat')->name('Download PDF Laporan Perceraian Desa Adat');
                //AKHIR PELAPORAN PERCERAIAN ROUTES

                //PELAPORAN MAPERAS ROUTES
                    Route::post('/laporan/maperas', 'DesaAdatController\PelaporanMutasiController@lapMaperas')->name('Laporan Maperas Desa Adat');
                    Route::get('/laporan/maperas', function () {
                        return redirect()->route('Laporan Maperas Desa Adat');
                    });
                    Route::post('/laporan/maperas/download', 'Exports\ExportPdfController@lapMaperasDesaAdat')->name('Download PDF Laporan Maperas Desa Adat');
                //AKHIR PELAPORAN MAPERAS ROUTES
            });
            //AKHIR PELAPORAN MUTASI ROUTES
        });
        # | End Pelaporan Routes
    });
// AKHIR DESA ROUTES