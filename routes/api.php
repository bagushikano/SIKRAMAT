<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Auth::routes(['register'=>false, 'login'=>false, 'verify'=>true]);
Route::middleware('verified')->get('/user', function (Request $request) {
    return $request->user();
});

// pake verified untuk ngecek email nya udh di verif/blm
Route::get('/duar', function () {

})->middleware('verified');

Route::middleware('auth:api')->post('/notifikasi/store-firebase-token', 'NotifikasiController\NotifikasiController@storeFirebaseToken');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', 'AuthController\Api\AuthController@login')->name('login-api');


Route::post('/regis-check-nik', 'UserController\Api\RegisterController@registerCheckNik');
Route::post('/regis', 'UserController\Api\RegisterController@register');



Route::group(['prefix' => 'public'], function () {
    Route::get('/get-pendidikan', 'UserController\Api\PublicController@getPendidikan');
    Route::get('/get-pekerjaan', 'UserController\Api\PublicController@getPekerjaan');
    Route::get('/get-kabupaten', 'UserController\Api\PublicController@getKabupaten');
    Route::get('/get-kecamatan', 'UserController\Api\PublicController@getKecamatan');
    Route::get('/get-desa-adat', 'UserController\Api\PublicController@getDesaAdat');
    Route::get('/get-banjar-adat', 'UserController\Api\PublicController@getBanjarAdat');
    Route::get('/get-provinsi', 'UserController\Api\PublicController@getProvinsi');
    Route::get('/get-kabupaten-provinsi', 'UserController\Api\PublicController@getKabupatenProvinsi');
    Route::get('/get-desa-dinas', 'UserController\Api\PublicController@getDesaDinas');
});




Route::group(['prefix' => 'report'], function () {
    Route::get('/krama', 'BanjarAdatController\Api\PelaporanKramaController@index');
    Route::post('/pdf-krama-mipil/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKramaMipil')->name('PDF Krama Mipil');
    Route::post('/pdf-krama-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKramaTamiu')->name('PDF Krama Tamiu');
    Route::post('/pdf-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapTamiu')->name('PDF Tamiu');

    Route::post('/excel-krama-mipil/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapKramaMipil')->name('Excel Krama Mipil');
    Route::post('/excel-krama-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapKramaTamiu')->name('Excel Krama Tamiu');
    Route::post('/excel-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapTamiu')->name('Excel Tamiu');

    Route::get('/cacah-krama', 'BanjarAdatController\Api\PelaporanCacahKramaController@index');
    Route::post('/pdf-cacah-krama-mipil/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKramaMipil')->name('PDF Cacah Krama Mipil');
    Route::post('/pdf-cacah-krama-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKramaTamiu')->name('PDF Cacah Krama Tamiu');
    Route::post('/pdf-cacah-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapTamiu')->name('PDF Cacah Tamiu');

    Route::post('/excel-cacah-krama-mipil/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapKramaMipil')->name('Excel Cacah Krama Mipil');
    Route::post('/excel-cacah-krama-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapKramaTamiu')->name('Excel Cacah Krama Tamiu');
    Route::post('/excel-cacah-tamiu/{banjar_adat_id}', 'Exports\Mobile\ExportExcelController@lapTamiu')->name('Excel Cacah Tamiu');

    Route::get('/mutasi', 'BanjarAdatController\Api\PelaporanMutasiController@index');
    Route::post('/pdf-kelahiran/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKelahiran')->name('PDF Kelahiran');
    Route::post('/pdf-kematian/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapKematian')->name('PDF Kematian');
    Route::post('/pdf-perkawinan/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapPerkawinan')->name('PDF Perkawinan');
    Route::post('/pdf-perceraian/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapPerceraian')->name('PDF Perceraian');
    Route::post('/pdf-maperas/{banjar_adat_id}', 'Exports\Mobile\ExportPdfController@lapMaperas')->name('PDF Maperas');
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'integrasi'], function () {
    Route::get('/get-krama-mipil', 'ApiController\KramaController@getKramaMipil');
    Route::get('/get-cacah-krama-mipil', 'ApiController\KramaController@getCacahKramaMipil');
    Route::get('/search-cacah-krama-mipil', 'ApiController\KramaController@searchCacahKramaMipil');
    Route::get('/get-krama-tamiu', 'ApiController\KramaController@getKramaTamiu');
    Route::get('/get-tamiu', 'ApiController\KramaController@getTamiu');

    Route::post('/return-cacah-krama-mipil', 'ApiController\KramaController@returnCacahkrama');
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'notifikasi'], function () {
    Route::get('/get-notifikasi/{role}', 'NotifikasiController\NotifikasiController@getNotifikasi')->name('get-notifikasi');
    Route::get('/read-all-notifikasi/{role}', 'NotifikasiController\NotifikasiController@markAllAsRead')->name('read-all-notifikasi');
});


Route::group(['middleware' => ['auth:api'], 'prefix' => 'master'], function () {
    Route::get('/get-tempekan', 'UserController\Api\MasterController@getTempekan');
});


Route::group(['middleware' => ['auth:api'], 'prefix' => 'admin'], function () {
    Route::get('/test', 'AdminController\Api\DashboardController@test');
    Route::get('/dashboard', 'AdminController\Api\DashboardController@dashboardData');
    Route::get('/dashboard/krama', 'AdminController\Api\DashboardController@dashboardKramaAdminData');

    /**
     * Route untuk banjar adat start here
     */
    Route::group(['prefix' => 'banjar-adat'], function () {

        Route::group(['prefix' => 'profile'], function () {
            Route::get('/get', 'BanjarAdatController\Api\ProfileController@get');
        });
        Route::group(['prefix' => 'cacah-krama'], function () {
            Route::get('/get-mipil', 'BanjarAdatController\Api\CacahKramaMipilController@get');
            Route::get('/get-mipil-detail/{id}', 'BanjarAdatController\Api\CacahKramaMipilController@detail');
            Route::get('/get-mipil-by-tempekan', 'BanjarAdatController\Api\CacahKramaMipilController@getByTempekan');
            Route::get('/get-tempekan-list-with-krama', 'BanjarAdatController\Api\CacahKramaMipilController@getTempekanWithKrama');
            Route::get('/get-all-mipil', 'BanjarAdatController\Api\CacahKramaMipilController@getAllKrama');

            Route::get('/get-cacah-krama-tamiu', 'BanjarAdatController\Api\CacahKramaTamiuController@get');
            Route::get('/get-cacah-krama-tamiu-detail/{id}', 'BanjarAdatController\Api\CacahKramaTamiuController@detail');

            Route::get('/get-cacah-tamiu', 'BanjarAdatController\Api\CacahTamiuController@get');
            Route::get('/get-cacah-tamiu-detail/{id}', 'BanjarAdatController\Api\CacahTamiuController@detail');
        });

        Route::group(['prefix' => 'krama'], function () {
            Route::get('/get-mipil', 'BanjarAdatController\Api\KramaMipilController@get');
            Route::get('/get-mipil-detail/{id}', 'BanjarAdatController\Api\KramaMipilController@detail');
            Route::get('/get-mipil/{id}', 'BanjarAdatController\Api\KramaMipilController@getSingle');

            Route::get('/get-krama-tamiu', 'BanjarAdatController\Api\KramaTamiuController@get');
            Route::get('/get-krama-tamiu/{id}', 'BanjarAdatController\Api\KramaTamiuController@getSingle');
            Route::get('/get-krama-tamiu-detail/{id}', 'BanjarAdatController\Api\KramaTamiuController@detail');

            Route::get('/get-tamiu', 'BanjarAdatController\Api\TamiuController@get');
            Route::get('/get-tamiu/{id}', 'BanjarAdatController\Api\TamiuController@getSingle');
            Route::get('/get-tamiu-detail/{id}', 'BanjarAdatController\Api\TamiuController@detail');
        });

        Route::group(['prefix' => 'kelahiran'], function () {
            Route::get('/get', 'BanjarAdatController\Api\KelahiranController@get');
            Route::get('/detail/{id}', 'BanjarAdatController\Api\KelahiranController@detail');
            Route::get('/get-ajuan', 'BanjarAdatController\Api\KelahiranController@getAjuan');
            Route::get('/detail-ajuan/{id}', 'BanjarAdatController\Api\KelahiranController@detailAjuan');
            Route::get('/approve/{id}', 'BanjarAdatController\Api\KelahiranController@approveKelahiran');
            Route::post('/tolak', 'BanjarAdatController\Api\KelahiranController@tolakKelahiran');
            Route::post('/proses', 'BanjarAdatController\Api\KelahiranController@prosesKelahiran');
        });
        Route::group(['prefix' => 'kematian'], function () {
            Route::get('/get', 'BanjarAdatController\Api\KematianController@get');
            Route::get('/detail/{id}', 'BanjarAdatController\Api\KematianController@detail');
            Route::get('/get-ajuan', 'BanjarAdatController\Api\KematianController@getAjuan');
            Route::get('/detail-ajuan/{id}', 'BanjarAdatController\Api\KematianController@detailAjuan');
            Route::get('/approve/{id}', 'BanjarAdatController\Api\KematianController@approveKematian');
            Route::post('/tolak', 'BanjarAdatController\Api\KematianController@tolakKematian');
            Route::post('/proses', 'BanjarAdatController\Api\KematianController@prosesKematian');
        });
        Route::group(['prefix' => 'perkawinan'], function () {
            Route::get('/get', 'BanjarAdatController\Api\PerkawinanController@get');
            Route::get('/detail', 'BanjarAdatController\Api\PerkawinanController@detail');

            Route::get('/get-purusa', 'BanjarAdatController\Api\PerkawinanController@getPurusa');
            Route::get('/get-pradana', 'BanjarAdatController\Api\PerkawinanController@getPradana');

            Route::post('/store-satu-banjar', 'BanjarAdatController\Api\PerkawinanController@store_satu_banjar_adat');
            Route::post('/sah-satu-banjar', 'BanjarAdatController\Api\PerkawinanController@sah_satu_banjar_adat');

            Route::post('/store-beda-banjar-adat', 'BanjarAdatController\Api\PerkawinanController@store_beda_banjar_adat');
            Route::post('/edit-beda-banjar-adat', 'BanjarAdatController\Api\PerkawinanController@editBedaBanjar');
            Route::post('/sah-beda-banjar-adat', 'BanjarAdatController\Api\PerkawinanController@konfirmasi_perkawinan_masuk');
            Route::post('/konfirmasi-beda-banjar-adat', 'BanjarAdatController\Api\PerkawinanController@konfirmasi_perkawinan_keluar');
            Route::get('/delete-beda-banjar', 'BanjarAdatController\Api\PerkawinanController@destroy_satu_banjar');
            Route::post('/tolak-beda-banjar-adat', 'BanjarAdatController\Api\PerkawinanController@tolak_perkawinan_keluar');

            Route::get('/prepare-campuran-masuk', 'BanjarAdatController\Api\PerkawinanController@prepareCampuranMasuk');
            Route::post('/store-campuran-masuk', 'BanjarAdatController\Api\PerkawinanController@store_campuran_masuk');
            Route::post('/sah-campuran-masuk', 'BanjarAdatController\Api\PerkawinanController@sah_campuran_masuk');

            Route::get('/prepare-campuran-keluar', 'BanjarAdatController\Api\PerkawinanController@sah_campuran_keluar');
            Route::post('/store-campuran-keluar', 'BanjarAdatController\Api\PerkawinanController@store_campuran_keluar');
            Route::post('/sah-campuran-keluar', 'BanjarAdatController\Api\PerkawinanController@sah_campuran_keluar');
        });

        Route::group(['prefix' => 'maperas'], function () {
            Route::get('/get', 'BanjarAdatController\Api\MaperasController@get');
            Route::get('/detail', 'BanjarAdatController\Api\MaperasController@detail');

            Route::get('/get-krama-mipil-lama', 'BanjarAdatController\Api\MaperasController@getKramaMipilLama');
            Route::get('/get-krama-mipil-baru', 'BanjarAdatController\Api\MaperasController@getKramaMipilBaru');
            Route::get('/get-daftar-anak', 'BanjarAdatController\Api\MaperasController@getDaftarAnak');
            Route::get('/get-ortu-lama', 'BanjarAdatController\Api\MaperasController@getOrtuLama');
            Route::get('/get-ortu-baru', 'BanjarAdatController\Api\MaperasController@getOrtuBaru');

            Route::post('/store-satu-banjar', 'BanjarAdatController\Api\MaperasController@storeSatuBanjar');
            Route::get('/sah-satu-banjar', 'BanjarAdatController\Api\MaperasController@sahSatuBanjar');

            Route::post('/store-beda-banjar-adat', 'BanjarAdatController\Api\MaperasController@storeBedaBanjar');
            Route::post('/edit-beda-banjar-adat', 'BanjarAdatController\Api\MaperasController@editBedaBanjar');
            Route::get('/sah-beda-banjar-adat', 'BanjarAdatController\Api\MaperasController@konfirmasiMasukBanjar');
            Route::get('/konfirmasi-beda-banjar-adat', 'BanjarAdatController\Api\MaperasController@konfirmasiKeluarBanjar');
            Route::post('/tolak-beda-banjar-adat', 'BanjarAdatController\Api\MaperasController@tolakBedaBanjar');
            Route::get('/delete-beda-banjar', 'BanjarAdatController\Api\MaperasController@destroy_satu_banjar');

            Route::get('/sah-campuran-masuk', 'BanjarAdatController\Api\MaperasController@sahCampuranMasuk');
            Route::post('/store-campuran-masuk', 'BanjarAdatController\Api\MaperasController@storeCampuranMasuk');

            Route::get('/sah-campuran-keluar', 'BanjarAdatController\Api\MaperasController@sahCampuranKeluar');
            Route::post('/store-campuran-keluar', 'BanjarAdatController\Api\MaperasController@storeCampuranKeluar');

        });

        Route::group(['prefix' => 'perceraian'], function () {
            Route::get('/get', 'BanjarAdatController\Api\PerceraianController@get');
            Route::get('/detail', 'BanjarAdatController\Api\PerceraianController@detail');

            Route::get('/get-list-krama-mipil', 'BanjarAdatController\Api\PerceraianController@getKramaMipil');
            Route::get('/get-krama-mipil-selected', 'BanjarAdatController\Api\PerceraianController@getKramaMipilSelectedWithAnggota');
            Route::post('/get-list-krama-tujuan', 'BanjarAdatController\Api\PerceraianController@getKramaMipilBaruForKrama');
            Route::post('/get-list-krama-pasangan', 'BanjarAdatController\Api\PerceraianController@getKramaMipilBaruForPasangan');
            Route::post('/store', 'BanjarAdatController\Api\PerceraianController@store');

            Route::get('/sah', 'BanjarAdatController\Api\PerceraianController@sahPerceraian');
            Route::get('/konfirmasi', 'BanjarAdatController\Api\PerceraianController@konfirmasi_perceraian');
            Route::post('/tolak', 'BanjarAdatController\Api\PerceraianController@tolak_perceraian');
        });
    });

    /**
     * Route untuk desa adat start here
     */
    Route::group(['prefix' => 'banjar'], function () {
        Route::get('/get-banjar-adat', 'DesaAdatController\Api\BanjarController@getAdat');
        Route::get('/get-banjar-dinas', 'DesaAdatController\Api\BanjarController@getDinas');
    });

    Route::group(['prefix' => 'cacah-krama'], function () {
        Route::get('/get-mipil', 'DesaAdatController\Api\CacahKramaMipilController@get');
        Route::get('/get-mipil-detail/{id}', 'DesaAdatController\Api\CacahKramaMipilController@detail');

        Route::get('/get-tamiu', 'DesaAdatController\Api\CacahKramaTamiuController@get');
        Route::get('/get-tamiu-detail/{id}', 'DesaAdatController\Api\CacahKramaTamiuController@detail');
    });

    Route::group(['prefix' => 'krama'], function () {
        Route::get('/get-mipil', 'DesaAdatController\Api\KramaMipilController@get');
        Route::get('/get-mipil-detail/{id}', 'DesaAdatController\Api\KramaMipilController@detail');
        Route::get('/get-mipil/{id}', 'DesaAdatController\Api\KramaMipilController@getSingle');

        Route::get('/get-krama-tamiu', 'DesaAdatController\Api\KramaTamiuController@get');
        Route::get('/get-krama-tamiu-detail/{id}', 'DesaAdatController\Api\KramaTamiuController@detail');
    });
});

/**
 * Route untuk user start here
 */
Route::group(['middleware' => ['auth:api'], 'prefix' => 'user'], function () {
    Route::post('/change-password', 'AuthController\Api\AuthController@changePassword');
    Route::group(['prefix' => 'krama'], function () {
        Route::get('/get-mipil', 'UserController\Api\KramaController@getKramaMipil');
        Route::get('/get-mipil-detail/{id}', 'DesaAdatController\Api\KramaMipilController@detail');
        Route::get('/get-mipil/{id}', 'DesaAdatController\Api\KramaMipilController@getSingle');

        Route::get('/get-tamiu', 'UserController\Api\KramaController@getKramaTamiu');
        Route::get('/get-tamiu-detail/{id}', 'DesaAdatController\Api\KramaTamiuController@detail');
        Route::get('/get-tamiu/{id}', 'DesaAdatController\Api\KramaMipilController@getSingle');

        Route::get('/kartu-keluarga/{id}', 'UserController\Api\KramaController@kartuKeluargaMipil');
    });

    Route::group(['prefix' => 'cacah-krama'], function () {
        Route::get('/get-cacah-krama', 'UserController\Api\CacahKramaController@getCacahMipilTamiu');
        Route::get('/get-cacah-mipil', 'UserController\Api\CacahKramaController@getCacahMipil');
        Route::get('/get-cacah-tamiu', 'UserController\Api\CacahKramaController@getCacahTamiu');
    });

    Route::group(['prefix' => 'master'], function () {
        Route::get('/get-pendidikan', 'UserController\Api\MasterController@getPendidikan');
        Route::get('/get-profesi', 'UserController\Api\MasterController@getProfesi');
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::post('/edit-foto', 'UserController\Api\ProfileController@changeFoto');
        Route::post('/edit-profile', 'UserController\Api\ProfileController@editProfile');
    });


    Route::group(['prefix' => 'penduduk'], function () {
        Route::get('/get', 'UserController\Api\PendudukController@getPenduduk');

    });

    Route::group(['prefix' => 'kelahiran'], function () {
        Route::post('/create-pengajuan', 'UserController\Api\KelahiranController@storePengajuan');
        Route::get('/get', 'UserController\Api\KelahiranController@get');
        Route::get('/detail/{id}', 'UserController\Api\KelahiranController@detail');
        Route::get('/get-ajuan', 'UserController\Api\KelahiranController@getAjuan');
        Route::get('/detail-ajuan/{id}', 'UserController\Api\KelahiranController@detailAjuan');
    });
    Route::group(['prefix' => 'kematian'], function () {
        Route::get('/get-list-cacah-mipil', 'UserController\Api\KematianController@getListCacahMipil');
        Route::post('/create-pengajuan', 'UserController\Api\KematianController@storeKematian');
        Route::get('/get', 'UserController\Api\KematianController@get');
        Route::get('/detail/{id}', 'UserController\Api\KematianController@detail');
        Route::get('/get-ajuan', 'UserController\Api\KematianController@getAjuan');
        Route::get('/detail-ajuan/{id}', 'UserController\Api\KematianController@detailAjuan');
    });
});



