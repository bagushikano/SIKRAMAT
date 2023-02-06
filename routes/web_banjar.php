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

//BANJAR ROUTES
Route::group(['middleware' => ['role:admin_banjar_adat,kelihan_adat,pangliman_banjar,penyarikan_banjar,patengen_banjar', 'verified'], 'prefix' => 'banjar-adat'], function () {
    Route::get('/', 'BanjarAdatController\DashboardController@index')->name('banjar-dashboard');

    //MASTER DATA ROUTES
    Route::group(['prefix' => 'master'], function () {
        //TEMPEKAN ROUTES
        Route::group(['prefix' => 'tempekan'], function () {
            Route::get('/', 'BanjarAdatController\TempekanController@index')->name('banjar-tempekan-home');
            Route::get('/get-kode-tempekan', 'BanjarAdatController\TempekanController@get_kode_tempekan')->name('banjar-tempekan-get-kode-tempekan');
            Route::post('/store', 'BanjarAdatController\TempekanController@store')->name('banjar-tempekan-store');
            Route::get('/edit/{id}', 'BanjarAdatController\TempekanController@edit')->name('banjar-tempekan-edit');
            Route::post('/update/{id}', 'BanjarAdatController\TempekanController@update')->name('banjar-tempekan-update');
            Route::delete('/delete/{id}', 'BanjarAdatController\TempekanController@destroy')->name('banjar-tempekan-delete');
        });
        //AKHIR TEMPEKAN ROUTES
    });
    //AKHIR MASTER DATA ROUTES

    //CACAH KRAMA MIPIL ROUTES
    Route::group(['prefix' => 'cacah-krama-mipil'], function () {
        //GET KRAMA
        Route::get('/datatable-krama-mipil', 'BanjarAdatController\KelahiranController@datatable_krama_mipil')->name('banjar-cacah-krama-mipil-datatable-krama-mipil');
        Route::get('/get-anggota-keluarga/{id}', 'BanjarAdatController\CacahKramaMipilController@get_anggota_keluarga')->name('banjar-cacah-krama-mipil-get-anggota-keluarga');

        Route::get('/datatable', 'BanjarAdatController\CacahKramaMipilController@datatable')->name('banjar-cacah-krama-mipil-datatable');
        Route::get('/', 'BanjarAdatController\CacahKramaMipilController@index')->name('banjar-cacah-krama-mipil-home');
        Route::get('/get-penduduk/{nik}', 'BanjarAdatController\CacahKramaMipilController@get_penduduk')->name('banjar-cacah-krama-mipil-get-penduduk');
        Route::get('/create', 'BanjarAdatController\CacahKramaMipilController@create')->name('banjar-cacah-krama-mipil-create');
        Route::post('/store', 'BanjarAdatController\CacahKramaMipilController@store')->name('banjar-cacah-krama-mipil-store');
        Route::get('/edit/{id}', 'BanjarAdatController\CacahKramaMipilController@edit')->name('banjar-cacah-krama-mipil-edit');
        Route::post('/update/{id}', 'BanjarAdatController\CacahKramaMipilController@update')->name('banjar-cacah-krama-mipil-update');
        Route::post('/delete/{id}', 'BanjarAdatController\CacahKramaMipilController@destroy')->name('banjar-cacah-krama-mipil-delete');
        Route::get('/detail/{id}', 'BanjarAdatController\CacahKramaMipilController@detail')->name('banjar-cacah-krama-mipil-detail');
        Route::get('{id}/riwayat-perubahan', 'BanjarAdatController\CacahKramaMipilController@daftar_riwayat')->name('banjar-cacah-krama-mipil-daftar-riwayat');
        Route::get('{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\CacahKramaMipilController@detail_riwayat')->name('banjar-cacah-krama-mipil-detail-riwayat');
        Route::get('/orang-tua/search', 'BanjarAdatController\CacahKramaMipilController@search_orang_tua')->name('api-banjar-cacah-krama-mipil-ortu-search');
    });
    //AKHIR CACAH KRAMA MIPIL ROUTES

    //CACAH KRAMA TAMIU ROUTES
    Route::group(['prefix' => 'cacah-krama-tamiu'], function () {
        Route::get('/datatable', 'BanjarAdatController\CacahKramaTamiuController@datatable')->name('banjar-cacah-krama-tamiu-datatable');
        Route::get('/datatable-krama-tamiu', 'BanjarAdatController\CacahKramaTamiuController@datatable_krama_tamiu')->name('banjar-cacah-krama-tamiu-datatable-krama-tamiu');
        Route::get('/get-anggota-keluarga/{id}', 'BanjarAdatController\CacahKramaTamiuController@get_anggota_keluarga')->name('banjar-cacah-krama-tamiu-get-anggota-keluarga');

        Route::get('/', 'BanjarAdatController\CacahKramaTamiuController@index')->name('banjar-cacah-krama-tamiu-home');
        Route::get('/get-penduduk/{nik}', 'BanjarAdatController\CacahKramaTamiuController@get_penduduk')->name('banjar-cacah-krama-tamiu-get-penduduk');
        Route::get('/create', 'BanjarAdatController\CacahKramaTamiuController@create')->name('banjar-cacah-krama-tamiu-create');
        Route::post('/store', 'BanjarAdatController\CacahKramaTamiuController@store')->name('banjar-cacah-krama-tamiu-store');
        Route::get('/edit/{id}', 'BanjarAdatController\CacahKramaTamiuController@edit')->name('banjar-cacah-krama-tamiu-edit');
        Route::post('/update/{nomor_krama_mipil}', 'BanjarAdatController\CacahKramaTamiuController@update')->name('banjar-cacah-krama-tamiu-update');
        Route::post('/delete/{id}', 'BanjarAdatController\CacahKramaTamiuController@destroy')->name('banjar-cacah-krama-tamiu-delete');
        Route::get('/detail/{id}', 'BanjarAdatController\CacahKramaTamiuController@detail')->name('banjar-cacah-krama-tamiu-detail');
        Route::get('{id}/riwayat-perubahan', 'BanjarAdatController\CacahKramaTamiuController@daftar_riwayat')->name('banjar-cacah-krama-tamiu-daftar-riwayat');
        Route::get('{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\CacahKramaTamiuController@detail_riwayat')->name('banjar-cacah-krama-tamiu-detail-riwayat');
        Route::get('/orang-tua/search', 'BanjarAdatController\CacahKramaTamiuController@search_orang_tua')->name('api-banjar-cacah-krama-tamiu-ortu-search');
    });
    //AKHIR CACAH KRAMA TAMIU ROUTES

    //CACAH TAMIU ROUTES
    Route::group([ 'prefix' => 'cacah-tamiu'], function () {
        Route::get('/', 'BanjarAdatController\CacahTamiuController@index')->name('banjar-cacah-tamiu-home');
        Route::get('/wni/datatable', 'BanjarAdatController\CacahTamiuController@datatable_wni')->name('banjar-cacah-tamiu-wni-datatable');
        Route::get('/datatable-tamiu', 'BanjarAdatController\CacahTamiuController@datatable_tamiu')->name('banjar-cacah-tamiu-datatable-tamiu');
        Route::get('/get-anggota-keluarga/{id}', 'BanjarAdatController\CacahTamiuController@get_anggota_keluarga')->name('banjar-cacah-tamiu-get-anggota-keluarga');

        Route::get('/get-penduduk/{nik}', 'BanjarAdatController\CacahTamiuController@get_penduduk')->name('banjar-cacah-tamiu-get-penduduk');
        Route::get('/wni/create', 'BanjarAdatController\CacahTamiuController@create_tamiu_wni')->name('banjar-cacah-tamiu-wni-create');
        Route::post('/wni/store', 'BanjarAdatController\CacahTamiuController@store_tamiu_wni')->name('banjar-cacah-tamiu-wni-store');
        Route::get('/wni/edit/{id}', 'BanjarAdatController\CacahTamiuController@edit_tamiu_wni')->name('banjar-cacah-tamiu-wni-edit');
        Route::post('/wni/update/{nomor_krama_mipil}', 'BanjarAdatController\CacahTamiuController@update_tamiu_wni')->name('banjar-cacah-tamiu-wni-update');
        Route::post('/wni/delete/{id}', 'BanjarAdatController\CacahTamiuController@delete_tamiu_wni')->name('banjar-cacah-tamiu-wni-delete');
        Route::get('/wni/detail/{id}', 'BanjarAdatController\CacahTamiuController@detail_wni')->name('banjar-cacah-tamiu-wni-detail');
        Route::get('/wni/orang-tua/search', 'BanjarAdatController\CacahTamiuController@search_orang_tua')->name('api-banjar-cacah-tamiu-wni-ortu-search');
        Route::get('/wni/{id}/riwayat-perubahan', 'BanjarAdatController\CacahTamiuController@daftar_riwayat_wni')->name('banjar-cacah-tamiu-wni-daftar-riwayat');
        Route::get('/wni/{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\CacahTamiuController@detail_riwayat_wni')->name('banjar-cacah-tamiu-wni-detail-riwayat');

        Route::get('/wna/datatable', 'BanjarAdatController\CacahTamiuController@datatable_wna')->name('banjar-cacah-tamiu-wna-datatable');
        Route::get('/wna/create', 'BanjarAdatController\CacahTamiuController@create_tamiu_wna')->name('banjar-cacah-tamiu-wna-create');
        Route::get('/get-wna/{nomor_paspor}', 'BanjarAdatController\CacahTamiuController@get_wna')->name('banjar-cacah-tamiu-get-wna');
        Route::post('/wna/store', 'BanjarAdatController\CacahTamiuController@store_tamiu_wna')->name('banjar-cacah-tamiu-wna-store');
        Route::get('/wna/edit/{id}', 'BanjarAdatController\CacahTamiuController@edit_tamiu_wna')->name('banjar-cacah-tamiu-wna-edit');
        Route::post('/wna/update/{id}', 'BanjarAdatController\CacahTamiuController@update_tamiu_wna')->name('banjar-cacah-tamiu-wna-update');
        Route::post('/wna/delete/{id}', 'BanjarAdatController\CacahTamiuController@delete_tamiu_wna')->name('banjar-cacah-tamiu-wna-delete');
        Route::get('/wna/detail/{id}', 'BanjarAdatController\CacahTamiuController@detail_wna')->name('banjar-cacah-tamiu-wna-detail');
    });
    //AKHIR CACAH TAMIU ROUTES

    //MANAJEMEN KRAMA ROUTES
    Route::group(['prefix' => 'krama'], function () {
        //MANAJEMEN KRAMA MIPIL ROUTES
        Route::group(['prefix' => 'krama-mipil'], function () {
            Route::get('/datatable', 'BanjarAdatController\KramaMipilController@datatable')->name('banjar-krama-mipil-datatable');
            Route::get('/datatable-cacah-krama-mipil', 'BanjarAdatController\KramaMipilController@datatable_cacah_krama_mipil')->name('banjar-krama-mipil-datatable-cacah-krama-mipil');
            Route::get('/', 'BanjarAdatController\KramaMipilController@index')->name('banjar-krama-mipil-home');
            Route::get('/get-penduduk/{nik}', 'BanjarAdatController\KramaMipilController@get_penduduk')->name('banjar-krama-mipil-get-penduduk');

            Route::get('/create', 'BanjarAdatController\KramaMipilController@create')->name('banjar-krama-mipil-create');
            Route::post('/store', 'BanjarAdatController\KramaMipilController@store')->name('banjar-krama-mipil-store');
            Route::get('/{id}', 'BanjarAdatController\KramaMipilController@anggota')->name('banjar-krama-mipil-detail');
            Route::get('/edit/{id}', 'BanjarAdatController\KramaMipilController@edit')->name('banjar-krama-mipil-edit');
            Route::get('/detail/{id}', 'BanjarAdatController\KramaMipilController@detail')->name('banjar-krama-mipil-detail-krama-mipil');
            Route::post('/update/{id}', 'BanjarAdatController\KramaMipilController@update')->name('banjar-krama-mipil-update');
            Route::post('/ganti/{id}', 'BanjarAdatController\KramaMipilController@ganti')->name('banjar-krama-mipil-ganti');
            Route::post('/delete/{id}', 'BanjarAdatController\KramaMipilController@delete')->name('banjar-krama-mipil-delete');
            Route::get('/{id}/riwayat-keluarga', 'BanjarAdatController\KramaMipilController@riwayat_keluarga')->name('banjar-krama-mipil-riwayat-keluarga');
            Route::get('/{id}/riwayat-keluarga/{id_riwayat}', 'BanjarAdatController\KramaMipilController@detail_riwayat_keluarga')->name('banjar-krama-mipil-detail-riwayat-keluarga');
            Route::get('{id}/riwayat-perubahan', 'BanjarAdatController\KramaMipilController@daftar_riwayat')->name('banjar-krama-mipil-daftar-riwayat');
            Route::get('{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\KramaMipilController@detail_riwayat')->name('banjar-krama-mipil-detail-riwayat');

            Route::get('/anggota-krama-mipil/create/{id}', 'BanjarAdatController\AnggotaKramaMipilController@create')->name('banjar-anggota-krama-mipil-create');
            Route::post('/anggota-krama-mipil/store/{krama_mipil_id}', 'BanjarAdatController\AnggotaKramaMipilController@store')->name('banjar-anggota-krama-mipil-store');
            Route::get('/anggota-krama-mipil/edit/{id}', 'BanjarAdatController\AnggotaKramaMipilController@edit')->name('banjar-anggota-krama-mipil-edit');
            Route::post('/anggota-krama-mipil/update/{id}', 'BanjarAdatController\AnggotaKramaMipilController@update')->name('banjar-anggota-krama-mipil-update');
            Route::post('/anggota-krama-mipil/delete/{id}', 'BanjarAdatController\AnggotaKramaMipilController@destroy')->name('banjar-anggota-krama-mipil-delete');

            Route::get('/kartu-keluarga/{id}', 'BanjarAdatController\KramaMipilController@kartu_keluarga')->name('banjar-krama-mipil-kartu-keluarga');
        });
        //AKHIR MANAJEMEN KRAMA MIPIL ROUTES

        //MANAJEMEN KRAMA TAMIU ROUTES
        Route::group(['prefix' => 'krama-tamiu'], function () {
            Route::get('/datatable', 'BanjarAdatController\KramaTamiuController@datatable')->name('banjar-krama-tamiu-datatable');
            Route::get('/', 'BanjarAdatController\KramaTamiuController@index')->name('banjar-krama-tamiu-home');
            Route::get('/generate-nomor-krama-tamiu/{tanggal_registrasi}', 'BanjarAdatController\KramaTamiuController@generate_nomor_krama_tamiu')->name('banjar-krama-tamiu-generate-nomor-krama-tamiu');
            Route::get('/get-penduduk/{nik}', 'BanjarAdatController\KramaTamiuController@get_penduduk')->name('banjar-krama-tamiu-get-penduduk');
            Route::get('/create', 'BanjarAdatController\KramaTamiuController@create')->name('banjar-krama-tamiu-create');
            Route::post('/store', 'BanjarAdatController\KramaTamiuController@store')->name('banjar-krama-tamiu-store');
            Route::get('/{id}', 'BanjarAdatController\KramaTamiuController@anggota')->name('banjar-krama-tamiu-detail');
            Route::get('/edit/{id}', 'BanjarAdatController\KramaTamiuController@edit')->name('banjar-krama-tamiu-edit');
            Route::post('/update/{id}', 'BanjarAdatController\KramaTamiuController@update')->name('banjar-krama-tamiu-update');
            Route::get('/detail/{id}', 'BanjarAdatController\KramaTamiuController@detail')->name('banjar-krama-tamiu-detail-krama-tamiu');
            Route::post('/delete/{id}', 'BanjarAdatController\KramaTamiuController@delete')->name('banjar-krama-tamiu-delete');
            Route::get('{id}/riwayat-perubahan', 'BanjarAdatController\KramaTamiuController@daftar_riwayat')->name('banjar-krama-tamiu-daftar-riwayat');
            Route::get('{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\KramaTamiuController@detail_riwayat')->name('banjar-krama-tamiu-detail-riwayat');
            Route::post('/ganti/{id}', 'BanjarAdatController\KramaTamiuController@ganti')->name('banjar-krama-tamiu-ganti');

            Route::get('/anggota-krama-tamiu/create/{id}', 'BanjarAdatController\AnggotaKramaTamiuController@create')->name('banjar-anggota-krama-tamiu-create');
            Route::post('/anggota-krama-tamiu/store/{krama_tamiu_id}', 'BanjarAdatController\AnggotaKramaTamiuController@store')->name('banjar-anggota-krama-tamiu-store');
            Route::get('/anggota-krama-tamiu/edit/{id}', 'BanjarAdatController\AnggotaKramaTamiuController@edit')->name('banjar-anggota-krama-tamiu-edit');
            Route::post('/anggota-krama-tamiu/update/{id}', 'BanjarAdatController\AnggotaKramaTamiuController@update')->name('banjar-anggota-krama-tamiu-update');
            Route::post('/anggota-krama-tamiu/delete/{id}', 'BanjarAdatController\AnggotaKramaTamiuController@destroy')->name('banjar-anggota-krama-tamiu-delete');
        });
        //AKHIR MANAJEMEN KRAMA TAMIU ROUTES

        //MANAJEMEN TAMIU ROUTES
        Route::group(['prefix' => 'tamiu'], function () {
            Route::get('/datatable', 'BanjarAdatController\TamiuController@datatable')->name('banjar-tamiu-datatable');
            Route::get('/get-penduduk/{nik}', 'BanjarAdatController\TamiuController@get_penduduk')->name('banjar-tamiu-get-penduduk');
            Route::get('/', 'BanjarAdatController\TamiuController@index')->name('banjar-tamiu-home');
            Route::get('/create', 'BanjarAdatController\TamiuController@create')->name('banjar-tamiu-create');
            Route::post('/store', 'BanjarAdatController\TamiuController@store')->name('banjar-tamiu-store');
            Route::get('/{id}', 'BanjarAdatController\TamiuController@anggota')->name('banjar-tamiu-detail');
            Route::get('/edit/{id}', 'BanjarAdatController\TamiuController@edit')->name('banjar-tamiu-edit');
            Route::post('/update/{id}', 'BanjarAdatController\TamiuController@update')->name('banjar-tamiu-update');
            Route::get('/detail/{id}', 'BanjarAdatController\TamiuController@detail')->name('banjar-tamiu-detail-tamiu');
            Route::post('/delete/{id}', 'BanjarAdatController\TamiuController@delete')->name('banjar-tamiu-delete');
            Route::get('{id}/riwayat-perubahan', 'BanjarAdatController\TamiuController@daftar_riwayat')->name('banjar-tamiu-daftar-riwayat');
            Route::get('{id}/detail-perubahan/{id_riwayat}', 'BanjarAdatController\TamiuController@detail_riwayat')->name('banjar-tamiu-detail-riwayat');
            Route::post('/ganti/{id}', 'BanjarAdatController\TamiuCOntroller@ganti')->name('banjar-tamiu-ganti');

            Route::get('/anggota-tamiu/create/{id}', 'BanjarAdatController\AnggotaTamiuController@create')->name('banjar-anggota-tamiu-create');
            Route::post('/anggota-tamiu/store/{krama_tamiu_id}', 'BanjarAdatController\AnggotaTamiuController@store')->name('banjar-anggota-tamiu-store');
            Route::get('/anggota-tamiu/edit/{id}', 'BanjarAdatController\AnggotaTamiuController@edit')->name('banjar-anggota-tamiu-edit');
            Route::post('/anggota-tamiu/update/{id}', 'BanjarAdatController\AnggotaTamiuController@update')->name('banjar-anggota-tamiu-update');
            Route::post('/anggota-tamiu/delete/{id}', 'BanjarAdatController\AnggotaTamiuController@destroy')->name('banjar-anggota-tamiu-delete');
        });
        //AKHIR MANAJEMEN TAMIU ROUTES
    });
    //AKHIR MANAJEMEN KRAMA ROUTES

    //MANAJEMEN KELAHIRAN ROUTES
    Route::group(['prefix' => 'kelahiran'], function () {
        Route::get('/datatable', 'BanjarAdatController\KelahiranController@datatable')->name('banjar-kelahiran-datatable');
        Route::get('/datatable-krama-mipil', 'BanjarAdatController\KelahiranController@datatable_krama_mipil')->name('banjar-kelahiran-datatable-krama-mipil');
        Route::get('/', 'BanjarAdatController\KelahiranController@index')->name('banjar-kelahiran-home');
        Route::get('/get-anggota-keluarga/{id}', 'BanjarAdatController\KelahiranController@get_anggota_keluarga')->name('banjar-kelahiran-get-anggota-keluarga');
        Route::get('/create', 'BanjarAdatController\KelahiranController@create')->name('banjar-kelahiran-create');
        Route::post('/store/{status}', 'BanjarAdatController\KelahiranController@store')->name('banjar-kelahiran-store');
        Route::get('/edit/{id}', 'BanjarAdatController\KelahiranController@edit')->name('banjar-kelahiran-edit');
        Route::post('/update/{id}/{status}', 'BanjarAdatController\KelahiranController@update')->name('banjar-kelahiran-update');
        Route::get('/detail/{id}', 'BanjarAdatController\KelahiranController@detail')->name('banjar-kelahiran-detail');
        Route::delete('/delete/{id}', 'BanjarAdatController\KelahiranController@destroy')->name('banjar-kelahiran-delete');
    });
    //AKHIR MANAJEMEN KELAHIRAN

    //MANAJEMEN KEMATIAN ROUTES
    Route::group(['prefix' => 'kematian'], function () {
        Route::get('/datatable', 'BanjarAdatController\KematianController@datatable')->name('banjar-kematian-datatable');
        Route::get('/datatable-cacah-krama-mipil', 'BanjarAdatController\KematianController@datatable_cacah_krama_mipil')->name('banjar-kematian-datatable-cacah-krama-mipil');
        Route::get('/', 'BanjarAdatController\KematianController@index')->name('banjar-kematian-home');
        Route::get('/create', 'BanjarAdatController\KematianController@create')->name('banjar-kematian-create');
        Route::post('/store/{status}', 'BanjarAdatController\KematianController@store')->name('banjar-kematian-store');
        Route::get('/edit/{id}', 'BanjarAdatController\KematianController@edit')->name('banjar-kematian-edit');
        Route::post('/update/{id}/{status}', 'BanjarAdatController\KematianController@update')->name('banjar-kematian-update');
        Route::get('/detail/{id}', 'BanjarAdatController\KematianController@detail')->name('banjar-kematian-detail');
        Route::delete('/delete/{id}', 'BanjarAdatController\KematianController@destroy')->name('banjar-kematian-delete');
    });
    //AKHIR MANAJEMEN KEMATIAN ROUTES

    //MANAJEMEN PERKAWINAN ROUTES
    Route::group(['prefix' => 'perkawinan'], function () {
        Route::get('/datatable', 'BanjarAdatController\PerkawinanController@datatable')->name('banjar-perkawinan-datatable');
        Route::get('/datatable-purusa', 'BanjarAdatController\PerkawinanController@datatable_purusa')->name('banjar-perkawinan-datatable-purusa');
        Route::get('/datatable-pradana', 'BanjarAdatController\PerkawinanController@datatable_pradana')->name('banjar-perkawinan-datatable-pradana');
        Route::get('/get-calon-kepala-keluarga/{purusa_id}/{pradana_id}', 'BanjarAdatController\PerkawinanController@get_calon_kepala_keluarga')->name('banjar-perkawinan-get-calon-kk');
        Route::get('/', 'BanjarAdatController\PerkawinanController@index')->name('banjar-perkawinan-home');
        Route::get('/{jenis_perkawinan}/create', 'BanjarAdatController\PerkawinanController@create')->name('banjar-perkawinan-create');
        Route::get('/edit/{id}', 'BanjarAdatController\PerkawinanController@edit')->name('banjar-perkawinan-edit');
        Route::delete('delete/{id}', 'BanjarAdatController\PerkawinanController@destroy')->name('banjar-perkawinan-delete');
        Route::get('/detail/{id}', 'BanjarAdatController\PerkawinanController@detail')->name('banjar-perkawinan-detail');

        //PERKAWINAN SATU BANJAR ADAT ROUTES
            Route::post('/satu-banjar-adat/store/{status}', 'BanjarAdatController\PerkawinanController@store_satu_banjar_adat')->name('banjar-perkawinan-satu-banjar-adat-store');
            Route::post('/satu-banjar-adat/update/{id}/{status}', 'BanjarAdatController\PerkawinanController@update_satu_banjar_adat')->name('banjar-perkawinan-satu-banjar-adat-update');
        //AKHIR PERKAWINAN SATU BANJAR ADAT ROUTES

        //PERKAWINAN BEDA BANJAR ADAT ROUTES
            Route::post('/beda-banjar-adat/store/{status}', 'BanjarAdatController\PerkawinanController@store_beda_banjar_adat')->name('banjar-perkawinan-beda-banjar-adat-store');
            Route::post('/beda-banjar-adat/update/{id}/{status}', 'BanjarAdatController\PerkawinanController@update_beda_banjar_adat')->name('banjar-perkawinan-beda-banjar-adat-update');
            Route::get('/perkawinan-keluar/detail/{id}', 'BanjarAdatController\PerkawinanController@edit_perkawinan_keluar')->name('banjar-perkawinan-keluar-detail');
            Route::post('/perkawinan-keluar/tolak/{id}', 'BanjarAdatController\PerkawinanController@tolak_perkawinan_keluar')->name('banjar-perkawinan-keluar-tolak');
            Route::get('/perkawinan-keluar/konfirmasi/{id}', 'BanjarAdatController\PerkawinanController@konfirmasi_perkawinan_keluar')->name('banjar-perkawinan-keluar-konfirmasi');

            Route::get('/perkawinan-masuk/detail/{id}', 'BanjarAdatController\PerkawinanController@edit_perkawinan_masuk')->name('banjar-perkawinan-masuk-detail');
            Route::get('/perkawinan-masuk/konfirmasi/{id}', 'BanjarAdatController\PerkawinanController@konfirmasi_perkawinan_masuk')->name('banjar-perkawinan-masuk-konfirmasi');
        //AKHIR PERKAWINAN BEDA BANJAR ADAT ROUTES

        //PERKAWINAN CAMPURAN MASUK ROUTES
        Route::post('/campuran-masuk/store/{status}', 'BanjarAdatController\PerkawinanController@store_campuran_masuk')->name('banjar-perkawinan-campuran-masuk-store');
        Route::post('/campuran-masuk/update/{id}/{status}', 'BanjarAdatController\PerkawinanController@update_campuran_masuk')->name('banjar-perkawinan-campuran-masuk-update');
        //AKHIR PERKAWINAN CAMPURAN MASUK ROUTES

        //PERKAWINAN CAMPURAN KELUAR ROUTES
        Route::post('/campuran-keluar/store/{status}', 'BanjarAdatController\PerkawinanController@store_campuran_keluar')->name('banjar-perkawinan-campuran-keluar-store');
        Route::post('/campuran-keluar/update/{id}/{status}', 'BanjarAdatController\PerkawinanController@update_campuran_keluar')->name('banjar-perkawinan-campuran-keluar-update');
        //AKHIR PERKAWINAN CAMPURAN KELUAR ROUTES
    });
    //AKHIR MANAJEMEN PERKAWINAN ROUTES

    //MANAJEMEN PERCERAIAN ROUTES
    Route::group(['prefix' => 'perceraian'], function () {
        //INDEX
        Route::get('/datatable', 'BanjarAdatController\PerceraianController@datatable')->name('banjar-perceraian-datatable');

        //KRAMA MIPIL YANG AKAN DI CERAI
        Route::get('/datatable-krama-mipil', 'BanjarAdatController\PerceraianController@datatable_krama_mipil')->name('banjar-perceraian-datatable-krama-mipil');
        Route::get('/pilih-krama-mipil/{id}', 'BanjarAdatController\PerceraianController@pilih_krama_mipil')->name('banjar-perceraian-pilih-krama-mipil');
        
        //KRAMA MIPIL BARU
        Route::get('/datatable-krama-mipil-baru-krama-mipil', 'BanjarAdatController\PerceraianController@datatable_krama_mipil_baru_krama_mipil')->name('banjar-perceraian-datatable-krama-mipil-baru-krama-mipil');
        Route::get('/datatable-krama-mipil-baru-pasangan', 'BanjarAdatController\PerceraianController@datatable_krama_mipil_baru_pasangan')->name('banjar-perceraian-datatable-krama-mipil-baru-pasangan');

        //CRUD
        Route::get('/', 'BanjarAdatController\PerceraianController@index')->name('banjar-perceraian-home');
        Route::get('/create', 'BanjarAdatController\PerceraianController@create')->name('banjar-perceraian-create');
        Route::post('/store/{status}', 'BanjarAdatController\PerceraianController@store')->name('banjar-perceraian-store');
        Route::get('/edit/{id}', 'BanjarAdatController\PerceraianController@edit')->name('banjar-perceraian-edit');
        Route::post('/update/{id}/{status}', 'BanjarAdatController\PerceraianController@update')->name('banjar-perceraian-update');
        Route::delete('delete/{id}', 'BanjarAdatController\PerceraianController@destroy')->name('banjar-perceraian-delete');
        Route::get('/detail/{id}', 'BanjarAdatController\PerceraianController@detail')->name('banjar-perceraian-detail');

        //TAMBAHAN KETIKA BEDA BANJAR
        Route::post('/tolak/{id}', 'BanjarAdatController\PerceraianController@tolak_perceraian')->name('banjar-perceraian-tolak');
        Route::get('/konfirmasi/{id}', 'BanjarAdatController\PerceraianController@konfirmasi_perceraian')->name('banjar-perceraian-konfirmasi');
    });
    //AKHIR MANAJEMEN PERCERAIAN ROUTES

    //MANAJEMEN MAPERAS ROUTES
    Route::group(['prefix' => 'maperas'], function () {
        Route::get('/datatable', 'BanjarAdatController\MaperasController@datatable')->name('banjar-maperas-datatable');
        Route::get('/datatable-krama-mipil-lama', 'BanjarAdatController\MaperasController@datatable_krama_mipil_lama')->name('banjar-maperas-datatable-krama-mipil-lama');
        Route::get('/datatable-krama-mipil-baru', 'BanjarAdatController\MaperasController@datatable_krama_mipil_baru')->name('banjar-maperas-datatable-krama-mipil-baru');
        Route::get('/', 'BanjarAdatController\MaperasController@index')->name('banjar-maperas-home');
        Route::get('/{jenis_maperas}/create', 'BanjarAdatController\MaperasController@create')->name('banjar-maperas-create');
        Route::get('/edit/{id}', 'BanjarAdatController\MaperasController@edit')->name('banjar-maperas-edit');
        Route::delete('delete/{id}', 'BanjarAdatController\MaperasController@destroy')->name('banjar-maperas-delete');
        Route::get('/detail/{id}', 'BanjarAdatController\MaperasController@detail')->name('banjar-maperas-detail');

        Route::get('/get-daftar-anak/{id}', 'BanjarAdatController\MaperasController@get_daftar_anak')->name('banjar-maperas-get-daftar-anak');
        Route::get('/get-orangtua-lama-anak/{id}', 'BanjarAdatController\MaperasController@get_orangtua_lama_anak')->name('banjar-maperas-get-orangtua-lama-anak');
        Route::get('/get-orangtua-baru-anak/{id}', 'BanjarAdatController\MaperasController@get_orangtua_baru_anak')->name('banjar-maperas-get-orangtua-baru-anak');

        //MAPERAS SATU BANJAR ADAT ROUTES
            Route::post('/satu-banjar-adat/store/{status}', 'BanjarAdatController\MaperasController@store_satu_banjar_adat')->name('banjar-maperas-satu-banjar-adat-store');
            Route::post('/satu-banjar-adat/update/{id}/{status}', 'BanjarAdatController\MaperasController@update_satu_banjar_adat')->name('banjar-maperas-satu-banjar-adat-update');
        //AKHIR MAPERAS SATU BANJAR ADAT ROUTES

        //MAPERAS BEDA BANJAR ADAT ROUTES
            Route::post('/beda-banjar-adat/store/{status}', 'BanjarAdatController\MaperasController@store_beda_banjar_adat')->name('banjar-maperas-beda-banjar-adat-store');
            Route::post('/beda-banjar-adat/update/{id}/{status}', 'BanjarAdatController\MaperasController@update_beda_banjar_adat')->name('banjar-maperas-beda-banjar-adat-update');

            Route::get('/maperas-keluar/detail/{id}', 'BanjarAdatController\MaperasController@detail_keluar_banjar')->name('banjar-maperas-keluar-detail');
            Route::post('/maperas-keluar/tolak/{id}', 'BanjarAdatController\MaperasController@tolak_keluar_banjar')->name('banjar-maperas-keluar-tolak');
            Route::get('/maperas-keluar/konfirmasi/{id}', 'BanjarAdatController\MaperasController@konfirmasi_keluar_banjar')->name('banjar-maperas-keluar-konfirmasi');

            Route::get('/maperas-masuk/detail/{id}', 'BanjarAdatController\MaperasController@detail_masuk_banjar')->name('banjar-maperas-masuk-detail');
            Route::get('/maperas-masuk/konfirmasi/{id}', 'BanjarAdatController\MaperasController@konfirmasi_masuk_banjar')->name('banjar-maperas-masuk-konfirmasi');
        //AKHIR MAPERAS BEDA BANJAR ADAT ROUTES

        //MAPERAS CAMPURAN MASUK ROUTES
            Route::post('/campuran-masuk/store/{status}', 'BanjarAdatController\MaperasController@store_campuran_masuk')->name('banjar-maperas-campuran-masuk-store');
            Route::post('/campuran-masuk/update/{id}/{status}', 'BanjarAdatController\MaperasController@update_campuran_masuk')->name('banjar-maperas-campuran-masuk-update');
        //AKHIR MAPERAS CAMPURAN MASUK ROUTES

        //MAPERAS CAMPURAN KELUAR ROUTES
            Route::post('/campuran-keluar/store/{status}', 'BanjarAdatController\MaperasController@store_campuran_keluar')->name('banjar-maperas-campuran-keluar-store');
            Route::post('/campuran-keluar/update/{id}/{status}', 'BanjarAdatController\MaperasController@update_campuran_keluar')->name('banjar-maperas-campuran-keluar-update');
        //AKHIR MAPERAS CAMPURAN KELUAR ROUTES

    });
    //AKHIR MANAJEMEN MAPERAS ROUTES

    //MANAJEMEN AJUAN KELAHIRAN ROUTES
    Route::group(['prefix' => 'ajuan-kelahiran'], function () {
        Route::get('/datatable', 'BanjarAdatController\AjuanKelahiranController@datatable')->name('banjar-ajuan-kelahiran-datatable');
        Route::get('/', 'BanjarAdatController\AjuanKelahiranController@index')->name('banjar-ajuan-kelahiran-home');
        Route::get('/detail/{id}', 'BanjarAdatController\AjuanKelahiranController@detail')->name('banjar-ajuan-kelahiran-detail');
        Route::get('/proses/{id}', 'BanjarAdatController\AjuanKelahiranController@proses_kelahiran')->name('banjar-ajuan-kelahiran-proses');
        Route::post('/tolak/{id}', 'BanjarAdatController\AjuanKelahiranController@tolak_kelahiran')->name('banjar-ajuan-kelahiran-tolak');
        Route::get('/sahkan/{id}', 'BanjarAdatController\AjuanKelahiranController@sahkan_kelahiran')->name('banjar-ajuan-kelahiran-sahkan');
    });
    //AKHIR MANAJEMEN AJUAN KELAHIRAN ROUTES

    //MANAJEMEN AJUAN KEMATIAN ROUTES
    Route::group(['prefix' => 'ajuan-kematian'], function () {
        Route::get('/datatable', 'BanjarAdatController\AjuanKematianController@datatable')->name('banjar-ajuan-kematian-datatable');
        Route::get('/', 'BanjarAdatController\AjuanKematianController@index')->name('banjar-ajuan-kematian-home');
        Route::get('/detail/{id}', 'BanjarAdatController\AjuanKematianController@detail')->name('banjar-ajuan-kematian-detail');
        Route::get('/proses/{id}', 'BanjarAdatController\AjuanKematianController@proses_kematian')->name('banjar-ajuan-kematian-proses');
        Route::post('/tolak/{id}', 'BanjarAdatController\AjuanKematianController@tolak_kematian')->name('banjar-ajuan-kematian-tolak');
        Route::get('/sahkan/{id}', 'BanjarAdatController\AjuanKematianController@sahkan_kematian')->name('banjar-ajuan-kematian-sahkan');
    });
    //AKHIR MANAJEMEN AJUAN KEMATIAN ROUTES

    //PRAJURU BANJAR ADAT ROUTES
    Route::group(['middleware' => ['role:admin_banjar_adat'], 'prefix' => 'prajuru'], function () {
        Route::get('/datatable', 'BanjarAdatController\PrajuruBanjarAdatController@datatable')->name('banjar-prajuru-datatable');
        Route::get('/datatable-krama-mipil', 'BanjarAdatController\PrajuruBanjarAdatController@datatable_krama_mipil')->name('banjar-prajuru-datatable-krama-mipil');
        Route::get('/datatable-krama-mipil-edit', 'BanjarAdatController\PrajuruBanjarAdatController@datatable_krama_mipil_edit')->name('banjar-prajuru-datatable-krama-mipil-edit');
        Route::get('/', 'BanjarAdatController\PrajuruBanjarAdatController@index')->name('banjar-prajuru-home');
        Route::post('/store', 'BanjarAdatController\PrajuruBanjarAdatController@store')->name('banjar-prajuru-store');
        Route::get('/edit/{id}', 'BanjarAdatController\PrajuruBanjarAdatController@edit')->name('banjar-prajuru-edit');
        Route::post('/update/{id}', 'BanjarAdatController\PrajuruBanjarAdatController@update')->name('banjar-prajuru-update');
        Route::get('/detail/{id}', 'BanjarAdatController\PrajuruBanjarAdatController@detail')->name('banjar-prajuru-detail');
        Route::delete('/delete/{id}', 'BanjarAdatController\PrajuruBanjarAdatController@destroy')->name('banjar-prajuru-delete');
    });
    //AKHIR PRAJURU BANJAR ADAT ROUTES

    //PELAPORAN ROUTES
    Route::group(['prefix' => 'pelaporan'], function () {
        Route::group(['prefix' => 'krama'], function () {
            Route::get('/', 'BanjarAdatController\PelaporanKramaController@index')->name('banjar-laporan-krama-home');

            Route::post('/laporan/krama-mipil', 'BanjarAdatController\PelaporanKramaController@lapKramaMipil')->name('Laporan Krama Mipil');
            Route::get('/laporan/krama-mipil', function () {
                return redirect()->route('banjar-laporan-krama-home');
            });

            Route::post('laporan/krama-tamiu', 'BanjarAdatController\PelaporanKramaController@lapKramaTamiu')->name('Laporan Krama Tamiu');
            Route::get('/laporan/krama-tamiu', function () {
                return redirect()->route('banjar-laporan-krama-home');
            });

            Route::post('laporan/tamiu', 'BanjarAdatController\PelaporanKramaController@lapTamiu')->name('Laporan Tamiu');
            Route::get('/laporan/tamiu', function () {
                return redirect()->route('banjar-laporan-krama-home');
            });
        });

        Route::prefix('cacah-krama')->group( function(){
            Route::get('/', 'BanjarAdatController\PelaporanCacahController@index')->name('banjar-laporan-cacah-home');

            Route::post('/laporan/krama-mipil', 'BanjarAdatController\PelaporanCacahController@lapKramaMipil')->name('Laporan Cacah Krama Mipil');
            Route::get('/laporan/krama-mipil', function () {
                return redirect()->route('banjar-laporan-cacah-home');
            });

            Route::post('laporan/krama-tamiu', 'BanjarAdatController\PelaporanCacahController@lapKramaTamiu')->name('Laporan Cacah Krama Tamiu');
            Route::get('/laporan/krama-tamiu', function () {
                return redirect()->route('banjar-laporan-cacah-home');
            });

            Route::post('laporan/tamiu', 'BanjarAdatController\PelaporanCacahController@lapTamiu')->name('Laporan Cacah Tamiu');
            Route::get('/laporan/tamiu', function () {
                return redirect()->route('banjar-laporan-cacah-home');
            });
        });

        Route::prefix('download/pdf')->group( function(){
            Route::post('/laporan/krama-mipil', 'Exports\ExportPdfController@lapKramaMipil')->name('Download PDF Laporan Krama Mipil');
            Route::post('/laporan/krama-tamiu', 'Exports\ExportPdfController@lapKramaTamiu')->name('Download PDF Laporan Krama Tamiu');
            Route::post('/laporan/tamiu', 'Exports\ExportPdfController@lapTamiu')->name('Download PDF Laporan Tamiu');

            Route::post('/laporan/cacah-krama-mipil', 'Exports\ExportPdfController@lapCacahKramaMipil')->name('Download PDF Laporan Cacah Krama Mipil');
            Route::post('/laporan/cacah-krama-tamiu', 'Exports\ExportPdfController@lapCacahKramaTamiu')->name('Download PDF Laporan Cacah Krama Tamiu');
            Route::post('/laporan/cacah-tamiu', 'Exports\ExportPdfController@lapCacahTamiu')->name('Download PDF Laporan Cacah Tamiu');
        });

        Route::prefix('download/excel')->group( function(){
            Route::post('/laporan/krama-mipil', 'Exports\ExportExcelController@lapKramaMipil')->name('Download Excel Laporan Krama Mipil');
            Route::post('/laporan/krama-tamiu', 'Exports\ExportExcelController@lapKramaTamiu')->name('Download Excel Laporan Krama Tamiu');
            Route::post('/laporan/tamiu', 'Exports\ExportExcelController@lapTamiu')->name('Download Excel Laporan Tamiu');

            Route::post('/laporan/cacah-krama-mipil', 'Exports\ExportExcelController@lapCacahKramaMipil')->name('Download Excel Laporan Cacah Krama Mipil');
            Route::post('/laporan/cacah-krama-tamiu', 'Exports\ExportExcelController@lapCacahKramaTamiu')->name('Download Excel Laporan Cacah Krama Tamiu');
            Route::post('/laporan/cacah-tamiu', 'Exports\ExportExcelController@lapCacahTamiu')->name('Download Excel Laporan Cacah Tamiu');
        });

        //PELAPORAN MUTASI ROUTES
        Route::group(['prefix' => 'mutasi'], function () {
            Route::get('/', 'BanjarAdatController\PelaporanMutasiController@index')->name('banjar-laporan-mutasi-home');

            //PELAPORAN KELAHIRAN ROUTES
                Route::post('/laporan/kelahiran', 'BanjarAdatController\PelaporanMutasiController@lapKelahiran')->name('Laporan Kelahiran');
                Route::get('/laporan/kelahiran', function () {
                    return redirect()->route('Laporan Kelahiran');
                });
                Route::post('/laporan/kelahiran/download', 'Exports\ExportPdfController@lapKelahiran')->name('Download PDF Laporan Kelahiran');
            //AKHIR PELAPORAN KELAHIRAN ROUTES

            //PELAPORAN KEMATIAN ROUTES
                Route::post('/laporan/kematian', 'BanjarAdatController\PelaporanMutasiController@lapKematian')->name('Laporan Kematian');
                Route::get('/laporan/kematian', function () {
                    return redirect()->route('Laporan Kematian');
                });
                Route::post('/laporan/kematian/download', 'Exports\ExportPdfController@lapKematian')->name('Download PDF Laporan Kematian');
            //AKHIR PELAPORAN KEMATIAN ROUTES

            //PELAPORAN PERKAWINAN ROUTES
                Route::post('/laporan/perkawinan', 'BanjarAdatController\PelaporanMutasiController@lapPerkawinan')->name('Laporan Perkawinan');
                Route::get('/laporan/perkawinan', function () {
                    return redirect()->route('Laporan Perkawinan');
                });
                Route::post('/laporan/perkawinan/download', 'Exports\ExportPdfController@lapPerkawinan')->name('Download PDF Laporan Perkawinan');
            //AKHIR PELAPORAN PERKAWINAN ROUTES

            //PELAPORAN PERCERAIAN ROUTES
                Route::post('/laporan/perceraian', 'BanjarAdatController\PelaporanMutasiController@lapPerceraian')->name('Laporan Perceraian');
                Route::get('/laporan/perceraian', function () {
                    return redirect()->route('Laporan Perceraian');
                });
                Route::post('/laporan/perceraian/download', 'Exports\ExportPdfController@lapPerceraian')->name('Download PDF Laporan Perceraian');
            //AKHIR PELAPORAN PERCERAIAN ROUTES

            //PELAPORAN MAPERAS ROUTES
                Route::post('/laporan/maperas', 'BanjarAdatController\PelaporanMutasiController@lapMaperas')->name('Laporan Maperas');
                Route::get('/laporan/maperas', function () {
                    return redirect()->route('Laporan Maperas');
                });
                Route::post('/laporan/maperas/download', 'Exports\ExportPdfController@lapMaperas')->name('Download PDF Laporan Maperas');
            //AKHIR PELAPORAN MAPERAS ROUTES
        });
        //AKHIR PELAPORAN MUTASI ROUTES
    });
    //AKHIR PELAPORAN ROUTES

    //PROFILE ROUTES
        Route::get('/profile', 'BanjarAdatController\ProfileKramaController@profile')->name('banjar-profile-prajuru');
    //AKHIR PROFILE ROUTES
});
//AKHIR BANJAR ROUTES