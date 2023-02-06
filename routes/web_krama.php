<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Krama Routes
|--------------------------------------------------------------------------
|
| Route untuk halaman krama
| Setting route dapat diakses pada RouteServiceProvider.php
|
*/

# | Registrasi Krama Desa Adat
Route::get('/register', 'KramaController\Register\RegisterController@register_awal')->name('Register Awal')->middleware('guest');
Route::post('/register', 'KramaController\Register\RegisterController@register_akhir')->name('Register Akhir')->middleware('guest');
Route::post('/register/success', 'KramaController\Register\RegisterController@register')->name('Register')->middleware('guest');


Route::group(['middleware' => ['role:krama,bendesa,pangliman,penyarikan,patengen,kelihan_adat,pangliman_banjar,penyarikan_banjar,patengen_banjar', 'verified'], 'prefix' => 'krama'], function () {

    # | Dashboard Krama Desa Adat
    Route::get('/', 'KramaController\Dashboard\DashboardKramaController@index')->name('Dashboard Krama');

    # | Profile Krama Desa Adat
    Route::get('/profile', 'KramaController\Profile\ProfileKramaController@profile')->name('Profile Krama');
    Route::post('/profile/change-password/{user_id}', 'KramaController\Profile\ProfileKramaController@changePassword')->name('Change Password');
    Route::post('/profile/change-img/{penduduk_id}', 'KramaController\Profile\ProfileKramaController@changeProfileImage')->name('Change Profile Image Krama');
    Route::get('/profile/show-img/{penduduk_id}', 'KramaController\Profile\ProfileKramaController@showProfileImage')->name('Show Profile Image Krama');
    Route::post('/profile/change-profile/{penduduk_id}', 'KramaController\Profile\ProfileKramaController@changeProfile')->name('Change Profile Krama');

    # | Anggota Keluarga Krama Desa Adat
    Route::get('/keluarga', 'KramaController\Keluarga\KeluargaController@index')->name('Keluarga Krama');
    Route::get('/keluarga/detail-krama/{id}/', 'KramaController\Keluarga\KeluargaController@detail_krama')->name('Detail Krama');
    Route::get('/keluarga/detail-anggota/{id}/', 'KramaController\Keluarga\KeluargaController@detail_anggota')->name('Detail Anggota');
    Route::get('/kartu-keluarga/{id}', 'KramaController\Keluarga\KeluargaController@kartu_keluarga')->name('Kartu Keluarga Krama');

    # | Ajuan Data Kelahiran
    Route::get('/kelahiran', 'KramaController\Kelahiran\KelahiranController@index')->name('Kelahiran Home');
    Route::get('/kelahiran/detail/{id}', 'KramaController\Kelahiran\KelahiranController@detail_kelahiran')->name('Kelahiran Detail');

    Route::get('/kelahiran/ajuan/create', 'KramaController\Kelahiran\KelahiranController@create_ajuan')->name('Kelahiran Create Ajuan');
    Route::get('/kelahiran/ulang-ajuan/{id}/create', 'KramaController\Kelahiran\KelahiranController@create_ajuan_ulang')->name('Kelahiran Create Ajuan Ulang');
    Route::post('/kelahiran/ajuan/store', 'KramaController\Kelahiran\KelahiranController@store_ajuan')->name('Kelahiran Store Ajuan');
    Route::get('/kelahiran/ajuan/detail/{id}', 'KramaController\Kelahiran\KelahiranController@detail_ajuan')->name('Kelahiran Detail Ajuan');

    # | Ajuan Data Kematian
    Route::get('/kematian', 'KramaController\Kematian\KematianController@index')->name('Kematian Home');
    Route::get('/kematian/detail/{id}', 'KramaController\Kematian\KematianController@detail_kematian')->name('Kematian Detail');

    Route::get('/kematian/ajuan/create', 'KramaController\Kematian\KematianController@create_ajuan')->name('Kematian Create Ajuan');
    Route::get('/kematian/ulang-ajuan/{id}/create', 'KramaController\Kematian\KematianController@create_ajuan_ulang')->name('Kematian Create Ajuan Ulang');
    Route::post('/kematian/ajuan/store', 'KramaController\Kematian\KematianController@store_ajuan')->name('Kematian Store Ajuan');
    Route::get('/kematian/ajuan/detail/{id}', 'KramaController\Kematian\KematianController@detail_ajuan')->name('Kematian Detail Ajuan');
});
